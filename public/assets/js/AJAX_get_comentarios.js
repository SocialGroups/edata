// Get Comentários
jQuery(document).ready(function(){
    jQuery('.loadMoreComentarios').live("submit",function(){

      var dados = jQuery( this ).serialize();


      var ID = $(this).attr("id");


      var feedID = document.getElementById('feedID').value;

      jQuery.ajax({
        type: "POST",
        url: "/comentarios/get",
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == false){

            $('.loadMoreComentarios'+ID).remove();

            
                 Messenger().post({
   message: 'Não há mais comentários neste box!',
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

  $tsel.themeSelector({
    themes: ['flat', 'future', 'block', 'air', 'ice']
  }).on('update', function(theme){
    style = theme;

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
// Get Comentários