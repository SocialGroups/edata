// Recuperar Senha
  jQuery(document).ready(function(){
    jQuery('.recuperarSenha').live("submit",function(){
      
      var dados = jQuery( this ).serialize();
 
      jQuery.ajax({
        type: "POST",
        url: '/recuperar-senha',
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){


           Messenger().post("Ok, nós lhe enviamos um E-Mail com as instruções para recuperar a sua senha. Por favor verifique também na sua caixa de SPAM ou lixo eletronico");

  var loc = ['bottom', 'right'];
  var style = 'flat';

  var $output = $('.controls output');
  var $lsel = $('.location-selector');
  var $tsel = $('.theme-selector');

  var update = function(){
    var classes = 'messenger-fixed';

    for (var i=0; i < loc.length; i++)
      classes += ' messenger-on-' + loc[i];

    $.globalMessenger({ extraClasses: classes, theme: style });
    Messenger.options = { extraClasses: classes, theme: style };

    $output.text("Messenger.options = {\n    extraClasses: '" + classes + "',\n    theme: '" + style + "'\n}");
  };

  update();

  $lsel.locationSelector()
    .on('update', function(pos){
      loc = pos;

      update();
    })
  ;
            
          }else if(data == 'limiteAtingido'){


            Messenger().post({
   message: 'Erro, você atingiu o limite de solicitação de recuperação de senhas por hoje, nós já lhe enviamos um E-Mail com as instruções para recuperar sua senha...',
   type: 'error',
         showCloseButton: true
            }); 

  var loc = ['bottom', 'right'];
  var style = 'flat';

  var $output = $('.controls output');
  var $lsel = $('.location-selector');
  var $tsel = $('.theme-selector');

  var update = function(){
    var classes = 'messenger-fixed';

    for (var i=0; i < loc.length; i++)
      classes += ' messenger-on-' + loc[i];

    $.globalMessenger({ extraClasses: classes, theme: style });
    Messenger.options = { extraClasses: classes, theme: style };

    $output.text("Messenger.options = {\n    extraClasses: '" + classes + "',\n    theme: '" + style + "'\n}");
  };

  update();

  $lsel.locationSelector()
    .on('update', function(pos){
      loc = pos;

      update();
    })
  ;
            
          }else{


                       Messenger().post({
   message: 'Erro, desculpe mais este E-Mail não esta cadastrado em nossas bases de dados, por favor verifique se você digitou o E-Mail corretamente!',
   type: 'error',
         showCloseButton: true
            }); 

  var loc = ['bottom', 'right'];
  var style = 'flat';

  var $output = $('.controls output');
  var $lsel = $('.location-selector');
  var $tsel = $('.theme-selector');

  var update = function(){
    var classes = 'messenger-fixed';

    for (var i=0; i < loc.length; i++)
      classes += ' messenger-on-' + loc[i];

    $.globalMessenger({ extraClasses: classes, theme: style });
    Messenger.options = { extraClasses: classes, theme: style };

    $output.text("Messenger.options = {\n    extraClasses: '" + classes + "',\n    theme: '" + style + "'\n}");
  };

  update();

  $lsel.locationSelector()
    .on('update', function(pos){
      loc = pos;

      update();
    })
  ;
            
          }
        }
      });
      
      return false;
    });
  });
// Criar Grupo parte 2 Modal