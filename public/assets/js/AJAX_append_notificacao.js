// Exibe Notificação com append 
  jQuery(document).ready(function(){
    jQuery('.exibirNotificacaoPendente').live("click",function(){
      
      var dados = jQuery( this ).serialize();

      // ID do box
      var href = this.href;
 
      jQuery.ajax({
        type: "POST",
        url: href,
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data > ''){

            $('.popover').hide(0);
            $('.appendNumeroNotificacoesPendentes').remove();

            $('.appendNotificacaoPendente').append(data);

            var NewNumeroNotificacoesPendentes = {{numeroNotificacoesPendentes|raw}}-1;

            $('.ContentAppendNumeroNotificacoes').append('<div class="appendNumeroNotificacoesPendentes"><span class="badge badge-important"style="margin:5px;">'+NewNumeroNotificacoesPendentes+'</span>Notificações Pendentes</span></div> ');


                              // Photoset Grid Basic
      $('.photoset-grid-basic').photosetGrid({
        onComplete: function(){
          $('.photoset-grid-basic').attr('style', '');
        }
      });

      // Photoset Grid Custom
      $('.photoset-grid-custom').photosetGrid({
        gutter: '5px',
        layout: '234',
        highresLinks: true,
        rel: 'print-gallery',

        onComplete: function(){
          $('.photoset-grid-custom').attr('style', '');
        }
      });

     $('.photoset-grid-lightbox').photosetGrid({
      highresLinks: true,
      rel: 'withhearts-gallery',
      gutter: '2px',

      onComplete: function(){
        $('.photoset-grid-lightbox').attr('style', '');
        $('.photoset-grid-lightbox a').colorbox({
          photo: true,
          scalePhotos: true,
          maxHeight:'100%',
          maxWidth:'100%'
        });
      }
    });
    // Photoset Grid Basic

            //$('#EditarDados').modal('hide');
            
          }else{


                       Messenger().post({
   message: 'você não tem permição para visualizar este box!',
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
  // Exibe Notificação com append 