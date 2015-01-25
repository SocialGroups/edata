<?php

namespace Medcenter\Application\Model;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class Dashboard implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function renderLayout()
    {

        $data = array(

            'name'  => 'lucas dos santos alves',
            'email' => 'lucas.santos@e-smart.com.br'

        );
        $vars['data']  = $data;
        $vars['_view'] = 'dashboard.html.twig';

        return $vars;
    }

}
