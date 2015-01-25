<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class Assuntos implements Routable
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


    // Recupera boxes que foram marcados com hashtags

        public function getHashtags($hashtag, $pagina = null)
        {
            $limite = 10;

            if($pagina == '' OR $pagina == '0'){

                $inicio = 0;
            
            }else{

            $inicio = ($pagina * $limite) - $limite;

            }

            // Get boxes ou interações
            $SQL = "SELECT t.tipo,t.topic, t.meta_value, t.dataHora, gb.id,gb.grupo_id,gb.titulo,gb.descricao,gb.usuario_id,gi.conteudo,gi.usuario_id, us.nome, us.sobre_nome, us.foto_perfil  FROM topics AS t

                    LEFT JOIN grupo_box AS gb ON t.tipo='box' AND gb.id=t.meta_value

                    LEFT JOIN grupo_interacoes AS gi ON t.tipo='interacao' AND  gi.id=t.meta_value

                    INNER JOIN usuario AS us ON us.id = gb.usuario_id OR us.id = gi.usuario_id

                    LEFT JOIN grupo_midia AS gm ON gm.grupo_box_id = gb.id

                    WHERE t.topic = '#$hashtag' AND gb.descricao > '' OR t.topic = '#$hashtag' AND gi.conteudo > ''

                    GROUP BY t.id

                    ORDER BY gb.id desc

                    LIMIT $inicio,$limite 

                    ";

            $arrayGetHashtags = $db = new DB($this->c->pdo);
            $arrayGetHashtags = $db->query($SQL);
            $arrayGetHashtags = $db->fetchAll();
            // Get boxes ou interações


            // Recupera trending topics Interations boxes
            $dataInicioBuscar = date('Y-m-d 00:00:00');
            $dataFinalBuscar  = date('Y-m-d 23:59:59');
            $SQLTopics = "SELECT count(topic) repeticoes,topic from topics WHERE dataHora BETWEEN '$dataInicioBuscar' AND  '$dataFinalBuscar' group by topic having repeticoes >1 ORDER BY repeticoes desc limit 15";
            $arrayTrendingTopics = $db = new DB($this->c->pdo);
            $arrayTrendingTopics = $db->query($SQLTopics);
            $arrayTrendingTopics = $db->fetchAll();


            // Recupera mídias dos feed`s

                $arrayMidias = array();

                $arrayYoutube = array();

                foreach ($arrayGetHashtags as $dadosFeed) {

                    // Get mídias deste box
                    $getBoxMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $dadosFeed->id))->fetchAll();

                    // Get Vídeo deste box
                    $regex = '/https?\:\/\/[^\" ]+/i';
                    preg_match_all($regex, $dadosFeed->descricao, $matches);
                    $youtube = $matches[0];

                        foreach ($youtube as $urlVideo) {
                            
                            $url = $urlVideo;

                            //$thumnail = $this->get_youtube_thumbnail($url);

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

            if(COUNT($arrayGetHashtags) == 0){

                $vars['resultNull']   = 1;

            }

            // Envia usuário para Pagina de Autenticação de chave privada
            $vars['arrayGetHashtags']   = $arrayGetHashtags;
            $vars['midias']             = $arrayMidias;
            $vars['youtube']            = $arrayYoutube;
            $vars['hashtag']            = $hashtag;
            $vars['TrendingTopics']     = $arrayTrendingTopics;
            $vars['_view']              = 'GetHashtags.html.twig';

            return $vars;

        }

    // Recupera boxes que foram marcados com hashtags


    public function get($arg1 = null, $arg2 = null)
    {    
        $hashtag    = $arg1;
        $pagina     = $arg2;
        
        return $this->getHashtags($hashtag, $pagina);

    }


    public function post($acao = null, $arg1 = null)
    {   

        $hashtag    = $_POST['hashtag'];
        $pagina     = 1;
        
        return $this->getHashtags($hashtag, $pagina);

    }

}
