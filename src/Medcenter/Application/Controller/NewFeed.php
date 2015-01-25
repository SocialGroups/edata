<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;
use SocialGroups\Application\Controller\LiveMention;
use SocialGroups\Application\Controller\InjectionGrupoDados;
use SocialGroups\Application\Controller\DeleteObjectCache;
use SocialGroups\Application\Controller\RecomendaGrupo;
use SocialGroups\Application\Controller\RecomendaFollowing;

class NewFeed implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por retornar thumnail de video do youtube
    function get_youtube_thumbnail($url)
    {

        // Turn off all error reporting
       
        $parse = parse_url($url);
        if(!empty($parse['query'])) {
        preg_match("/v=([^&]+)/i", $url, $matches);
        $id = $matches[1];
        } else {
        //to get basename
        $info = pathinfo($url);
        $id = $info['basename'];
        }
        $img = "http://img.youtube.com/vi/$id/1.jpg";
        return $img;
    }
    // Método responsável por retornar thumnail de video do youtube


    // Método responsável por recuperar o feed

        public function getFeed($pagina, $return = null)
        {

            // Turn off all error reporting
           

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu

            // Get usuário ID
            $usuarioID = $_SESSION['usuario_id'];

            // Algoritimo de paginação
            $limite = 10;
            if($pagina == ''){
                $inicio = 0;
            }else{
                $inicio = ($pagina * $limite) - $limite;
            }
            // Algoritimo de paginação

            // Recupera feed de notificias e armazena em um array

            $SQL = "SELECT g.nome as GrupoTitulo, gbo.id, gbo.box_id as boxID, gbo.grupo_id, gbo.box_tipo, gbo.usuario_id as usuarioID, gb.titulo, gb.descricao, gb.dataHora, gb.ativo, us.nome, us.sobre_nome, us.foto_perfil, us.nick_name, cc.conteudo AS conteudoCompartilhamento, g.nome as grupoNome, ginfo.numero_seguidores, ginfo.numero_boxes 

                FROM grupo_box_ordenacao AS gbo 

                INNER JOIN usuario as us ON gbo.usuario_id = us.id

                LEFT JOIN grupo_informacoes as ginfo ON ginfo.grupo_id = gbo.grupo_id

                LEFT JOIN seguidores AS s ON s.follower_id = '$usuarioID' AND s.ativo = 0

                LEFT JOIN grupo_usuario AS gu ON gu.usuario_id = '$usuarioID'

                LEFT JOIN grupo_box AS gb ON gb.id = gbo.box_id

                LEFT JOIN compartilhamento_conteudo AS cc ON cc.gbo_id = gbo.id

                LEFT JOIN grupo as g ON gb.grupo_id = g.id

                WHERE gbo.grupo_id = gu.grupo_id AND gb.ativo = 0 AND gu.ativo = 0 AND g.privilegios <> 'privado' AND gbo.ativo = 0 

                OR gbo.grupo_id = gu.grupo_id AND gb.ativo = 0 AND gu.ativo = 0 AND g.privilegios = 'privado' AND gu.grupo_id = gbo.grupo_id AND gbo.ativo = 0

                OR gbo.usuario_id = s.following_id AND gb.ativo = 0  AND g.privilegios <> 'privado' AND gbo.ativo = 0

                OR gbo.usuario_id = s.following_id AND gb.ativo = 0  AND g.privilegios = 'privado' AND gu.grupo_id = gbo.grupo_id AND gbo.ativo = 0

                GROUP BY gbo.id

                ORDER BY gbo.id DESC 

                LIMIT $inicio,$limite 
                ";

            $arrayFeed = $db = new DB($this->c->pdo);
            $arrayFeed = $db->query($SQL);
            $arrayFeed   = $db->fetchAll();

            // Recupera feed de notificias e armazena em um array

            // Recupera número de grupos
            $getNumeroGrupos = COUNT($this->mapper->grupo(array('usuario_criacao' => $usuarioID))->fetchAll());

            // Recupera número de interações
            $getNumeroBoxes = COUNT($this->mapper->grupo_box(array('usuario_id' => $usuarioID))->fetchAll());

            // Recupera número de interações
            $getNumeroInteracoes = COUNT($this->mapper->grupo_interacoes(array('usuario_id' => $usuarioID))->fetchAll());

            // Recupera ultimos 5 boxes que foram criados
            $getUltimosBoxesCriados = $this->mapper->grupo_box->fetchAll(Sql::orderBy('id')->desc()->limit(10));

            // Recupera trending topics Interations boxes
            $dataInicioBuscar = date('Y-m-d 00:00:00');
            $dataFinalBuscar  = date('Y-m-d 23:59:59');
            $SQLTopics = "SELECT count(topic) repeticoes,topic from topics WHERE dataHora BETWEEN '$dataInicioBuscar' AND  '$dataFinalBuscar' group by topic having repeticoes >1 ORDER BY repeticoes desc limit 5";
            $arrayTrendingTopics = $db = new DB($this->c->pdo);
            $arrayTrendingTopics = $db->query($SQLTopics);
            $arrayTrendingTopics = $db->fetchAll();


            // Recupera mídias dos feed`s

                $arrayMidias = array();

                $arrayYoutube = array();

                $arrayComentarios = array();

                foreach ($arrayFeed as $dadosFeed) {

                    $boxID = $dadosFeed->boxID;

                    // Get 3 comentários de cada box
                    $SQL = "SELECT gi.id, gi.grupo_box_id, gi.conteudo, gi.dataHora, gi.status, gi.usuario_id, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil, gbo.id as gboID
                            FROM grupo_interacoes as gi

                            INNER JOIN usuario as user ON user.id = gi.usuario_id
                            INNER JOIN grupo_box_ordenacao as gbo ON gbo.box_id = gi.grupo_box_id

                            WHERE gi.grupo_box_id = '$boxID' AND gi.status = 0

                            ORDER BY gi.id desc

                            LIMIT 2
                    ";

                    $getComentarios = $db = new DB($this->c->pdo);
                    $getComentarios = $db->query($SQL);
                    $getComentarios = $db->fetchAll();

                    // Armazena comentarios em arrays
                    $arrayComentarios[] = array('gboID' => $dadosFeed->id, 'comentarios' => $getComentarios);

                    // Get mídias deste box
                    $getBoxMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $dadosFeed->boxID))->fetchAll();

                    // Get Vídeo deste box
                    $regex = '/https?\:\/\/[^\" ]+/i';
                    preg_match_all($regex, $dadosFeed->descricao, $matches);
                    $youtube = $matches[0];

                        foreach ($youtube as $urlVideo) {
                            
                            $url = $urlVideo;

                            $thumnail = $this->get_youtube_thumbnail($url);

                            $parts = parse_url($url);
                            parse_str($parts['query'], $query);
                            $videoID = $query['v'];
                            $boxIDVideo = $dadosFeed->id;

                //             $json_output = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&alt=json");
                //             $json = json_decode($json_output, true);

                //             //This gives you the video description
                //             $videoDescricao = $json['entry']['media$group']['media$description']['$t'];

                //             // remove url do vídeo do conteúdo
                //             $conteudoString = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $dadosFeed->descricao);

                //             $videoTitle = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&fields=title");

                //             preg_match("/<title>(.+?)<\/title>/is", $videoTitle, $titleOfVideo);
                //             $videoTitle = $titleOfVideo[1];
                               $videoTitle = 'Indisponível temporáriamente';
                                $videoDescricao = 'Indisponível temporáriamente';

                            if(isset($videoID)){

                                $arrayYoutube[] = array('boxID' => $boxIDVideo, 'youtube' => $videoID, 'videoTitulo' => $videoTitle, 'videoDescricao' => $videoDescricao);

                            }
                        }

                    if(COUNT($getBoxMidias) > 0){

                        $arrayMidias[] = array('boxID' => $dadosFeed->id, 'midias' => $getBoxMidias);

                    }

                }

            // Recupera mídias dos feed`s

            // Get dados usuário
            $getDadosUsuario = $this->mapper->usuario(array('id' => $usuarioID))->fetchAll();

            // get dados usuário
            $getDadosUsuario = $this->mapper->usuario(array('id' => $_SESSION['usuario_id']))->fetchall();

            // recupera grupos criado pelo usuário com os seus dados
            $SQLgetGrupos = "SELECT g.id, g.nome, g.descricao, g.grupo_avatar, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes FROM grupo as g

                    LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id

                    WHERE g.usuario_criacao = '$usuarioID' ORDER BY g.id ";

            // Executa a consulta novamente
            $getGrupos = $db = new DB($this->c->pdo);
            $getGrupos = $db->query($SQLgetGrupos);
            $getGrupos = $db->fetchAll(); 


            // Instância classe de recomendação de grupos
            $getGruposRecomendados = new RecomendaGrupo();
            // Instância classe de recomendação de grupos

            // Instância classe de recomendação de usuários
            $getUsuariosRecomendados = new RecomendaFollowing();
            // Instância classe de recomendação de usuários


            // recupera grupos criado pelo usuário com os seus dados

            // Retorna dados para o FrontEnd
            $vars['usuarioID']          = $usuarioID;
            $vars['numeroGrupos']       = COUNT($getGrupos);
            $vars['dadosGruposUsuario'] = $getGrupos;
            $vars['arrayFeed']          = $arrayFeed;
            $vars['comentarios']        = $arrayComentarios;
            $vars['dadosUsuario']       = $getDadosUsuario;
            $vars['midias']             = $arrayMidias;
            $vars['youtube']            = $arrayYoutube;
            $vars['NumeroGrupo']        = $getNumeroGrupos;
            $vars['NumeroBoxes']        = $getNumeroBoxes;
            $vars['NumeroInteracoes']   = $getNumeroInteracoes;
            $vars['ultimosBoxCriados']  = $getUltimosBoxesCriados;
            $vars['TrendingTopics']     = $arrayTrendingTopics;
            $vars['return']             = $return;
            $vars['pagina']             = $pagina;
            $vars['dadosUsuarioLogado'] = $getDadosUsuario;
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['numeroGruposUsuario']    = $getDadosmenu->getNumeroGrupos();
            $vars['numeroNotificacoesPendentes'] = $getDadosmenu->numeroNotificacoesPendentes();
            $vars['notificacoesPendentes'] = $getDadosmenu->notificacoesPendentes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();
            $vars['gruposRecomendados'] = $getGruposRecomendados->getGruposRecomendar();
            $vars['usuariosRecomendados'] = $getUsuariosRecomendados->getFollowingRecomendar();
            $vars['_view']              = 'new-feed.html.twig';

            return $vars;

        }

    // Método resposnável por recuperar o feed


       // Método responsável por criar um box dentro de um grupo

        public function InsertBox($grupoID, $titulo, $descricao, $pessoas, $tmp_name = null, $nome_arquivo = null, $return = null)
        {  

            // Turn off all error reporting
            
         
            $grupoNome = 'aaa';
             // Inicio - Faz o upload da capa para o s3 e retorna a url

            $bucket="bucket-socialgroups";
            $awsAccessKey="AKIAIKVT6CC24NGCKLPQ";
            $awsSecretKey="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";
            $s3 = new S3($awsAccessKey, $awsSecretKey);

            $array_url_arquivos = array();

        if($tmp_name > ''){

            $posicao =0;

                foreach ($tmp_name as $tmpname) {

                $extencao = strrchr($_FILES['file']['name'][$posicao], '.');
                $codigoFile = md5($_FILES['file']['name'][$posicao].date('Y-m-d H:i:s'));

                $posicao++;


                    if($s3->putObjectFile($tmpname, $bucket , $codigoFile.$extencao, S3::ACL_PUBLIC_READ) ){
                                $s3file='https://'.$bucket.'.s3.amazonaws.com/'.$codigoFile.$extencao;
                                $capa = $s3file;

                                // Armazena dados no array
                                $array_url_arquivos[] = array('midiaURL' => $s3file, 'thumbURL' => "thumbs/$codigoFile$extencao");

                    }

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

                if($verificaPermissao >='1'){

                    $travaPermissao = 'liberado';

                }else{

                    $travaPermissao = 'travado';
                }

            }else{

                    $travaPermissao = 'liberado';
            }

            if($travaPermissao == 'liberado'){

                if($titulo > '' AND $descricao > ''){

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


                        if($grupoBoxID == ''){

                             $retornoInsert  = 'false';

                             $return = 'false';

                        }else{

                             $retornoInsert  = 'publicacao';

                             $return = 'true';

                        }
                            header("Location: http://socialgroups.com.br/newhome/1/$return");
                            exit;



        }

    // Método responsáfel por criar um box dentro de um grupo


    public function get($pagina = null, $return = null)
    {    
        // Turn off all error reporting
         
        return $this->getFeed($pagina, $return);

    }



    public function post($acao = null, $arg1 = null)
    {

        if($acao == 'InsertBox'){

            // Recupera todos os parametros e valores para inserssão no banco de dados
            $grupoID        = $_POST['grupoID'];
            $titulo         = $_POST['titulo'];
            $descricao      = $_POST['descricao'];       

            if(isset($_FILES['file']['tmp_name'])){

            $tmp_name       =   $_FILES['file']['tmp_name'];
            $nome_arquivo   =   $_FILES['file']['name'];

            }else{

                $tmp_name = '';
                $nome_arquivo = '';

            }


            if(isset($_POST['pessoasMencoes'])){

               $pessoas = $_POST['pessoasMencoes'];

            }else{

                $pessoas = '';
            }   

            return $this->InsertBox($grupoID, $titulo, $descricao, $pessoas, $tmp_name, $nome_arquivo );

        }
    }

}
