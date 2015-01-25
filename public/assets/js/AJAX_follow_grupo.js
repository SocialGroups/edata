// Unfollow Grupo
  jQuery(document).ready(function(){
    jQuery('#followGrupo').live("submit",function(){
      
      var dados       = jQuery( this ).serialize();
      var ID          = $(this).attr("id");
      var boxID       = '#boxID';
      var grupoID  = document.getElementById('grupoID').value;
      var tipoExclusao = $('.tipoExclusao'+ID).val();
 
      jQuery.ajax({
        type: "POST",
        url: "/acao-grupo/follow",
        data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

           // $('#unfollow').hide("slow");
            $('#followGrupo').remove();

             $('.formFollowUnfollow').append('<form name="unfollow" id="unfollow" action="" method="POST" class="unfollow" style="float:left; margin-right:15px; margin-bottom:5px;"><input type="hidden" name="tipoFollow" value="unfollow"><input type="hidden" name="grupoID" id="grupoID" value="'+grupoID+'"><input type="submit" class="btn btn-primary btn-small" id="unfollow" value="Deixar Grupo" ></form>');

            // limpa todos os campos do form

Messenger().post("você esta seguindo este grupo. A partir de agora você pode acompanhar todas as novas publicações deste grupo no seu feed de noticias");

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
   message: 'Erro, você não segue este usuário!',
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

// Unfollow Grupo