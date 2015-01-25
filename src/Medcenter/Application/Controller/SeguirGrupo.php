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

        public function SeguirGrupo($grupoID)
        {   

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se o grupo não é um grupo privado
            $verificaTipoGrupo = $this->mapper->grupo(array('id' => $grupoID))->fetchAll();

            foreach ($verificaTipoGrupo as $dadosGrupo) {
                
                if($dadosGrupo->privilegios == 'publico'){

                    // Verifica se o usuário já segue o grupo
                    $Verifica = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

                    if($Verifica == 0){

                        // Insere usuário como seguidor do grupo

                            $adicionaUsuario = new \stdClass;
                            $adicionaUsuario->grupo_id      = $grupoID;
                            $adicionaUsuario->usuario_id    = $_SESSION['usuario_id'];
                            $adicionaUsuario->nivel         = 'usuario';
                            $adicionaUsuario->status        = 'ativo';

                            $this->mapper->grupo_usuario->persist($adicionaUsuario);
                            $this->mapper->flush();

                        // Insere usuário como seguidor do grupo

                        // Injection Grupo Dados
                        $InjectionGrupoDados = new InjectionGrupoDados();
                        $coluna = 'seguidores';
                        $tipoUpdate = 'insert';
                        $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                        // Injection Grupo Dados

                        // Adiciona Grupo No neo4j
                        $AdicionaSeguirGrupoNeo4j = new InjectionDadosNeo4j();
                        $AdicionaSeguirGrupoNeo4j->neo4jAdicionaSeguidorGrupo($usuarioID, $grupoID);
                        // Adiciona Grupo No neo4j                            

                        return 'true';

                    }else{

                        // Verifica se o usuário já seguiu este grupo em algum momento
                        $verificaSeguiuAnteriormente = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id'], 'ativo' => 1))->fetchAll());

                        if($verificaSeguiuAnteriormente == 1){

                            // Atualiza status para ativo novamente
                            $reativaFollowerGrupo = $this->mapper->grupo_usuario(array('usuario_id' => $usuarioID, 'grupo_id' => $grupoID))->fetch();
                            $reativaFollowerGrupo->ativo = 0;
                            $this->mapper->grupo_usuario->persist($reativaFollowerGrupo);
                            $this->mapper->flush();

                            // Injection Grupo Dados
                            $InjectionGrupoDados = new InjectionGrupoDados();
                            $coluna = 'seguidores';
                            $tipoUpdate = 'insert';
                            $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                            // Injection Grupo Dados

                            return 'true';


                            // Atualiza status para ativo novamente

                        }else{

                            return 'false';
                        }
                    }


                }else{

                    $Verifica = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

                    $validaSeguraca = COUNT($this->mapper->usuario_permissao(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

                    if($Verifica == 0 AND $validaSeguraca == 1){

                        // Insere usuário como seguidor do grupo

                            $adicionaUsuario = new \stdClass;
                            $adicionaUsuario->grupo_id      = $grupoID;
                            $adicionaUsuario->usuario_id    = $_SESSION['usuario_id'];
                            $adicionaUsuario->nivel         = 'usuario';
                            $adicionaUsuario->status        = 'ativo';

                            $this->mapper->grupo_usuario->persist($adicionaUsuario);
                            $this->mapper->flush();

                        // Insere usuário como seguidor do grupo

                        // Injection Grupo Dados
                        $InjectionGrupoDados = new InjectionGrupoDados();
                        $coluna = 'seguidores';
                        $tipoUpdate = 'insert';
                        $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                        // Injection Grupo Dados

                        return 'true';

                    }else{

                        //Verifica se o usuário já seguiu este grupo em algum momento
                        $verificaSeguiuAnteriormente = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id'], 'ativo' => 1))->fetchAll());

                        $validaSeguraca = COUNT($this->mapper->usuario_permissao(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

                        if($verificaSeguiuAnteriormente == 1 AND $validaSeguraca == 1){

                            // Atualiza status para ativo novamente
                            $reativaFollowerGrupo = $this->mapper->grupo_usuario(array('usuario_id' => $usuarioID, 'grupo_id' => $grupoID))->fetch();
                            $reativaFollowerGrupo->ativo = 0;
                            $this->mapper->grupo_usuario->persist($reativaFollowerGrupo);
                            $this->mapper->flush();

                            // Injection Grupo Dados
                            $InjectionGrupoDados = new InjectionGrupoDados();
                            $coluna = 'seguidores';
                            $tipoUpdate = 'insert';
                            $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                            // Injection Grupo Dados

                            return 'true';


                            // Atualiza status para ativo novamente

                        }else{

                            // Verifica se o usuário já seguiu esta grupo uma vex
                            $verificaSeguiuAnteriormente = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id'], 'ativo' => 0))->fetchAll());

                            $validaSeguraca = COUNT($this->mapper->usuario_permissao(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

                            if($verificaSeguiuAnteriormente == 1 AND $validaSeguraca == 1){

                            // Atualiza status para ativo novamente
                            $reativaFollowerGrupo = $this->mapper->grupo_usuario(array('usuario_id' => $usuarioID, 'grupo_id' => $grupoID))->fetch();
                            $reativaFollowerGrupo->ativo = 0;
                            $this->mapper->grupo_usuario->persist($reativaFollowerGrupo);
                            $this->mapper->flush();

                            // Injection Grupo Dados
                            $InjectionGrupoDados = new InjectionGrupoDados();
                            $coluna = 'seguidores';
                            $tipoUpdate = 'insert';
                            $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                            // Injection Grupo Dados

                            return 'true';


                            }
                        }

                    }

                }

            }
        }

    // Seguir Grupo


    // Método responsável por deixa de seguir um grupo

        public function unfollow($grupoID)
        {

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se o usuário já segue o gurpo
            $Verifica = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

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

             $grupoID    = $_POST['grupoID'];
        
            return $this->unfollow($grupoID);

        }else{

             $grupoID    = $_POST['grupoID'];
        
            return $this->SeguirGrupo($grupoID);

        }

    }

}
