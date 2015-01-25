<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class AjaxGetGrupos implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    public function ajaxPrestador($valor)
    {   

        $usuarioID = $_SESSION['usuario_id'];

        // Retorna o prestador por Ajax
        $q = $valor;

        // Recupera os prestadores pelo nome digitado -> Apenas procedimentos que a unidade realiza
        $SQL = "SELECT g.id, gu.usuario_id, g.nome, g.grupo_avatar from grupo_usuario as gu 
                Inner JOIN grupo as g ON g.id = gu.grupo_id
                
                WHERE g.nome LIKE  '%$valor%' AND gu.usuario_id = '$usuarioID'
                GROUP BY g.id
                ORDER BY g.nome ASC
               ";

        // // recupera todos os grupos que este usuário segue
        // $SQL = "SELECT gu.id, gu.usuario_id, g.nome from grupo_usuario as gu Inner JOIN grupo as g ON g.id = gu.grupo_id WHERE gu.usuario_id = '$usuarioID'";

        $arrayGruposUsuario = $db = new DB($this->c->pdo);
        $arrayGruposUsuario = $db->query($SQL);
        $arrayGruposUsuario = $db->fetchAll();


            $results = array();
            foreach ($arrayGruposUsuario as $dadosPrestadores) {
                
                $ID    = $dadosPrestadores->id;
                $texto = $dadosPrestadores->nome;

                $results[] = array('id' => $ID, 'text' => $texto, 'foto_perfil' => $dadosPrestadores->grupo_avatar, 'sobre_nome' => '');

            }

            return array('q' => $q, 'results' => $results);
    }


    // Método - Recupera todos os grupos temporários com usuários logados

    // Métodor esponsável por retornar todos os jogos cadastrados

        public function getJogos($valor)
        {

            $getJogosCadastrados = $this->mapper->grupo_lista_games->fetchAll();

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

            if($acao == 'jogos'){

            $valor = $_POST['data']['q'];

            return $this->getJogos($valor);

            }else{

            $valor = $_POST['data']['q'];

            return $this->ajaxPrestador($valor);

            }
    }
}
