<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;

class SnInterassoesFollowed implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método - retorna dados dos usuários que sigo

    	public function FollowedDados($UsuarioID){

            $SQL = "SELECT usuario.id, usuario.nome, usuario.email FROM seguidores 
                    INNER JOIN usuario   ON seguidores.follower_id=usuario.id
                    WHERE seguidores.follower_id = '$UsuarioID'
                    ";

            $verificaAmizade = $db = new DB($this->c->pdo);
            $verificaAmizade = $db->query($SQL);
            $verificaAmizade = $db->fetchAll();

            return $verificaAmizade;

    	}

    // Método - retorna dados dos usuários que sigo

    // Método - retorna interações dos usuários que sigo

        public function FollowedInteracoes($UsuarioID){

            $SQL = "SELECT usuario.id, usuario.nome, usuario.email, timeline_interacoes.interacao_tipo, timeline_interacoes.modo_interacao, timeline_interacoes.modo_interacao_id, timeline_interacoes.usuario_id, timeline_interacoes.conteudo FROM seguidores 
                    INNER JOIN usuario   ON seguidores.follower_id=usuario.id
                    INNER JOIN timeline_interacoes ON seguidores.following_id=timeline_interacoes.usuario_id

                    WHERE seguidores.follower_id = '$UsuarioID'

                    ORDER BY timeline_interacoes.id DESC
                    ";

            $verificaAmizade = $db = new DB($this->c->pdo);
            $verificaAmizade = $db->query($SQL);
            $verificaAmizade = $db->fetchAll();

            var_dump($verificaAmizade);

        }

    // Método - retorna interações dos usuários que sigo


    // Método - retorna usuários para criar um grupo temporário de acordo com o interesse

        public function SearchUsuariosOracle($palavra_chave){

            $arrayUsuariosGrupoTemporario = $this->mapper->oracle(array('palavra_chave LIKE' => $palavra_chave, 'status' => 'livre'))->fetchAll();

            var_dump($arrayUsuariosGrupoTemporario);

        }

    // Método - retorna usuários para criar um grupo temporário de acordo com o interesse

    public function get($acao = null, $arg1 = null)
    {

        if($acao == "dados") {

            $UsuarioID = $_SESSION['usuario_id'];

            return $this->FollowedDados($UsuarioID);
        
        }else if($acao == "interassoes"){

            $UsuarioID = $_SESSION['usuario_id'];

            return $this->FollowedInteracoes($UsuarioID);

        }else if($acao == "oracle"){

            $palavra_chave = "Counter-Strike";

            return $this->SearchUsuariosOracle($palavra_chave);

        }

    }
}
