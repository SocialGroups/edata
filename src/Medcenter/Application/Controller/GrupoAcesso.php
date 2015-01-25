<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Relational\Sql;
use Respect\Config\Container;
use SocialGroups\Application\Helper\Paginacao;
use SocialGroups\Application\Helper\ParsePut;
use Respect\Validation\Validator as v;
use SocialGroups\Application\Helper\Formatters as f;

class GrupoAcesso implements Routable
{
    private $mapper; 
    private $validatorNome;

    public function __construct()
    {
        $c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $c->mapper;
        $this->validatorNome = v::alnum("áÁãÃâÂàÀéÉêÊíÍóÓõÕôÔúÚçÇ")->notEmpty()->length(1,100);        
    }

    private function listar()
    {
        $vars['_view'] = 'grupo_acesso_listar.html.twig';
        return $vars;   
    }

    private function cadastro($idEditar = null)
    {
        $grupo_acesso = new \stdClass;
        if($idEditar != null) {
            $grupo_acesso = $this->mapper->grupo_acesso[$idEditar]->fetch();
        }
        $vars['_view'] = 'grupo_acesso_cadastro.html.twig';
        $vars['grupo_acesso'] = $grupo_acesso;

        return $vars;   
    }

    public function get($acao = null, $arg1 = null, $arg2 = null)
    {

        if($acao == "list") {
            return $this->listar();
        }

        if($acao == "ajax") {
            return $this->ajax();
        }

        if($acao == "cadastro") {
            return $this->cadastro();
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
        
        
        $totalRegistros = count($this->mapper->grupo_acesso($sWhereColumns)->fetchAll());
        $resultados = $this->mapper->grupo_acesso($sWhereColumns)->fetchAll(Sql::orderBy($sOrder)->limit($index,$qtdePagina));
        $qtdeRetornado = count($resultados);
        // var_dump($qtdeRetornado);

        $retornoJson["sEcho"] = intval($_GET['sEcho']);
        $retornoJson["iTotalRecords"] = $totalRegistros;
        $retornoJson["iTotalDisplayRecords"] = $totalRegistros;

        $retornoJson["aaData"] = array();
        foreach($resultados as $res) {
            $ativo = $res->ativo == 1 ? ' checked="checked" ' : "";
            $arrayRetornoDados = Array();
            $arrayRetornoDados[] = $res->id;
            $arrayRetornoDados[] = $res->nome.'<div class="btn_button_edit" idEditar="'.$res->id.'"><i class="icon-edit"></i></div>';
            $arrayRetornoDados[] = '<label>
                                        <input name="btnAtivar_'.$res->id.'" itemID="'.$res->id.'" class="ace ace-switch ace-switch-2 btnAtivar" type="checkbox" '.$ativo.'>
                                        <span class="lbl"></span>
                                    </label>
            ';

            $retornoJson["aaData"][] = $arrayRetornoDados;
        }

        return $retornoJson;
    }

    


    public function post($action = null, $arg1 = null, $arg2 = null)
    {
        
        $idEditar = $_POST["idEditar"];
        if($idEditar != "") {
            return $this->cadastro($idEditar);
        }

        $vars['_view'] = 'error404.html.twig';
        //$vars['message'] = "Argumentos Inválidos";
        return $vars;
    }

    private function editPermissao()
    {
        $put = ParsePut::parse();
        
        $grupo_acesso_id = $put["id_grupo_acesso"];
        $selecionados = $put['selecionados'];
        
        try {

            $permissoesExistentes = $this->mapper->grupo_acesso_permissao(array("grupo_acesso_id" => $grupo_acesso_id))->fetchAll();
            foreach($permissoesExistentes as $perm) {
                $this->mapper->grupo_acesso_permissao->remove($perm);
                $this->mapper->flush();
            }

            foreach($selecionados as $sel) {
                if($sel != '') {
                    $entidade = new \stdClass();
                    $entidade->grupo_acesso_id = $grupo_acesso_id;
                    $entidade->tela_permissao_id = $sel;
                    $this->mapper->grupo_acesso_permissao->persist($entidade);
                }
            }
            $this->mapper->flush();

            $return["result"] = true;
        } catch(\Exception $e) {
            $return["result"] = false;
            $errors["database"] = "Problema ao processar a solicitação no banco de dados";
            $return["errors"] = $errors; 
            $return["objectError"] = $e;             
        }
        return $return;
    }

    /**
    * Método que insere os dados via requisição ajax
    * @author Diego Pires
    *
    *
    */
    public function put($arg1 = null) {

        if($arg1 == "permissao") {
            return $this->editPermissao();
        }

        $put = ParsePut::parse();

        // var_dump($put);
        // die();
        $entidade = new \stdClass;

        try {
            if($put["id"]) {
                $entidade = $this->mapper->grupo_acesso[$put["id"]]->fetch();
            }   
        } catch(\Exception $e) {
            $return["result"] = false;
            $errors["database"] = "Problema ao processar a solicitação no banco de dados";
            $return["errors"] = $errors;             
        }

        try {           

            $nome = $put["nome"];
            $_POST["nome_old"] = $entidade->nome;
            
            $entidade->nome = $nome;

            $this->mapper->grupo_acesso->persist($entidade);
            $this->mapper->flush();

            $return["result"] = true;
            $return["item"] = $entidade;

        } catch(\Exception $e) {
            $return["result"] = false;
            $errors["database"] = "Problema ao gravar a solicitação no banco de dados";
            $return["errors"] = $e;  
        }
       return $return;
        
    }


    public function delete($tipo = null) {

        if($tipo == "permissao") {
            return $this->deletePermissao();
        }

        $put = ParsePut::parse();
        
        $entidade = $this->mapper->grupo_acesso(array("id" => $put["idItem"]))->fetch();
        $_POST["ativo_old"] = $entidade->ativo;
        $entidade->ativo = $entidade->ativo == 1 ? 0 : 1;

        try {
            $return["tipo"] = $entidade->ativo;
            $return["result"] = true;
            $this->mapper->grupo_acesso->persist($entidade);
            $this->mapper->flush();
        } catch(\Exception $e) {
            $return["result"] = false;
            $errors["database"] = "Problema ao processar a solicitação no banco de dados";
            $return["errors"] = $errors;             
        }

       return $return;
    }


    private function deletePermissao()
    {
        $put = ParsePut::parse();
        
        $entidade = $this->mapper->tela_permissao(array("id" => $put["idItem"]))->fetch();
        $_POST["ativo_old"] = $entidade->ativo;
        $entidade->ativo = 0;

        try {
            $return["tipo"] = $entidade->ativo;
            $return["result"] = true;
            $this->mapper->tela_permissao->persist($entidade);
            $this->mapper->flush();
        } catch(\Exception $e) {
            $return["result"] = false;
            $errors["database"] = "Problema ao processar a solicitação no banco de dados";
            $return["errors"] = $errors;             
        }

       return $return;        
    }
}
