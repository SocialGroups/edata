<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;
use SocialGroups\Application\Controller\InjectionGrupoDados;

class AcoesGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por dar unfollow de um usuário em um grupo

      public function unfollow($grupoID)
      {   

          // Usuáiro ID
          $usuarioID = $_SESSION['usuario_id'];

          // Verifica se usuário segue este grupo
          $verificaUsuarioSegueGrupo = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetchall());

          if($verificaUsuarioSegueGrupo == 1){

              // Aplica unfollow deste usuário para este grupo
              $aplicaUnfollow = $this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetch();
              $aplicaUnfollow->ativo = 1;
              $this->mapper->grupo_usuario->persist($aplicaUnfollow);
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

    // Método responsável por dar unfollow de um usuário em um grupo


    // Método responsável por dar follow em um grupo

        public function follow($grupoID){

          // Usuáiro ID
          $usuarioID = $_SESSION['usuario_id'];

          // Verifica se usuário segue este grupo
          $verificaUsuarioSegueGrupo = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetchall());

          if($verificaUsuarioSegueGrupo == 1){

              // Aplica unfollow deste usuário para este grupo
              $aplicaUnfollow = $this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetch();
              $aplicaUnfollow->ativo = 0;
              $this->mapper->grupo_usuario->persist($aplicaUnfollow);
              $this->mapper->flush();

              // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'seguidores';
                $tipoUpdate = 'insert';
                $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
              // Injection Grupo Dados

              return 'true';

          }else{

              // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'seguidores';
                $tipoUpdate = 'insert';
                $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
              // Injection Grupo Dados

              // Insere follow do usuário no grupo
              $insertFollowGrupo = new \stdClass;
              $insertFollowGrupo->grupo_id    = $grupoID;
              $insertFollowGrupo->usuario_id  = $usuarioID;
              $insertFollowGrupo->nivel       = 'usuario';
              $insertFollowGrupo->status      = 'ativo';

              $this->mapper->grupo_usuario->persist($insertFollowGrupo);
              $this->mapper->flush();

              return 'true';

          }

        }

    // Método resposável por dar um follow em um grupo

    public function post($acao = null, $arg1 = null)
    {

        if($acao == 'unfollow'){
          
          $grupoID    = $_POST['grupoID'];
          return $this->unfollow($grupoID);

        }else{

          $grupoID    = $_POST['grupoID'];
          return $this->follow($grupoID);

        }

    }

}


