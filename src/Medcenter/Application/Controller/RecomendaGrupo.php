<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class RecomendaGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function getGruposRecomendar()
    {		

    		// Instância método client do neo4j e passa dados do servidor
    		$client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

    		// Usuário ID
    		$usuarioID = $_SESSION['usuario_id'];

    		// Recupera 5 grupos aleatorios que este usuário segue

    		$SQL = "SELECT * FROM grupo_usuario WHERE usuario_id = '$usuarioID' ORDER BY RAND()";

            $arrayGrupos = $db = new DB($this->c->pdo);
            $arrayGrupos = $db->query($SQL);
            $arrayGrupos = $db->fetchAll();
    		// Recupera 5 grupos aleatórios que este usuário segue

    		// Array responsável por armazenar os ids dos grupos a serem recomendados
    		$arrayGruposRecomendadosID = array();

    		foreach ($arrayGrupos as $getGrupoID) {
    			
    			// ID do grupo
    			$grupoID = $getGrupoID->grupo_id;

    			$queryString = "MATCH (Grupo { grupo_id: '$grupoID' })-[:segue*2..2]-(friend_of_friend),(Usuario { usuario_id: '$usuarioID' })
WHERE NOT (Usuario)-[:segue]-(friend_of_friend)
RETURN distinct friend_of_friend.grupo_nome, friend_of_friend.grupo_id, COUNT(*)
ORDER BY COUNT(*) DESC , friend_of_friend.grupo_nome, friend_of_friend.grupo_id LIMIT 2";

    	   		$query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
				$result = $query->getResultSet();

				foreach ($result as $dados) {

					$arrayGruposRecomendadosID[] = $dados[1];

				}

    		}

    		$gruposIndicarIDs = array_unique($arrayGruposRecomendadosID);


    		$arrayDadosGruposIndicados = array();

    		// Recupera Dados dos grupos a serem indicados

    			foreach ($gruposIndicarIDs as $grupoID) {
    				
    				$dadosGrupoID = $grupoID;

    				// Get dados grupo
    				$getDadosGrupo = $this->mapper->grupo(array('id' => $dadosGrupoID))->fetch();

                    // get informacoes grupo
                    $getInformacoesGrupo = $this->mapper->grupo_informacoes(array('grupo_id' => $dadosGrupoID))->fetch();

                    // Get Autor Avatar e ID
                    $getAutorDados       = $this->mapper->usuario(array('id' => $getDadosGrupo->usuario_criacao))->fetch();

    				// Armazena dados do grupo em um array para retornar
    				$arrayDadosGruposIndicados[] = array('id' => $dadosGrupoID, 'nome' => $getDadosGrupo->nome, 'descricao' => $getDadosGrupo->descricao, 'capa' => $getDadosGrupo->grupo_avatar, 'numeroSeguidores' =>$getInformacoesGrupo->numero_seguidores, 'numeroBoxes' => $getInformacoesGrupo->numero_boxes, 'autorNickName' => $getAutorDados->nick_name, 'autorFotoPerfil' => $getAutorDados->foto_perfil);

    			}

    		// Recupera Dados dos grupos a serem indicados	

    			return $arrayDadosGruposIndicados;


    	    //print_r($client->getServerInfo());


    }

    public function get($acao = null, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
    {

    	return $this->getGruposRecomendar();
    }

}
