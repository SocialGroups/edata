<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\GetGrupos;

class InsertBox implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

       // Método responsável por criar um box dentro de um grupo

        public function InsertBoxInativo($grupoID, $conteudo, $tmp_name = null, $nome_arquivo = null)
        {  

            echo $grupoID;

            // ID do usuário
            $usuarioID = $_SESSION['usuario_id'];
         
             // Inicio - Faz o upload da capa para o s3 e retorna a url

            // Configurações de acesso ao bucket no s3
            $bucket="bucket-socialgroups";
            $awsAccessKey="AKIAIKVT6CC24NGCKLPQ";
            $awsSecretKey="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";
            $s3 = new S3($awsAccessKey, $awsSecretKey);
            // Configurações de acesso ao bucket no s3


        // arry de mídia
        $array_url_arquivos = array();

        if($verificaExisteMidia == 1){

            $posicao = 0;

                foreach ($tmp_name as $tmpname) {

                // Armazena a extensão do arquivo em uma url
                $extencao = strrchr($_FILES['file']['name'][$posicao], '.');
                $codigoFile = md5($_FILES['file']['name'][$posicao].date('Y-m-d H:i:s'));

                $posicao++;


                    if($s3->putObjectFile($tmpname, $bucket , $codigoFile.$extencao, S3::ACL_PUBLIC_READ) ){
                                $s3file='https://'.$bucket.'.s3.amazonaws.com/'.$codigoFile.$extencao;
                                $capa = $s3file;

                                // Armazena dados no array
                                $array_url_arquivos[] = array('midiaURL' => $s3file, 'thumbURL' => "thumbs/$codigoFile$extencao");

                    }

                }
            }

            
            // Final  - Faz o upload da capa para o S3 e retorna a url


            // Verifica tipo do grupo
            $getGrupoTipo = $this->mapper->grupo(array('id' => $grupoID))->fetchAll();

            foreach ($getGrupoTipo as $dadosGrupo) {
                    
                    $grupoTipo = $dadosGrupo->privilegios;
            }

            // Verifica permissão de acesso
            $permissaoAcessoGrupo = new PermissaoAcessoGrupo();
            $permissaoAcessoGrupo->verificaPermissaoGrupoPrivado($grupoID, $usuarioID); 
            // Verifica permissão de acesso


                if($titulo > '' AND $descricao > '' AND $grupoID > ''){

                        // Recupera Dados do trigger menu
                        $DeleteObjectCache = new DeleteObjectCache();
                        // recupera Dados do trigger menu

                        // instância método responsável por deletar os objetos deste grupo do MemCache
                        $DeleteObjectCache->deleteObjeto($grupoID);
                        // Instância método responsável por deletar os objetos deste grupo do MemCache

                        // Insert box no grupo
                        $InsertGrupoBox = new \StdClass;

                        $InsertGrupoBox->grupo_id       = $grupoID;
                        $InsertGrupoBox->titulo         = $titulo; 
                        $InsertGrupoBox->descricao      = $descricao;
                        $InsertGrupoBox->usuario_id     = $_SESSION['usuario_id'];

                        $this->mapper->grupo_box->persist($InsertGrupoBox);
                        $this->mapper->flush();

                        // ID do grupo que acabou de ser criado
                        $grupoBoxID = $InsertGrupoBox->id;

                        // Adiciona Box na tabela de ordenação
                            $InsertBoxOrdenacao = new \StdClass;

                            $InsertBoxOrdenacao->box_id       = $grupoBoxID;
                            $InsertBoxOrdenacao->grupo_id     = $grupoID;
                            $InsertBoxOrdenacao->box_tipo     = 'publicacao';
                            $InsertBoxOrdenacao->usuario_id   = $_SESSION['usuario_id'];
                            $this->mapper->grupo_box_ordenacao->persist($InsertBoxOrdenacao);
                            $this->mapper->flush();
                        // Adiciona Box na tabela de ordenação

                        // Verifica se existe hashtag e adiciona a tabela de topics
                        $topics = array();
                        preg_match_all('/#(\w+)/',$descricao,$matches);
                            
                            foreach ($matches[1] as $match) {

                                    $topics[] = $match;
                            }

                        if(COUNT($topics) > 0){

                            foreach ($topics as $topic) {

                                $InsertTopic = new \StdClass;

                                $InsertTopic->usuario_id    = $_SESSION['usuario_id'];;
                                $InsertTopic->topic         = "#$topic";
                                $InsertTopic->dataHora      = date('Y-m-d H:i:s');
                                $InsertTopic->tipo          = 'box';
                                $InsertTopic->meta_value    = $grupoBoxID;

                                $this->mapper->topics->persist($InsertTopic);
                                $this->mapper->flush();
                            }

                        }

                        // Verifica se existe hashtag e adiciona a tabela de topics

                }

            if($pessoas > ''){

                $meta_value   = $grupoBoxID;
                $tipo = 'box';

                // Recupera classe LiveMension
                $getLiveMention = new LiveMention();
                // Recupera classe LiveMension

                // Envia dados de menssão caso haja
                $getLiveMention->cadPersonMenson($tipo, $meta_value, $pessoas);

            }

            // Injection Grupo Dados
            $InjectionGrupoDados = new InjectionGrupoDados();
            $coluna = 'boxes';
            $tipoUpdate = 'insert';
            $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
            // Injection Grupo Dados


                // Inicio - liga arquivos importados ao box no banco de dados 
                $MidiasUrl = $array_url_arquivos;

                    foreach ($MidiasUrl as $fileUrl) {   

                        $extencao = strrchr($fileUrl['midiaURL'], '.');

                        if($extencao == '.jpg' OR $extencao == '.png' or $extencao == '.gif' or $extencao == '.mpeg' or $extencao == '.jpeg'){

                            $tipofile = 'imagem';

                        }else if($extencao == '.mp4' OR $extencao == '.wmv'){

                            $tipofile = 'video';
                        }


                        // Insere Midia(s)

                            $InsertMidiaBox = new \StdClass;
                            $InsertMidiaBox->grupo_box_id   = $grupoBoxID;
                            $InsertMidiaBox->tipo           = $tipofile; 
                            $InsertMidiaBox->midia_url      = $fileUrl['midiaURL'];
                            $InsertMidiaBox->midia_thumb    = $fileUrl['thumbURL'];

                            $this->mapper->grupo_midia->persist($InsertMidiaBox);
                            $this->mapper->flush();

                         // Insere Midia(s)
                        
                    }

                // Final - liga arquivos importados ao box no banco de dados 



        }

    // Método responsáfel por criar um box dentro de um grupo


    // Upload files para a amazon s3
        public function uploadFilesS3()
        {   

            $data = array();

            // Configurações de acesso ao bucket no s3
            $bucket="bucket-socialgroups";
            $awsAccessKey="AKIAIKVT6CC24NGCKLPQ";
            $awsSecretKey="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";
            $s3 = new S3($awsAccessKey, $awsSecretKey);
            // Configurações de acesso ao bucket no s3

            $error = false;
            $files = array();

            $posicao = 0;


            foreach($_FILES as $file){

                $tmpname = $file['tmp_name'];

                // Armazena a extensão do arquivo em uma url
                $extencao = strrchr($file['name'], '.');
                $codigoFile = md5($file['name'][$posicao].date('Y-m-d H:i:s'));

                $posicao++;


                    if($s3->putObjectFile($tmpname, $bucket , $codigoFile.$extencao, S3::ACL_PUBLIC_READ) ){

                                $s3file='https://'.$bucket.'.s3.amazonaws.com/'.$codigoFile.$extencao;
                               $files[] = $capa = $s3file;

                    }else{

                        $error = true;
                    }

            }
            
             $data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);

            return $data;


             

        }
    // Upload files para a amazon s3


    // Método responsável pela insert do box no banco de dados

        public function insertbox($grupoID, $conteudo, $midias = null)
        {

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica tipo do grupo
            $getGrupoTipo = $this->mapper->grupo(array('id' => $grupoID))->fetchAll();

            foreach ($getGrupoTipo as $dadosGrupo) {
                    
                    $grupoTipo = $dadosGrupo->privilegios;
            }

            // Verifica permissão de acesso
            $permissaoAcessoGrupo = new PermissaoAcessoGrupo();
            $permissaoAcessoGrupo->verificaPermissaoGrupoPrivado($grupoID, $usuarioID); 
            // Verifica permissão de acesso

            // Verifica se o usuário segue este grupo
            $verificaUsuarioSegueGrupo = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID, 'ativo' => 0))->fetchAll());

            if($grupoID > '' AND $conteudo > '' AND $verificaUsuarioSegueGrupo == 1){

                // Recupera Dados do trigger menu
                $DeleteObjectCache = new DeleteObjectCache();
                // recupera Dados do trigger menu

                // instância método responsável por deletar os objetos deste grupo do MemCache
                $DeleteObjectCache->deleteObjeto($grupoID);
                // Instância método responsável por deletar os objetos deste grupo do MemCache

                // Insert box no grupo
                $InsertGrupoBox = new \StdClass;

                $InsertGrupoBox->grupo_id       = $grupoID; 
                $InsertGrupoBox->descricao      = $conteudo;
                $InsertGrupoBox->usuario_id     = $_SESSION['usuario_id'];

                $this->mapper->grupo_box->persist($InsertGrupoBox);
                $this->mapper->flush();

                // ID do grupo que acabou de ser criado
                $grupoBoxID = $InsertGrupoBox->id;

                // Adiciona Box na tabela de ordenação
                    $InsertBoxOrdenacao = new \StdClass;

                    $InsertBoxOrdenacao->box_id       = $grupoBoxID;
                    $InsertBoxOrdenacao->grupo_id     = $grupoID;
                    $InsertBoxOrdenacao->box_tipo     = 'publicacao';
                    $InsertBoxOrdenacao->usuario_id   = $_SESSION['usuario_id'];
                    $this->mapper->grupo_box_ordenacao->persist($InsertBoxOrdenacao);
                    $this->mapper->flush();
                // Adiciona Box na tabela de ordenação


                // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'boxes';
                $tipoUpdate = 'insert';
                $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                // Injection Grupo Dados

            }

            if(isset($midias)){

            foreach ($midias as $key => $value) {

                $extencao = strrchr($value, '.');

                    if($extencao == '.jpg' OR $extencao == '.png' or $extencao == '.gif' or $extencao == '.mpeg' or $extencao == '.jpeg'){

                        $tipofile = 'imagem';

                    }


                        // Insere Midia(s)

                            $InsertMidiaBox = new \StdClass;
                            $InsertMidiaBox->grupo_box_id   = $grupoBoxID;
                            $InsertMidiaBox->tipo           = $tipofile; 
                            $InsertMidiaBox->midia_url      = $value;

                            $this->mapper->grupo_midia->persist($InsertMidiaBox);
                            $this->mapper->flush();

                         // Insere Midia(s)
                        
            }

            }

            if(isset($grupoBoxID) AND $grupoBoxID > ''){

                return true;

            }else{

                return false;
            }

        }

    // Método responsável pelo insert do box no banco de dados


    public function get($acao)
    {   

        if($acao == 'uploads3'){

            return $this->uploadFilesS3();

        }
            

    }



    public function post($acao)
    {   

        if($acao == 'uploads3'){

            return $this->uploadFilesS3();

        }else if($acao == 'postbox'){

            $grupoID    = $_POST['grupoID'];
            $conteudo   = $_POST['conteudo'];
            if(isset($_POST['filenames'])){

                $midias     = $_POST['filenames'];

                return $this->insertbox($grupoID, $conteudo, $midias);
            }else{

                return $this->insertbox($grupoID, $conteudo);

            }



            

        }
            

    }

}
