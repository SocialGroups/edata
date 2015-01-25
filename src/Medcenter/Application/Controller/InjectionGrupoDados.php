<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class InjectionGrupoDados implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por adicionar um grupo na base de dados do Injection

        public function InjectionInsertGrupo($grupoID)
        {

            $insertGrupo = new \stdClass;
            $insertGrupo->grupo_id = $grupoID;

            $this->mapper->grupo_informacoes->persist($insertGrupo);
            $this->mapper->flush();

        }

    // Método responsável por adicionar um grupo na base de dados do Injection


    // Método responsável por editar alguma informação de um grupo na base de dados do Injection

        public function InjectionUpdateGrupo($grupoID, $coluna, $tipoUpdate)
        {

            // Verifica se todos os campos foram preenchidos

                if($grupoID > '' AND $coluna > '' AND $tipoUpdate > ''){

                    if($tipoUpdate == 'insert'){

                        // Aplica Função de soma de um determinado registro

                            // Recupra coluna a ser somada
                            $getDadosGrupo = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();

                            // Verifica coluna a ser somado um registro
                            if($coluna == 'seguidores'){

                                $novoValor = $getDadosGrupo->numero_seguidores+1;

                                // Atualizando valor no banco de dados
                                $atualizaValorInjection = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();
                                $atualizaValorInjection->numero_seguidores = $novoValor;
                                $this->mapper->grupo_informacoes->persist($atualizaValorInjection);
                                $this->mapper->flush();

                            }else if($coluna == 'boxes'){

                                $novoValor = $getDadosGrupo->numero_boxes+1;

                                 // Atualizando valor no banco de dados
                                $atualizaValorInjection = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();
                                $atualizaValorInjection->numero_boxes = $novoValor;
                                $this->mapper->grupo_informacoes->persist($atualizaValorInjection);
                                $this->mapper->flush();

                            }else{

                                $novoValor = $getDadosGrupo->numero_interacoes+1;

                                 // Atualizando valor no banco de dados
                                $atualizaValorInjection = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();
                                $atualizaValorInjection->numero_interacoes = $novoValor;
                                $this->mapper->grupo_informacoes->persist($atualizaValorInjection);
                                $this->mapper->flush();

                            }

                        // Aplica Função de soma de um determinado registro

                    }else{

                        // Aplica Função de subtração de um determinado registro

                            // Recupra coluna a ser subtraida
                            $getDadosGrupo = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();

                            // Verifica coluna a ser subtraida um registro
                            if($coluna == 'seguidores'){

                                $novoValor = $getDadosGrupo->numero_seguidores-1;

                                // Atualizando valor no banco de dados
                                $atualizaValorInjection = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();
                                $atualizaValorInjection->numero_seguidores = $novoValor;
                                $this->mapper->grupo_informacoes->persist($atualizaValorInjection);
                                $this->mapper->flush();

                            }else if($coluna == 'boxes'){

                                $novoValor = $getDadosGrupo->numero_boxes-1;

                                 // Atualizando valor no banco de dados
                                $atualizaValorInjection = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();
                                $atualizaValorInjection->numero_boxes = $novoValor;
                                $this->mapper->grupo_informacoes->persist($atualizaValorInjection);
                                $this->mapper->flush();

                            }else{

                                $novoValor = $getDadosGrupo->numero_interacoes-1;

                                 // Atualizando valor no banco de dados
                                $atualizaValorInjection = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetch();
                                $atualizaValorInjection->numero_interacoes = $novoValor;
                                $this->mapper->grupo_informacoes->persist($atualizaValorInjection);
                                $this->mapper->flush();

                            }                        

                        // Aplica Função de subtração de um determinado registro

                    }

                }

            // Verifica se todos os campos foram preenchidos

        }

    // Método responsável por editar alguma informação de um grupo na base de dados do Injection


    // Método responsável por recuperar informações de um grupo na base de dados Injection

        public function InjectionGetDadosGrupo($grupoID)
        {

            // recupera informações sobre este grupo 

                $getDadosGrupo = $this->mapper->grupo_informacoes(array('grupo_id' => $grupoID))->fetchall();

                return $getDadosGrupo;

            // recupera informações sobre este grupo 

        }

    // Método responsável por recuperar informações de um grupo na base de dados Injection  

}
