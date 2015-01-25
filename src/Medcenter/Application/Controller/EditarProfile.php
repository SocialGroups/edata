<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\Profile;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;

class EditarProfile implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

        // Upload files para a amazon s3
        public function alterarFotoCapaProfile()
        {   

            $usuarioID = $_SESSION['usuario_id'];

            // Configurações de acesso ao bucket no s3
            $bucket="bucket-socialgroups";
            $awsAccessKey="AKIAIKVT6CC24NGCKLPQ";
            $awsSecretKey="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";
            $s3 = new S3($awsAccessKey, $awsSecretKey);
            // Configurações de acesso ao bucket no s3

            $posicao = 0;


            foreach($_FILES as $file){

                $tmpname = $file['tmp_name'];

                // Armazena a extensão do arquivo em uma url
                $extencao = strrchr($file['name'], '.');
                $codigoFile = md5($file['name'][$posicao].date('Y-m-d H:i:s'));

                $posicao++;


                    if($s3->putObjectFile($tmpname, $bucket , $codigoFile.$extencao, S3::ACL_PUBLIC_READ) ){

                                $s3file='https://'.$bucket.'.s3.amazonaws.com/'.$codigoFile.$extencao;
                                $capa = $s3file;

                    }


                // Atualiza Foto capa do grupo
                    $atualizaFotoCapaProfile = $this->mapper->usuario(array('id' => $usuarioID))->fetch();
                    $atualizaFotoCapaProfile->background_perfil = $capa;
                    $this->mapper->grupo->persist($atualizaFotoCapaProfile);
                    $this->mapper->flush();
                // Atualiza Foto capa do grupo

                }

                // Get usuário nick_name
                $SQL = " SELECT id, nick_name FROM usuario WHERE id = '$usuarioID' ";

                $getBasicInfoUser = $db = new DB($this->c->pdo);
                $getBasicInfoUser = $db->query($SQL);
                $getBasicInfoUser = $db->fetch();


                // Retorna dados após reload na pagina
                $Profile = new Profile();
                // Retorna dados após reload na pagina

                if($capa > ''){

                    $returnacao = 'true';

                }else{

                    $returnacao = 'false';

                }

                $nickName = $getBasicInfoUser->nick_name;
                $pagina = 0;

                return $Profile->profile($nickName,$pagina,$returnacao);

    }

    // Upload files para a amazon s3


    // FrontEnd Foto Capa

        public function FrontEndCapa()
        {

            $vars['_view'] = 'editar-profile-capa.html.twig';

            return $vars;

        }

    // FrontEnd Foto Capa

    // FrontEnd Nome Sobre Nome

    public function FrontEndDados()
    {

        $vars['_view'] = 'editar-profile-dados.html.twig';

        return $vars;

    }

    // FrontEnd Foto Capa

    // Método responsável por alterar dados pessoais
        public function alterarDadosProfile($nome = null, $sobreNome = null, $senha = null){

            // recupera ID do usuário logado
            $usuarioID      = $_SESSION['usuario_id'];
            
            // Altera dados do usuário
            $alteraUsuarioDados = $this->mapper->usuario[$usuarioID]->fetch();

            if($nome > ''){

                $alteraUsuarioDados->nome = $nome;

            }

            if($sobreNome > ''){

                $alteraUsuarioDados->sobre_nome = $sobreNome;

            }

            if($senha > ''){

                $alteraUsuarioDados->senha = $senha;

            }
            
            $this->mapper->usuario->persist($alteraUsuarioDados);
            $this->mapper->flush();

            if($senha > ''){

                // efetua logout do usuário pois a senha foi alterada
                session_destroy();
                return 'logout';

            }else{

                return 'true';

            }

        }
    // Método responsável por alterar dados pessoais
 

    public function get($acao = null)
    {      
        if($acao == 'capa'){

            return $this->FrontEndCapa();

        }else if($acao == 'dados'){

            return $this->FrontEndDados();

        }
    }


    public function post($acao = null, $arg1 = null, $arg2 = null)
    {           
                if($acao == 'capa'){

                return $this->alterarFotoCapaProfile();

                }if($acao == 'profile'){


                if(isset($_POST['nome']) OR isset($_POST['sobreNome']) OR isset($_POST['senha'])){

                    if(isset($_POST['nome'])){

                        $nome = $_POST['nome'];
                    }else{

                        $nome = '';
                    }

                     if(isset($_POST['sobreNome'])){

                        $sobreNome = $_POST['sobreNome'];
                    }else{

                        $sobreNome = '';
                    }

                     if(isset($_POST['senha']) AND $_POST['senha'] > ''){

                        $senha = md5($_POST['senha']);
                    }else{

                        $senha = '';
                    }

                    return $this->alterarDadosProfile($nome, $sobreNome, $senha);

                }else{

                    return 'false';
                }

                

                }


    }

}
