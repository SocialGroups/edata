<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class Deletar implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método resposável por deletar um box

        public function deletarBox($boxID){

        // Recupera ID do usuário que desejar deletar o box
        $usuarioID = $_SESSION['usuario_id'];

        // Verifica se o usuário é o autor do box
        $verificaUsuarioAutor = COUNT($this->mapper->grupo_box_ordenacao(array('id' => $boxID, 'usuario_id' => $usuarioID))->fetchAll());

            if($verificaUsuarioAutor == 1){

                // Recupera grupo do box
                $getBoxGrupo = $this->mapper->grupo_box_ordenacao(array('id' => $boxID))->fetch();

                $grupoID = $getBoxGrupo->grupo_id;

                // Efetua deleção do box selecioando
                $deletaBox = $this->mapper->grupo_box_ordenacao(array('id' => $boxID))->fetch();
                $deletaBox->ativo = 1;
                $this->mapper->grupo_box->persist($deletaBox);
                $this->mapper->flush();

                // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'boxes';
                $tipoUpdate = 'delete';
                $InjectionGrupoDados->InjectionUpdateGrupo($deletaBox->grupo_id, $coluna, $tipoUpdate); 
                // Injection Grupo Dados

                // Recupera Dados do trigger menu
                $DeleteObjectCache = new DeleteObjectCache();
                // recupera Dados do trigger menu

                // instância método responsável por deletar os objetos deste grupo do MemCache
                $DeleteObjectCache->deleteObjeto($grupoID);
                // Instância método responsável por deletar os objetos deste grupo do MemCache

                return 'true';
                // Efetua deleção do box selecioando

            }else{

                return 'false';

            }

        }

    // Método responsável por deletar um box


    // Método responsável por deletar um comentário

        public function deletarComentario($comentarioID)
        {

        // Recupera ID do usuário que desejar deletar o comentario
        $usuarioID = $_SESSION['usuario_id'];

        // Verifica se o usuário é o autor do comentario
        $verificaUsuarioAutor = COUNT($this->mapper->grupo_interacoes(array('id' => $comentarioID, 'usuario_id' => $usuarioID))->fetchAll());

            if($verificaUsuarioAutor == 1){

                // Efetua deleção do comentario selecioando
                $deletaBox = $this->mapper->grupo_interacoes(array('id' => $comentarioID))->fetch();
                $deletaBox->ativo = 1;
                $this->mapper->grupo_interacoes->persist($deletaBox);
                $this->mapper->flush();

                // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'interacao';
                $tipoUpdate = 'delete';
                $InjectionGrupoDados->InjectionUpdateGrupo($deletaBox->grupo_id, $coluna, $tipoUpdate); 
                // Injection Grupo Dados

                return 'true';
                // Efetua deleção do comentario selecioando

            }else{

                return 'false';

            }

        }

    // Método responsável por deletar um comentário


    // Método responsável por deletar uma associação de usuários

        public function usuarioUnfollow($usuarioUnfollowID)
        {

        // Recupera ID do usuário que desejar deletar o comentario
        $usuarioID = $_SESSION['usuario_id'];

        // Verifica se o usuário esta seguindo o followingID
        $verificaUsuarioFollowerUsuario = COUNT($this->mapper->seguidores(array('follower_id' => $usuarioID, 'following_id' => $usuarioUnfollowID))->fetchAll());

            if($verificaUsuarioFollowerUsuario == 1){

                // Efetua deleção do follower do grupo
                $deletaFollowerUsuario = $this->mapper->seguidores(array('follower_id' => $usuarioID, 'following_id' => $usuarioUnfollowID))->fetch();
                $deletaFollowerUsuario->ativo = 1;
                $this->mapper->seguidores->persist($deletaFollowerUsuario);
                $this->mapper->flush();

                return 'true';
                // Efetua deleção do comentario selecioando

            }else{

                return 'false';

            }

        }

    // Método responsável por deletar uma asseosiação de usuários




    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    
        $grupoID = $arg1;
        return $this->deletarFollowGrupo($grupoID);

    }


    public function post($acao = null, $arg1 = null)
    {   

        $tipoExclusao = $_POST['tipoExclusao'];

        if($tipoExclusao == 'box'){

           $boxID = $_POST['boxID'];

            return $this->deletarBox($boxID);

        }else if($tipoExclusao == 'comentario'){

            $comentarioID = $_POST['boxID'];

            return $this->deletarComentario($comentarioID);

        }else if($tipoExclusao == 'usuarioUnfollow'){

            $usuarioUnfollowID = $_POST['unfollowID'];

            return $this->usuarioUnfollow($usuarioUnfollowID);

        }
        
        return $this->GetGrupos($GetGrupos);

    }

}
