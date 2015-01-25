<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class TriggerRemoveSecurityGrupo implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método - Excluir Grupos Security que passaram de duas horas
        public function RemoveSecurityGrupo()
        {

            $dataHora = date('Y-m-d H:i:s');

            $arrayGrupo = $this->mapper->grupo(array('privilegios' => 'Security', 'grupo_status' => 'ativo'))->fetchAll();

                foreach ($arrayGrupo as $dadosGrupo) {

                    $data1 = $dadosGrupo->dataCriacao;
                    $data2 = $dataHora;
                    $unix_data1 = strtotime($data1);
                    $unix_data2 = strtotime($data2);

                    $nHoras   = ($unix_data2 - $unix_data1) / 3600;
                    $nMinutos = (($unix_data2 - $unix_data1) % 3600) / 60;

                    if(printf('%02d:%02d', $nHoras, $nMinutos) > '02:00'){

                        $grupoID = $dadosGrupo->id;

                        $DelecaoLogicaGrupoSecurity = $this->mapper->grupo(array('id' => $grupoID))->fetch();
                        $DelecaoLogicaGrupoSecurity->grupo_status = 'inativo';
                        $this->mapper->grupo->persist($DelecaoLogicaGrupoSecurity);
                        $this->mapper->flush();
                        
                    }

                }

        }

    // Método - Excluir Grupos Security que passaram de duas horas

    public function get($acao = null, $arg1 = null, $arg2 = null, $arg3 = null)
    {       

            return $this->RemoveSecurityGrupo();

    }



    public function post($acao = null, $arg1 = null)
    {

            $grupoID    = $_POST['grupoID'];
            $boxID      = $_POST['boxID'];
            $conteudo   = $_POST['conteudo'];


            return $this->adicionarInteracao($grupoID,$boxID,$conteudo);
    }
}
