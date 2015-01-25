<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\InjectionGrupoDados;
use SocialGroups\Application\Controller\InjectionDadosNeo4j;

class SeguirGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    
    // Seguir Grupo

        public function SeguirUsuario($profileID)
        {   

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se o grupo não é um grupo privado
            $verificaTipoGrupo = COUNT($this->mapper->seguidores(array('follower_id' => $usuarioID, 'following_id' => $profileID))->fetchAll());

            if($verificaTipoGrupo == 0){

                // Insere usuário como seguidor do grupo

                $adicionaUsuario = new \stdClass;
                $adicionaUsuario->follower_id     = $usuarioID;
                $adicionaUsuario->following_id    = $profileID

                $this->mapper->seguidores->persist($adicionaUsuario);
                $this->mapper->flush();

            }else{


            }

           
        }

    // Seguir Grupo


    // Método responsável por deixa de seguir um grupo

        public function unfollow($profileID)
        {

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se o usuário já segue o usuário
            $Verifica = COUNT($this->mapper->seguidores(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

            if($Verifica == 1){

                    // Atualiza status para ativo novamente
                    $reativaFollowerGrupo = $this->mapper->grupo_usuario(array('usuario_id' => $usuarioID, 'grupo_id' => $grupoID))->fetch();
                    $reativaFollowerGrupo->ativo = 1;
                    $this->mapper->grupo_usuario->persist($reativaFollowerGrupo);
                    $this->mapper->flush();

                    // Injection Grupo Dados
                    $InjectionGrupoDados = new InjectionGrupoDados();
                    $coluna = 'seguidores';
                    $tipoUpdate = 'delete';
                    $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                    // Injection Grupo Dados

                    return 'true';

            }else{

                return 'false';
            }


        }

    // Método responsável por deixar de seguir um grupo



    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {       
        

    }


    public function post($acao = null, $arg1 = null)
    {   

        if(isset($_POST['tipoFollow']) AND $_POST['tipoFollow'] == 'unfollow'){
        
            return $this->unfollow($grupoID);

        }else{

             $profileID    = $_POST['usuarioID'];
        
            return $this->SeguirUsuario($profileID);

        }

    }

}
