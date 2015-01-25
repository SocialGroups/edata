<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class PermissaoAcessoGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Verifica se o usuário tem acesso a um determinado grupo privado
    public function verificaPermissaoGrupoPrivado($grupoID,$usuarioID)
    {

    	// Verifica tipo do grupo
    	$getGrupoTipo = $this->mapper->grupo(array('id' => $grupoID))->fetch();

    	if($getGrupoTipo->privilegios == 'privado'){

    		// verifica se o usuario tem acesso a este grupo
    		$getPermissaoAcesso = COUNT($this->mapper->usuario_permissao(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetchall());

    		if($getPermissaoAcesso == 0){

    			// Redireciona usuário para a pagina de requisição de acesso
    			header("Location: /requisicoes/grupo/$grupoID"); 
				exit;

    		}

    	}

    }
    // Verifica se o usuário tem acesso a um determinado grupo privado

}