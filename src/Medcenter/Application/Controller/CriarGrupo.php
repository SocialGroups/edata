<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;
use SocialGroups\Application\Controller\LiveMention;
use SocialGroups\Application\Controller\InjectionGrupoDados;
use SocialGroups\Application\Controller\DeleteObjectCache;
use SocialGroups\Application\Controller\InjectionDadosNeo4j;

class CriarGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    
    // Método responsável por criar um grupo

        public function criarGrupo($grupoNome,$grupoDescricao,$convidarSeguidores)
        {  

            if($grupoNome > '' AND $convidarSeguidores > '' AND $grupoDescricao > ''){

            // Verifica se todos os dados obrigatórios foram preenchidos

                $usuarioID  = $_SESSION['usuario_id'];
                $capa       = '/img/capa_grupo.png';

                // Cadastra Grupo no banco de dados
                $cadastraGrupo = new \stdClass;
                $cadastraGrupo->nome            = $grupoNome;
                $cadastraGrupo->descricao       = $grupoDescricao;
                $cadastraGrupo->privilegios     = 'publico';
                $cadastraGrupo->grupo_tipo      = 'permanente';
                $cadastraGrupo->dataCriacao     = date('Y-m-d H:i:s');
                $cadastraGrupo->usuario_criacao = $usuarioID;
                $cadastraGrupo->grupo_avatar    = $capa;
                $cadastraGrupo->grupo_status    = 'ativo';

                $this->mapper->grupo->persist($cadastraGrupo);
                $this->mapper->flush();
                // Cadastra Grupo no banco de dados

                $grupoID = $cadastraGrupo->id;
                $nome    = $grupoNome;

                // Adiciona Grupo No neo4j
                $AdicionaGrupoNeo4j = new InjectionDadosNeo4j();
                $AdicionaGrupoNeo4j->neo4jAdicionaGrupo($grupoID, $nome);
                // Adiciona Grupo No neo4j



                // Cadastra dados do grupo na tabela de informações

                $cadastraGrupoInfo = new \stdClass;
                $cadastraGrupoInfo->grupo_id            = $cadastraGrupo->id;
                $cadastraGrupoInfo->numero_seguidores   = 0;
                $cadastraGrupoInfo->numero_boxes        = 0;
                $cadastraGrupoInfo->numero_interacoes   = 0;

                $this->mapper->grupo_informacoes->persist($cadastraGrupoInfo);
                $this->mapper->flush();

                $return = $grupoID;
                // Cadastra dados do grupo na tabela de informações


                // Convidar followers para o grupo

                    $getFollowers = $this->mapper->seguidores(array('following_id' => $usuarioID))->fetchAll();

                    foreach ($getFollowers as $dadosFollowers) {
                       
                        // Envia Convite para todos os followers
                            $InsertNotificacaoPendente = new \StdClass;

                            $InsertNotificacaoPendente->autor_id    = $usuarioID;
                            $InsertNotificacaoPendente->usuario_id  = $dadosFollowers->follower_id; 
                            $InsertNotificacaoPendente->tipo        = 'convite'; 
                            $InsertNotificacaoPendente->value       = $grupoID; 

                            $this->mapper->notificacoes->persist($InsertNotificacaoPendente);
                            $this->mapper->flush();

                    }

                // Convidar followers para o grupo

                // Cadastra este usuário como seguidor deste grupo
                $cadastraUsuarioFollowGrupo = new \stdClass;
                $cadastraUsuarioFollowGrupo->grupo_id     = $grupoID;
                $cadastraUsuarioFollowGrupo->usuario_id   = $usuarioID;
                $cadastraUsuarioFollowGrupo->nivel        = 'usuario';
                
                $this->mapper->grupo_usuario->persist($cadastraUsuarioFollowGrupo);
                $this->mapper->flush();


            }else{

                $return = false;
            }
            // Verifica se todos os dados obrigatórios foram preenchidos

                return $return;

        }

    // Método responsável por criar um grupo

    public function criarGrupoFrontEnd()
    {

        $vars['_view'] = 'criar-grupo.html.twig';

        return $vars;


    }

    public function criarGrupoFrontEndP2($grupoNome)
    {

        $vars['nome']  = $grupoNome;
        $vars['_view'] = 'criar-grupo-p2.html.twig';

        return $vars;


    }

    public function criarGrupoPublico()
    {

        $vars['_view'] = 'criar-grupo-publico.html.twig';
        return $vars;

    }

    public function criarGrupoPrivado()
    {

        $vars['_view'] = 'criar-grupo-privado.html.twig';
        return $vars;

    }

    public function criarGrupoPVP()
    {

        $vars['_view'] = 'criar-grupo-pvp.html.twig';
        return $vars;

    }

    public function get($acao = null, $arg1 = null)
    {   

        return $this->criarGrupoFrontEnd();
    }


    public function post($acao = null, $arg1 = null)
    {

                $grupoNome          = $_POST['nome'];
                $grupoDescricao     = $_POST['descricao'];
                $convidarSeguidores = $_POST['convidarFollowers'];

                return $this->criarGrupo($grupoNome,$grupoDescricao,$convidarSeguidores);
    }
}
