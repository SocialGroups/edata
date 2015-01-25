 //editar Dados pessoais
  jQuery(document).ready(function(){
    jQuery('.AlterarDadosPessoais').live("submit",function(){
      var dados = jQuery( this ).serialize();

    // ID do box
    var ID = $(this).attr("id");

    var nome       = document.getElementById('nome').value;
    var sobreNome  = document.getElementById('sobreNome').value;
 
      jQuery.ajax({
        type: "POST",
        url: "/editar-profile/profile",
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

            $('#dadosPessoasUsuario').remove();

            $('.dadosUsuario').append('<h3 id="dadosPessoasUsuario" style=" text-align:left;">'+nome+' '+sobreNome+' <a data-toggle="modal" href="/editar-profile/dados" data-target="#EditarDados"><storng style="font-size:12px;">Editar </strong></a></h3><br>');

            $('#EditarDados').modal('hide');

          Messenger().post("Dados Alterados com sucesso!");

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
            });
            
          }else if(data == 'logout'){

            setTimeout(function () { location.reload(true); }, 3000);

          Messenger().post("Dados Alterados com sucesso. Você será redirecionado para a página de login!");

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
            });
            
          }else{


                       Messenger().post({
   message: 'Atenção, já foram carregados todos os seguidores da @{{nickName|raw}}!',
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
// editar Dados pessoais !-->