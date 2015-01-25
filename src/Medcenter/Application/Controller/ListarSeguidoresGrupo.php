<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class ListarSeguidoresGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por listar seguidores de um grupo

        public function listarGrupoSeguidores($grupoID)
        {

            $usuarioID = $_SESSION['usuario_id'];

                // Recupera usuários do grupo
                 $SQL = "SELECT gu.id, gu.grupo_id, gu.usuario_id, gu.status, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil, user.nick_name
                         FROM grupo_usuario as gu
                         INNER JOIN usuario as user ON user.id = gu.usuario_id

                         WHERE gu.grupo_id = '$grupoID'

                         order by gu.id desc

                         LIMIT 20
                    ";

                $getGrupoSeguidores = $db = new DB($this->c->pdo);
                $getGrupoSeguidores = $db->query($SQL);
                $getGrupoSeguidores = $db->fetchAll();

                $vars['grupoID']            = $grupoID;
                $vars['grupoSeguidores']    = $getGrupoSeguidores;
                $vars['_view']              = 'listar-seguidores-grupo.html.twig';

                return $vars;

        }

    // Método responsável por listar seguidores de um grupo


    // Método responsável por carregar seguidores utilizanod o append do jquery na modal

        public function loadMoreSeguidores($grupoID, $pagina)
        {

            $usuarioID = $_SESSION['usuario_id'];

            $limite = 20;
            
            $inicio = ($pagina * $limite) - $limite;

             // Verifica se o usuário segue este grupo
            $validacaoSeguranca = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetchall());

            if($validacaoSeguranca == 1){

             // Recupera usuários do grupo
             $SQL = "SELECT gu.id, gu.grupo_id, gu.usuario_id, gu.status, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil, user.nick_name
                     FROM grupo_usuario as gu
                     INNER JOIN usuario as user ON user.id = gu.usuario_id

                     WHERE gu.grupo_id = '$grupoID'

                     order by gu.id desc

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
                  <input type="hidden" name="grupoID" value="'.$grupoID.'">

                  <input type="submit" class="btn btn-primary" value="Mais Seguidores">

                </form>';
            }

          }else{



          }

        }

    // Método responsável por carregar seguidores utilizanod o append do jquery na modal


    public function get($grupoID = null, $arg2 = null, $arg3 = null)
    {  

        return $this->listarGrupoSeguidores($grupoID);

    }

    public function post($acao = null, $arg1 = null)
    {   

        $grupoID    = $_POST['grupoID'];
        $pagina     = $_POST['pagina'];
        
        return $this->loadMoreSeguidores($grupoID, $pagina);

    }

}
