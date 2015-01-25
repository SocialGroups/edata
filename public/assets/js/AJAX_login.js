  jQuery(document).ready(function(){
    jQuery('#frm_login').live("submit",function(){
      var dados = jQuery( this ).serialize();
 
      jQuery.ajax({
        type: "POST",
        url: "/login/",
        data: dados,
            success: function( data )
            {
              //alert(  );

              if(data == true){

                    setTimeout(function () {
                      window.location.href = "/home"; //will redirect to your blog page (an ex: blog.html)
                    }, 1000); //will call the function after 2 secs.
                    // limpa todos os campos do form

                    Messenger().post("Login efetuado com sucesso!. Aguarde...");

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

         message: 'Erro, os dados de acesso não estão corretos. Por favor tente novamente!',
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