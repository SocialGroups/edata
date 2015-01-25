<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\PermissaoAcessoGrupo;
use SocialGroups\Application\Controller\TriggersTopMenu;

class Notificacoes implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método responsável por recuperar Notificações

        public function getNotificacoes($pagina)
        {

            // Usuário ID
            $usuarioID = $_SESSION['usuario_id'];

            // Recupera Notificações
            $SQL = "SELECT notifi.id, notifi.autor_id, notifi.usuario_id, notifi.value, notifi.tipo, notifi.status, user.nome, user.sobre_nome, user.nick_name, user.foto_perfil, gi.grupo_box_id, gi.conteudo, gi.dataHora, gb.id as BoxID, gb.descricao, gb.dataHora as BoxDataHora 
                    FROM notificacoes as notifi
                    LEFT JOIN usuario as user ON user.id = notifi.autor_id
                    LEFT JOIN grupo_interacoes as gi ON gi.id = notifi.value AND notifi.tipo = 'comentario'
                    LEFT JOIN grupo_box as gb ON gb.id = notifi.value AND notifi.tipo = 'compartilhamento'

                    WHERE notifi.usuario_id = '$usuarioID' AND notifi.status = 'pendente'

                    LIMIT 5
                    ";

            $getNotificacoes = $db = new DB($this->c->pdo);
            $getNotificacoes = $db->query($SQL);
            $getNotificacoes = $db->fetchAll();

            // Número de Notificações pendentes
            $numeroNotificacoesPendentes = COUNT($getNotificacoes);

            return array('numeroNotificacoes' => $numeroNotificacoesPendentes, 'notificacoes' => $getNotificacoes);

        }

    // Método responsável por recuperar Notificações


    public function get($pagina = null, $arg2 = null, $arg3 = null)
    {    
       
        return $this->getNotificacoes($pagina);

    }


    public function post($acao = null, $arg1 = null)
    {   

        $grupoNome = $_POST['grupoNome'];
        
        return $this->GetGrupos($GetGrupos);

    }

}
