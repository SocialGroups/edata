<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\InjectionGrupoDados;

class Comentarios implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // recupera comentários de um box

        public function getComentarios($boxID,$pagina, $feedID)
        {
            $usuarioID = $_SESSION['usuario_id'];

            // Lógica de paginação
            $limite = 2;

            if($pagina == '' OR $pagina == 0){

                $inicio = 0;
            
            }else{

            $inicio = ($pagina * $limite) - $limite;

            }

           $paginaRetorno = $pagina+1;
            // Lógica de paginação


        // Recupera comentários
        $SQLgetComentarios = "SELECT gi.id, gi.grupo_box_id, gi.conteudo, gi.dataHora, gi.status, gi.usuario_id, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil
                            FROM grupo_interacoes as gi

                            INNER JOIN usuario as user ON user.id = gi.usuario_id

                            WHERE gi.grupo_box_id = '$boxID' AND gi.status = 0

                            ORDER BY gi.id desc

                              LIMIT $inicio,$limite

                             ";

        $getComentarios = $db = new DB($this->c->pdo);
        $getComentarios = $db->query($SQLgetComentarios);
        $getComentarios = $db->fetchAll();

        $contaResults = COUNT($getComentarios);

        if($contaResults > 0){

            foreach ($getComentarios as $dadosComentario) {
                
                echo '<div class="notification-messages success labelComentario'.$dadosComentario->id.'">

                            <div class="user-profile"> 

                            <img src="'.$dadosComentario->foto_perfil.'" alt="" data-src="" data-src-retina="{{comentario.foto_perfil|raw}}" width="35" height="35"> 
                            </div>

                            <div class="message-wrapper">
                                <div class="heading"> 
                                    '.$dadosComentario->nome.' '.$dadosComentario->sobre_nome.'
                                </div>
                                <div class="description">
                                    '.$dadosComentario->conteudo.'
                                </div>
                            </div>

                            <div class="date pull-right">

                             

                            </div>
                            <div class="clearfix">
                            </div>';

                            if($usuarioID == $dadosComentario->usuario_id){

                            echo '

                            <form name="deletaComentario" class="deletaComentario deletaComentario'.$dadosComentario->id.'" id="'.$dadosComentario->id.'" action="" method="POST" style="font-size:12px;">

                                  <input type="hidden" name="comentarioID" value="'.$dadosComentario->id.'">

                                  <input type="submit" class="btn btn-link btn-cons" value="Excluir Comentário">

                            </form>';

                            }

                       echo '</div>';

            }


            echo '<form name="LoadMore" class="loadMoreComentarios loadMoreComentarios'.$feedID.'" id="'.$feedID.'" style="float:left; width:100%;">


                <input type="hidden" name="feedID" id="feedID" value="'.$feedID.'" >
                <input type="hidden" name="boxID" value="'.$boxID.'">

                <input type="hidden" name="pagina" value="'.$paginaRetorno.'">
               <input type="submit" class="btn btn-white btn-sm btn-small" style="width:25%; margin:5px; float:right;" value="Carregar mais comentários">

            </div>';

        }else{

            return false;
        }

    }

    // recupera comentários de um box

    // insert comentário

        public function insertComentario($grupoID,$boxID,$conteudo, $pessoas = null)
        {   

            // recupera ID do usuário que postou o comentário
            $usuarioID = $_SESSION['usuario_id'];

            // verifica se o conteúdo foi preenchido
            if($conteudo > '' AND $boxID > '' AND $grupoID){

                // insere comentário no banco de dados
                $InsertComentario = new \StdClass;

                $InsertComentario->grupo_id       = $grupoID;
                $InsertComentario->grupo_box_id   = $boxID; 
                $InsertComentario->conteudo       = $conteudo; 
                $InsertComentario->usuario_id     = $usuarioID;
                $InsertComentario->dataHora       = date('Y-m-d H:i:s');
                $InsertComentario->status         = 0;

                $this->mapper->grupo_interacoes->persist($InsertComentario);
                $this->mapper->flush();

                $comentarioID = $InsertComentario->id;

                // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'interacao';
                $tipoUpdate = 'insert';
                $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                // Injection Grupo Dados

                // Dispara Notificação para os envolvidos informando que um novo comentári foi postado

                     $SQLgetComentarios = "SELECT id, usuario_id, grupo_box_id, conteudo
                            FROM grupo_interacoes 

                            WHERE grupo_box_id = '$boxID' AND usuario_id <> '$usuarioID'

                            GROUP BY usuario_id

                             ";

                    $getUsuariosNotificar = $db = new DB($this->c->pdo);
                    $getUsuariosNotificar = $db->query($SQLgetComentarios);
                    $getUsuariosNotificar = $db->fetchAll();

                    // Dispara alerta para o autor do box

                    // Verifica se o autor do comentário não é o autor do box
                    $VerificaAutorBox = $this->mapper->grupo_box(array('usuario_id' => $usuarioID, 'id' => $boxID))->fetchAll();

                    $CountVerificaAutorBox = COUNT($VerificaAutorBox);

                    if($CountVerificaAutorBox == 0){

                        $getDadosBoxAutor = $this->mapper->grupo_box(array('id' => $boxID))->fetchAll();

                        foreach ($getDadosBoxAutor as $dadosAutorBox) {
                           
                            // Insere Notificação pendente para o usuário
                            $InsertNotificacaoPendente = new \StdClass;

                            $InsertNotificacaoPendente->autor_id    = $usuarioID;
                            $InsertNotificacaoPendente->usuario_id  = $dadosAutorBox->usuario_id; 
                            $InsertNotificacaoPendente->tipo        = 'comentario'; 
                            $InsertNotificacaoPendente->value       = $boxID; 

                            $this->mapper->notificacoes->persist($InsertNotificacaoPendente);
                            $this->mapper->flush();

                        }

                    }else{


                        foreach ($getUsuariosNotificar as $usuarioNotificar) {
                             
                            // Insere Notificação pendente para o usuário
                            $InsertNotificacaoPendente = new \StdClass;

                            $InsertNotificacaoPendente->autor_id    = $usuarioID;
                            $InsertNotificacaoPendente->usuario_id  = $usuarioNotificar->usuario_id; 
                            $InsertNotificacaoPendente->tipo        = 'comentario'; 
                            $InsertNotificacaoPendente->value       = $boxID; 

                            $this->mapper->notificacoes->persist($InsertNotificacaoPendente);
                            $this->mapper->flush();

                        }

                    }

                // Dispara Notificação para os envolvidos informando que um novo comentári foi postado

            if($pessoas > ''){

                $meta_value   = $comentarioID;
                $tipo = 'interacao';

                // Recupera classe LiveMension
                $getLiveMention = new LiveMention();
                // Recupera classe LiveMension

                // Envia dados de menssão caso haja
                $getLiveMention->cadPersonMenson($tipo, $meta_value, $pessoas);

            }                

                $return = $comentarioID;

            }else{

                $return = false;

            }

            return $return;



        }

    // Insert comentário

    // Deletar comentário

        public function deletarComentario($comentarioID)
        {   

            $usuarioID = $_SESSION['usuario_id'];

            // Verifica se o usuário é o autor deste comentário
            $validacaoSeguranca = COUNT($this->mapper->grupo_interacoes(array('id' => $comentarioID, 'usuario_id' => $usuarioID))->fetchAll());

            if($validacaoSeguranca == 1){

                // Efetua deleção lógica do comentário
                $deletaComentario = $this->mapper->grupo_interacoes[$comentarioID]->fetch();
                $deletaComentario->status = 1;
                $this->mapper->grupo_interacoes->persist($deletaComentario);
                $this->mapper->flush();

                return 'true';

            }else{

                return 'false';
            }

        }

    // deletar comentário


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {
            $boxID     = $arg1;
            $pagina    = $arg2;

            return $this->getComentarios($boxID,$pagina);

    }

    public function post($arg1 = null, $arg2 = null, $arg3 = null)
    {   

        if($arg1 == 'get'){

            $boxID  = $_POST['boxID'];
            $pagina = $_POST['pagina'];
            $feedID = $_POST['feedID'];

            return $this->getComentarios($boxID,$pagina,$feedID);

        }else if($arg1 == 'deletar-comentario'){

            $comentarioID  = $_POST['comentarioID'];

            return $this->deletarComentario($comentarioID);

        }else{

        $grupoID   = $_POST['grupoID']; 
        $boxID     = $_POST['boxID'];
        $conteudo  = $_POST['conteudo'];

        if(isset($_POST['pessoasMencoes'])){

            $pessoas = $_POST['pessoasMencoes'];

        }else{

            $pessoas = '';
        }

            return $this->insertComentario($grupoID,$boxID,$conteudo, $pessoas);

        }

    }

}
