<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use SocialGroups\Application\Controller\Login;
use SocialGroups\Application\Controller\InjectionDadosNeo4j;

class CriarUsuario implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }


    function removeAcentos($string, $slug = false) {
    $string = strtolower($string);

    // Código ASCII das vogais
    $ascii['a'] = range(224, 230);
    $ascii['e'] = range(232, 235);
    $ascii['i'] = range(236, 239);
    $ascii['o'] = array_merge(range(242, 246), array(240, 248));
    $ascii['u'] = range(249, 252);

    // Código ASCII dos outros caracteres
    $ascii['b'] = array(223);
    $ascii['c'] = array(231);
    $ascii['d'] = array(208);
    $ascii['n'] = array(241);
    $ascii['y'] = array(253, 255);

    foreach ($ascii as $key=>$item) {
        $acentos = '';
        foreach ($item AS $codigo) $acentos .= chr($codigo);
        $troca[$key] = '/['.$acentos.']/i';
    }

    $string = preg_replace(array_values($troca), array_keys($troca), $string);

    // Slug?
    if ($slug) {
        // Troca tudo que não for letra ou número por um caractere ($slug)
        $string = preg_replace('/[^a-z0-9]/i', $slug, $string);
        // Tira os caracteres ($slug) repetidos
        $string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
        $string = trim($string, $slug);
    }

    return $string;
}

    // Método responsável por cadastrar um usuário

        public function cadastraUsuario($nome, $sobreNome, $email, $senha, $repSenha)
        {

            if($nome > '' AND $sobreNome > '' AND $email > '' AND $senha > '' AND $repSenha > ''){

                if($senha == $repSenha){

                    // Trata dados dos usuários
                    $trataNome      = str_replace(" ","-",$nome);
                    $trataNome = $this->removeAcentos($trataNome);
                    $trataSobreNome = str_replace(" ","-",$sobreNome);
                    $trataSobreNome = $this->removeAcentos($trataSobreNome);
                    // Trata dados dos usuários

                    // Cadastra usuário
                    $InsertUsuario = new \StdClass;
                    $InsertUsuario->nome                = $nome;
                    $InsertUsuario->sobre_nome          = $sobreNome; 
                    $InsertUsuario->email               = $email;
                    $InsertUsuario->senha               = md5($senha);
                    $InsertUsuario->foto_perfil         = '/img/avatar_icon.png';
                    $InsertUsuario->nick_name           = $trataNome.$trataSobreNome;

                    $this->mapper->usuario->persist($InsertUsuario);
                    $this->mapper->flush();

                    $usuarioID = $InsertUsuario->id;

                    // Adiciona usuário no Neo4j
                    $AdicionaUsuarioNeo4j = new InjectionDadosNeo4j();
                    $AdicionaUsuarioNeo4j->neo4jAdicionaUsuario($usuarioID, $nome);
                    // Adiciona usuário no Neo4j

                     $_SESSION['login']         = $email;
                     $_SESSION['usuario_id']    = $usuarioID;

                    return 'true';    

                }else{

                    return 'senhaNaoBate';
                }

            }else{

                return 'camposObrigatorios';
            }

        }

    // Método responsável por cadastrar um usuário

 

    public function post($acao = null, $arg1 = null, $arg2 = null)
    {    
         $nome          = $_POST['nome'];
         $sobreNome     = $_POST['sobre_nome'];
         $email         = $_POST['email'];
         $senha         = $_POST['senha'];
         $repSenha      = $_POST['rep_senha'];

         return $this->cadastraUsuario($nome, $sobreNome, $email, $senha, $repSenha);
    }


}
