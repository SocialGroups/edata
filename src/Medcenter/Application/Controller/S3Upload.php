<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Relational\Sql;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Helper\ParsePut;
use SocialGroups\Application\Controller\s3class;

use SocialGroups\Application\Controller\ThumbGerador;

class S3Upload implements Routable
{

    // Passa mapper do banco de dados
    private $mapper; 
    private $c;
    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function config($tmp_name, $nome_arquivo)
    {

            //include_once("/s3class");

            $extencao = strrchr($nome_arquivo, '.');
            $codigoFile = md5($nome_arquivo);

         
            $bucket="bucket-socialgroups";
            $awsAccessKey="AKIAIKVT6CC24NGCKLPQ";
            $awsSecretKey="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";
            $s3 = new S3($awsAccessKey, $awsSecretKey);
         
            if($s3->putObjectFile($tmp_name, $bucket , $codigoFile.$extencao, S3::ACL_PUBLIC_READ) ){
                $s3file='https://'.$bucket.'.s3.amazonaws.com/'.$codigoFile.$extencao;
                return $s3file;
            }

    }

    public function post($acao = null)
    {
        
        if($acao == "upload") {

            $tmp_name       =   $_FILES['file']['tmp_name'];
            $nome_arquivo   =   $_FILES['file']['name'];
            
            return $this->config($tmp_name, $nome_arquivo);
        }
        
    }

     public function get($acao = null, $arg1 = null)
    {

        if($acao == "teste") {

            $vars['_view'] = 'awsteste.html.twig';
        
        }

            return $vars;

    }

}


?>
