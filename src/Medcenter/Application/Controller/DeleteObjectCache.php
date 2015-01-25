<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;

class DeleteObjectCache implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    // Método responsável por deletar o cache

    	public function deleteObjeto($grupoID)
    	{

    		// Instância Memcache
            $memcache = new \Memcache;
            $memcache->connect('localhost', 11211);
            // Instância Memcache

            // Criar Loop para deleção dos objetos em cache deste grupo
            for( $i = 1; $i <= 5; $i++ ){

            	$limite = 10;

            	switch ($i) {
				    case 0:
				        $inicio = 0;
				        break;
				    case 1:
				        $inicio = 0;
				        break;
				    case 2:
				        $inicio = 10;
				        break;
				    case 3:
				        $inicio = 30;
				        break;
				    case 4:
				        $inicio = 40;
				        break;
				    case 5:
				        $inicio = 50;
				        break;
				}

            	// Deletando objeto do grupo em que este box será postado do cache
                $SQLarrayGrupoBoxes = "SELECT gbo.id, gbo.box_id as boxID, gbo.grupo_id, gbo.box_tipo, gbo.usuario_id as usuarioID, gb.titulo, gb.descricao, gb.dataHora,us.nome, us.sobre_nome, us.foto_perfil, cc.conteudo AS conteudoCompartilhamento 

                                    FROM grupo_box_ordenacao AS gbo 

                                    INNER JOIN usuario as us ON gbo.usuario_id = us.id

                                    LEFT JOIN grupo_box AS gb ON gb.id = gbo.box_id 

                                    LEFT JOIN compartilhamento_conteudo AS cc ON cc.gbo_id = gbo.id

                                    WHERE gbo.grupo_id = '$grupoID' AND gb.ativo = 0 AND gbo.ativo = 0

                                    ORDER BY gbo.id DESC 

                                    LIMIT $inicio,$limite";

                // chave - Query
                $chaveGrupoBoxes = md5($SQLarrayGrupoBoxes);

                // Deleta objeto no cache do grupo relacionado a este box
                memcache_delete($memcache, $chaveGrupoBoxes);

               // $memcache->flush();

            }
            // Criar Loop para deleção dos objetos em cache deste grupo

    	}

    // Método responsável por deletar o cache
 }