<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class ajaxGetJogos implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Métodor esponsável por retornar todos os jogos cadastrados

        public function getJogos($valor)
        {


            // Retorna o prestador por Ajax
            $q = $valor;

             $SQL = "SELECT * FROM grupo_lista_games
                
                WHERE jogo LIKE  '%$q%'

                ORDER BY id ASC
               ";
        $getJogosCadastrados = $db = new DB($this->c->pdo);
        $getJogosCadastrados = $db->query($SQL);
        $getJogosCadastrados = $db->fetchAll();
        
            $results = array();

            foreach ($getJogosCadastrados as $dadosJogos) {
               
                    $ID = $dadosJogos->id;
                    $texto = $dadosJogos->jogo;

                    $results[] = array('id' => $ID, 'text' => $texto, 'foto_perfil' => '', 'sobre_nome' => '');

            }

            return array('q' => $q, 'results' => $results);

        }

    // Método responsável por retornar todos os jogos cadastrados

    public function get($acao = null, $arg1 = null, $arg2 = null, $arg3 = null)
    {

    }



    public function post($acao = null, $arg1 = null)
    {

            $valor = $_POST['data']['q'];

            return $this->getJogos($valor);
    }
}
