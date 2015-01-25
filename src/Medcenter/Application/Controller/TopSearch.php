<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\TriggersTopMenu;

class TopSearch implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    public function topsearch($q)
    {

        // Trata variavel contra SQL Injection
        $qNoSqlInjection = addslashes($q);

        // Recupera Primeira Letra para verificar se a pesquisa é sobre um usuário
        $Chamada = substr($q,0,1);

        // Se o usuário estiver buscando um usuário
        if($Chamada == '@'){

            $qNoSqlInjection = substr($qNoSqlInjection, 1);

            // Recupera pessoas

            $SQL = "SELECT * FROM usuario as s

                    WHERE s.nome LIKE '$qNoSqlInjection%' OR s.sobre_nome LIKE '$qNoSqlInjection%'

                    ORDER BY s.nome ASC 

                    LIMIT 0,7
                    ";

            $arrayUsuarios = $db = new DB($this->c->pdo);
            $arrayUsuarios = $db->query($SQL);
            $arrayUsuarios = $db->fetchAll();

            // Recupera pessoas

            /* Get the data into a format that Smart Suggest will read (see documentation). */
            $usuarios = array('header' => array(), 'data' => array());
            $usuarios['header'] = array(
                                    'title' => 'Usuarios',                                        # Appears at the top of this category
                                    'num' => COUNT($arrayUsuarios),         # Displayed as the total number of results.
                                    'limit' => 6                                                        # An arbitrary number that you want to limit the results to.
                                   );

            // Foreach com os usuários encontrados com a palavra chave
            foreach ($arrayUsuarios as $dadosUsuario) {
                        
                $usuarios['data'][] = array(
                    'primary' => $dadosUsuario->nome.' '.$dadosUsuario->sobre_nome,
                    'secondary' => '<strong> <br> Perfil: @</strong>'.$dadosUsuario->nick_name,
                    'image' => $dadosUsuario->foto_perfil,                                                                          # Optional URL of 40x40px image
                    'onclick' => 'window.location=\'/profile/'.$dadosUsuario->nick_name.'\';', # JavaScript to call when this result is clicked on
                    'fill_text' => strtolower('amanda')                                                                        # Used for "auto-complete fill style" example
                );

            }
            // Foreach com os usuários encontrados com a palavra chave

                $usuarios['data'][] = array(
                'primary' => 'Mais Resultados',                                                                                         # Title of result row
                'secondary' => 'Não encontrou o que procura? Clique aqui',                                                                         # Optional URL of 40x40px image
                'onclick' => 'window.location=\'/socialsearch?q='."$qNoSqlInjection&tipoBusca=pessoa".'\';', # JavaScript to call when this result is clicked on
                'fill_text' => strtolower('amanda')                                                                        # Used for "auto-complete fill style" example
            );

        }
        // Se o usuário estiver buscando um usuário

        // Recupera grupos

            $SQL = "SELECT * FROM grupo as g

                    WHERE g.nome LIKE '$qNoSqlInjection%' OR g.descricao LIKE '$qNoSqlInjection%'

                    ORDER BY g.nome ASC 

                    LIMIT 0,5
                    ";

            $arrayGrupos = $db = new DB($this->c->pdo);
            $arrayGrupos = $db->query($SQL);
            $arrayGrupos   = $db->fetchAll();

        // Recupera Grupos

        // Recupera Boxes

            $SQLbox = "SELECT gs.id as grupoBoxID, gs.titulo, gs.descricao, g.privilegios, gs.ativo FROM grupo_box as gs

                       LEFT JOIN grupo as g ON gs.grupo_id = g.id

                    WHERE gs.titulo LIKE '$qNoSqlInjection%' AND gs.ativo = 0 OR gs.descricao LIKE '$qNoSqlInjection%' AND gs.ativo = 0

                    ORDER BY gs.titulo ASC 

                    LIMIT 0,5
                    ";

            $arrayBoxes = $db = new DB($this->c->pdo);
            $arrayBoxes = $db->query($SQLbox);
            $arrayBoxes   = $db->fetchAll();

        // Recupera Boxes


    /* Get the data into a format that Smart Suggest will read (see documentation). */
    $grupos = array('header' => array(), 'data' => array());
    $grupos['header'] = array(
        'title' => 'Grupos',                                        # Appears at the top of this category
        'num' => COUNT($arrayGrupos),         # Displayed as the total number of results.
        'limit' => 5                                                        # An arbitrary number that you want to limit the results to.
    );

    /* Get the data into a format that Smart Suggest will read (see documentation). */
    $Boxes = array('header' => array(), 'data' => array());
    $Boxes['header'] = array(
        'title' => 'Boxes',                                        # Appears at the top of this category
        'num' => COUNT($arrayBoxes),         # Displayed as the total number of results.
        'limit' => 5                                                       # An arbitrary number that you want to limit the results to.
    );

    foreach ($arrayGrupos as $dadosGrupo) {

            $limitaDescricao = substr($dadosGrupo->descricao,0,100);
            
            $grupos['data'][] = array(
                'primary' => $dadosGrupo->nome,                                                                                         # Title of result row
                'secondary' => 'Grupo',                                                        # Description below title on result row
                'image' => $dadosGrupo->grupo_avatar,                                                                          # Optional URL of 40x40px image
                'onclick' => 'window.location=\'/grupos/get/'.$dadosGrupo->id.'\';', # JavaScript to call when this result is clicked on
                'fill_text' => strtolower('amanda')                                                                        # Used for "auto-complete fill style" example
            );

     }


         foreach ($arrayBoxes as $dadosBoxes) {

            // Verifica se o box é de um grupo privado
            if($dadosBoxes->privilegios == 'privado'){

                $descricaoBox = 'A descrição não pode ser exibida por se tratar de um grupo privado =(';
            
            }else{

                $descricaoBox = $dadosBoxes->descricao;

            }

            
            $grupos['data'][] = array(
                'primary' => $dadosBoxes->titulo,                                                                                         # Title of result row
                'secondary' => '<strong> Descricão: </strong> <br> '.$descricaoBox,                            
                'onclick' => 'window.location=\'/grupos/box/'.$dadosBoxes->grupoBoxID.'\';', # JavaScript to call when this result is clicked on
                'fill_text' => strtolower('amanda')                                                                        # Used for "auto-complete fill style" example
            );

     }


            $grupos['data'][] = array(
                'primary' => 'Mais Resultados',                                                                                         # Title of result row
                'secondary' => 'Não encontrou o que procura? Clique aqui',                                                                         # Optional URL of 40x40px image
                'onclick' => 'window.location=\'/socialsearch?q='."$qNoSqlInjection&tipoBusca=GrupoBox".'\';', # JavaScript to call when this result is clicked on
                'fill_text' => strtolower('amanda')                                                                        # Used for "auto-complete fill style" example
            );


      if($Chamada == '@'){

        $final = array($usuarios);

      }else{

        $final = array($grupos);
        
     }

        header('Content-type: application/json');
        echo json_encode($final);
        die();

    }



    public function get($q = null)
    {    
        $q = $_GET['q'];
        return $this->topsearch($q);

    }



    public function post($acao = null, $arg1 = null)
    {
    }

}
