<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;

class SocialSearch implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável pela busca de pessoas

        public function SearchPessoa($q, $inicio, $limite)
        {

            // Trata variavel contra SQL Injection
            $qNoSqlInjection = addslashes($q);

            $qNoSqlInjection = substr($qNoSqlInjection, 1);

            $qNoSqlInjection;

            // Recupera usuários
            $SQL = "SELECT s.id, s.nome, s.sobre_nome, s.foto_perfil, s.nick_name FROM usuario as s

                    WHERE s.nome LIKE '%$qNoSqlInjection%' OR s.sobre_nome LIKE '%$qNoSqlInjection%'

                    ORDER BY s.id DESC 

                    LIMIT $inicio, $limite
                    ";

            $result = $db = new DB($this->c->pdo);
            $result = $db->query($SQL);
            $result = $db->fetchAll();
            // Recupera usuários

            // Recupera dados dos usuários

                $arrayDadosPessoas = array();

                foreach ($result as $dadosUsuarios) {
                    
                    $resultUsuarioID = $dadosUsuarios->id;

                     // Recupera número de grupos
                    $getNumeroGrupos = COUNT($this->mapper->grupo(array('usuario_criacao' => $resultUsuarioID))->fetchAll());

                    // Recupera boxes
                    $getNumeroBoxes = COUNT($this->mapper->grupo_box(array('usuario_id' => $resultUsuarioID))->fetchAll());

                    // Recupera número de interações
                    $getNumeroInteracoes = COUNT($this->mapper->grupo_interacoes(array('usuario_id' => $resultUsuarioID))->fetchAll());


                    $arrayDadosPessoas[] = array('pessoaID' => $resultUsuarioID, 'numeroGrupos' => $getNumeroGrupos, 'numeroBoxes' => $getNumeroBoxes, 'numeroInteracoes' => $getNumeroInteracoes);

                }

            // Recupera dados dos usuários

            $searchResult = array('tipo' => 'pessoa', 'result' => $result, 'dadosPessoas' => $arrayDadosPessoas);

            return $searchResult;

        }

    // Método responsável pela busca de pessoas


    // Método responsável pela busca de hashtags

        public function SearchHashtag($q, $inicio, $limite)
        {

            // Trata variavel contra SQL Injection
            $qNoSqlInjection = addslashes($q);

            $qNoSqlInjection = substr($qNoSqlInjection, 1);

            $SQL = "SELECT t.id, t.topic, t.tipo, t.dataHora, t.meta_value, gb.id as grupoBoxID, gb.titulo, gb.descricao, gi.grupo_box_id, gi.conteudo, gi.dataHora, user.id as usuarioID, user.nome, user.sobre_nome, user.foto_perfil FROM topics as t

                    LEFT JOIN grupo_box as gb ON t.tipo = 'box' AND t.meta_value = gb.id

                    LEFT JOIN grupo_interacoes as gi ON t.tipo = 'interacao' AND t.meta_value = gi.id 

                    LEFT JOIN usuario as user ON user.id = gb.usuario_id OR user.id = gi.usuario_id

                    WHERE t.topic = '#$qNoSqlInjection'

                    ORDER BY t.id DESC 

                    LIMIT $inicio, $limite
                    ";

            $result = $db = new DB($this->c->pdo);
            $result = $db->query($SQL);
            $result = $db->fetchAll();

            $searchResult = array('tipo' => 'hashtag', 'result' => $result);

            return $searchResult;

        }

    // Método responsável pela busca de hashtags


    // Método responsável pela busca de grupos & boxes

        public function SearchGrupoBox($q, $inicio, $limite)
        {

             // Trata variavel contra SQL Injection
            $qNoSqlInjection = addslashes($q);

            $qNoSqlInjection = substr($qNoSqlInjection, 1);

            // Recupera grupos
            $SQL = "SELECT g.id, g.nome, g.descricao, g.grupo_avatar, gi.id as grupoInformacoesID, gi.numero_seguidores, gi.numero_boxes, gi.numero_interacoes FROM grupo as g

                LEFT JOIN grupo_informacoes as gi ON g.id = gi.grupo_id

                WHERE g.nome LIKE '%$qNoSqlInjection%' OR g.descricao LIKE '%$qNoSqlInjection%'

                ORDER BY g.id DESC 

                LIMIT $inicio, $limite
            ";

            $arrayGrupos = $db = new DB($this->c->pdo);
            $arrayGrupos = $db->query($SQL);
            $arrayGrupos   = $db->fetchAll();

            // Recupera Grupos

            // Recupera Boxes

            $SQLbox = "SELECT gb.id as grupoBoxID, gb.titulo, gb.descricao, g.privilegios, user.id as usuarioID, user.nick_name, user.nome, user.sobre_nome, user.foto_perfil, g.grupo_avatar, g.id as grupoID FROM grupo_box as gb

                       LEFT JOIN grupo as g ON gb.grupo_id = g.id

                       INNER JOIN usuario as user on gb.usuario_id = user.id

                    WHERE gb.titulo LIKE '%$qNoSqlInjection%' AND gb.ativo = 0 OR gb.descricao LIKE '%$qNoSqlInjection%' AND gb.ativo = 0

                    ORDER BY gb.id DESC 

                    LIMIT $inicio, 15
                    ";

            $arrayBoxes = $db = new DB($this->c->pdo);
            $arrayBoxes = $db->query($SQLbox);
            $arrayBoxes   = $db->fetchAll();

            // Recupera Boxes


            // Agrupa Grupo e box em uma só variavel

            $searchResult = array('tipo' => 'GruposBoxes', 'grupos' => $arrayGrupos, 'boxes' => $arrayBoxes);

            // Agrupa grupo e box em uma só variavel

                

            return $searchResult;



        }

    // Método responsável pela busca de grupos & boxes


    // Método responsável pelo Social Search recurso de busca avançada da aplicação

        public function Search($q, $pagina = null, $tipoBusca = null)
        {

            // Calcula pagina de retorno
            $paginaRetorno = $pagina+1;
            // Calcula pagina de retorno

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu

            // Trata variavel contra SQL Injection
            $qNoSqlInjection = addslashes($q);

            // Recupera Primeira Letra para verificar se a pesquisa é sobre um usuário
           $Chamada = substr($q,0,1);

            $limite = 10;
            if($pagina == ''){
                $inicio = 0;
            }else{
                $inicio = ($pagina * $limite) - $limite;
            }

            if($tipoBusca == 'usuario' OR $Chamada == '@'){

                $result = $this->SearchPessoa($q, $inicio, $limite);

            }else if($tipoBusca == 'hashtag' OR $Chamada == '#'){

                $result = $this->SearchHashtag($q, $inicio, $limite);

            }else{

                $limite = 5;

                $result = $this->SearchGrupoBox($q, $inicio, $limite);
            }

            //var_dump($result);

            // Retorna resultado da pesquisa
            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['numeroGruposUsuario']    = $getDadosmenu->getNumeroGrupos();
            $vars['numeroNotificacoesPendentes'] = $getDadosmenu->numeroNotificacoesPendentes();
            $vars['notificacoesPendentes'] = $getDadosmenu->notificacoesPendentes();
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();
            $vars['searchResult'] = $result;
            $vars['q']          = $q;
            $vars['tipoBusca'] = $tipoBusca;
            $vars['pagina']     = $paginaRetorno;
            $vars['_view']      = 'results-social-search.html.twig';
            return $vars;

        }

    // Método responsável pelo Social Search recurso de busca avançada da aplicação

    // Método responsável por retornar o FrontEnd de search

        public function getSearchFront()
        {   

            // Recupera Dados do trigger menu
            $getDadosmenu = new TriggersTopMenu();
            // recupera Dados do trigger menu

            $vars['numeroMencoesPendentes'] = $getDadosmenu->getMencoes();
            $vars['numeroSolicitacoes'] = $getDadosmenu->GrupoSolicitacoesPendentes(); 
            $vars['dadosTopBar']        = $getDadosmenu->dadosUsuarioTopBar();
            $vars['_view']  = 'social-search-frontend.html.twig';
            return $vars;

        }

    // Método responsável por retornar o FrontEnd de search

    public function get($acao = null, $pagina = null)
    { 

        

        if(isset($_GET['p']) AND $_GET['p'] > 0){
            
            $pagina = $_GET['p'];
            $tipoBusca = $_GET['tipoBusca'];

        }else{

            $pagina = 1;
        }

        if(isset($_GET['q']) AND strlen($_GET['q']) >= 2 AND $_GET['q'] <> '' AND $_GET['q'] <> '  '){

            $q  = $_GET['q'];
            $tipoBusca = $_GET['tipoBusca'];
            return $this->Search($q, $pagina, $tipoBusca);
            
        }else{

             return $this->getSearchFront();

        }

    }

}
