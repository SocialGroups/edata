// deleta Comentário 
jQuery(document).ready(function(){
    jQuery('.deletaComentario').live("submit",function(){

      var dados = jQuery( this ).serialize();


      var ID = $(this).attr("id");

      jQuery.ajax({
        type: "POST",
        url: "/comentarios/deletar-comentario",
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

            $('.labelComentario'+ID).remove();

            Messenger().post("Comentário deletado com sucesso!");

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

            //$('#loadMoreComentarios'+feedID).hide("slow");

              //document.getElementById('loadMoreComentarios'+ID).remove();

              $('.loadMoreComentarios'+ID).remove();

              $('.appendcomentario'+ID).append(data);

            
          }
        



        }
      });

      return false;

    });
});
// Deleta Comentário