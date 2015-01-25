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
            $memcache->connect('192.168.56.101', 11211);
            // Instância Memcache

            $limite = 10;

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
                $SQLgetGrupos = "SELECT g.id, g.nome, g.descricao, g.grupo_avatar, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes FROM grupo as g

                                LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id

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
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['numeroSolicitacoes'] = $getDadosmenu->GrupoSolicitacoesPendentes(); 
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar(); 
            $vars['_view']       = 'index.html.twig';

            return $vars;
        }

    // Get Grupos


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {    
            $pagina         = $arg1;

            return $this->GetGrupos($pagina);
    }

}
