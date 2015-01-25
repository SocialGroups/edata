<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\s3class;
use SocialGroups\Application\Controller\geradorthumb;


// Classe responsável pelas solicitações

class solicitacoes implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método responsável pelos registros das ações na TimeLine
    public function AcionaTimeLine($interacaoTipo, $modoInteracao, $modoInteracaoID){

            // Registra Ação na TimeLine
            $registraAcaoTimeLine = new \stdClass;
            $registraAcaoTimeLine->interacao_tipo       = $interacaoTipo;
            $registraAcaoTimeLine->modo_interacao       = $modoInteracao;
            $registraAcaoTimeLine->modo_interacao_id    = $modoInteracaoID;
            $registraAcaoTimeLine->usuario_id           = $_SESSION['usuario_id'];

            $this->mapper->timeline_interacoes->persist($registraAcaoTimeLine);
            $this->mapper->flush();

    }
    // Método responsável pelos registros das ações na TimeLine


    // Método responsável por adicionar um following

        public function seguir($follower_id, $following_id)
        {   

            // Verifica se o usuário já segue
            $validaAcao = COUNT($this->mapper->seguidores(array('follower_id' => $follower_id, 'following_id' => $following_id))->fetchAll());

            if($validaAcao == '0'){

                // Registra no banco de dados que um usuário esta seguindo outro usuário
                $registraFollow = new \stdClass;
                $registraFollow->follower_id    = $follower_id;
                $registraFollow->following_id   = $following_id;

                $this->mapper->seguidores->persist($registraFollow);
                $this->mapper->flush();

                $RetornoID = $registraFollow->id;

                $return = true;

                // Aciona TimeLine

                // Parametros
                $interacaoTipo      = 'comecou a seguir';
                $modoInteracao      = 'seguidores';
                $modoInteracaoID    = $RetornoID;

                $this->AcionaTimeLine($interacaoTipo, $modoInteracao, $modoInteracaoID);

            }else{

                $return = false;

            }

                return $return;

        }

    // Método responsável por adicionar um following

    // Método responsável por seguir um grupo público

        public function grupo($grupoID)
        {   

            // Verifica se o usuário já segue
            $validaAcao = COUNT($this->mapper->grupo_usuario(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id']))->fetchAll());

            if($validaAcao == '0'){

                // Registra no banco de dados que o usuário esta seguindo o grupo
                $registraFollowGrupo = new \stdClass;
                $registraFollowGrupo->grupo_id      = $grupoID;
                $registraFollowGrupo->usuario_id    = $_SESSION['usuario_id'];
                $registraFollowGrupo->nivel         = 'usuario';
                $registraFollowGrupo->status        = 'Ativo';

                $this->mapper->grupo_usuario->persist($registraFollowGrupo);
                $this->mapper->flush();

                $return = true;

                // Aciona TimeLine

                // Parametros
                $interacaoTipo      = 'comecou a seguir';
                $modoInteracao      = 'grupo';
                $modoInteracaoID    = $grupoID;

                $this->AcionaTimeLine($interacaoTipo, $modoInteracao, $modoInteracaoID);


            }else{

                $return = false;

            }

            return $return;

        }

    // Método responsável por seguir um grupo público


    // Método responsável por solicitar a um usuário que ele siga um grupo privado

        public function solicitarGrupoPrivado($grupoID, $usuarioSolicitadoID)
        {   

            // Verifica se o usuário já segue
            $validaAcao = COUNT($this->mapper->solicitacao(array('grupo_id' => $grupoID, 'usuario_id' => $usuarioSolicitadoID))->fetchAll());

            if($validaAcao == '0'){

                // Envia Convite para o usuário
                $registraEnvioConvite = new \stdClass;
                $registraEnvioConvite->grupo_id      = $grupoID;
                $registraEnvioConvite->usuario_id    = $usuarioSolicitadoID;

                $this->mapper->solicitacao->persist($registraEnvioConvite);
                $this->mapper->flush();

                $return = true;

            }else{

                $return = false;

            }

        }

    // Método responsável por solicitar a um usuário que ele siga um grupo privado

    // Método responsável por aceitar solicitação para participação de um grupo privado

        public function aceitarSolicitacaoGrupo($grupoID)
        {

            // Valida se o usuário que esta aceitando a solicitação realmente é o usuário o qual a solicitação foi enviada
            $validaAcaoSeguraca = COUNT($this->mapper->solicitacao(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id'], 'status' => 'pendente'))->fetchAll());

            if($validaAcaoSeguraca == '1'){

                // Adiciona usuário no grupo
                $registraFollowGrupo = new \stdClass;
                $registraFollowGrupo->grupo_id      = $grupoID;
                $registraFollowGrupo->usuario_id    = $_SESSION['usuario_id'];
                $registraFollowGrupo->nivel         = 'usuario';
                $registraFollowGrupo->status        = 'Ativo';

                $this->mapper->grupo_usuario->persist($registraFollowGrupo);
                $this->mapper->flush();

                $return = true; 


                // Aciona TimeLine

                // Parametros
                $interacaoTipo      = 'comecou a seguir';
                $modoInteracao      = 'grupo';
                $modoInteracaoID    = $grupoID;

                $this->AcionaTimeLine($interacaoTipo, $modoInteracao, $modoInteracaoID);


                // Atualiza status da solicitação para aceita
                $post122 = $this->mapper->solicitacao(array('grupo_id' => $grupoID, 'usuario_id' => $_SESSION['usuario_id'], 'status' => 'pendente'))->fetch();
                $post122->status = 'aceito';
                $this->mapper->solicitacao->persist($post122);
                $this->mapper->flush();

            }else{

                $return = false;

            }

        }

    // Método responsável por aceitar solicitação para participação de um grupo privado


    public function get($acao = null, $arg1 = null)
    {      

        if($acao == 'seguir'){

            $follower_id    = $_GET['follower_id'];
            $following_id   = $_GET['following_id'];

            return $this->seguir($follower_id, $following_id);
        
        }else if($acao == 'seguir-grupo'){

            $grupoID = $_GET['grupoID'];

            return $this->grupo($grupoID);

        }else if($acao == 'solicitar-grupo-privado'){

            $grupoID    = $_GET['grupoID'];
            $usuarioSolicitadoID  = $_GET['usuarioID'];

            return $this->solicitarGrupoPrivado($grupoID, $usuarioSolicitadoID);

        }else if($acao == 'aceitar-solicitacao-grupo'){

            $grupoID = $_GET['grupoID'];

            return $this->aceitarSolicitacaoGrupo($grupoID);

        }

    }


    public function post($acao = null, $arg1 = null)
    {   

    }
}
