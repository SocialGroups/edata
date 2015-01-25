<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\s3class;


class TimeLine implements Routable 
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    public function home($pagina)
    {        

            if($pagina == '2')
            {
                $pagina = 1;
            }

            $limite = 15;

            $inicio = ($pagina * $limite) - $limite;

            // ID do usuário logado
            $usuarioID = $_SESSION['usuario_id'];

            $sqlArrayGrupos = " SELECT *

                                FROM grupo_usuario

                                lEFT  JOIN seguidores ON seguidores.follower_id = '$usuarioID'

                                LEFT JOIN timeline_interacoes  

                                ON grupo_usuario.grupo_id=timeline_interacoes.modo_interacao_id 
                                OR timeline_interacoes.usuario_id = seguidores.following_id

                                WHERE grupo_usuario.usuario_id = '$usuarioID' OR seguidores.following_id = timeline_interacoes.usuario_id

                                GROUP BY timeline_interacoes.id

                                ORDER BY timeline_interacoes.id desc

                                LIMIT $inicio,$limite 
                              ";

            $arrayGrupos = $db = new DB($this->c->pdo);
            $arrayGrupos = $db->query($sqlArrayGrupos);
            $arrayGrupos = $db->fetchAll();

            echo '<div id="container" class="variable-sizes clearfix infinite-scrolling" style="width:100%; height:700px; margin:0px auto !important;">';

            foreach ($arrayGrupos as $dados) {

                // Inicio - Recupera mídias da interação

                    if($dados->interacao_tipo == 'publicou foto' OR $dados->interacao_tipo == 'publicou video'){

                        // Recupera mídias publicadas
                        $arrayMidiasPulibicadas = $this->mapper->grupo_midia(array('grupo_box_id' => $dados->modo_interacao_id))->fetchAll();

                    }


                // Final  - Recupera méidias da interação

                $arrayStyle = array('element alkali metal', 'variable-sizes', 'variable-sizes', 'variable-sizes');
                $styleRandom = array_rand($arrayStyle);
                
                echo '<div class="element '.$arrayStyle[$styleRandom].'   width8 height9  " data-symbol="H" data-category="other" style="height:auto !important; width:auto !important; max-width:400px !important;">';

                    // Inicio - Verifica se existe midias, se sim cria box com as mídias

                        if(isset($arrayMidiasPulibicadas)){

                            foreach ($arrayMidiasPulibicadas as $midiasUrl) {
                                
                                echo "<img src='$midiasUrl->midia_url' width='150'>";

                            }

                        }   

                    // Final  - Verifica se existe midias, se sim cria box com as mídias

                echo $dados->conteudo;




                echo '</div>';


            }

            echo '</div>';

            // Retorna todos os grupos do usuário cadastrado
            //$arrayGrupos = $this->mapper->grupo_usuario(array('usuario_id' => $_SESSION['usuario_id']))->fetchAll();

            // Armazena id dos usuários em um array
            $arrayUsuariosInteracoes = array();

            // retorna grupo de usuário que pertecense ao grupo que o usuário faz parte
            
    }


    public function get($acao = null, $arg1 = null)
    {   

         if($acao > "1" AND $acao <> 'timeline'){

            // Início - Lógica de paginação

                $pagina = $acao;

            // Final  - Lógica de paginação

            return $this->home($pagina);

        }if($acao == "timeline"){

             // Inicio - Recupera valores a serem passados para a timeline

                $retornaDadosUsuario = $this->mapper->usuario(array('id' => $_SESSION['usuario_id']))->fetchAll();

                foreach ($retornaDadosUsuario as $dadosUsuario) {
                    
                    $vars['usuarioNome']            = $dadosUsuario->nome;
                    $vars['usuarioFotoPerfil']      = $dadosUsuario->foto_perfil;
                    $vars['usuarioBackgroudImagem'] = $dadosUsuario->background_perfil;
                    $vars['usuarioSobreNome']       = $dadosUsuario->sobre_nome;

                }


             // Final  - Recupera valores a serem passados para a timeline


             $vars['_view'] = 'TimeLine.html.twig';
             return $vars;

        }



    }


    public function post($acao = null, $arg1 = null)
    {   

    }
}
