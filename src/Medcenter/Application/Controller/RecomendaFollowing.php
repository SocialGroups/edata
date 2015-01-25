<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class RecomendaFollowing implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function getFollowingRecomendar()
    {		

            $usuarioID = $_SESSION['usuario_id'];

    		// Instância método client do neo4j e passa dados do servidor
    		$client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);


    			$queryString = "MATCH (Usuario { usuario_id: '$usuarioID' })-[:SegueUsuario*2..2]-(friend_of_friend)
WHERE NOT (Usuario)-[:SegueUsuario]-(friend_of_friend)
RETURN distinct friend_of_friend.usuario_nome, friend_of_friend.usuario_id, COUNT(*)
ORDER BY COUNT(*) DESC , friend_of_friend.usuario_nome, friend_of_friend.usuario_id LIMIT 10";

    	   		$query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
				$result = $query->getResultSet();

                // Array com os dados dos usuários recomendados para retornar
                $arrayUsuariosRecomendados = array();

				foreach ($result as $dados) {


                    if($dados[1] <> $usuarioID){

                        // Recupera dados do usuário
                        $getDadosUsuario = $this->mapper->usuario(array('id' => $dados[1]))->fetch();

                        // Armazena dados em um array
                        $arrayUsuariosRecomendados[] = array('id' => $dados[1], 'nome' => $getDadosUsuario->nome, 'sobreNome' => $getDadosUsuario->sobre_nome, 'foto_avatar' => $getDadosUsuario->foto_perfil, 'nickName' => $getDadosUsuario->nick_name);

                    }

				}

                return $arrayUsuariosRecomendados;

    }

    public function get($acao = null, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
    {

    	return $this->getFollowingRecomendar();
    }

}
