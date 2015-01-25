<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class RelatarBug implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método responsável por relatar um bug

        public function relatarBug($mensagem)
        {

            // Usuáiro ID
            $usuarioID = $_SESSION['usuario_id'];

            if($mensagem > ''){

                $insertBug = new \StdClass;

                $insertBug->usuario_id   = $usuarioID;
                $insertBug->bug          = $mensagem; 

                $this->mapper->bugs->persist($insertBug);
                $this->mapper->flush();

                return 'true';

            }else{

                return 'false';
            }

        }

    // Método responsável por relatar um bug


    // monta pagina detalhada de um box


        public function relatarBugFrontEnd()
        {

            $vars['_view'] = 'criar-convites.html.twig';

            return $vars;

        }


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    

        return $this->relatarBugFrontEnd();

    }


    public function post($acao = null, $arg1 = null)
    {   

        $mensagem = $_POST['descricao'];
        
        return $this->relatarBug($mensagem);

    }

}
