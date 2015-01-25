<?php
ini_set("display_errors", true);
require __DIR__ . '/../bootstrap.php';

use Respect\Rest\Router;

$auth = new Medcenter\Application\Routine\Auth;
$authenticated = function() use($auth) {return $auth();};
$r = new Router;
$r->isAutoDispatched = false;

// Rota responsável pelos métodos da api do magento
$r->any('/magento-api/*/*/*/*/*/*', 'SocialGroups\Application\Controller\ApiMagento');

// Rota responsável pelo login dos usuários
$r->any('/login/*', 'Medcenter\Application\Controller\LoginController');

// Rota responsável pelo logout dos usuários
$r->get('/logout', 'Medcenter\Application\Controller\Logout');

// Rota responsável por redirecionar um usuário para a home
$r->any('/', 'SocialGroups\Application\Controller\Home')
    ->by($authenticated);

$r->any('/zendesk/*/*/*/*', 'Medcenter\Application\Controller\ZendeskController');

// Rota responsável pelo Feed / Home da aplicação
$r->any('/home/*/*/*/*', 'Medcenter\Application\Controller\DashboardController')
    ->by($authenticated);

// Rota responsável pelo cadastro de novos usuários
$r->any('/cadastrar-usuario/*', 'SocialGroups\Application\Controller\CriarUsuario');

// Rota responsável pelos métodos de solicitações
$r->any('/solicitacao/*/*', 'SocialGroups\Application\Controller\solicitacoes')
    ->by($authenticated);

// Rota responsável pelos métodos de solicitações
$r->any('/recuperar-senha/*/*', 'SocialGroups\Application\Controller\RecuperarSenha');

// Rota responsável pela pagina de notificações completa
$r->any('/notificacao-pendente-completa/*/*', 'SocialGroups\Application\Controller\AppendNotificacaoPendente')
    ->by($authenticated);

// Rota responsável pelos métodos de Menções
$r->any('/mencoes/*/*/*', 'SocialGroups\Application\Controller\Mencoes')
    ->by($authenticated);

// Rota responsável por recomendar um grupo
$r->any('/recomenda-grupo/*', 'SocialGroups\Application\Controller\RecomendaGrupo')
    ->by($authenticated);

// Rota responsável recomendar um usuário
$r->any('/recomenda-usuario/*', 'SocialGroups\Application\Controller\RecomendaFollowing')
    ->by($authenticated);

// Rota responsável pelos dados do profile
$r->any('/profile-dados/*/*/*', 'SocialGroups\Application\Controller\ProfileInformacoes')
    ->by($authenticated);

// Rota responsável pelos grupos
$r->any('/grupos/*/*/*/*', 'SocialGroups\Application\Controller\GetGrupos')
    ->by($authenticated);

// Rota responsável por efetuar uma denúncia de um box
$r->any('/denuncia/*/*', 'SocialGroups\Application\Controller\denuncia')
    ->by($authenticated);

// Rota responsável por editar um grupo
$r->any('/editar-grupo/*/*', 'SocialGroups\Application\Controller\EditarGrupo')
    ->by($authenticated);

// Rota responsável por relatar um bug
$r->any('/relatar-bug/*/*', 'SocialGroups\Application\Controller\RelatarBug')
    ->by($authenticated);

// Rota responsável cadastro de um usuário
$r->any('/cadastro/*/*', 'SocialGroups\Application\Controller\CadastroConvidado');

// Rota responsável pelas pesquisas de grupos,usuários etc...
$r->any('/socialsearch/*', 'SocialGroups\Application\Controller\SocialSearch')
    ->by($authenticated);

// Rota responsável por editar um profile
$r->any('/editar-profile/*/*', 'SocialGroups\Application\Controller\EditarProfile')
    ->by($authenticated);

// Rota responsável por criar um grupo
$r->any('/criargrupo/*', 'SocialGroups\Application\Controller\CriarGrupo')
    ->by($authenticated);

// Rota responsável pela classe de Follow/Unfollow em um grupo
$r->any('/acao-grupo/*', 'SocialGroups\Application\Controller\AcoesGrupo')
    ->by($authenticated);

// Rota responsável por deletar um box
$r->any('/deletar/*', 'SocialGroups\Application\Controller\Deletar')
    ->by($authenticated);

// Rota responsável pela aplicação de cache
$r->any('/memcached/*', 'SocialGroups\Application\Controller\Memcached')
    ->by($authenticated);

// Rota responsável pela pagina de top search
$r->any('/topsearch/*/*/*', 'SocialGroups\Application\Controller\TopSearch')
    ->by($authenticated);

// Rota responsável pela lista de seguidores de um grupo
$r->any('/listar-seguidores-grupos/*/*', 'SocialGroups\Application\Controller\ListarSeguidoresGrupo')
    ->by($authenticated);

// Rota responsável pela lista de seguidores de um profile
$r->any('/listar-seguidores-profile/*/*', 'SocialGroups\Application\Controller\ListarSeguidoresProfile')
    ->by($authenticated);

    // Rota responsável pela lista de usuários de um grupo
$r->any('/listar-grupos-usuario/*/*', 'SocialGroups\Application\Controller\ListarGruposUsuario')
    ->by($authenticated);

// Rota responsável por inserir um box
$r->any('/insertbox/*/*/*', 'SocialGroups\Application\Controller\InsertBox')
    ->by($authenticated);

// Rota responsável por postar um comentário em um box
$r->any('/postcomentarios/*/*', 'SocialGroups\Application\Controller\InsertComentarios')
    ->by($authenticated);

// Rota responsável por visualizar um box com todos os detalhes
$r->any('/box/*/*', 'SocialGroups\Application\Controller\BoxDetalhes')
    ->by($authenticated);

// Rota responsável pelos métodos de solicitações
$r->any('/seguirgrupo/*/*', 'SocialGroups\Application\Controller\SeguirGrupo')
    ->by($authenticated);

// Rota responsável pelas requisições feitas
$r->any('/requisicoes/*/*', 'SocialGroups\Application\Controller\RequisicaoGrupoPrivado')
    ->by($authenticated);

// Rota responsável pelos métodos de solicitações
$r->any('/solicitacoes/*/*', 'SocialGroups\Application\Controller\SolicitacoesPendentes')
    ->by($authenticated);

// Rota responsável pela verificação de permição de acesso a um determinado grupo
$r->any('/pa/*/*', 'SocialGroups\Application\Controller\PermissaoAcessoGrupo')
    ->by($authenticated);

// Rota responsável pelos comentários de um determinado box
$r->any('/comentarios/*/*', 'SocialGroups\Application\Controller\Comentarios')
    ->by($authenticated);

// Rota responsável por compartilhar um box
$r->any('/compartilhar/*/*', 'SocialGroups\Application\Controller\compartilhar')
    ->by($authenticated);

// Rota responsável pelos profiles
$r->any('/profile/*/*/*', 'SocialGroups\Application\Controller\Profile')
    ->by($authenticated);

// Rota responsável pela class de geração das thumb`s
$r->any('/geradorthumb/*/*', 'SocialGroups\Application\Controller\easyphpthumbnail')
    ->by($authenticated);

// Rota responsável pelas menções
$r->any('/mention/*/*', 'SocialGroups\Application\Controller\LiveMention')
    ->by($authenticated);

// Rota responsável pelo Neo4j aplicação de banco de dados não relacional
$r->any('/neo4j/*/*', 'SocialGroups\Application\Controller\neo4jNOSQL')
    ->by($authenticated);

// Rota responsável pela inserção de imagens no aws S3
$r->any('/s3/*/*', 'SocialGroups\Application\Controller\S3Upload')
    ->by($authenticated);

// Rota responsável pela inserção de imagens no aws S3
$r->any('/s3class/*/*', 'SocialGroups\Application\Controller\S3')
    ->by($authenticated);

// Rota responsável pelas interações dos usuários que sigo
$r->any('/followedIDS/*/*', 'SocialGroups\Application\Controller\SnInterassoesFollowed')
    ->by($authenticated);

// Rota responsável pelo gerenciamento do acesso aos grupos
$r->any('/grupo-acesso/*/*/*', 'SocialGroups\Application\Controller\GrupoAcesso')
    ->by($authenticated);

// Rota responsável pelo Ajax
$r->any('/ajax/*/*', 'SocialGroups\Application\Controller\AjaxGetGrupos')
    ->by($authenticated);

// rotas dirétas para o frot end
$r->any('/router/*/*', 'SocialGroups\Application\Controller\directs')
    ->by($authenticated);

// Rota responsável pela API do Neo4j
$r->any('/neo4jAPI/*/*/*/*', 'SocialGroups\Application\Controller\InjectionVisualizacao')
    ->by($authenticated);

// Rota responsável pelo search
$r->any('/search/*/*', 'SocialGroups\Application\Controller\Search')
    ->by($authenticated);


$r->always(
    'Accept',
    array(
        'text/html'        => new Medcenter\Application\Routine\Twig,
        'text/plain'       => $json = new Medcenter\Application\Routine\Json,
        'application/json' => $json,
        'text/json'        => $json
    )
);

print $r->run();
