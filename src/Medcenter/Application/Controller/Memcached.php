<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class Memcached implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Criamos a função que fará os cálculos atravéz do comando microtime() do PHP
function execucao(){ 
    $sec = explode(" ",microtime());
    $tempo = $sec[1] + $sec[0];
    return $tempo; 
}

    // Testando o memcached

        public function testar()
        {

                    $InsertGrupoBox = new \StdClass;

                    $InsertGrupoBox->grupo_id       = '51';
                    $InsertGrupoBox->titulo         = 'dasd'; 
                    $InsertGrupoBox->descricao      = 'cc';
                    $InsertGrupoBox->usuario_id     = $_SESSION['usuario_id'];
                    $this->mapper->grupo_box->persist($InsertGrupoBox);
                    $this->mapper->flush();

            $inicio = $this->execucao();

            $memcache = new \Memcache;
            $memcache->connect('192.168.56.101', 11211);
            echo 'Memcached ver: ' . $memcache->getVersion();

            $grupoID = 51;

            // Recupera todos os rows de uma coluna
                 $SQL = "SELECT 
        COUNT(DISTINCT gbo.id) AS numeroBoxes,
        COUNT(DISTINCT gi.id) AS numeroInteracoes,
        COUNT(DISTINCT gu.id) AS numeroSeguidores,
        g.id,g.nome,g.descricao,g.privilegios,g.grupo_tipo,g.dataCriacao,g.usuario_criacao,g.grupo_avatar,g.grupo_status

        FROM grupo as g

        LEFT JOIN grupo_box_ordenacao as gbo ON gbo.grupo_id = '$grupoID'

        LEFT JOIN grupo_box as gb ON gb.grupo_id = '$grupoID'

        LEFT JOIN grupo_interacoes as gi ON gb.grupo_id = '$grupoID'

        LEFT JOIN grupo_usuario as gu ON gu.grupo_id = '$grupoID'

         WHERE g.id = '$grupoID'
      ";

$chave = md5($SQL);


// Buscamos o resultado na memória
$cache = $memcache->get($chave);

// Verifica se o resultado não existe ou expirou
if ($cache === false) {
    // Executa a consulta novamente
    $arrayFeed = $db = new DB($this->c->pdo);
    $arrayFeed = $db->query($SQL);
    $arrayFeed   = $db->fetchAll();

    $tempo = 60 * 60; // 3600s
    $memcache->set($chave, $arrayFeed, 0, $tempo);

}else {
    // A consulta está salva na memória ainda, então pegamos o resultado:
    $arrayFeed = $cache;
}

           

            //- Variavel Inicio

            $fim = $this->execucao();

            $tempo = number_format(($fim-$inicio),6);

// Agora á só imprimir o resultado
print "<br>Tempo de Execução: <b>".$tempo."</b> segundos";


        

        }

    // testando o memcached


    public function get($arg1 = null, $arg2 = null, $arg3 = null)
    {       
            
        return $this->testar();

    }


    public function post($acao = null, $arg1 = null)
    {   

        $grupoID    = $_POST['grupoID'];
        
        return $this->SeguirGrupo($grupoID);

    }

}
