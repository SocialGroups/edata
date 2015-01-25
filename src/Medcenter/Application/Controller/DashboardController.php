<?php

namespace Medcenter\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Medcenter\Application\Model\Dashboard;

class DashboardController implements Routable
{

    public function __construct()
    {

        $this->getModel = new Dashboard();

    }

    public function get()
    {

        return $this->getModel->renderLayout();

    }

}
