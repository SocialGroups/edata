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

    public function getTicketData()
    {

        $usuario = $this->mapper->ticket->ticket_field->author(array("name"=>"Alexandre"))->fetchAll();

    }

    public function renderLayout()
    {

        $vars['userEmail']          = $_SESSION['email'];
        $vars['userDepartament']    = $_SESSION['departament'];
        $vars['_view']              = 'dashboard.html.twig';

        return $vars;
    }

}
