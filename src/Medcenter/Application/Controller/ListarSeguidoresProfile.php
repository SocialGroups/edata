<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class ListarSeguidoresProfile implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por listar seguidores de um grupo

        public function listarProfileSeguidores($profileID)
        {

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se o usuário segue este usuario
            $validacaoSeguranca = COUNT($this->mapper->seguidores(array('follower_id' => $usuarioID, 'following_id' => $profileID))->fetchAll());

            if($validacaoSeguranca == 1 OR $usuarioID == $profileID){

                // Recupera usuários do grupo
                 $SQL = "SELECT se.id, se.follower_id, se.following_id, se.ativo, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil, user.nick_name
                         FROM seguidores as se
                         INNER JOIN usuario as user ON user.id = se.follower_id

                         WHERE se.following_id = '$profileID'

                         order by se.id desc

                         LIMIT 20
                    ";

                $getGrupoSeguidores = $db = new DB($this->c->pdo);
                $getGrupoSeguidores = $db->query($SQL);
                $getGrupoSeguidores = $db->fetchAll();

                $numeroSeguidoresProfile = COUNT($getGrupoSeguidores);

                $vars['numeroSeguidores']   = $numeroSeguidoresProfile;
                $vars['profileID']          = $profileID;
                $vars['grupoSeguidores']    = $getGrupoSeguidores;
                $vars['_view']              = 'listar-seguidores-profile.html.twig';

                return $vars;


            }

        }

    // Método responsável por listar seguidores de um grupo


    // Método responsável por carregar seguidores utilizanod o append do jquery na modal

        public function loadMoreSeguidores($profileID, $pagina)
        {

            $usuarioID = $_SESSION['usuario_id'];

            $limite = 20;
            
            $inicio = ($pagina * $limite) - $limite;

             // Verifica se o usuário segue este grupo
            $validacaoSeguranca = COUNT($this->mapper->seguidores(array('follower_id' => $usuarioID, 'following_id' => $profileID))->fetchAll());

            if($validacaoSeguranca == 1){

             // Recupera usuários do grupo
             $SQL = "SELECT se.id, se.follower_id, se.following_id, se.ativo, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil, user.nick_name
                     FROM seguidores as se
                     INNER JOIN usuario as user ON user.id = se.follower_id

                     WHERE se.following_id = '$profileID'

                     order by se.id desc

                     LIMIT $inicio,$limite
                ";

            $getGrupoSeguidores = $db = new DB($this->c->pdo);
            $getGrupoSeguidores = $db->query($SQL);
            $getGrupoSeguidores = $db->fetchAll();

            $numeroSeguidores = COUNT($getGrupoSeguidores);

            if($numeroSeguidores == 0){

                return false;

            }else{ 

                foreach ($getGrupoSeguidores as $dadosSeguidores) {

                        echo '<div class="elementModalSeguidores">

                        <!-- Avatar usuário !-->
                          <img src="'.$dadosSeguidores->foto_perfil.'" width="65" height="65" style="float:left; margin-right: 15px;">
                        <!-- Avatar usuário !-->

                        <!-- Nome do usuário !-->
                          <div class="nomeUsuario">
                            '.$dadosSeguidores->nome.' '.$dadosSeguidores->sobre_nome.'
                          </div>
                        <!-- Nome do usuário !-->

                        <!-- Nome do usuário !-->
                          <div class="nickUsuario">
                            @'.$dadosSeguidores->nick_name.'
                          </div>
                        <!-- Nome do usuário !-->

                      </div>';
                }

                $paginaRetorno = $pagina+1;

                echo '<form name="LoadMoreSeguidores" class="LoadMoreSeguidores" id="FormLoadMoreSeguidores" style="float:left;">

                  <input type="hidden" name="pagina" value="'.$paginaRetorno.'">
                  <input type="hidden" name="profileID" value="'.$profileID.'">

                  <input type="submit" class="btn btn-primary" value="Mais Seguidores">

                </form>';
            }

          }else{



          }

        }

    // Método responsável por carregar seguidores utilizanod o append do jquery na modal


    public function get($profileID = null, $arg2 = null, $arg3 = null)
    {  

        return $this->listarProfileSeguidores($profileID);

    }

    public function post($acao = null, $arg1 = null)
    {   

        $profileID    = $_POST['profileID'];
        $pagina       = $_POST['pagina'];
        
        return $this->loadMoreSeguidores($profileID, $pagina);

    }

}
