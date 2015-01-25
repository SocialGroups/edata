<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class GetGrupos implements Routable
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

    // Get Grupos

        public function GetGrupos($pagina, $grupoNome = null)
        {


            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu  

            // Instância Memcache
            $memcache = new \Memcache;
            $memcache->connect('localhost', 11211);
            // Instância Memcache

            $limite = 20;

            if($pagina == '' OR $pagina == '0'){

                $inicio = 0;
            
            }else{

            $inicio = ($pagina * $limite) - $limite;

            }

            // Verifica se foi buscado um grupo especifico
            if($grupoNome > ''){


            }else{

                // recupera ultimos 10 grupos criados
                //$getGrupos =  $this->mapper->grupo(array(''))->fetchAll(Sql::orderBy('id')->desc()->limit($inicio,$limite));
                $SQLgetGrupos = "SELECT g.id, g.nome, g.descricao, g.usuario_criacao, g.grupo_avatar, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes, user.id as usuarioID, user.foto_perfil, user.nick_name FROM grupo as g

                                LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id
                                INNER JOIN usuario as user ON g.usuario_criacao = user.id

                                WHERE g.privilegios <> 'security' ORDER BY g.id desc LIMIT $inicio,$limite ";


                // chave - Query
                $chave = md5($SQLgetGrupos);

                // Buscamos o resultado na memória
                $cache = $memcache->get($chave);

                // Verifica se o resultado não existe ou expirou
                if ($cache === false) {
                    // Executa a consulta novamente
                    $getGrupos = $db = new DB($this->c->pdo);
                    $getGrupos = $db->query($SQLgetGrupos);
                    $getGrupos = $db->fetchAll(); 

                    $tempo = 60 * 60; // 3600s
                   // $memcache->set($chave, $getGrupos, 0, $tempo);

                }else {
                    
                    // A consulta está salva na memória ainda, então pegamos o resultado:
                    $getGrupos = $cache;

                }

                

            }

            $vars['arrayGrupos']        = $getGrupos;
            $vars['numeroNotificacoesPendentes'] = $getDadosmenu->numeroNotificacoesPendentes();
            $vars['notificacoesPendentes'] = $getDadosmenu->notificacoesPendentes();
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();
            $vars['numeroGruposUsuario']    = $getDadosmenu->getNumeroGrupos(); 
            $vars['_view']       = 'grupos.html.twig';

            return $vars;
        }

    // Get Grupos


    // Mostra grupo selecionado

        public function grupoSelecionado($grupoID,$grupoPagina, $returnacao = null)
        {      

             $usuarioID = $_SESSION['usuario_id'];

            // Verifica permissão de acesso
            $permissaoAcessoGrupo = new PermissaoAcessoGrupo();
            $permissaoAcessoGrupo->verificaPermissaoGrupoPrivado($grupoID, $usuarioID); 
            // Verifica permissão de acesso

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu


            // Verifica se existe notificação para este grupo com este usuário
                $VerificaNotificacaoPendente = $this->mapper->notificacoes(array('usuario_id' => $usuarioID, 'value' => $grupoID, 'status' => 'pendente'))->fetchAll();

                $NumeroVerificaNotificacaoPendente = COUNT($VerificaNotificacaoPendente);

                if($NumeroVerificaNotificacaoPendente == 1){

                    foreach ($VerificaNotificacaoPendente as $dadosNotificacaoPendente) {
                        
                        // Marca como visualizada a notificação
                        $atualizaStatus = $this->mapper->notificacoes(array('usuario_id' => $usuarioID, 'id' => $dadosNotificacaoPendente->id))->fetch();
                        $atualizaStatus->status = 'visualizado';
                        $this->mapper->notificacoes->persist($atualizaStatus);
                        $this->mapper->flush();

                    }

                }
            // Verifica se existe notificação para este grupo com este usuário

            // Limit

            $pagina = $grupoPagina;

            $limite = 10;

            if($pagina == '' OR $pagina == '0'){

                $inicio = 0;
            
            }else{

            $inicio = ($pagina * $limite) - $limite;

            }

            // Limit

            // Instância Memcache
            $memcache = new \Memcache;
            $memcache->connect('localhost', 11211);
            // Instância Memcache

            // Recupera dados do grupo

                 $SQL = "SELECT g.id, g.nome, g.usuario_criacao, g.descricao, g.grupo_avatar, user.id as usuarioID, user.foto_perfil, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes FROM grupo as g

                                LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id

                                INNER JOIN usuario as user ON g.usuario_criacao = user.id

                                WHERE g.id = '$grupoID' AND g.privilegios <> 'security'
                    ";

                    $getGrupoDados = $db = new DB($this->c->pdo);
                    $getGrupoDados = $db->query($SQL);
                    $getGrupoDados = $db->fetchAll();

            // Recupera dados do grupo

            // Recupera Conteúdo dos grupos

            $SQLarrayGrupoBoxes = "SELECT gbo.id, gbo.box_id as boxID, gbo.grupo_id, gbo.box_tipo, gbo.usuario_id as usuarioID, gb.titulo, gb.descricao, gb.dataHora,us.nome, us.sobre_nome, us.foto_perfil, us.nick_name, cc.conteudo AS conteudoCompartilhamento 

                                    FROM grupo_box_ordenacao AS gbo 

                                    INNER JOIN usuario as us ON gbo.usuario_id = us.id

                                    LEFT JOIN grupo_box AS gb ON gb.id = gbo.box_id 

                                    LEFT JOIN compartilhamento_conteudo AS cc ON cc.gbo_id = gbo.id

                                    WHERE gbo.grupo_id = '$grupoID' AND gb.ativo = 0 AND gbo.ativo = 0

                                    ORDER BY gbo.id DESC 

                                    LIMIT $inicio,$limite";


                // chave - Query
                $chaveGrupoBoxes = md5($SQLarrayGrupoBoxes);

                // Buscamos o resultado na memória
                $cacheGrupoBoxes = $memcache->get($chaveGrupoBoxes);

                    // Executa a consulta novamente
                    $arrayGrupoBoxes = $db = new DB($this->c->pdo);
                    $arrayGrupoBoxes = $db->query($SQLarrayGrupoBoxes);
                    $arrayGrupoBoxes = $db->fetchAll();

                    if($inicio <= 50){
                        $tempo = 60 * 60; // 3600s
                        //$memcache->set($chaveGrupoBoxes, $arrayGrupoBoxes, 0, $tempo);
                    }

            // Recupera Conteúdo dos grupos

            // Recupera mídias dos boxes

                $arrayMidias = array();

                $arrayYoutube = array();

                $arrayComentarios = array();

                foreach ($arrayGrupoBoxes as $dadosBoxes) {

                    $boxID = $dadosBoxes->boxID;

                    // Get mídias deste box
                    $getBoxMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $dadosBoxes->boxID))->fetchAll();

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
                    $arrayComentarios[] = array('gboID' => $dadosBoxes->id, 'comentarios' => $getComentarios);

                    // Get Vídeo deste box
                    $regex = '/https?\:\/\/[^\" ]+/i';
                    preg_match_all($regex, $dadosBoxes->descricao, $matches);
                    $youtube = $matches[0];

                        foreach ($youtube as $urlVideo) {
                            
                            $url = $urlVideo;

                            $thumnail = $this->get_youtube_thumbnail($url);

                            $parts = parse_url($url);
                            parse_str($parts['query'], $query);
                            $videoID = $query['v'];
                            $boxIDVideo = $dadosBoxes->boxID;

                //             $json_output = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&alt=json");
                //             $json = json_decode($json_output, true);

                //             //This gives you the video description
                //             $videoDescricao = $json['entry']['media$group']['media$description']['$t'];

                //             // remove url do vídeo do conteúdo
                //             $conteudoString = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $dadosBoxes->descricao);

                //             $videoTitle = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&fields=title");

                //             preg_match("/<title>(.+?)<\/title>/is", $videoTitle, $titleOfVideo);
                //             $videoTitle = $titleOfVideo[1];
                               $videoTitle = 'Indisponível temporáriamente';
                                $videoDescricao = 'Indisponível temporáriamente';

                            if(isset($videoID)){

                                $arrayYoutube[] = array('boxID' => $dadosBoxes->id, 'youtube' => $videoID, 'videoTitulo' => $videoTitle, 'videoDescricao' => $videoDescricao);

                            }
                        }

                    if(COUNT($getBoxMidias) > 0){

                        $arrayMidias[] = array('boxID' => $dadosBoxes->id, 'midias' => $getBoxMidias);

                    }

                }

            // Recupera mídias dos boxes

            // Verifica se o usuário segue o grupo
            $verificaUsuarioSegueGrupo = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID, 'ativo' => 0))->fetchAll());

            // número de boxes
            $numeroBoxes = COUNT($arrayGrupoBoxes);

            // Retorna os arrays para o FrontEnd
            if(isset($InsertReturn) AND $InsertReturn > ''){

                $vars['return'] = $InsertReturn;

            }

            $getDadosUsuario = $this->mapper->usuario(array('id' => $usuarioID))->fetchAll();

            $vars['dadosUsuarioLogado']     = $getDadosUsuario;
            $vars['grupoID']                = $grupoID;
            $vars['usuarioID']              = $usuarioID;
            $vars['grupoDados']             = $getGrupoDados;
            $vars['grupoBoxes']             = $arrayGrupoBoxes;
            $vars['numeroBoxes']            = $numeroBoxes;
            $vars['midias']                 = $arrayMidias;
            $vars['youtube']                = $arrayYoutube;
            $vars['comentarios']            = $arrayComentarios;
            $vars['grupoID']                = $grupoID;
            $vars['numeroNotificacoesPendentes'] = $getDadosmenu->numeroNotificacoesPendentes();
            $vars['notificacoesPendentes'] = $getDadosmenu->notificacoesPendentes();
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['VerificaSeguirGrupo']    = $verificaUsuarioSegueGrupo;
            $vars['dadosTopBar']            = $getDadosmenu->dadosUsuarioTopBar();
            $vars['numeroGruposUsuario']    = $getDadosmenu->getNumeroGrupos();

            if(isset($returnacao) AND $returnacao > ''){

                $vars['returnacao']             = $returnacao;
            }

            $vars['_view']                  = 'grupo_view.html.twig';

            return $vars;


        }

    // Mostra grupo selecionado

    // monta pagina detalhada de um box

        public function BoxView($boxID)
        {   

            // Turn off all error reporting
            error_reporting(0);

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu 

            $usuarioID = $_SESSION['usuario_id'];

            // Recupera dados do box e do autor
                $SQL = "SELECT gb.id, gb.titulo, gb.descricao, gb.grupo_id, gb.usuario_id, gb.dataHora, user.id as usuarioID, gb.ativo, user.nome, user.sobre_nome, user.foto_perfil 
                        FROM grupo_box as gb

                        LEFT JOIN grupo_box_ordenacao AS gbo ON gbo.box_id = gb.id AND gbo.box_tipo = 'publicacao'
                        LEFT JOIN usuario as user ON user.id = gb.usuario_id

                        WHERE gb.id = '$boxID' AND gb.ativo = 0 AND gbo.ativo = 0
                       ";

                $arrayDadosBox = $db = new DB($this->c->pdo);
                $arrayDadosBox = $db->query($SQL);
                $arrayDadosBox = $db->fetchAll();
            // Recupera dados do box e do autor
            // dados usuário logado 

                $getDadosUsuarioLogado = $this->mapper->usuario(array('id' => $usuarioID))->fetchAll();

            // dados usuário logado

            // Recupera comentários

            $SQLgetComentarios = "SELECT gi.id, gi.conteudo, us.id as usuarioID, us.nome, us.sobre_nome, us.foto_perfil FROM grupo_interacoes as gi 

                              INNER JOIN usuario as us ON gi.usuario_id = us.id

                              WHERE gi.grupo_box_id = '$boxID' AND gi.ativo = 0 

                              ORDER BY gi.id desc

                              LIMIT 0,4

                             ";

            $getComentarios = $db = new DB($this->c->pdo);
            $getComentarios = $db->query($SQLgetComentarios);
            $getComentarios = $db->fetchAll();

            // recupera comentários

            // Recupera mídias

                $arrayMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $boxID))->fetchAll();

                $arrayYoutube = array();

                foreach ($arrayDadosBox as $dadosBoxes) {

                    $grupoID = $dadosBoxes->grupo_id;

                    // Verifica permissão de acesso
                    $permissaoAcessoGrupo = new PermissaoAcessoGrupo();
                    $permissaoAcessoGrupo->verificaPermissaoGrupoPrivado($grupoID, $usuarioID); 
                    // Verifica permissão de acesso 

                    // Recupera dados do grupo

                        $SQL = "SELECT g.id, g.nome, g.descricao, g.grupo_avatar, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes FROM grupo as g

                                LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id

                                WHERE g.id = '$grupoID' AND g.privilegios <> 'security'
                    ";

                    $getGrupoDados = $db = new DB($this->c->pdo);
                    $getGrupoDados = $db->query($SQL);
                    $getGrupoDados = $db->fetchAll();

                     // recupera dados do grupo


                    // Get Vídeo deste box
                    $regex = '/https?\:\/\/[^\" ]+/i';
                    preg_match_all($regex, $dadosBoxes->descricao, $matches);
                    $youtube = $matches[0];

                        foreach ($youtube as $urlVideo) {
                            
                            $url = $urlVideo;


                            $thumnail = $this->get_youtube_thumbnail($url);

                            $parts = parse_url($url);
                            parse_str($parts['query'], $query);
                            $videoID = $query['v'];

                //             $json_output = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&alt=json");
                //             $json = json_decode($json_output, true);

                //             //This gives you the video description
                //             $videoDescricao = $json['entry']['media$group']['media$description']['$t'];

                //             // remove url do vídeo do conteúdo
                //             $conteudoString = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $dadosBoxes->descricao);

                //             $videoTitle = file_get_contents("http://gdata.youtube.com/feeds/api/videos/$videoID?v=2&fields=title");

                //             preg_match("/<title>(.+?)<\/title>/is", $videoTitle, $titleOfVideo);
                //             $videoTitle = $titleOfVideo[1];
                               $videoTitle = 'Indisponível temporáriamente';
                                $videoDescricao = 'Indisponível temporáriamente';

                            if(isset($videoID)){

                                $arrayYoutube[] = array('youtube' => $videoID, 'videoTitulo' => $videoTitle, 'videoDescricao' => $videoDescricao);

                            }
                        }


                }         

            // Recupera mídias

            // Verifica se o usuário segue o grupo
            $verificaUsuarioSegueGrupo = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID, 'ativo' => 0))->fetchAll());

            // Retorna dados para o FrontEnd
            $vars['usuarioID']      = $usuarioID;
            $vars['grupoDados']     = $arrayDadosBox;
            $vars['midias']         = $arrayMidias;
            $vars['youtube']        = $arrayYoutube;
            $vars['dadosGrupo']     = $getGrupoDados;
            $vars['comentarios']    = $getComentarios;
            $vars['numeroComentarios'] = COUNT($getComentarios);
            $vars['boxID']          = $boxID;
            $vars['grupoID']        = $grupoID;
            $vars['dadosUsuario']   = $getDadosUsuarioLogado;
            $vars['VerificaSeguirGrupo'] = $verificaUsuarioSegueGrupo;
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['numeroSolicitacoes'] = $getDadosmenu->GrupoSolicitacoesPendentes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();  
            $vars['numeroGruposUsuario']    = $getDadosmenu->getNumeroGrupos();
            $vars['_view']          = 'grupo_box_view.html.twig';

            return $vars;

        }


    // monta pagina detalhada de um box

    public function FrontEnd404()
    {

       header('Location: /home/');
       exit;

    }


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    
        $pagina         = $arg1;
        $grupoID        = $arg2;
        $grupoPagina    = $arg3;

        if($grupoID == '' AND $arg1 == 'get'){

            return $this->FrontEnd404();

        }else{

            if($arg1 == 'box'){

                $boxID = $arg2;

                return $this->BoxView($boxID);


            }else if(isset($grupoID) AND $grupoID > ''){
            
                return $this->grupoSelecionado($grupoID,$grupoPagina);

            }else{

                return $this->GetGrupos($pagina);

            }
        }

    }


    public function post($acao = null, $arg1 = null)
    {   

        $grupoNome = $_POST['grupoNome'];
        
        return $this->GetGrupos($GetGrupos);

    }

}
