<?php

namespace Medcenter\Application\Controller;

use Respect\Rest\Routable;

class Logout implements Routable
{
    public function get()
    {
        session_destroy();
        header('Location: /');
    }

    public function post()
    {

    }
}
