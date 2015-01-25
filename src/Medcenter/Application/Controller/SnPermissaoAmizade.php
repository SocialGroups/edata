<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;

class SnPermissaoAmizade implements Routable
{
    private $mapper;

    public function __construct()
    {
        $c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $c->mapper;
    }

    // Método - Verifica se o usuário tem amizade com o usuário o qual ele esta visualizado o perfil

    	public function NivelAmizade($PerfilVisitanteID){

    		$usuario_id = $_SESSION['usuario_id'];

            echo $PerfilVisitanteID;

    	}

    // Método - Verifica se o usuário tem amizade com o usuário o qual ele esta visualizado o perfil

    public function get($acao = null, $arg1 = null)
    {

        if($acao == "nivel") {

            $PerfilVisitanteID = 1;
            return $this->NivelAmizade($PerfilVisitanteID);
        
        }

    }
}
