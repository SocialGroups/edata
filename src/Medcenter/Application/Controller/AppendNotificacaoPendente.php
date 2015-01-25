<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class AppendNotificacaoPendente implements Routable
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


    // Método responsável por recuperar Notificações

        public function getNotificacao($boxID, $notificacaoID)
        {   

            // Usuário ID
            $usuarioID = $_SESSION['usuario_id'];

            //Atualiza Status para visualizada
            $atualizaStatus = $this->mapper->notificacoes(array('usuario_id' => $usuarioID, 'id' => $notificacaoID))->fetch();
            $atualizaStatus->status = 'visualizado';
            $this->mapper->notificacoes->persist($atualizaStatus);
            $this->mapper->flush();

            // Recupera Notificações
             $SQL = "SELECT gbo.id, gbo.box_id as boxID, gbo.grupo_id, gbo.box_tipo, gbo.usuario_id as usuarioID, gb.titulo, gb.descricao, gb.dataHora, gb.ativo, us.nome, us.sobre_nome, us.foto_perfil, us.nick_name 

                FROM grupo_box_ordenacao AS gbo 

                INNER JOIN usuario as us ON gbo.usuario_id = us.id

                LEFT JOIN grupo_box AS gb ON gb.id = gbo.box_id

                WHERE gb.id = '$boxID'

                group by gb.id
                ";

            $arrayFeed = $db = new DB($this->c->pdo);
            $arrayFeed = $db->query($SQL);
            $arrayFeed   = $db->fetchAll();



            foreach ($arrayFeed as $dadosFeed) {

                $newBoxID = $dadosFeed->boxID;

                echo '<div class="ol-md-6 col-vlg-4 col-sm-12" style="margin-top:20px;" id="boxID'.$dadosFeed->id.'">
<div class="widget-item narrow-margin">
<div class="tiles white ">
<div class="tiles-body">
<div class="row">
<div class="user-comment-wrapper pull-left">
<a href="/profile/{{feed.nick_name|raw}}">
<div class="profile-wrapper"> <img src="'.$dadosFeed->foto_perfil.'" alt="" data-src="'.$dadosFeed->foto_perfil.'" data-src-retina="'.$dadosFeed->foto_perfil.'" width="35" height="35"> </div>
</a>
<div class="comment">
<div class="preview-wrapper" style="font-size:22px; margin-top:5px;"> Notificação Pendente :</h5> </div>
</div>
<div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="p-l-15 p-t-10 p-r-20">
<p>'.$dadosFeed->descricao.'</p>
';

    // Get mídias deste box
    $getBoxMidias = $this->mapper->grupo_midia(array('grupo_box_id' => $dadosFeed->boxID))->fetchAll();

    echo '  <!-- Mídia !--> <div class="photoset-grid-custom" >';

    foreach ($getBoxMidias as $midias) {
            
            echo '<img src="'.$midias->midia_url.'" style="max-width:600px !important;">';
    } 

    echo '</div> <!-- Mídia !-->';        


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


                    echo '   
                    <!-- Vídeo !-->

                            <div class="videos">
                              
                              <div class="youtube-video"> <a href="http://www.youtube.com/watch?v='.$videoID.'"> <img src="http://img.youtube.com/vi/'.$videoID.'/1.jpg" title="Play" alt="Play"/> </a> </div>
                              <div class="details">
                                <h6>'.$videoTitle.'</h6>
                                <p class="desc">'.$videoDescricao.'</p>
                              </div>
                            </div>
                    <!-- Vídeo !--> ';

                }
            }

 if($usuarioID == $dadosFeed->usuarioID){

    echo '<!-- Excluir Box !-->

          <form name="deletar" method="POST" action="" class="deletar" id="'.$dadosFeed->id.'" style="float:right; margin:15px;">
                
                    <input type="hidden" name="tipoExclusao" value="box" class="tipoExclusao'.$dadosFeed->id.'">

                    <input type="submit" class="btn btn-danger" value="Excluir">

                </form> 

        <!-- Excluir Box !-->';

 }else{

    echo '<!-- Denunciar Box !-->

            <a data-toggle="modal" class="btn btn-danger" href="/denuncia/'.$dadosFeed->boxID.'" data-target="#denunciar'.$dadosFeed->boxID.'" style="float:right; margin:15px; margin-right:0px;">Denunciar</a>
 
      <!-- Modal Denunciar Box -->
      <div class="modal fade" id="denunciar'.$dadosFeed->boxID.'" tabindex="-1" role="dialog" aria-labelledby="denunciar" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
              </div> <!-- /.modal-content -->
          </div> <!-- /.modal-dialog -->
      </div> <!-- /.modal -->
      <!-- Modal Denunciar Box !-->


        <!-- Denunciar Box !-->';
 }
  
  echo '

    <!-- Compartilhar Box !-->

      <a data-toggle="modal" class="btn btn-info" href="/compartilhar/'.$dadosFeed->boxID.'" data-target="#myModal'.$dadosFeed->boxID.'" style="float:right; margin:15px; margin-right:0px;">Compartilhar</a>
 
      <!-- Modal Compartilhar -->
      <div class="modal fade" id="myModal'.$dadosFeed->boxID.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
              </div> <!-- /.modal-content -->
          </div> <!-- /.modal-dialog -->
      </div> <!-- /.modal -->
      <!-- Modal Compartilhar !-->

    <!-- Compartilhar Box !-->

  <!-- Mídia !-->     
<div class="post p-t-10 p-b-10">

<div class="clearfix"></div>
</div>
</div>
</div>
</div>

<!-- Comentários !-->
<div class="appendcomentario'.$dadosFeed->id.'" style="width:100%; height:auto; margin-bottom:15px;">';

// Get 3 comentários de cada box
$SQL = "SELECT gi.id, gi.grupo_box_id, gi.conteudo, gi.dataHora, gi.status, gi.usuario_id, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil
        FROM grupo_interacoes as gi

        INNER JOIN usuario as user ON user.id = gi.usuario_id

        WHERE gi.grupo_box_id = '$newBoxID' AND gi.status = 0

        ORDER BY gi.id desc

        LIMIT 2
";

$getComentarios = $db = new DB($this->c->pdo);
$getComentarios = $db->query($SQL);
$getComentarios = $db->fetchAll();

    foreach ($getComentarios as $comentario) {
            
        echo '<div class="notification-messages success labelComentario'.$comentario->id.'" id="'.$dadosFeed->id.'">

              <div class="user-profile"> 

              <img src="'.$comentario->foto_perfil.'" alt="" data-src="" data-src-retina="'.$comentario->foto_perfil.'" width="35" height="35"> 
              </div>

              <div class="message-wrapper">
                <div class="heading"> 
                  '.$comentario->nome.'
                </div>
                <div class="description">
                  '.$comentario->conteudo.'
                </div>
              </div>

              <div class="date pull-right">

               '.$comentario->dataHora.'

              </div>
              <div class="clearfix">
              </div>';

              if($usuarioID == $comentario->usuarioID){

                echo '<form name="deletaComentario" class="deletaComentario deletaComentario'.$comentario->id.'" id="'.$comentario->id.'" action="" method="POST" style="font-size:12px;">

                  <input type="hidden" name="comentarioID" value="'.$comentario->id.'">

                  <input type="submit" class="btn btn-link btn-cons" value="Excluir Comentário">

              </form>';

              }

              echo '</div>';

    }


echo '
<div class="clearfix"></div>
<div class="p-b-10 p-l-10 p-r-10">

      <form name="LoadMore" class="loadMoreComentarios loadMoreComentarios'.$dadosFeed->id.'" id="'.$dadosFeed->id.'" style="float:left; width:100%;">


        <input type="hidden" name="feedID" id="feedID" value="'.$dadosFeed->id.'" >
        <input type="hidden" name="boxID" value="'.$dadosFeed->boxID.'">

        <input type="hidden" name="pagina" value="2">
        <input type="submit" class="btn btn-white btn-sm btn-small" style="width:25%; margin:5px; float:right;" value="Carregar mais comentários">

      </form>';

// Get dados usuário
$getDadosUsuario = $this->mapper->usuario(array('id' => $usuarioID))->fetchAll();
    

    foreach ($getDadosUsuario as $dadosUsuario) {
        
        echo '


        <form name="adicionaComentario" class="adicionaComentatio" id="'.$dadosFeed->id.'" method="POST" action="">
 <input type="hidden" name="grupoID" value="'.$dadosFeed->grupo_id.'">
                  <input type="hidden" name="boxID" value="'.$dadosFeed->boxID.'">

                  <input type="hidden" name="fotoAutor" value="'.$dadosUsuario->foto_perfil.'" id="fotoAutor'.$dadosFeed->id.'">
                   <input type="hidden" name="nomeAutor" value="'.$dadosUsuario->nome.'" id="nomeAutor'.$dadosFeed->id.'">

                
                <div class="profile-img-wrapper pull-left"> <img src="'.$dadosUsuario->foto_perfil.'" alt="" data-src="'.$dadosUsuario->foto_perfil.'" data-src-retina="'.$dadosUsuario->foto_perfil.'" width="35" height="35"> </div>
                <div class="inline pull-right" style="width:94%">
                  <div class="input-group transparent ">
                    <input type="text" class="form-control" name="conteudo" id="conteudoComentarioBox'.$dadosFeed->id.'" placeholder="Adicionar um comentário a este box">
                    <span class="input-group-addon">  </span> </div>
                </div>
                <div class="clearfix"></div>

              </form>
              
      </div>
<div class="clearfix"></div>
</div>
</div>
</div>';

    }
    


            }

        }

    // Método responsável por recuperar Notificações


    public function get($boxID = null, $arg2 = null, $arg3 = null)
    {    
       
        return $this->getNotificacao($boxID);

    }


    public function post($boxID = null, $notificacaoID = null)
    {   
        
        return $this->getNotificacao($boxID, $notificacaoID);

    }

}
