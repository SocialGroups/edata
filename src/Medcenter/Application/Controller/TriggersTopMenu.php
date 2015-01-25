<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\Controller\Notificacoes;

class TriggersTopMenu implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Verifica se o usuário tem grupo privado e se existe solicitações pendentes

        public function notificacoesPendentes()
        {

            // Usuário ID
            $usuarioID = $_SESSION['usuario_id'];

            // Recupera Notificações
            $SQL = "SELECT notifi.id, notifi.autor_id, notifi.usuario_id, notifi.value, notifi.tipo, notifi.status, user.nome, user.sobre_nome, user.nick_name, user.foto_perfil, gi.grupo_box_id, gi.conteudo, gi.dataHora, gb.id as BoxID, gb.descricao, gb.dataHora as BoxDataHora, gbo.id as gboID, g.id as grupoID, g.dataCriacao as grupoDataHora, g.nome as grupoNome 
                    FROM notificacoes as notifi
                    LEFT JOIN usuario as user ON user.id = notifi.autor_id
                    LEFT JOIN grupo_interacoes as gi ON gi.id = notifi.value AND notifi.tipo = 'comentario'
                    LEFT JOIN grupo_box as gb ON gb.id = notifi.value AND notifi.tipo = 'compartilhamento'
                    LEFT JOIN grupo_box_ordenacao as gbo ON gbo.box_id = gb.id
                    LEFT JOIN grupo as g ON g.id = notifi.value AND notifi.tipo = 'convite'

                    WHERE notifi.usuario_id = '$usuarioID' AND notifi.status = 'pendente'

                    LIMIT 5
                    ";

            $getNotificacoes = $db = new DB($this->c->pdo);
            $getNotificacoes = $db->query($SQL);
            $getNotificacoes = $db->fetchAll();

            return $getNotificacoes;

        }

    // Verifica se o usuário tem grupo privado e se existe solicitações pendentes

    // Retorna número de notificações Pendentes
        public function numeroNotificacoesPendentes()
        {

            // Usuário ID
            $usuarioID = $_SESSION['usuario_id'];

            return $numeroNotificacoesPendentes = COUNT($this->mapper->notificacoes(array('usuario_id' => $usuarioID, 'status' => 'pendente'))->fetchAll());

        }
    // Retorna número de notificações Pendentes

    // Retorna dados do usuário logado

        public function dadosUsuarioTopBar()
        {   

            // ID do usuário logado
            $usuarioID = $_SESSION['usuario_id'];

            // Get dados usuário
            $getDadosUsuario = $this->mapper->usuario(array('id' => $usuarioID))->fetchAll();
            // Recupera número de grupos
            $getNumeroGrupos = COUNT($this->mapper->grupo(array('usuario_criacao' => $usuarioID))->fetchAll());

            // Recupera número de interações
            $getNumeroBoxes = COUNT($this->mapper->grupo_box(array('usuario_id' => $usuarioID))->fetchAll());

            // Recupera número de interações
            $getNumeroInteracoes = COUNT($this->mapper->grupo_interacoes(array('usuario_id' => $usuarioID))->fetchAll());

            $vars = array('dadosUsuarioTopBar' => $getDadosUsuario,'NumeroGrupos' => $getNumeroGrupos, 'NumeroBoxes' => $getNumeroBoxes, 'NumeroInteracoes' => $getNumeroInteracoes);

            return $vars;

        }

    // Retorna dados do usuário logado

    // Método responsável por recuperar as menções

    public function getMencoes()
    {

        // Recupera Dados do trigger menu
        $getDadosmenu = new TriggersTopMenu();
        // recupera Dados do trigger menu  

        // Usuário ID
        $usuarioID = $_SESSION['usuario_id'];

        // Recupera Menções não visualizadas por este usuário
        $NumeroMencoesPendentes = COUNT($this->mapper->usuario_mencao(array('mencionado_id' => $usuarioID, 'status' => 'pendente'))->fetchAll());

        return $NumeroMencoesPendentes;

    }

    // Método responsável por recuperar as menções

     // Método responsável por recuperar o número de grupos que o usuário segue

        public function getNumeroGrupos()
        {

            // ID do usuário logado
            $usuarioID = $_SESSION['usuario_id'];

            // Recupera número de grupos
            $numeroGrupos = COUNT($this->mapper->grupo_usuario(array('usuario_id' => $usuarioID))->fetchAll());

            return $numeroGrupos;

        }

    // Método responsável por recuperar o número de grupos que o usuário segue


}