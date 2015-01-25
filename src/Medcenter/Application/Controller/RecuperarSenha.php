<?php

namespace SocialGroups\Application\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use SocialGroups\Application\libSendGrid\SendGrid;


class RecuperarSenha implements Routable
{
    private $mapper;

    public function __construct()
    {
        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
    }

    // Método responsável por recuperar senha

        public function recuperar($email)
        {   

            $dataAtual = date('Y-m-d');

            // Verifica se o usuário já esta cadastrado na base de dados
            $verificaEmailExisteBase = COUNT($this->mapper->usuario(array('email' => $email))->fetchall());

            if($verificaEmailExisteBase == 1){

                // Verifica se o usuário já atingiu o número maximo de solicitação no dia
                $verificaMaximoSolicitacao = COUNT($this->mapper->log_recuperar_senha(array('email' => $email, 'data' => $dataAtual))->fetchall());

                if($verificaMaximoSolicitacao < 3){

                    // Insere solicitação de recuperação de senha no log
                    $InsertLogRecuperarSenha = new \stdClass;
                    $InsertLogRecuperarSenha->email = $email;
                    $InsertLogRecuperarSenha->data = date('Y-m-d');

                    $this->mapper->log_recuperar_senha->persist($InsertLogRecuperarSenha);
                    $this->mapper->flush();

                    // Gera chave para enviar para o usuário
                    $chave = md5($email.date('Y-m-d H:i:s'));

                    // Inserede Chave no banco de dados
                    $InsertChaveDB = new \stdClass;
                    $InsertChaveDB->chave = $chave;
                    $InsertChaveDB->email = $email;

                    $this->mapper->recuperar_senha_chave->persist($InsertChaveDB);
                    $this->mapper->flush();


                    // Envia E-Mail Utilizando a API do SendGrid
                    $sendgrid = new SendGrid('socialgroups', 'lounge0197');
                    
                    $email    = new SendGrid\Email();
                    $email->addTo('socialgroups@outlook.com')->
                    setFrom('recover@socialgroups.com.br')->
                    setSubject('Alguém solicitou uma nova senha para a sua conta do Social Groups')->
                    setText('Um nova senha foi solicitada para sua conta no Social Groups, para recuperar a sua senha <a href=""> Clique Aqui </a>')->
                    setHtml('<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;width:100%"><tbody><tr><td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:0;border-left:none;border-right:none;border-top:none;border-bottom:none"><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse"><tbody><tr><td style="padding:0;width:100%"><span style="color:#ffffff;display:none!important;font-size:1px">Olá,</span></td></tr><tr><td style="padding:0;width:100%"><table cellspacing="0" cellpadding="0" width="100%" bgcolor="#435E9C" style="border-collapse:collapse;width:100%;background:#0aa699;background-image:-webkit-linear-gradient(top,#5c77b5,#435e9c);border-color:#0a1f4f;border-style:solid;border-width:0px 0px 1px 0px;height:47px"><tbody><tr><td><center><table cellspacing="0" cellpadding="0" width="610" height="44" style="border-collapse:collapse"><tbody><tr><td align="left" style="width:100%;line-height:47px"><table cellspacing="0" cellpadding="0" style="border-collapse:collapse"><tbody><tr><td><a href="http://socialgroups.com.br" style="color:#ffffff;text-decoration:none;font-weight:bold;font-family:lucida grande,tahoma,verdana,arial,sans-serif;vertical-align:baseline;font-size:20px;letter-spacing:-0.03em;text-align:left" target="_blank"> Social Groups </a></td><td width="10" style="width:10px"></td><td><font color="white" size="3"></font></td></tr></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table></td></tr><tr><td style="padding:0;width:100%"><table cellspacing="0" cellpadding="0" width="100%" bgcolor="#e0e1e5" style="border-collapse:collapse"><tbody><tr><td><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse"><tbody><tr><td height="19">&nbsp;</td></tr></tbody></table><center><table cellspacing="0" cellpadding="0" width="610" style="border-collapse:collapse"><tbody><tr><td align="left" style="background-color:#ffffff;border-color:#c1c2c4;border-style:solid;display:block;border-width:1px;border-radius:5px;overflow:hidden"><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse"><tbody><tr><td style="padding:15px"><table cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:100%"><tbody><tr><td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding-bottom:6px"><div>Olá,</div></td></tr><tr><td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding-top:6px;padding-bottom:6px">foi solicitado uma recuperação de senha para esta conta no Social Groups.<br> <a href="http://socialgroups.com.br/recuperar-senha/resetar/'.$chave.'"> Clique aqui para recuperar sua senha </a></td></tr><tr><td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding-top:6px;padding-bottom:6px"><center></tr></tbody></table></center></td></tr><tr><td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding-top:6px;padding-bottom:6px"><div><span style="color:#333333;font-weight:bold"></td></tr><tr><td height="7" colspan="3" style="line-height:7px">&nbsp;</td></tr></tbody></table></a></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table></td></tr><tr><td style="padding:0;width:100%"><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse"><tbody><tr><td><center><table cellspacing="0" cellpadding="0" width="610" style="border-collapse:collapse"><tbody><tr><td><table cellspacing="0" cellpadding="0" width="610" border="0" style="border-collapse:collapse"><tbody><tr><td style="font-size:12px;font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;padding:18px 0;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#6a7180;font-weight:300;line-height:16px;text-align:center;border:none"></td></tr></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>
                        ');

                    $sendgrid->send($email);
                    // Envia E-Mail Utilizando a API do SendGrid

                    return 'true';

                }else{

                    return 'limiteAtingido';
                }

            }else{

                return 'false';

            }

        }

    // Método responsável por resetar uma senha

        public function resetar($chave)
        {

            // Verifica se a chave é válida
            $verificaChave = COUNT($this->mapper->recuperar_senha_chave(array('chave' => $chave, 'status' => 'pendente'))->fetchall());

            if($verificaChave == 1){


                // retorna view para recuperar a senha
                $vars['chave'] = $chave;
                $vars['_view'] = 'resetar_senha.html.twig';

            }else{

                $vars['_view'] = 'recuperar_senha_chave_invalida.html.twig';

            }

            return $vars;

        }

    // Método responsável por resetar uma senha


    // Método responsável por resetar a senha do usuário

        public function resetarSenha($chave, $senha, $repSenha)
        {

            if($chave > '' AND $senha > '' AND $repSenha > ''){

                // Recupera Email do usuário a qual a senha deve ser trocada
                $getDadosChave = $this->mapper->recuperar_senha_chave(array('chave' => $chave))->fetch();

                // verifica se a senha é igual ao campo repetir senha
                if($senha == $repSenha){

                    // efetua troca da senha do usuário
                    $atualizaSenha = $this->mapper->usuario(array('email' =>$getDadosChave->email))->fetch();
                    $atualizaSenha->senha = md5($senha);
                    $this->mapper->usuario->persist($atualizaSenha);
                    $this->mapper->flush();

                    // Atualiza Chave para utilizada
                    $atualizaStatusChave = $this->mapper->recuperar_senha_chave(array('chave' => $chave))->fetch();
                    $atualizaStatusChave->status = 'utilizada';
                    $this->mapper->recuperar_senha_chave->persist($atualizaStatusChave);
                    $this->mapper->flush();

                    return 'true';

                }else{

                    return 'senhaNaoBate';
                }

            }else{

                return 'false';
            }

        }

    // Método responsável por resetar a senha do usuário


    public function get($acao, $chave){

        if($acao == 'resetar'){

            return $this->resetar($chave);

        }

    }

    // Método responsável por recuperar senha

    public function post($arg1 = null)
    {  
        if($arg1 == 'resetar-senha'){

            $chave      = $_POST['chave'];
            $senha      = $_POST['senha'];
            $repSenha   = $_POST['rep_senha'];

            return $this->resetarSenha($chave, $senha, $repSenha);

        }else{

            $email = $_POST['email'];
            return $this->recuperar($email);

        }
        
    }
}
