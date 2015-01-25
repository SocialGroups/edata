<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

use \Everyman\Neo4j\Client;
use	\Everyman\Neo4j\Index\NodeIndex;
use	\Everyman\Neo4j\Relationship;
use	\Everyman\Neo4j\Node;
use	\Everyman\Neo4j\Cypher;


class neo4jNOSQL implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function testesNeo4j()
    {		

    		// Instância método client do neo4j e passa dados do servidor
    		$client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

    		// Usuário ID
    		$usuarioID = $_SESSION['usuario_id'];

    		// Recupera 5 grupos aleatorios que este usuário segue

    		$SQL = "SELECT * FROM grupo_usuario WHERE usuario_id = '$usuarioID' ORDER BY RAND() LIMIT 5";

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
ORDER BY COUNT(*) DESC , friend_of_friend.grupo_nome, friend_of_friend.grupo_id LIMIT 3";

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

    				// Armazena dados do grupo em um array para retornar
    				$arrayDadosGruposIndicados[] = array('id' => $dadosGrupoID, 'nome' => $getDadosGrupo->nome, 'descricao' => $getDadosGrupo->descricao, 'capa' => $getDadosGrupo->grupo_avatar);

    			}

    		// Recupera Dados dos grupos a serem indicados	

    			var_dump($arrayDadosGruposIndicados);


    	    //print_r($client->getServerInfo());


    }

    public function get($acao = null, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
    {

    	return $this->testesNeo4j();
    }

}


// 	//$queryString = 'start n=node(*)  match n-[:box|interacao*]->m  return distinct n';

 	

	//"START n=node(*) return n";




// // start n=node(436) 
// // match n-[rels*]->m return n,m

// 	// START node=node(*) MATCH node-[n:box]-[n:interacao]  RETURN node

// 	// START node=node(*) MATCH node-[n:box]->interacao RETURN interacao, n, node


// 	// START person=node(*) MATCH person-[n:box]->interacao-person-[n:box]->interacao2 RETURN interacao, person


// 	// START s=node(*) MATCH path=s-[?:chases*..2]->() RETURN path;

// 	// START node=node(*) MATCH node-[r:box]->(task:interacao) RETURN node,task,r


// 	// START node=node(*) MATCH node-[r:box]->(task:interacao) RETURN node,task,r


// 	//start n=node(*) OPTIONAL MATCH ()-[:box]-(n)-[:interacao]-(i)-[:midia]-(m) return n, i, m


// MATCH (u:usuario),(g:Grupo)
// WHERE u.id = '7' AND g.id = '1'
// CREATE (u)-[r:segue]->(g)
// RETURN r

// // Lógica de indicação de grupos baseado no algoritimo amigos dos amigos



// MATCH (grupo { id: '1' })-[:segue*2..2]-(friend_of_friend)
// WHERE NOT (grupo)-[:segue]-(friend_of_friend)
// RETURN friend_of_friend.name, COUNT(*)
// ORDER BY COUNT(*) DESC , friend_of_friend.name


// MATCH (grupo { id: '1' })-[:segue*2..2]-(friend_of_friend)
// WHERE NOT (grupo)-[:segue]-(friend_of_friend)
// RETURN friend_of_friend.name, COUNT(*)
// ORDER BY COUNT(*) DESC , friend_of_friend.name





