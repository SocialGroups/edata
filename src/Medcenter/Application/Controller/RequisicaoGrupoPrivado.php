<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class RequisicaoGrupoPrivado implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Solicitar Requisição grupo privado

        public function VerificaSolicitacaoDePermissao($grupoID)
        {

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu

            $usuarioID = $_SESSION['usuario_id'];


            // Verifica se já existe uma solicitação
            $getSolicitacaoExistente = $this->mapper->grupo_privado_solicitacao(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetchall();

            // Recupera dados do grupo solicitado
             $SQL = "SELECT g.id, g.privilegios, g.nome, g.descricao, g.grupo_avatar, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes FROM grupo as g

                        LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id

                        WHERE g.id = '$grupoID'
                    ";

                $getGrupoDados = $db = new DB($this->c->pdo);
                $getGrupoDados = $db->query($SQL);
                $getGrupoDados = $db->fetchAll();

            foreach ($getGrupoDados as $dadosGrupo) {
                
                $grupoPrivilegios = $dadosGrupo->privilegios;
            }

            if($grupoPrivilegios == 'publico'){

                // Redireciona usuário para a pagina do grupo
                header("Location: /grupos/get/$grupoID");
                exit;

            }else{

            $numeroSolicitacoes = COUNT($getSolicitacaoExistente);

            
            if($numeroSolicitacoes == 0){

                $vars['PermissaoSolicitacao'] = 'true';

            }else{

                foreach ($getSolicitacaoExistente as $dadosSolicitacao) {
                    
                    $vars['PermissaoSolicitacao'] = $dadosSolicitacao->status;

                }

            }

            $vars['dadosGrupo'] = $getGrupoDados;
            $vars['grupoID']    = $grupoID;
            $vars['numeroSolicitacoes'] = $getDadosmenu->GrupoSolicitacoesPendentes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();  
            $vars['_view']      = 'requisicaoGrupoPrivado.html.twig';
            return $vars;

            }


        }

    // Solicitar Requisição grupo privado

    // Insere solicitação no banco de dados

        public function solicitacao($grupoID, $solicitacao)
        {   
            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se já existe uma solicitação
            $getSolicitacaoExistente = COUNT($this->mapper->grupo_privado_solicitacao(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetchall());

            if($getSolicitacaoExistente == 0){

                // Insere Solicitação no banco de dados
                $InsertSolicitacao = new \StdClass;

                $InsertSolicitacao->grupo_id     = $grupoID;
                $InsertSolicitacao->usuario_id   = $usuarioID; 
                $InsertSolicitacao->pedido       = $solicitacao; 
                $InsertSolicitacao->dataHora     = date('Y-m-d H:i:s');
                $InsertSolicitacao->status       = 'pendente';

                $this->mapper->grupo_privado_solicitacao->persist($InsertSolicitacao);
                $this->mapper->flush();

                // Insere Solicitação no trigger de solicitações
                $InsertTriggerSolicitacao = new \StdClass;

                $InsertTriggerSolicitacao->grupo_id     = $grupoID;
                $InsertTriggerSolicitacao->usuario_id   = $usuarioID; 
                $InsertTriggerSolicitacao->dataHora     = date('Y-m-d H:i:s');
                $InsertTriggerSolicitacao->status       = 'pendente';

                $this->mapper->trigger_solicitacao_acesso->persist($InsertTriggerSolicitacao);
                $this->mapper->flush();


                $return = 'true';

            }else{

                $return = 'false';
            }

            return $return;

        }

    // Insere solicitação no banco de dados


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    
           $grupoID = $arg2;

            return $this->VerificaSolicitacaoDePermissao($grupoID);

    }


    public function post($acao = null, $arg1 = null)
    {   

        $grupoID     = $_POST['grupoID'];
        $solicitacao = $_POST['solicitacao'];
        
        return $this->solicitacao($grupoID, $solicitacao);

    }

}
