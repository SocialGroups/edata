// Seguir Usuário
  jQuery(document).ready(function(){
    jQuery('#SeguirUsuario').live("submit",function(){
      var dados = jQuery( this ).serialize();

    // ID do box
    var ID = $(this).attr("id");

    var followID = document.getElementById('followID').value;

    // tipoBox
    var tipoExclusao = $('.tipoExclusao'+ID).val();
 
      jQuery.ajax({
        type: "POST",
        url: "/profile/acaoseguir/",
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

            //$('#SeguirUsuario').hide("slow");
            $('#SeguirUsuario').remove();

             $('.formFollowUnfollow').append('<form name="unfollow" id="unfollow" action="" method="POST" class="unfollow" style="float:left; margin-right:15px; margin-bottom:5px;"><input type="hidden" name="tipoExclusao" value="usuarioUnfollow"><input type="hidden" name="unfollowID" id="unfollowID" value="'+followID+'"><input type="submit" class="btn btn-primary btn-small" id="unfollow" value="Deixar de seguir"></form>');

            // limpa todos os campos do form

Messenger().post("Parabéns, agora você esta seguindo @{{nickName|raw}} e pode acompanhar as suas publicações a partir do seu feed de notícias.");

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
   message: 'Erro, já segue este grupo!',
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

// Seguir Usuário