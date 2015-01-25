<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\s3class;
use SocialGroups\Application\Controller\geradorthumb;

class SocialCrud implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    public function config($tmp_name, $nome_arquivo)
    {

            //include_once("/s3class");

    }



    // Método responsável por criar um grupo

        public function InsertGrupo($grupoNome, $grupoDescricao, $grupoPrivilegios, $grupoTipo)
        {      



            // validação
            if($grupoNome > '' AND $grupoDescricao > '' AND $grupoPrivilegios > '' AND $grupoTipo > ''){

                $InsertGrupo = new \StdClass;

                $InsertGrupo->nome               = $grupoNome;
                $InsertGrupo->descricao          = $grupoDescricao;
                $InsertGrupo->privilegios        = $grupoPrivilegios;
                $InsertGrupo->grupo_tipo         = $grupoTipo;
                $InsertGrupo->dataCriacao        = date('Y-m-d H:i:s');
                $InsertGrupo->usuario_criacao    = $_SESSION['usuario_id'];

                $this->mapper->grupo->persist($InsertGrupo);
                $this->mapper->flush();

            }

               // ID do grupo que acabou de ser criado
               $grupoID = $InsertGrupo->id;

                if($grupoID == ''){

                   return $return = 'false';

                }else{

                    // Cadastrar usuário como administrador do grupo
                    $InsertUsuarioGrupo = new \StdClass;

                    $InsertUsuarioGrupo->grupo_id           = $grupoID;
                    $InsertUsuarioGrupo->usuario_id         = $_SESSION['usuario_id'];
                    $InsertUsuarioGrupo->nivel              = 'full_administrador';
                    $InsertUsuarioGrupo->status             = 'ativo';

                    $this->mapper->grupo_usuario->persist($InsertUsuarioGrupo);
                    $this->mapper->flush();

                    return $return = 'true';

                }

        }

    // Método responsável por criar um grupo


    // Método responsável por criar um box dentro de um grupo

        public function InsertBox($grupoID, $titulo, $descricao, $tmp_name, $nome_arquivo)
        {




             // Inicio - Faz o upload da capa para o s3 e retorna a url

                    $bucket="bucket-socialgroups";
                    $awsAccessKey="AKIAIKVT6CC24NGCKLPQ";
                    $awsSecretKey="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";
                    $s3 = new S3($awsAccessKey, $awsSecretKey);

                   

                    $capa = 'dasdasd';


                    $array_url_arquivos = array();

                    foreach ($nome_arquivo as $arquivo) {

                         $extencao = strrchr($arquivo, '.');
                         $codigoFile = md5($arquivo.date('Y-m-d H:i:s'));

                         foreach ($tmp_name as $tmpname) {

                    # Caminho da imagem a ser redimensionada: 
                    $input_image = $tmpname;
                     
                    // Pega o tamanho original da imagem e armazena em um Array:
                    $size = getimagesize( $input_image );
                     
                    // Configura a nova largura da imagem:
                    $thumb_width = "400";
                     
                    // Calcula a altura da nova imagem para manter a proporção na tela: 
                    $thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
                     
                    // Cria a imagem com as cores reais originais na memória.
                    $thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );

                    $whiteBackground = imagecolorallocate($thumbnail, 255, 255, 255);
                    imagefill($thumbnail,0,0,$whiteBackground);
                     
                    // Criará uma nova imagem do arquivo.

                    if($extencao == '.jpg'){

                        $ImageCreateFrom = ImageCreateFromJPEG( $input_image );

                    }else if($extencao == '.png'){

                        $ImageCreateFrom = imagecreatefrompng( $input_image );

                    }

                    $src_img = $ImageCreateFrom;
                     
                    // Criará a imagem redimensionada:
                    ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
                     
                    // Informe aqui o novo nome da imagem e a localização:
                    ImageJPEG( $thumbnail, "thumbs/$codigoFile$extencao", 100);
                     
                    // Limpa da memoria a imagem criada temporáriamente: 
                    ImageDestroy( $thumbnail );


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

            if($grupoTipo == 'privado'){

                // Verifica se o usuário tem permissão para inserir boxes neste grupo
                $verificaPermissao = COUNT($this->mapper->grupo_usuario(array('usuario_id' => $_SESSION['usuario_id'], 'grupo_id' => $grupoID))->fetchAll());

                if($verificaPermissao == '1'){

                    $travaPermissao = 'liberado';

                }else{

                    $travaPermissao = 'travado';
                }

            }else{

                    $travaPermissao = 'liberado';
            }

            if($travaPermissao == 'liberado'){

                if($titulo > '' AND $descricao > ''){

                        // Cadastrar usuário como administrador do grupo
                        $InsertGrupoBox = new \StdClass;

                        $InsertGrupoBox->grupo_id       = $grupoID;
                        $InsertGrupoBox->titulo         = $titulo; 
                        $InsertGrupoBox->descricao      = $descricao;
                        $InsertGrupoBox->capa_url       = $capa;
                        $InsertGrupoBox->usuario_id     = $_SESSION['usuario_id'];

                        $this->mapper->grupo_box->persist($InsertGrupoBox);
                        $this->mapper->flush();

                        // ID do grupo que acabou de ser criado
                        $grupoBoxID = $InsertGrupoBox->id;

                }
            }

                // Inicio - liga arquivos importados ao box no banco de dados 
                $MidiasUrl = array_unique($array_url_arquivos);

                    foreach ($MidiasUrl as $fileUrl) {   

                        $extencao = strrchr($fileUrl['midiaURL'], '.');

                        if($extencao == '.jpg' OR $extencao == '.png' or $extencao == '.gif' or $extencao == '.mpeg' or $extencao == '.jpeg'){

                            $tipofile = 'imagem';

                        }else if($extencao == '.mp4' OR $extencao == '.wmv'){

                            $tipofile = 'video';
                        }


                       

                        
                        $InsertMidiaBox = new \StdClass;
                        $InsertMidiaBox->grupo_box_id   = $grupoBoxID;
                        $InsertMidiaBox->tipo           = $tipofile; 
                        $InsertMidiaBox->midia_url      = $fileUrl['midiaURL'];
                        $InsertMidiaBox->midia_thumb    = $fileUrl['thumbURL'];

                        $this->mapper->grupo_midia->persist($InsertMidiaBox);
                        $this->mapper->flush();
                        
                    }

                // Final - liga arquivos importados ao box no banco de dados 


                        if($grupoBoxID == ''){

                           // return $return = 'false';

                        }else{

                            //return $return = 'true';

                        }

        }

    // Método responsáfel por criar um box dentro de um grupo

    // Método - Crud de Insert Público
        
    public function Insert($ParametrosValores, $TabelaInsert)
        {
                // Remove Warning
                error_reporting(E_ERROR | E_PARSE);

                $variavelInsert = new \StdClass;
                
                $campos = array_keys($ParametrosValores); // Aqui, separamos os indices do array para pegarmos o nome do campo, e não seu valor 
                for ($i=0; $i <sizeof($campos) ; $i++) { // Fazemos uma repetição para salvarmos na variável $cp o nome dos campos
                                
                    $cp .= $campos[$i].",";
                    
                }
                
                for ($i=0; $i <sizeof($campos) ; $i++) { // Fazemos uma nova repetição para salvarmos os valores

                    $cp2 = $campos[$i];

                    if($cp2 <> 'tabela'){

                        if($cp2 == 'dataCriacao'){

                            $variavelInsert->$cp2 = date('Y-m-d H:i:s');

                        }else if($cp2 == 'usuario_criacao'){

                            $variavelInsert->$cp2 = $_SESSION['usuario_id'];

                        }else{

                            $variavelInsert->$cp2 = $ParametrosValores[$cp2];
                        }

                    }

                    if($ParametrosValores[$cp2] == ''){

                        $trava = 1;
                    }
                }

                $this->mapper->$TabelaInsert->persist($variavelInsert);
                $this->mapper->flush();


                if($trava == 1){

                   return $return = 'false';

                }else{

                    return $return = 'true';

                }

        }

    // Método - Crud de Insert Público

    // Método - Crud de Insert com permissão
        
    public function InsertPrivado($ParametrosValores, $TabelaInsert)
        {
                // Remove Warning
                error_reporting(E_ERROR | E_PARSE);

                $variavelInsert = new \StdClass;
                
                $campos = array_keys($ParametrosValores); // Aqui, separamos os indices do array para pegarmos o nome do campo, e não seu valor 
                for ($i=0; $i <sizeof($campos) ; $i++) { // Fazemos uma repetição para salvarmos na variável $cp o nome dos campos
                                
                    $cp .= $campos[$i].",";
                    
                }
                
                for ($i=0; $i <sizeof($campos) ; $i++) { // Fazemos uma nova repetição para salvarmos os valores

                    $cp2 = $campos[$i];

                    if($cp2 <> 'tabela'){

                        if($cp2 == 'dataCriacao'){

                            $variavelInsert->$cp2 = date('Y-m-d H:i:s');

                        }else{

                            $variavelInsert->$cp2 = $ParametrosValores[$cp2];
                        }

                    }

                    if($ParametrosValores[$cp2] == ''){

                        $trava = 1;
                    }
                }

                $this->mapper->$TabelaInsert->persist($variavelInsert);
                $this->mapper->flush();


                if($trava == 1){

                   return $return = 'false';

                }else{

                    return $return = 'true';

                }

        }

    // Método - Crud de Insert Público

    // Método - retorna usuários para criar um grupo temporário de acordo com o interesse

        public function SearchUsuariosOracle($palavra_chave, $grupo_temporario_id){

            $arrayUsuariosGrupoTemporario = $this->mapper->oracle(array('palavra_chave LIKE' => $palavra_chave, 'status' => 'livre'))->fetchAll();

            var_dump($arrayUsuariosGrupoTemporario);

            // Adiciona o usuário no grupo temporário

                foreach ($arrayUsuariosGrupoTemporario as $dadosUsuario) {
                    
                    $dadosUsuario->usuario_id;

                    $adicionaUsuario = new \stdClass;
                    $adicionaUsuario->grupo_id      = $grupo_temporario_id;
                    $adicionaUsuario->usuario_id    = $dadosUsuario->usuario_id;
                    $adicionaUsuario->nivel         = 'usuario';
                    $adicionaUsuario->status        = 'inativo';

                    $this->mapper->grupo_usuario->persist($adicionaUsuario);
                    $this->mapper->flush();
                }

            // Adiciona o usuário no grupo temporário

        }

    // Método - retorna usuários para criar um grupo temporário de acordo com o interesse

    // Método - Recupera todos os grupos temporários com usuários logados

        public function AdicionaUsuarioGrupoTemporario($palavra_chave, $usuario_id){

            $trataDataHora = mktime(date("H")-10, date("i"),date("s") ,date("m"), date("d"), date("Y")); 
            $dataHoraAtual  = date('Y-m-d H:i:s');
            $dataHoraBuscar = date("Y-m-d H:i:s", $trataDataHora); 

            // Recupera um grupo disponível
            $SQL =  "SELECT * FROM grupo 
                    INNER JOIN tag   ON tag.grupo_id=grupo.id
                    WHERE tag.tag like '%$palavra_chave%' AND grupo.dataCriacao BETWEEN '$dataHoraBuscar' AND '$dataHoraAtual'
                    ";

            $arrayGruposTemporariosDisponiveis = $db = new DB($this->c->pdo);
            $arrayGruposTemporariosDisponiveis = $db->query($SQL);
            $arrayGruposTemporariosDisponiveis = $db->fetchAll();

            var_dump($arrayGruposTemporariosDisponiveis);

        }

    // Método - Recupera todos os grupos temporários com usuários logados

    public function get($acao = null, $arg1 = null)
    {   

         if($acao == "insert"){

            // Recupera todos os parametros e valores para inserssão no banco de dados
            $ParametrosValores  = $_POST;
            $TabelaInsert       = $_POST['tabela'];

            return $this->Insert($ParametrosValores, $TabelaInsert);

        }else if($acao == "InsertPrivado"){

            // Recupera todos os parametros e valores para inserssão no banco de dados
            $ParametrosValores  = $_POST;
            $TabelaInsert       = $_POST['tabela'];

            return $this->Insert($ParametrosValores, $TabelaInsert);

        }else if($acao == "search-grupo-temporario"){

            $palavra_chave = "PHP";
            $usuario_id = '1';

            return $this->AdicionaUsuarioGrupoTemporario($palavra_chave, $usuario_id);

        }

    }


    public function post($acao = null, $arg1 = null)
    {   

         if($acao == "InsertGrupo"){

            // Recupera todos os parametros e valores para inserssão no banco de dados
            $grupoNome              = $_POST['nome'];
            $grupoDescricao         = $_POST['descricao'];
            $grupoPrivilegios       = $_POST['privilegios'];
            $grupoTipo              = $_POST['tipo'];

            return $this->InsertGrupo($grupoNome, $grupoDescricao, $grupoPrivilegios, $grupoTipo);

        }else if($acao == "InsertBox"){

            // Recupera todos os parametros e valores para inserssão no banco de dados
            $grupoID      = $_POST['grupoID'];
            $titulo       = $_POST['titulo'];
            $descricao    = $_POST['descricao'];

            $tmp_name       =   $_FILES['file']['tmp_name'];
            $nome_arquivo   =   $_FILES['file']['name'];

            return $this->InsertBox($grupoID, $titulo, $descricao, $tmp_name, $nome_arquivo);

        }else if($acao == "search-grupo-temporario"){

            $palavra_chave = "PHP";
            $usuario_id = '1';

            return $this->AdicionaUsuarioGrupoTemporario($palavra_chave, $usuario_id);

        }

    }
}
