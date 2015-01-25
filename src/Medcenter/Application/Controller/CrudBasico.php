<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Relational\Sql;
use Respect\Config\Container;
use SocialGroups\Application\Helper\Paginacao;
use SocialGroups\Application\Helper\ParsePut;
use Respect\Validation\Validator as v;
use SocialGroups\Application\Helper\PermissaoAcesso as p;

class CrudBasico implements Routable
{

    private $mapper; 
    private $validatorNome;
    protected $nomeEntidade;
    private $entidadeBanco;
    protected $prefixoPermissao;
    private $reflectionClass;

    public function __construct()
    {
        $c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $c->mapper;
        $this->validatorNome = v::alnum("áÁãÃâÂàÀéÉêÊíÍóÓõÕôÔúÚçÇ")->notEmpty()->length(1,100);
        $nomeEntidade = $this->nomeEntidade;
        $this->entidadeBanco = $this->mapper->$nomeEntidade;
        $this->reflectionClass = new \ReflectionClass(get_class($this));
        
    }


    private function listar()
    {
        $nomeConst = $this->prefixoPermissao."_LISTAR";       
        if(p::verificaPermissao($this->reflectionClass->getConstant($nomeConst))) {
            $vars['_view'] = $this->nomeEntidade.'_listar.html.twig';
        } else {
            $vars['_view'] = 'permissao_negada.html.twig';
        }

        return $vars;   
    }

    private function editAjax()
    {

        $nomeConst = $this->prefixoPermissao."_EDITAR";       
        if(!p::verificaPermissao($this->reflectionClass->getConstant($nomeConst))) {
            $return["result"] = false;
            $errors["database"] = "Você não possui permissão para realizar essa operação";
            $return["errors"] = $errors; 
            return json_encode($return);
        } 

        $novoValor = $_POST["newvalue"];
        $id = substr($_POST["elementid"], strpos($_POST["elementid"], "_")+1);

        try {
            $this->validatorNome->assert($novoValor);
            $nomeEntidade = $this->nomeEntidade;
            $entidade = $this->mapper->$nomeEntidade(array("id" => $id))->fetch();
            $_POST["nome_old"] = $entidade->nome;
            $entidade->nome = $novoValor;
            
            try {
                $this->mapper->$nomeEntidade->persist($entidade);
                $this->mapper->flush();

                $return["result"] = true;
                $return["item"] = $entidade;

            } catch(\Exception $e) {
                $return["result"] = false;
                $errors["database"] = "Problema ao processar a solicitação no banco de dados";
                $return["errors"] = $errors; 

                // var_dump($e); 
            }
        } catch(Argument $e) {
            $errors = $e->findMessages(array(
                'alnum'        => 'Nome pode conter somente letras e números',
                'length'       => 'Nome não pode conter mais de 100 caracteres',
                'notEmpty' => 'Nome não pode ser vazio'
            ));
            $return["result"] = false;
            $return["errors"] = $errors;
        }

        return json_encode($return);

        
    }

    public function get($acao = null, $arg1 = null)
    {

        if($acao == "list") {
            return $this->listar();
        }

        if($acao == "ajax") {
            return $this->ajax();
        }

        $vars['_view'] = 'error404.html.twig';
        //$vars['message'] = "Argumentos Inválidos";
        return $vars;
    }

    private function ajax()
    {
        // $this->getFilters();

        $aColumns = array( 'id', 'nome', 'ativo' );

        // LIMIT
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $index = intval($_GET['iDisplayStart']);
            $qtdePagina = intval($_GET['iDisplayLength']);
        }

        // ORDER
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
                        ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') ." ";
                }
            }
        }

        // WHERE INDIVIDUAL
        $sWhereColumns = array();
        $sWhereColumnsArr = array();
        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                $sWhereColumns[$aColumns[$i]." LIKE "] = '%'.$_GET['sSearch_'.$i].'%';
            }
        }
        


        // $sWhereColumns = array();
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhereColumns = "";
            for ( $i=1 ; $i<count($aColumns) ; $i++ )
            {
                $sWhereColumns .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
            }
            $sWhereColumns = substr_replace( $sWhereColumns, "", -3 );
            
        }
        
        $nomeEntidade = $this->nomeEntidade;
        $totalRegistros = count($this->mapper->$nomeEntidade($sWhereColumns)->fetchAll());
        $resultados = $this->mapper->$nomeEntidade($sWhereColumns)->fetchAll(Sql::orderBy($sOrder)->limit($index,$qtdePagina));
        $qtdeRetornado = count($resultados);

        $retornoJson["sEcho"] = intval($_GET['sEcho']);
        $retornoJson["iTotalRecords"] = $totalRegistros;
        $retornoJson["iTotalDisplayRecords"] = $totalRegistros;

        $retornoJson["aaData"] = array();
        foreach($resultados as $res) {
            $ativo = $res->ativo == 1 ? ' checked="checked" ' : "";
            $arrayRetornoDados = Array();
            $arrayRetornoDados[] = $res->id;
            $arrayRetornoDados[] = '<div class="editable'.$res->id.' divEdit" id="item_'.$res->id.'">'.$res->nome.'</div><div class="btn_button_edit" idEditar="'.$res->id.'"><i class="icon-edit"></i></div>';
            $arrayRetornoDados[] = '<label>
                                        <input name="btnAtivar_'.$res->id.'" itemID="'.$res->id.'" class="ace ace-switch ace-switch-2 btnAtivar" type="checkbox" '.$ativo.'>
                                        <span class="lbl"></span>
                                    </label>
            ';

            $retornoJson["aaData"][] = $arrayRetornoDados;
        }

        return $retornoJson;
    }



    public function post($action = null)
    {
        
        if($action == "editAjax") {
            return $this->editAjax();
        }
        
    }


    /**
    * Método que insere os dados via requisição ajax
    * @author Diego Pires
    *
    *
    */
    public function put() {

        $nomeConst = $this->prefixoPermissao."_INSERIR";       
        if(!p::verificaPermissao($this->reflectionClass->getConstant($nomeConst))) {
            $return["result"] = false;
            $errors["database"] = "Você não possui permissão para realizar essa operação";
            $return["errors"] = $errors;  
            return $return;
        }

        $put = ParsePut::parse();
        try {
            $this->validatorNome->assert($put["nome"]);

            $nome = $put["nome"];
            
            $entidade = new \stdClass;
            $entidade->nome = $nome;

            try {
                $nomeEntidade = $this->nomeEntidade;
                $this->mapper->$nomeEntidade->persist($entidade);
                $this->mapper->flush();
                $return["result"] = true;
                $return["item"] = $entidade;

            } catch(\Exception $e) {
                
                $return["result"] = false;
                $errors["database"] = "Problema ao processar a solicitação no banco de dados";
                $return["errors"] = $errors;             
            }

        } catch(Argument $e) {
            $errors = $e->findMessages(array(
                'alnum'        => 'Nome pode conter somente letras e números',
                'length'       => 'Nome não pode conter mais de 100 caracteres',
                'notEmpty' => 'Nome não pode ser vazio'
            ));
            $return["result"] = false;
            $return["errors"] = $errors;
        }
       return $return;
        
    }

    public function delete() {

        $nomeConst = $this->prefixoPermissao."_DELETAR";       
        if(!p::verificaPermissao($this->reflectionClass->getConstant($nomeConst))) {
            $return["result"] = false;
            $errors["database"] = "Você não possui permissão para realizar essa operação";
            $return["errors"] = $errors;  
            return $return;
        }

        $put = ParsePut::parse();
        
        $nomeEntidade = $this->nomeEntidade;
        $entidade = $this->mapper->$nomeEntidade(array("id" => $put["idItem"]))->fetch();
        $_POST["ativo_old"] = $entidade->ativo;
        $entidade->ativo = $entidade->ativo == 1 ? 0 : 1;

        try {
            $return["tipo"] = $entidade->ativo;
            $return["result"] = true;
            $this->mapper->$nomeEntidade->persist($entidade);
            $this->mapper->flush();

        } catch(\Exception $e) {
            
            $return["result"] = false;
            $errors["database"] = "Problema ao processar a solicitação no banco de dados";
            $return["errors"] = $errors;             
        }


       return $return;

    }
}
