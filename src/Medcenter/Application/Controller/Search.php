<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;

class Search implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável pelo search

        public function getResults($q, $pagina)
        {

            // Paginação
            $limite = 10;
            if($pagina == '' OR $pagina == '0'){
                $inicio = 0;
            }else{
            $inicio = ($pagina * $limite) - $limite;
            }
            // Paginação

        }

    // Método responsável pelo search


    public function get($pagina = null)
    {   

        $q  = $_GET['q'];
        return $this->getResults($q, $pagina);

    }

     public function post()
    {   

       

    }

}
