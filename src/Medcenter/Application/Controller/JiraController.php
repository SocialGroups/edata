<?php

namespace Medcenter\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Medcenter\Application\Model\GetJiraData;

class JiraController implements Routable
{

    public function __construct()
    {

        $this->getModel = new GetJiraData();

    }

    public function get()
    {

        return $this->getModel->getData();

    }

}
