<?php

namespace Medcenter\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Medcenter\Application\Model\Login;

class LoginController implements Routable
{

    public function __construct()
    {

        $this->getModel = new Login();

    }

    public function get()
    {

        return $this->getModel->renderLayout();

    }

    public function post()
    {

        return $this->getModel->post();

    }

}
