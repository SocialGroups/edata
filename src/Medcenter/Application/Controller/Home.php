<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class Home implements Routable
{

public function getFeed(){

	header('Location: /home/');
	exit;
}

  public function get($pagina = null, $arg1 = null)
    {    

        return $this->getFeed();

    }

}
?>
