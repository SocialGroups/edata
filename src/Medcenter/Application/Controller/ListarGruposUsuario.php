<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;

class ListarGruposUsuario implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por listar os grupos de um usuário

      public function listarGrupos($usuarioID, $pagina = null)
      {

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu      

            // Lógica de paginação
            $limite = 15;
            if($pagina == '' OR $pagina == 0){
                $inicio = 0;
            }else{
                $inicio = ($pagina * $limite) - $limite;
            }
            // Lógica de paginação

          // Recupera ID do usuário que desejar listar os grupos
          $usuarioLogadoID = $_SESSION['usuario_id'];

          // recupera dados usuário
          $getDadosUsuario = $this->mapper->usuario(array('id' => $usuarioID))->fetch();

          // Recupera Grupos do usuário logado
           $SQL = "SELECT gu.id, gu.grupo_id, gu.status, gu.ativo, g.id as grupoID, g.nome, g.grupo_avatar, g.descricao, user.id as usuarioID, user.foto_perfil, user.nick_name

                  FROM `grupo_usuario` as gu

                  INNER JOIN grupo as g ON g.id = gu.grupo_id
                  INNER JOIN usuario as user ON user.id = g.usuario_criacao

                  WHERE gu.usuario_id = '$usuarioID'

                  ORDER BY gu.id DESC

                  LIMIT $inicio,$limite 
                  ";
          // Recupera Grupos do usuário logado

          $arrayGrupos = $db = new DB($this->c->pdo);
          $arrayGrupos = $db->query($SQL);
          $arrayGrupos = $db->fetchAll();

          $vars['nickName']                     = $getDadosUsuario->nick_name;
          $vars['usuarioID']                    = $usuarioID;
          $vars['arrayGrupos']                  = $arrayGrupos;
          $vars['NumeroGrupos']                 = $getDadosmenu->getNumeroGrupos();
          $vars['numeroNotificacoesPendentes']  = $getDadosmenu->numeroNotificacoesPendentes();
          $vars['notificacoesPendentes']        = $getDadosmenu->notificacoesPendentes();
          $vars['numeroMencoesPendentes']       = $getDadosmenu->getMencoes();
          $vars['dadosTopBar']                  = $getDadosmenu->dadosUsuarioTopBar();
          $vars['numeroGruposUsuario']          = $getDadosmenu->getNumeroGrupos();
          $vars['_view']                        = 'listar-grupos-usuario.html.twig';

          return $vars;

      }

    // Método responsável por listar os grupos de um usuário


    public function get($usuarioID = null, $pagina = null)
    {

        return $this->listarGrupos($usuarioID, $pagina);

    }

    public function post($acao = null, $arg1 = null)
    {

        $grupoID    = $_POST['grupoID'];
        $pagina     = $_POST['pagina'];

        return $this->loadMoreSeguidores($grupoID, $pagina);

    }

}


