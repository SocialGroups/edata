<?php

namespace Medcenter\Application\Model;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class Login implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function renderLayout($arg1 = null)
    {

        $vars['_view'] = 'login-novo.html.twig';

        return $vars;
    }

    public function post()
    {

        $vars   = array('_view'=>'index.html.twig');
        $login  = filter_input(INPUT_POST, 'login');
        $pass   = md5(filter_input(INPUT_POST, 'password'));

        $usuario = $this->mapper->user(array('email'=>$login,'password'=> $pass))->fetch();

        if($usuario) {

            $_SESSION['login']          = $_POST['login'];
            $_SESSION['userId']         = $usuario->id;
            $_SESSION['email']          = $usuario->email;

            return true;

        } else {

            session_destroy();
            $vars['loginErro']   = 'true';
            $vars['_view']       = 'login-novo.html.twig';

            return false;

        }

    }
}
