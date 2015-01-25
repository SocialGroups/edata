<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class ProfileInformacoes implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por retornar as informações dos grupos criados por um usuário

        public function getProfileGrupos($nickName, $pagina = null)
        {

            $limite = 10;

            if($pagina == '' OR $pagina == '0'){

                $inicio = 0;
            
            }else{

            $inicio = ($pagina * $limite) - $limite;

            }

            // Recupera informações simples sobre o usuário
            $SQL = " SELECT id, nome, sobre_nome, foto_perfil, background_perfil FROM usuario WHERE nick_name = '$nickName' ";

            $getBasicInfoUser = $db = new DB($this->c->pdo);
            $getBasicInfoUser = $db->query($SQL);
            $getBasicInfoUser = $db->fetch();

            if($getBasicInfoUser->id == ''){

                 header('Location: /home/');
                 exit;
            }

            $SQLgetGrupos = "SELECT g.id, g.nome, g.descricao, g.grupo_avatar, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes FROM grupo as g

            LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id

            WHERE g.privilegios <> 'security' AND g.usuario_criacao = '$getBasicInfoUser->id'

            ORDER BY g.id desc LIMIT $inicio,$limite ";


            $getGrupos = $db = new DB($this->c->pdo);
            $getGrupos = $db->query($SQLgetGrupos);
            $getGrupos = $db->fetchAll(); 
            

             // Recupera número de grupos
            $getNumeroGrupos = COUNT($this->mapper->grupo(array('usuario_criacao' => $getBasicInfoUser->id))->fetchAll());

            // Recupera boxes
            $getNumeroBoxes = COUNT($this->mapper->grupo_box(array('usuario_id' => $getBasicInfoUser->id))->fetchAll());

            // Recupera número de interações
            $getNumeroInteracoes = COUNT($this->mapper->grupo_interacoes(array('usuario_id' => $getBasicInfoUser->id))->fetchAll());

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu 

            if($getBasicInfoUser->id == $_SESSION['usuario_id']){

                $verificaFollow = 1;

            }else{

                // Verifica se o usuário do profile já é seguido pelo visitante
                $verificaFollow = COUNT($this->mapper->seguidores(array('follower_id' => $_SESSION['usuario_id'], 'following_id' => $getBasicInfoUser->id, 'ativo' => 0))->fetchall());
            }

            $vars['arrayGrupos']        = $getGrupos;
            $vars['usuarioLogadoID']    = $_SESSION['usuario_id'];
            $vars['ProfileID']          = $getBasicInfoUser->id;
            $vars['ProfileNome']        = $getBasicInfoUser->nome;
            $vars['ProfileSobreNome']   = $getBasicInfoUser->sobre_nome;
            $vars['ProfileFotoPerfil']  = $getBasicInfoUser->foto_perfil;
            $vars['ProfileFotoCapa']    = $getBasicInfoUser->background_perfil;
            $vars['NumeroGrupo']        = $getNumeroGrupos;
            $vars['NumeroBoxes']        = $getNumeroBoxes;
            $vars['NumeroInteracoes']   = $getNumeroInteracoes;
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();
            $vars['StatusFollow']       = $verificaFollow;
            $vars['nickName']           = $nickName;
            $vars['_view']          = 'profile-informacoes-grupos.html.twig';

            return $vars;

        }

    // Método responsável por retornar as informações dos grupos criados por um usuário


    // Método responsável por retornar as informações dos boxes criados por um usuário

        public function getProfileBoxes($nickName, $pagina = null)
        {

            $limite = 10;

            if($pagina == '' OR $pagina == '0'){

                $inicio = 0;
            
            }else{

            $inicio = ($pagina * $limite) - $limite;

            }  

            // Recupera informações simples sobre o usuário
            $SQLDadosUser = " SELECT id, nome, sobre_nome, foto_perfil, background_perfil FROM usuario WHERE nick_name = '$nickName' ";

            $getBasicInfoUser = $db = new DB($this->c->pdo);
            $getBasicInfoUser = $db->query($SQLDadosUser);
            $getBasicInfoUser = $db->fetch();
            
             $SQL = "SELECT gbo.id, gb.grupo_id, gb.titulo, gb.descricao, gb.usuario_id, gb.dataHora, gbo.box_id as boxID, gbo.box_tipo, user.id as usuarioID, user.nome, user.foto_perfil

                        FROM  `grupo_box_ordenacao` AS gbo

                        INNER JOIN grupo_box AS gb ON gb.id = gbo.box_id

                        LEFT JOIN usuario AS user ON user.id = gb.usuario_id

                        LEFT JOIN grupo as g ON gbo.grupo_id = g.id

                        WHERE user.id = '$getBasicInfoUser->id' AND gb.ativo = 0 AND g.privilegios <> 'privado' AND gbo.ativo = 0
                    
                    ORDER BY gbo.id DESC 

                    LIMIT $inicio,$limite 
                    ";

            $arrayFeed = $db = new DB($this->c->pdo);
            $arrayFeed = $db->query($SQL);
            $arrayFeed = $db->fetchAll();

            // recupera dados do usuário
            $getDadosUsuario = $this->mapper->usuario(array('id' => $getBasicInfoUser->id))->fetchAll();

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu 


             // Recupera número de grupos
            $getNumeroGrupos = COUNT($this->mapper->grupo(array('usuario_criacao' => $getBasicInfoUser->id))->fetchAll());

            // Recupera boxes
            $getNumeroBoxes = COUNT($this->mapper->grupo_box(array('usuario_id' => $getBasicInfoUser->id))->fetchAll());

            // Recupera número de interações
            $getNumeroInteracoes = COUNT($this->mapper->grupo_interacoes(array('usuario_id' => $getBasicInfoUser->id))->fetchAll());

            if($getBasicInfoUser->id == $_SESSION['usuario_id']){

                $verificaFollow = 1;

            }else{

                // Verifica se o usuário do profile já é seguido pelo visitante
                $verificaFollow = COUNT($this->mapper->seguidores(array('follower_id' => $_SESSION['usuario_id'], 'following_id' => $getBasicInfoUser->id, 'ativo' => 0))->fetchall());
            }

            $vars['usuarioLogadoID']    = $_SESSION['usuario_id'];
            $vars['usuarioID']          = $getBasicInfoUser->id;
            $vars['ProfileID']          = $getBasicInfoUser->id;
            $vars['ProfileNome']        = $getBasicInfoUser->nome;
            $vars['ProfileSobreNome']   = $getBasicInfoUser->sobre_nome;
            $vars['ProfileFotoPerfil']  = $getBasicInfoUser->foto_perfil;
            $vars['ProfileFotoCapa']    = $getBasicInfoUser->background_perfil;
            $vars['NumeroGrupo']        = $getNumeroGrupos;
            $vars['NumeroBoxes']        = $getNumeroBoxes;
            $vars['NumeroInteracoes']   = $getNumeroInteracoes;
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();
            $vars['StatusFollow']       = $verificaFollow;
            $vars['nickName']           = $nickName;
            $vars['arrayFeed']         = $arrayFeed;
            $vars['_view']              = 'profile-informacoes-boxes.html.twig';

            return $vars;       

        }

    // Método responsável por retornar as informações dos boxes criadas por um usuário


    // Método responsável por retornar as informações das interações criadas por um usuário

        public function getProfileInteracoes($nickName, $pagina = null)
        {

            $limite = 10;

            if($pagina == '' OR $pagina == '0'){

                $inicio = 0;
            
            }else{

            $inicio = ($pagina * $limite) - $limite;

            }  

            $SQL = "SELECT gi.id, gi.conteudo, gb.id as boxID, gb.titulo FROM grupo_interacoes as gi

                    INNER JOIN grupo_box as gb on gi.grupo_box_id = gb.id

                    INNER JOIN grupo as g on gb.grupo_id = g.id

                    WHERE gi.usuario_id = '$usuarioID' AND g.privilegios <> 'privado'
                    
                    ORDER BY gi.id DESC 

                    LIMIT $inicio,$limite 
                    ";

            $arrayFeed = $db = new DB($this->c->pdo);
            $arrayFeed = $db->query($SQL);
            $arrayFeed = $db->fetchAll();

            // recupera dados do usuário
            $getDadosUsuario = $this->mapper->usuario(array('id' => $usuarioID))->fetchAll();

            $vars['arrayBoxes']     = $arrayFeed;
            $vars['dadosUsuario']   = $getDadosUsuario;
            $vars['_view']          = 'profile-informacoes-informacoes.html.twig';

            return $vars;



        }

    // Método responsável por retornar as informações das interações criadas por um usuário


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {   

        $nickName  = $arg2;

        if($nickName == ''){

           header('Location: /home/');
           exit;

        }else if($arg1 == 'grupos'){

            $nickName  = $arg2;
            $pagina     = $arg3;

            return $this->getProfileGrupos($nickName   ,$pagina);

        }else if($arg1 == 'boxes'){

            $nickName  = $arg2;
            $pagina     = $arg3;

            return $this->getProfileBoxes($nickName,$pagina);

        }else if($arg1 == 'interacoes'){

            $nickName  = $arg2;
            $pagina     = $arg3;

            return $this->getProfileInteracoes($nickName,$pagina);
            

        }

        

    }


    public function post($acao = null, $arg1 = null)
    {   

        $mensagem = $_POST['descricao'];
        
        return $this->relatarBug($mensagem);

    }

}
