<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\TriggersTopMenu;
use SocialGroups\Application\Controller\Profile;

class CriarConvite implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por criar convite

        public function convidar($nome, $email)
        {   

            // Recupera Dados do trigger menu
            $getProfile = new Profile();
            // recupera Dados do trigger menu      

            // Verifica se já existe este E-mail Cadastrado no banco de dados
            $verificaEmailCadastrado = COUNT($this->mapper->usuario(array('email' => $email))->fetchall());


            if($verificaEmailCadastrado == 0){

                $token = '66818d9881aff5';

                $dataHora = date('Y-m-d H:i:s');

                $chave = md5($token.$email.$dataHora);

                // Cadastra Convite no banco de dados

                $cadastraConvite = new \stdClass;
                $cadastraConvite->chave = $chave;

                $this->mapper->convite->persist($cadastraConvite);
                $this->mapper->flush();

                // Cadastra Convite no banco de dados


                // Envia E-mail para o convidado com a chave



                // Envia E-mail para o convidado com a chave

                return 'true';

            }else{

                return 'false';
            }

        }

    // Método responsável por criar convite


    // FrontEnd

        public function criarConviteFrontEnd()
        {

            $vars['_view'] = 'criar-convites.html.twig';

            return $vars;

        }

    // FrontEnd

 

    public function get($acao = null, $arg1 = null, $arg2 = null)
    {   

         return $this->criarConviteFrontEnd();
    }


    public function post($acao = null, $arg1 = null, $arg2 = null)
    {       

        $nome   = $_POST['nome'];
        $email  = $_POST['email'];
        return $this->convidar($nome, $email);

    }

}
