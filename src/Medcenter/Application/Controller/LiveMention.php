<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;

class LiveMention implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Métedo responsável por recuperar Pessoas Mencionadas em boxes ou comentários

        public function getPersonMention($q)
        {   

            // Trata variavel contra SQL Injection
            $qNoSqlInjection = $q;

            // Remove o @ da variavel
            $qNoSqlInjection = substr($qNoSqlInjection, 1);

            // Recupera pessoas
            $SQL = "SELECT s.id, s.nome, s.sobre_nome, s.foto_perfil, s.nick_name FROM usuario as s

                    WHERE s.nick_name LIKE '%$qNoSqlInjection%'

                    ORDER BY s.id DESC 

                    LIMIT 0,10
                    ";

            $arrayUsuarios = $db = new DB($this->c->pdo);
            $arrayUsuarios = $db->query($SQL);
            $arrayUsuarios = $db->fetchAll();
            // Recupera pessoas

            $results = array();
            foreach ($arrayUsuarios as $dadosUsuario) {
                
                $results[] = array('id' => $dadosUsuario->id, 'foto_perfil' => $dadosUsuario->foto_perfil,'text' => '@'.$dadosUsuario->nick_name, 'sobre_nome' => '', 'nick_name' => $dadosUsuario->nick_name);

            }

             return array('q' => $q, 'results' => $results);

        }

    // Métedo responsável por recuperar Pessoas Mencionadas em boxes ou comentários


    // Método responsável por cadastrar uma mensão

        public function cadPersonMenson($tipo = null, $meta_value = null, $pessoas = null)
        {

           $mencionadorID = $_SESSION['usuario_id'];

            foreach ($pessoas as $mencionadoID) {

                // Cadastra menssão as pessoas
                    $insertMencao = new \stdClass;
                    $insertMencao->mencionador_id   = $mencionadorID;
                    $insertMencao->mencionado_id    = $mencionadoID;
                    $insertMencao->tipo             = $tipo;
                    $insertMencao->meta_value       = $meta_value;

                    $this->mapper->usuario_mencao->persist($insertMencao);
                    $this->mapper->flush();
                // Cadastra menssão as pessoas

            }

        }

    // Método responsável por cadastrar uma mensão

    public function homologacaoFrontEnd(){

        $vars['_view'] = 'Hlive-montion.html.twig';
        return $vars;

    }


    public function get($acao = null, $pagina = null)
    {   
        if($acao == 'homologacao'){

            return $this->homologacaoFrontEnd();

        }

    }

    public function post($acao = null){

        $q = $_POST['data']['q'];
        return $this->getPersonMention($q); 

    }

}
