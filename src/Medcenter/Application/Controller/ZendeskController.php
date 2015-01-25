<?php

namespace Medcenter\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Medcenter\Application\Model\GetZendesckData;

class ZendeskController implements Routable
{

    public function __construct()
    {

        $this->getModel = new GetZendesckData();

    }

    public function get()
    {

        return $this->getModel->getData();

    }

}
