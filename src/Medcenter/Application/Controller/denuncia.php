<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\TriggersTopMenu;
use SocialGroups\Application\Controller\InjectionGrupoDados;

class denuncia implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método responsável efetuar uma denuncia

        public function insertStrike($boxID, $motivo)
        {   

            if($motivo == ''){

                return 'false';

            }else{

            // Usuário ID
            $usuarioID = $_SESSION['usuario_id'];

            // Recupera autor do box
            $getAutorBox = $this->mapper->grupo_box(array('id' => $boxID))->fetch();

            // Autor do box
            $autorID = $getAutorBox->id;

            // Verifica se já existe uma denuncia deste usuário para este box
            $verificaRepeticaoDenuncia = COUNT($this->mapper->strikes(array('box_id' => $boxID, 'usuario_id' => $usuarioID))->fetchall());


            if($verificaRepeticaoDenuncia == 0){

                // insere strike para este box

                    $InsertStrike = new \StdClass;

                    $InsertStrike->box_id           = $boxID;
                    $InsertStrike->autor_box_id     = $autorID; 
                    $InsertStrike->usuario_id       = $usuarioID;
                    $InsertStrike->motivo           = $motivo;
                    $InsertStrike->dataHora         = date('Y-m-d H:i:s');

                    $this->mapper->strikes->persist($InsertStrike);
                    $this->mapper->flush();

                // instância método responsável por deletar o box
                return $this->triggerBlockBox($boxID);

            }else{

                    return 'repetida';
            }

          }
        }   

    // Método responsável efetuar uma denuncia


    // Método responsável por efetuar o bloqueio de um box

        public function triggerBlockBox($boxID)
        { 

            // Recupera Quantidade de usuários deste grupo
            $getGrupoID = $this->mapper->grupo_box(array('id' => $boxID))->fetch();

           $grupoID = $getGrupoID->grupo_id;

            // Recupera Quantidade de seguidores deste grupo
            $getNumeroSeguidores = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();

            // Número de seguidores
            $numeroSeguidoresGrupo = $getNumeroSeguidores->numero_seguidores;

            // Conta Quantidade de denuncias que o box tem
            $getQuantidadeDenuncias = COUNT($this->mapper->strikes(array('box_id' => $boxID))->fetchall());


            if($numeroSeguidoresGrupo >= 1){

                $PorcentagemNumeroSeguidores = (1 / $numeroSeguidoresGrupo) * 100;

            }else{

                $PorcentagemNumeroSeguidores = 0;

            }

            if($PorcentagemNumeroSeguidores >= 5 AND $numeroSeguidoresGrupo >= 10){

                // Bloqueia Box

                // Efetua deleção do box selecioando
                $deletaBox = $this->mapper->grupo_box_ordenacao(array('id' => $boxID))->fetch();
                $deletaBox->ativo = 1;
                $this->mapper->grupo_box->persist($deletaBox);
                $this->mapper->flush();

                // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'boxes';
                $tipoUpdate = 'delete';
                $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                // Injection Grupo Dados

                // Recupera Dados do trigger menu
                $DeleteObjectCache = new DeleteObjectCache();
                // recupera Dados do trigger menu

                // instância método responsável por deletar os objetos deste grupo do MemCache
                $DeleteObjectCache->deleteObjeto($grupoID);
                // Instância método responsável por deletar os objetos deste grupo do MemCache

                // Bloqueia Box

            }else if($getQuantidadeDenuncias >= 5){

                
                // Bloqueia Box

                // Efetua deleção do box selecioando
                $deletaBox = $this->mapper->grupo_box_ordenacao(array('box_id' => $boxID, 'box_tipo' => 'publicacao'))->fetch();
                    
                    $deletaBox->ativo = 1;
                    $this->mapper->grupo_box->persist($deletaBox);
                    $this->mapper->flush();
                

                // Injection Grupo Dados
                $InjectionGrupoDados = new InjectionGrupoDados();
                $coluna = 'boxes';
                $tipoUpdate = 'delete';
                $InjectionGrupoDados->InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate); 
                // Injection Grupo Dados

                // Recupera Dados do trigger menu
                $DeleteObjectCache = new DeleteObjectCache();
                // recupera Dados do trigger menu

                // instância método responsável por deletar os objetos deste grupo do MemCache
                $DeleteObjectCache->deleteObjeto($grupoID);
                // Instância método responsável por deletar os objetos deste grupo do MemCache

                // Bloqueia Box

            }

            return 'true';

        }

    // Método responsável por efetuar o bloqueio de um box


    // Método responsável por efetuar o FrontEnd da denuncia

        public function FrontEndDenuncia($boxID)
        {

            $vars['boxID'] = $boxID;
            $vars['_view'] = 'denuncia.html.twig';
            return $vars;

        }

    // Método responsável por efetuar o FrontEnd da denuncia


    public function get($acao = null, $valor = null, $arg2 = null)
    {       
            if ($acao == 'trigger'){

                    $boxID = $valor;

                return $this->triggerBlockBox($boxID);

            }else{ 

                    $boxID = $acao;
                
                return $this->FrontEndDenuncia($boxID);
            }
    }


    public function post($acao = null, $arg1 = null, $arg2 = null)
    {       

            $boxID  = $_POST['boxID'];
            $motivo = $_POST['motivo'];

            return $this->insertStrike($boxID, $motivo);
    }

}
