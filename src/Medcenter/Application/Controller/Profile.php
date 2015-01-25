<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\TriggersTopMenu;
use SocialGroups\Application\Controller\InjectionDadosNeo4j;

class Profile implements Routable
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
            error_reporting(0);

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


    public function Neo4jInjectionVisualizacoes($TipoInteracao, $id)
    {   
            // Turn off all error reporting
            error_reporting(0);

            $client = new \Everyman\Neo4j\Client('54.207.39.230', 7474);

            $InjectionVisualizacao = $client->makeNode();
            $InjectionVisualizacao->setProperty('id', $id)
                ->setProperty('TipoInteracao', $TipoInteracao)
                ->setProperty('usuario_id', $_SESSION['usuario_id'])
                ->setProperty('parametro', 'visualizacao')
                ->save();
    }


    public function profile($nickName,$pagina = null,$returnacao = null)
    {   
             // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu       

            // Algoritimo de paginação
            $limite = 10;
            if($pagina == ''){
                $inicio = 0;
            }else{
                $inicio = ($pagina * $limite) - $limite;
            }

            // Recupera informações simples sobre o usuário
            $SQL = " SELECT id, nome, sobre_nome, foto_perfil, background_perfil FROM usuario WHERE nick_name = '$nickName' ";

            $getBasicInfoUser = $db = new DB($this->c->pdo);
            $getBasicInfoUser = $db->query($SQL);
            $getBasicInfoUser = $db->fetch();

            // Validade se usuário existe
            if(isset($getBasicInfoUser->id)){}else{

                 // Envia usuário para pagina de erro
                $vars['_view'] = 'error404.html.twig';
                return $vars;

            }

            if($getBasicInfoUser->id == $_SESSION['usuario_id']){

                $verificaFollow = 1;

            }else{

                // Verifica se o usuário do profile já é seguido pelo visitante
                $verificaFollow = COUNT($this->mapper->seguidores(array('follower_id' => $_SESSION['usuario_id'], 'following_id' => $getBasicInfoUser->id, 'ativo' => 0))->fetchall());
            }

            $usuarioID = $getBasicInfoUser->id;

            // Get usuario interations

                $SQL = "SELECT  g.id as grupoID, g.nome as GrupoTitulo,gbo.id, gb.grupo_id, gb.titulo, gb.descricao, gb.usuario_id, gb.dataHora, gbo.box_id as boxID, gbo.box_tipo, user.id as usuarioID, user.nome, user.foto_perfil, user.nick_name

                        FROM  `grupo_box_ordenacao` AS gbo

                        INNER JOIN grupo_box AS gb ON gb.id = gbo.box_id

                        LEFT JOIN usuario AS user ON user.id = gb.usuario_id

                        LEFT JOIN grupo as g ON gbo.grupo_id = g.id

                        WHERE user.id = '$usuarioID' AND gb.ativo = 0 AND g.privilegios <> 'privado' AND gbo.ativo = 0
                    
                    ORDER BY gbo.id DESC 

                    LIMIT $inicio,$limite 
                    ";

            $arrayFeed = $db = new DB($this->c->pdo);
            $arrayFeed = $db->query($SQL);
            $arrayFeed   = $db->fetchAll();

            //var_dump($arrayFeed);

            //die();

            // Get usuario Interations


             // Recupera número de grupos
            $getNumeroGrupos = COUNT($this->mapper->grupo(array('usuario_criacao' => $getBasicInfoUser->id))->fetchAll());

            // Recupera boxes
            $getNumeroBoxes = COUNT($this->mapper->grupo_box(array('usuario_id' => $getBasicInfoUser->id))->fetchAll());

            // Recupera número de interações
            $getNumeroInteracoes = COUNT($this->mapper->grupo_interacoes(array('usuario_id' => $getBasicInfoUser->id))->fetchAll());

            $numeroResultados = COUNT($arrayFeed);

            // Recupera mídias dos feed`s

                $arrayMidias = array();

                $arrayYoutube = array();

                $arrayComentarios = array();

                foreach ($arrayFeed as $dadosFeed) {

                   $boxID = $dadosFeed->boxID;

                    // Get 3 comentários de cada box
                    $SQL = "SELECT gi.id, gi.grupo_box_id, gi.conteudo, gi.dataHora, gi.status, gi.usuario_id, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil
                            FROM grupo_interacoes as gi

                            INNER JOIN usuario as user ON user.id = gi.usuario_id

                            WHERE gi.grupo_box_id = '$boxID' AND gi.status = 0

                            ORDER BY gi.id desc

                            LIMIT 2
                    ";

                    $getComentarios = $db = new DB($this->c->pdo);
                    $getComentarios = $db->query($SQL);
                    $getComentarios = $db->fetchAll();

                    // Armazena comentarios em arrays
                    $arrayComentarios[] = array('boxID' => $dadosFeed->boxID, 'comentarios' => $getComentarios);

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

                            // $json_output = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&alt=json");
                            // $json = json_decode($json_output, true);

                            // //This gives you the video description
                            // $videoDescricao = $json['entry']['media$group']['media$description']['$t'];

                            // // remove url do vídeo do conteúdo
                            // $conteudoString = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $dadosFeed->descricao);

                            // $videoTitle = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&fields=title");

                            // preg_match("/<title>(.+?)<\/title>/is", $videoTitle, $titleOfVideo);
                            // $videoTitle = $titleOfVideo[1];

                            // provisorio
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

                // Inicio - Injection Visualização

                    // $client = new \Everyman\Neo4j\Client('54.207.39.230', 7474);

                    // $arrayQuantidadeVisualizacoes = array();

                    // foreach ($arrayFeed as $dadosInjectionVisualizacao) {

                    //         $TipoInteracao = $dadosInjectionVisualizacao->box_tipo;
                    //         $id = $dadosInjectionVisualizacao->id;
                            
                    //         $this->Neo4jInjectionVisualizacoes($TipoInteracao, $id);

                    //         // Inicio - Recpera Número de visualizações

                    //        $queryString = 'START n=node(*) WHERE has(n.parametro) AND n.parametro = "visualizacao" AND n.TipoInteracao = "'.$TipoInteracao.'" AND n.id = "'.$id.'" return n';

                    //         //"START n=node(*) return n";
                    //         $query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
                    //         $numeroVisualizacoes = COUNT($query->getResultSet());

                    //         $arrayQuantidadeVisualizacoes[] = array('id' => $id, 'quantidade' => $numeroVisualizacoes);

                    //         // Final - Recupera número de visualizações

                    // }

                // Final  - Injection Visualização

               
            // Recupera número de seguidores

                $numeroSeguidores = COUNT($this->mapper->seguidores(array('following_id' => $getBasicInfoUser->id))->fetchall());

            // Recupera número de seguidores

            // Recupera número de interações

                $numeroInteracoes = COUNT($this->mapper->timeline_interacoes(array('usuario_id' => $getBasicInfoUser->id))->fetchall());

            // Recupera número de interações

            // get dados usuário
            $getDadosUsuario = $this->mapper->usuario(array('id' => $_SESSION['usuario_id']))->fetchall();

            // Paramentros a serem passados para o FrontEnd
            $vars['usuarioLogadoID']    = $_SESSION['usuario_id'];
            $vars['ProfileID']          = $getBasicInfoUser->id;
            $vars['ProfileNome']        = $getBasicInfoUser->nome;
            $vars['ProfileSobreNome']   = $getBasicInfoUser->sobre_nome;
            $vars['ProfileFotoPerfil']  = $getBasicInfoUser->foto_perfil;
            $vars['ProfileFotoCapa']    = $getBasicInfoUser->background_perfil;
            $vars['StatusFollow']       = $verificaFollow;
            $vars['NumeroSeguidores']   = $numeroSeguidores;
            $vars['dadosUsuarioLogado'] = $getDadosUsuario;
            //$vars['ArrayNumeroVisualizacoes'] = $arrayQuantidadeVisualizacoes;
            $vars['AnoAtual']           = date('Y');
            $vars['MesAtual']           = date('m');
            $vars['DiaAtual']           = date('d');
            $vars['NumeroGrupo']        = $getNumeroGrupos;
            $vars['NumeroBoxes']        = $getNumeroBoxes;
            $vars['NumeroInteracoes']   = $getNumeroInteracoes;
            $vars['numeroRetornoFeed']  = $numeroResultados;
            $vars['numeroNotificacoesPendentes'] = $getDadosmenu->numeroNotificacoesPendentes();
            $vars['notificacoesPendentes'] = $getDadosmenu->notificacoesPendentes();
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar(); 

            $numeroMidias = COUNT($arrayMidias);


            // Ultimas 15 intarações no feed de notícias
            $vars['arrayFeed']      = $arrayFeed;
            $vars['comentarios']    = $arrayComentarios;
            $vars['midias']         = $arrayMidias;
            $vars['numeroMidias']   = $numeroMidias;
            $vars['youtube']        = $arrayYoutube;
            $vars['nickName']       = $nickName;

            if(isset($returnacao) AND $returnacao > ''){

                $vars['returnacao'] = $returnacao;
            }

            $vars['_view'] = 'Profile.html.twig';
            return $vars;




    }

    public function acaoseguir($usuarioFollowID){


        if($usuarioFollowID <> $_SESSION['usuario_id'] AND $usuarioFollowID > ''){

            // Verifica se o usuário já é seguido por este usuário
            $validaAcao = COUNT($this->mapper->seguidores(array('follower_id' => $_SESSION['usuario_id'], 'following_id' => $usuarioFollowID))->fetchAll());

            if($validaAcao == 0){

                $addFollow = new \stdClass;
                $addFollow->follower_id = $_SESSION['usuario_id'];
                $addFollow->following_id = $usuarioFollowID;

                $this->mapper->seguidores->persist($addFollow);
                $this->mapper->flush();

                $usuarioID      = $_SESSION['usuario_id'];
                $followingID    = $usuarioFollowID;

                // Adiciona Grupo No neo4j
                $AdicionaSeguirUsuarioNeo4j = new InjectionDadosNeo4j();
                $AdicionaSeguirUsuarioNeo4j->neo4jAdicionaSeguidorUsuario($usuarioID, $followingID);
                // Adiciona Grupo No neo4j      

                return $return = 'true';

            }else{

                 // Verifica se o usuário já seguiu este grupo em algum momento
                $verificaSeguiuAnteriormente = COUNT($this->mapper->seguidores(array('follower_id' => $_SESSION['usuario_id'], 'following_id' => $usuarioFollowID, 'ativo' => 1))->fetchAll());

                if($verificaSeguiuAnteriormente == 1){

                     // Atualiza status para ativo novamente
                    $reativaFollowerUsuario = $this->mapper->seguidores(array('follower_id' => $_SESSION['usuario_id'], 'following_id' => $usuarioFollowID, 'ativo' => 1))->fetch();
                    $reativaFollowerUsuario->ativo = 0;
                    $this->mapper->seguidores->persist($reativaFollowerUsuario);
                    $this->mapper->flush();

                    return $return = 'true';

                }

            }

        }

    }

    public function get($acao = null, $arg1 = null, $arg2 = null)
    {   

        // Turn off all error reporting
        error_reporting(0);

        if($acao == "feed" AND $arg1 > 1){

            // Início - Lógica de paginação

                $pagina = $arg1;
                $profileID = $arg2;

            // Final  - Lógica de paginação

            return $this->feed($pagina, $profileID);

        }else if($acao == "acaoseguir"){

            $usuarioFollowID = $arg1;

            return $this->acaoseguir($usuarioFollowID);

        }else{

            // Recupera todos os parametros e valores para inserssão no banco de dados
            $nickName = $acao;
            $pagina   = $arg1;

            return $this->profile($nickName,$pagina);
        }

    }


    public function post($acao = null, $arg1 = null, $arg2 = null)
    {   
        // Turn off all error reporting
        error_reporting(0);

        if($acao == "acaoseguir"){

            $usuarioFollowID = $_POST['followID'];

            return $this->acaoseguir($usuarioFollowID);

        }

    }

}
