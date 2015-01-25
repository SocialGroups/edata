<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\GetGrupos;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;

class EditarGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

        // Upload files para a amazon s3
        public function alterarFotoCapaGrupo($grupoID)
        {   

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se este usuário é administrador deste grupo
            $verificaUsuarioCriouGrupo = COUNT($this->mapper->grupo(array('usuario_criacao' => $usuarioID, 'id' => $grupoID))->fetchall());

            if($verificaUsuarioCriouGrupo == 1){

            $grupoPagina = 0;

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
                    $atualizaFotoCapaGrupo = $this->mapper->grupo(array('id' => $grupoID))->fetch();
                    $atualizaFotoCapaGrupo->grupo_avatar = $capa;
                    $this->mapper->grupo->persist($atualizaFotoCapaGrupo);
                    $this->mapper->flush();
                // Atualiza Foto capa do grupo

                }


                // Retorna dados após reload na pagina
                $GetGrupos = new GetGrupos();
                // Retorna dados após reload na pagina

                $returnacao = 'alterarCapaGrupo';

                return $GetGrupos->grupoSelecionado($grupoID, $grupoPagina, $returnacao);

            }




             

        }
    // Upload files para a amazon s3


    // FrontEnd Foto Capa

        public function FrontEnd($grupoID)
        {

            $vars['grupoID'] = $grupoID;
            $vars['_view'] = 'editar-grupo-capa.html.twig';

            return $vars;

        }

    // FrontEnd Foto Capa
 

    public function get($grupoID = null, $arg1 = null, $arg2 = null)
    {      

        return $this->FrontEnd($grupoID);
    }


    public function post($acao = null, $arg1 = null, $arg2 = null)
    {           
                $grupoID = $_POST['grupoID'];

                return $this->alterarFotoCapaGrupo($grupoID);


    }

}
