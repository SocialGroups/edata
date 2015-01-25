<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class Mencoes implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método responsável por recuperar as menções

    	public function getMencoes()
    	{

    		// Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu  

    		// Usuário ID
    		$usuarioID = $_SESSION['usuario_id'];

    		// Recupera Menções não visualizadas por este usuário
    		 $SQLDadosMencoes = "SELECT um.id, um.mencionador_id, um.mencionado_id, um.tipo, um.meta_value, gb.id as grupoBoxID, gb.titulo, gi.id as grupoInteracaoID, gi.conteudo, gi.grupo_box_id, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil  FROM usuario_mencao as um

    		 					 LEFT JOIN grupo_box as gb ON um.tipo = 'box' AND um.meta_value = gb.id

    		 					 LEFT JOIN grupo_interacoes as gi ON um.tipo = 'interacao' AND um.meta_value = gi.id

    		 					 INNER JOIN usuario as user ON um.mencionador_id = user.id

    		 					 WHERE um.mencionado_id = '$usuarioID' AND um.status = 'pendente'

    		 					 LIMIT 20

    		 					";

            $getMencoesPendentes = $db = new DB($this->c->pdo);
            $getMencoesPendentes = $db->query($SQLDadosMencoes);
            $getMencoesPendentes = $db->fetchAll(); 

            $numeroMencoesPendentes = count($getMencoesPendentes);


            // altera status das menções para visualizado

            	foreach ($getMencoesPendentes as $dadosVisualizacoes) {
            		

					$atualizaStatus = $this->mapper->usuario_mencao(array('id' => $dadosVisualizacoes->id))->fetch();
					$atualizaStatus->status = "visualizado";
					$this->mapper->usuario_mencao->persist($atualizaStatus);
					$this->mapper->flush();

            	}

            // altera status das menções para visualizado 

    		// retorna dados para o FrontEnd
            $vars['numeroSolicitacoes'] = $numeroMencoesPendentes;
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();  
    		$vars['MensoesPendentes'] 	= $getMencoesPendentes;
    		$vars['_view'] = 'mencoes.html.twig';

    		return $vars;

    	}

    // Método responsável por recuperar as menções



    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    

            return $this->getMencoes();

    }

}
