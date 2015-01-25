// Criar Grupo parte 2 Modal
  jQuery(document).ready(function(){
    jQuery('.criarGrupo').live("submit",function(){
      
      var dados = jQuery( this ).serialize();
 
      jQuery.ajax({
        type: "POST",
        url: '/criargrupo',
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data > ''){

           $('#CriarGrupo').modal('hide');

           Messenger().post("Grupo Criado com sucesso! você será redirecionado para a pagina do seu grupo em breve...");

           setTimeout(function () {
       window.location.href = "/grupos/get/"+data; //will redirect to your blog page (an ex: blog.html)
    }, 2000); //will call the function after 2 secs.

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
   message: 'Erro, você precisa preencher o nome do grupo!',
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