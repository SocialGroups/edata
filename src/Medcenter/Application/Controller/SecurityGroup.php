<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class SecurityGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function get($acao = null, $arg1 = null, $arg2 = null, $arg3 = null)
    {
            $boxID          = $arg1;
            $grupoTitulo    = $arg2;
            $boxTitulo      = $arg3;

            return $this->viewBox($boxID,$boxTitulo,$grupoTitulo);}

    }



    public function post($acao = null, $arg1 = null)
    {

            $grupoID    = $_POST['grupoID'];
            $boxID      = $_POST['boxID'];
            $conteudo   = $_POST['conteudo'];


            return $this->adicionarInteracao($grupoID,$boxID,$conteudo);
    }
}
