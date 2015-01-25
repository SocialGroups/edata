<?php

namespace PSocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class InsertComentarios implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método responsável por adicionar uma interação

    public function adicionarInteracao($grupoID,$boxID,$conteudo){

        if($boxID > '' AND $grupoID > ''){


                $addInteracao = new \stdClass;
                $addInteracao->grupo_id     = $grupoID;
                $addInteracao->grupo_box_id = $boxID;
                $addInteracao->usuario_id   = $_SESSION['usuario_id'];
                $addInteracao->conteudo     = $conteudo;
                $addInteracao->dataHora     = date('Y-m-d H:i:s');

                $this->mapper->grupo_interacoes->persist($addInteracao);
                $this->mapper->flush();

                 // Verifica se existe hashtag e adiciona a tabela de topics
                    $topics = array();
                    preg_match_all('/#(\w+)/',$conteudo,$matches);
                        
                        foreach ($matches[1] as $match) {

                                $topics[] = $match;
                        }

                    if(COUNT($topics) > 0){

                        foreach ($topics as $topic) {

                            $InsertTopic = new \StdClass;

                            $InsertTopic->usuario_id    = $_SESSION['usuario_id'];;
                            $InsertTopic->topic         = "#$topic";
                            $InsertTopic->dataHora      = date('Y-m-d H:i:s');
                            $InsertTopic->tipo          = 'interacao';
                            $InsertTopic->meta_value    = $boxID;

                            $this->mapper->topics->persist($InsertTopic);
                            $this->mapper->flush();
                        }

                    }

                    // Verifica se existe hashtag e adiciona a tabela de topics

                return $return = $comentarioID;

        }else{

                return $return = false;
        }

        // Notificar 

    }

    // Método resposnável por adicionar uma interação

    public function get($acao = null, $arg1 = null)
    {

          return $this->adicionarInteracao();  

    }


    public function post($acao = null, $arg1 = null)
    {

            $grupoID    = $_POST['grupoID'];
            $boxID      = $_POST['boxID'];
            $conteudo   = $_POST['conteudo'];

            return $this->adicionarInteracao($grupoID,$boxID,$conteudo);

        }
}