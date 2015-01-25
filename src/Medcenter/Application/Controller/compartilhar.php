<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class compartilhar implements Routable
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

    // Método responsável por carregar a view
        public function viewCompartilhamento($boxID){

            $getBoxDados = $this->mapper->grupo_box(array('id' => $boxID))->fetchAll();

            // Recupera mídias dos boxes
            $SQLmidia = "SELECT * FROM grupo_midia WHERE grupo_box_id = '$boxID' ORDER BY RAND() LIMIT 1";

            $arrayMidias = $db = new DB($this->c->pdo);
            $arrayMidias = $db->query($SQLmidia);
            $arrayMidias = $db->fetchAll();

                $arrayYoutube = array();

                foreach ($getBoxDados as $dadosBoxes) {

                    // Get mídias deste box
                    $getBoxMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $dadosBoxes->id))->fetchAll();

                    $descricao = substr($dadosBoxes->descricao, 0, 200);
                    $titulo    = $dadosBoxes->titulo;

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
            $vars['titulo']       = $titulo;
            $vars['descricao']    = $descricao;
            $vars['midias']       = $arrayMidias;
            $vars['boxID']        = $boxID;

            if(isset($videoID)){

                $vars['youtube']      = $videoID;

            }else{

                $vars['youtube']     = '';

            }

            $vars['dadosUsuarioLogado'] = $getDadosUsuario;
            $vars['numeroMidias'] = $numeroMidias;
            $vars['_view']      = 'compartilhamento_view.html.twig';

            return $vars;

        }
    // Método responsável por carregar a view

    // Método responsável pelo compartilhamento de box

        public function compartilharBox($boxID,$grupoID,$conteudo)
        {   

            $usuarioID = $_SESSION['usuario_id'];


            // Validação de segurança
            $ValidaRepublicacaoExiste = COUNT($this->mapper->grupo_box_republicacao(array('box_id' => $boxID, 'grupo_id' => $grupoID))->fetchAll());
            $ValidaUsuarioSegueGrupo = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioID))->fetchAll());

            if($ValidaUsuarioSegueGrupo == 1){

                // Insere box na tabela de republicações

                    $InsertReplicacao = new \stdClass;
                    $InsertReplicacao->box_id       = $boxID;
                    $InsertReplicacao->grupo_id     = $grupoID;
                    $InsertReplicacao->usuario_id   = $_SESSION['usuario_id'];

                    $this->mapper->grupo_box_republicacao->persist($InsertReplicacao);
                    $this->mapper->flush();

                // Insere box na tabela de republicações

                // Adiciona Box na tabela de ordenação
                    $InsertBoxOrdenacao = new \StdClass;

                    $InsertBoxOrdenacao->box_id       = $boxID;
                    $InsertBoxOrdenacao->grupo_id     = $grupoID;
                    $InsertBoxOrdenacao->box_tipo     = 'compartilhamento';
                    $InsertBoxOrdenacao->usuario_id   = $_SESSION['usuario_id'];
                    $this->mapper->grupo_box_ordenacao->persist($InsertBoxOrdenacao);
                    $this->mapper->flush();
                // Adiciona Box na tabela de ordenação

                // Adiciona conteúdo do box compartilhado]
                    $InsertCompartilhamentoConteudo = new \StdClass;

                    $InsertCompartilhamentoConteudo->gbo_id       = $InsertBoxOrdenacao->id;
                    $InsertCompartilhamentoConteudo->conteudo     = $conteudo;
                    $this->mapper->compartilhamento_conteudo->persist($InsertCompartilhamentoConteudo);
                    $this->mapper->flush();
                // Adiciona conteúdo do box compartilhado

                $return = 'true';

            }else{

                $return = 'false';

            }

            return $return;

        }

    // Método responsável pelo compartilhamento de box

    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    
            $boxID     = $arg1;

            return $this->viewCompartilhamento($boxID);

    }


    public function post($arg1 = null, $arg2 = null, $arg3 = null)
    {    
            $boxID      = $_POST['boxID'];
            $grupoID    = $_POST['grupoID'];
            $conteudo   = $_POST['conteudoCompartilhamento'];
            return $this->compartilharBox($boxID,$grupoID,$conteudo);

    }

}
