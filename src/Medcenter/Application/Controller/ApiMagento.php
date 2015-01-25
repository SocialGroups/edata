<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use Respect\Rest\Router;
use Respect\Rest\Request;

class ApiMagento implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Registra usuário no Neo4j

        public function RegistraNovoUsuario($token, $chaveIdentificacao, $nome = null, $email = null)
        {   

            // Verifica se o usuário consta no Neo4j

            $verificaUsuarioRegistrado = COUNT($this->mapper->log_api_magento_usuario(array('chave' => $chaveIdentificacao))->fetchall());

                if($verificaUsuarioRegistrado == 0){

                    // Registra usuário na tabela log de controle de usuários cadastrados
                    $registraUsuarioLog = new \stdClass;
                    $registraUsuarioLog->chave = $chaveIdentificacao;

                    $this->mapper->log_api_magento_usuario->persist($registraUsuarioLog);
                    $this->mapper->flush();

                    // Instância método client do neo4j e passa dados do servidor
                    $client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

                    // Query responseavel por adicionar o usuário no neo4j
                    $queryString = "CREATE (n:MGUsuario { chave: '$chaveIdentificacao', nome: '$nome', email : '$email' })";

                    // Executa query
                    $query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
                    $result = $query->getResultSet();


                    return true;

                }else{

                    return false;
                }

            // Verifica se o usuário consta no Neo4j

        }

    // Registra usuário no Neo4j

    // Registra Produto no Neo4j

        public function RegistraProduto($token, $produtoID, $produtoNome)
        {

                if($token > '' AND $produtoID > '' AND $produtoNome > ''){

                    // Instância método client do neo4j e passa dados do servidor
                    $client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

                    // Query responseavel um produto no servidor
                    $queryString = "CREATE (n:MGProduto { id: '$produtoID', nome: '$produtoNome'})";

                    // Executa query
                    $query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
                    $result = $query->getResultSet();

                }else{

                    return false;
                }

        }

    // Registra Produto no Neo4j


    // Registra usuário visualizou produto

        public function RegistraVisualizouProduto($token, $chaveIdentificacao, $produtoSKU)
        {

                if($token > '' AND $chaveIdentificacao > '' AND $produtoSKU > ''){

                    // Instância método client do neo4j e passa dados do servidor
                    $client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

                    $queryString = "MATCH (u:MGUsuario),(p:MGProduto)
                                    WHERE u.chave = '$chaveIdentificacao' AND p.id = '$produtoSKU'
                                    CREATE (u)-[r:visualizou]->(p)
                                    RETURN r";

                    // Executa query
                    $query  = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
                    $result = $query->getResultSet();

                    return true;

                }else{

                    return false;
                }

        }

    // Registra usuário visualizou produto

    // Retorna indicações de produtos a partir de um produto específico

        public function RetornaIndicacoesProdutoEspecifico($token, $chaveIndentificacao, $produtoID)
        {       
                // Instância método client do neo4j e passa dados do servidor
                $client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

                $returnProdutosIndicados = array();

                $queryString = "MATCH (MGProduto { id: '$produtoID' })-[:visualizou*2..2]-(friend_of_friend),(MGUsuario { chave : '$chaveIndentificacao' })
WHERE NOT (MGUsuario)-[:visualizou]-(friend_of_friend)
RETURN distinct friend_of_friend.id, friend_of_friend.nome, COUNT(*)
ORDER BY COUNT(*) DESC , friend_of_friend.id, friend_of_friend.nome LIMIT 50";

                $query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
                $result = $query->getResultSet();

                foreach ($result as $dados) {

                    $returnProdutosIndicados[] = array('id' => $dados[0]);

                }

                return json_encode($returnProdutosIndicados);
        }

    // Retorna indicações de produtos a partir de um produto específico

    // Retorna indicações de produtos a partir do histórico de produtos visitados

        public function RetornaIndicacoesHome($token, $chaveIndentificacao)
        {

            if($token > '' AND $chaveIndentificacao > ''){

                // array produtos indicados
                $returnProdutosIndicados = array();

                // Instância método client do neo4j e passa dados do servidor
                $client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

                $queryStringVisualizou = "MATCH (MGUsuario { chave:'apimagento2@hotmail.com' })--(MGProduto) RETURN MGProduto.id";

                $queryVisualizou = new \Everyman\Neo4j\Cypher\Query($client, $queryStringVisualizou);
                $resultVisualizou = $queryVisualizou->getResultSet();

                foreach ($resultVisualizou as $produtoVisualizouID) {
                            
                    $produtoID = $produtoVisualizouID[0];

                    $queryString = "MATCH (MGProduto { id: '$produtoID' })-[:visualizou*2..2]-(friend_of_friend),(MGUsuario { chave : 'apimagento2@hotmail.com' })
WHERE NOT (MGUsuario)-[:visualizou]-(friend_of_friend)
RETURN distinct friend_of_friend.id, friend_of_friend.nome, COUNT(*)
ORDER BY COUNT(*) DESC , friend_of_friend.id, friend_of_friend.nome LIMIT 50";

                    $query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
                    $result = $query->getResultSet();

                    foreach ($result as $dados) {

                        $returnProdutosIndicados[] = array('id' => $dados[0]);

                    }

                }


                return json_encode($returnProdutosIndicados);


            }else{

                return false;
            }

        }

    // Retorna indicações de produtos a partir do histórico de produtos visitados

    // Verifica se produto existe na base dados

        public function verificaProdutoCadastrado($token,$produtoSKU, $produtoNome)
        {

                // Instância método client do neo4j e passa dados do servidor
                $client = new \Everyman\Neo4j\Client('54.207.75.134', 7474);

                $queryString = "MATCH (n:`MGProduto`) WHERE n.id = '$produtoSKU' RETURN n LIMIT 25";

                // Executa query
                $query  = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
                $result = $query->getResultSet();
                $numeroResultado = COUNT($result);

                if($numeroResultado == 0){

                        // Cadastra produto no Neo4j
                        $queryStringRegistraProduto = "CREATE (n:MGProduto { id: '$produtoSKU', nome: '$produtoNome'})";

                        // Executa query
                        $queryRegistraProduto  = new \Everyman\Neo4j\Cypher\Query($client, $queryStringRegistraProduto);
                        $resultRegistraProduto = $queryRegistraProduto->getResultSet();

                        return true;

                }else{

                        return false;
                }

        }

    // Verifica se o produto existe na base de dados


    public function get($acao)
    {    

        if($acao == 'verifica-usuario'){

            $token              = $_GET['token'];
            $chaveIdentificacao = $_GET['chaveIdentificacao'];

            if(isset($_GET['nome'])){

                $nome = $_GET['nome'];

            }else{

                $nome = '';
            }

            if(isset($_GET['email'])){

                $email = $_GET['email'];
                
            }else{

                $email = '';
            }   

            return $this->RegistraNovoUsuario($token,$chaveIdentificacao,$nome,$email);          

        }else if($acao == 'registra-visualizacao-produto'){

            $token              = $_GET['token'];
            $chaveIdentificacao = $_GET['chaveIdentificacao'];
            $produtoSKU         = $_GET['produtoSKU'];

            return $this->RegistraVisualizouProduto($token, $chaveIdentificacao, $produtoSKU);

        }else if($acao == 'verifica-produto-registrado'){

            $token          = $_GET['token'];
            echo $produtoSKU     = $_GET['produtoSKU'];
            $produtoNome    = $_GET['produtoNome'];

            return $this->verificaProdutoCadastrado($token,$produtoSKU, $produtoNome);

        }

    }



    public function post($acao = null)
    {       

    }
}
