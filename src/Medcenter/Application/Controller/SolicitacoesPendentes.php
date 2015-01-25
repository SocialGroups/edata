<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class SolicitacoesPendentes implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Recupera solicitações pendentes

        public function SolicitacoesPendentes()
        {

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu  

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se o usuário é administrador de algum grupo privado
            $getGrupoPrivado = $this->mapper->grupo(array('usuario_criacao' => $usuarioID, 'privilegios' => 'privado'))->fetchAll();

            // Recupera Quantidade de Solicitações
            $arraySolicitacoesPendentes = array();

                foreach ($getGrupoPrivado as $dadosGruposPrivado) {
                   
                    $grupoID = $dadosGruposPrivado->id;

                     $SQLDadosSolicitacao = "SELECT gps.id, gps.grupo_id, gps.pedido, gps.dataHora, g.nome, g.grupo_avatar,user.id as usuarioID, user.nome as userNome, user.sobre_nome, user.foto_perfil FROM grupo_privado_solicitacao as gps
                                             INNER JOIN grupo as g ON gps.grupo_id = g.id
                                             INNER JOIN usuario as user ON gps.usuario_id = user.id

                                             WHERE gps.grupo_id = '$grupoID' AND gps.status = 'pendente'

                                             ORDER BY gps.id DESC";

                    $getDadosSolicitacao = $db = new DB($this->c->pdo);
                    $getDadosSolicitacao = $db->query($SQLDadosSolicitacao);
                    $getDadosSolicitacao = $db->fetchAll(); 

                    $arraySolicitacoesPendentes[] = $getDadosSolicitacao;

                }

            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['solicitacoesPendentes'] = $arraySolicitacoesPendentes;
            $vars['numeroSolicitacoes'] = $getDadosmenu->GrupoSolicitacoesPendentes(); 
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();  
            $vars['_view']  = 'solicitacoes.html.twig';

            return $vars;

        }

    // recupera solicitações pendentes

    // Acao Solicitação

        public function acaoSolicitacao($grupoID,$usuarioID,$acao)
        {   
            $usuarioAcaoID = $_SESSION['usuario_id'];

            // Validações de segurança

                // Verifica se usuário que executou a ação é o criador do grupo
               $validaCriadorGrupo = COUNT($this->mapper->grupo(array('id' => $grupoID, 'usuario_criacao' => $usuarioAcaoID))->fetchAll());

            // Validações de segurança

            if($validaCriadorGrupo > 0){

                // Verifica ação a ser tomada
                if($acao == 'aceitar'){

                // Atualiza status da solicitação
                $AtualizaStatusSolicitacao = $this->mapper->grupo_privado_solicitacao(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetch();
                $AtualizaStatusSolicitacao->status = "aceito";
                $this->mapper->grupo_privado_solicitacao->persist($AtualizaStatusSolicitacao);
                $this->mapper->flush(); 

                // Atualiza Trigger ação acesso
                $AtualizaTrigger = $this->mapper->trigger_solicitacao_acesso(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetch();
                $AtualizaTrigger->status = "visualizado";
                $this->mapper->trigger_solicitacao_acesso->persist($AtualizaTrigger);
                $this->mapper->flush();

                // Insere usuário como seguidor do grupo
                $InsertSeguirGrupo = new \StdClass;

                $InsertSeguirGrupo->grupo_id       = $grupoID;
                $InsertSeguirGrupo->usuario_id     = $usuarioID;
                $InsertSeguirGrupo->nivel          = 'usuario';
                $InsertSeguirGrupo->status         = 'Ativo';  

                $this->mapper->grupo_usuario->persist($InsertSeguirGrupo);
                $this->mapper->flush();

                // libera acesso ao grupo para este usuário no banco de dados
                $InsertAcessoGrupo = new \StdClass;

                $InsertAcessoGrupo->grupo_id       = $grupoID;
                $InsertAcessoGrupo->usuario_id     = $usuarioID; 

                $this->mapper->usuario_permissao->persist($InsertAcessoGrupo);
                $this->mapper->flush();

                return 'true';

                }else{

               // Atualiza status da solicitação
                $AtualizaStatusSolicitacao = $this->mapper->grupo_privado_solicitacao(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetch();
                $AtualizaStatusSolicitacao->status = "negado";
                $this->mapper->grupo_privado_solicitacao->persist($AtualizaStatusSolicitacao);
                $this->mapper->flush(); 

                // Atualiza Trigger ação acesso
                $AtualizaTrigger = $this->mapper->trigger_solicitacao_acesso(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetch();
                $AtualizaTrigger->status = "visualizado";
                $this->mapper->trigger_solicitacao_acesso->persist($AtualizaTrigger);
                $this->mapper->flush(); 

                return 'true';

                }

            }else{

                return 'false';

            }

        }

    // Ação Solicitação


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {     
            return $this->SolicitacoesPendentes();

    }


    public function post($acao = null, $arg1 = null)
    {   

        $grupoID    = $_POST['grupoID'];
        $usuarioID  = $_POST['usuarioID'];
        $acao       = $_POST['acaoSolicitacao'];
        
        return $this->acaoSolicitacao($grupoID,$usuarioID,$acao);

    }

}
