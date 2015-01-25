<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;
use SocialGroups\Application\Controller\LiveMention;
use SocialGroups\Application\Controller\InjectionGrupoDados;
use SocialGroups\Application\Controller\DeleteObjectCache;

class InjectionDadosNeo4j implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por adicionar usuários no Neo4j

    public function neo4jAdicionaUsuario($usuarioID, $nome)
    {	

    		// Instância método client do neo4j e passa dados do servidor
    		$client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

    		// Query responseavel por adicionar o usuário no neo4j
    	 	$queryString = "CREATE (n:Usuario { usuario_nome: '$nome', usuario_id : '$usuarioID' })";

    	 	// Executa query
    	   	$query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
    	   	$result = $query->getResultSet();

    }

    // Método responsável por adicionar usuários no Neo4j

    // Método responsável por adicionar um grupo no Neo4j

    public function neo4jAdicionaGrupo($grupoID, $nome)
    {

    		// Instância método client do neo4j e passa dados do servidor
    		$client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

    		// Query responseavel por adicionar o usuário no neo4j
    	 	$queryString = "CREATE (n:Grupo { grupo_id: '$grupoID', grupo_nome : '$nome' })";

    	 	// Executa query
    	   	$query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
    	   	$result = $query->getResultSet();

    }

    // Método responsável por adicionar um grupo no Neo4j

    // Método responsável por adicionar Follow em um grupo

    public function neo4jAdicionaSeguidorGrupo($usuarioID, $grupoID)
    {

    	// Instância método client do neo4j e passa dados do servidor
    	$client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

		$queryString = "MATCH (u:Usuario),(g:Grupo)
						WHERE u.usuario_id = '$usuarioID' AND g.grupo_id = '$grupoID'
						CREATE (u)-[r:segue]->(g)
						RETURN r";

		// Executa query
    	$query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
    	$result = $query->getResultSet();

    }

    // Método responsável por adicionar Follow em um grupo

    // Método responsável por adicionar Follow em um usuário

    public function neo4jAdicionaSeguidorUsuario($usuarioID, $followingID)
    {

    	// Instância método client do neo4j e passa dados do servidor
    	$client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

		$queryString = "MATCH (a:Usuario),(b:Usuario)
						WHERE a.usuario_id = '$usuarioID' AND b.usuario_id = '$followingID'
						CREATE (a)-[r:SegueUsuario]->(b)
						RETURN r";

		// Executa query
    	$query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
    	$result = $query->getResultSet();

    }

    // Método responsável por adicionar Follow em um usuário



}