<?php

namespace Medcenter\Application\Routine;

use \PHP_SESSION_ACTIVE;
use Respect\Config\Container;

class Auth
{
    public function __construct()
    {
        session_start();
    }

    public function __invoke()
    {
        if (isset($_SESSION['login'])) {
 
            return true;
        }

        header('HTTP/1.1 403 Permission denied');
        header('Location: /login');
        return false;
    }
}
