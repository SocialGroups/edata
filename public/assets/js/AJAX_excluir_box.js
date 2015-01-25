  // Excluir Box 
  jQuery(document).ready(function(){
    jQuery('.deletar').live("submit",function(){
      var dados = jQuery( this ).serialize();

    // ID do box
    var ID = $(this).attr("id");

    var boxID = '#boxID';

    // tipoBox
    var tipoExclusao = $('.tipoExclusao'+ID).val();

    var divBoxID = boxID+ID;
 
      jQuery.ajax({
        type: "POST",
        url: "/deletar",
        data: {boxID: ID, tipoExclusao: tipoExclusao},
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){


            $(divBoxID).hide("slow");



Messenger().post("Box deletado com sucesso!");

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


            Messenger().post({
   message: 'Erro, você não tem permição para deletar este box!',
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
            
          }
        }
      });
      
      return false;
    });
  });
// Excluir Box 