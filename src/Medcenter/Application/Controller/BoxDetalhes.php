<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class BoxDetalhes implements Routable
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


    // Recupera dados do box
    public function BoxDetalhes($boxID)
    {

        $getBoxDados = $this->mapper->grupo_box(array('id' => $boxID))->fetchAll();

        // Recupera comentários deste box
        $SQLgetComentarios = "SELECT * FROM grupo_interacoes as gi

                              INNER JOIN usuario as us ON gi.usuario_id = us.id

                              WHERE gi.grupo_box_id = '$boxID'
                    ";

        $getComentarios = $db = new DB($this->c->pdo);
        $getComentarios = $db->query($SQLgetComentarios);
        $getComentarios = $db->fetchAll();

            // Recupera mídias dos boxes

                $arrayMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $boxID))->fetchAll();

                $arrayYoutube = array();

                foreach ($getBoxDados as $dadosBoxes) {

                    // Get mídias deste box
                    $getBoxMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $dadosBoxes->id))->fetchAll();

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
                            $boxIDVideo = $dadosBoxes->id;

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

                                $arrayYoutube[] = array('boxID' => $boxIDVideo, 'youtube' => $videoID, 'videoTitulo' => $videoTitle, 'videoDescricao' => $videoDescricao);

                            }
                        }
                }

            // Recupera mídias dos boxes 

             // get dados usuário
            $getDadosUsuario = $this->mapper->usuario(array('id' => $_SESSION['usuario_id']))->fetchall();

            $numeroMidias = COUNT($arrayMidias);

            // Retorna os arrays para o FrontEnd
            $vars['boxDados']     = $getBoxDados;
            $vars['midias']       = $arrayMidias;

            if(isset($videoID)){

                $vars['youtube']      = $videoID;

            }else{

                $vars['youtube']     = '';

            }

            $vars['dadosUsuarioLogado'] = $getDadosUsuario;
            $vars['numeroMidias'] = $numeroMidias;
            $vars['comentarios'] = $getComentarios;
            $vars['_view']        = 'box_view.html.twig';

            return $vars;    

    }
    // Recupera dados do box


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    
        $boxID     = $arg1;

            return $this->BoxDetalhes($boxID);

    }

}
