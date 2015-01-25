  //Ajax Carrega mais seguidores na modal
  jQuery(document).ready(function(){
    jQuery('.LoadMoreSeguidores').live("submit",function(){
      var dados = jQuery( this ).serialize();

    // ID do box
    var ID = $(this).attr("id");

    var boxID = '#boxID';

    // tipoBox
    var tipoExclusao = $('.tipoExclusao'+ID).val();

    var divBoxID = boxID+ID;
 
      jQuery.ajax({
        type: "POST",
        url: "/listar-seguidores-grupos/",
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data > ''){

              $('.contentGrupoSeguidores').append(data);

              document.getElementById('FormLoadMoreSeguidores').remove();
            
          }else{

            document.getElementById('FormLoadMoreSeguidores').remove();

                       Messenger().post({
   message: 'Atenção, já foram carregados todos os usuários deste grupo!',
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