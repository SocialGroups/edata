// Adiciona Comentário 
  jQuery(document).ready(function(){
    jQuery('.adicionaComentatio').live("submit",function(){
      var dados = jQuery( this ).serialize();

    // ID do box
    var ID = $(this).attr("id");

    var boxID = '#boxID';

    // tipoBox
    var tipoExclusao = $('.tipoExclusao'+ID).val();

    var conteudoComentario  = document.getElementById('conteudoComentarioBox'+ID).value;
    var nomeAutor       = document.getElementById('nomeAutor'+ID).value;
    var fotoAutor       = document.getElementById('fotoAutor'+ID).value;

    var divBoxID = boxID+ID;
 
      jQuery.ajax({
        type: "POST",
        url: "/comentarios/",
      data: dados,
        success: function( data )
        {

          if(data > ''){

            $('[name="conteudo"]').val('');

            $('.appendcomentario'+ID).append('<div class="notification-messages success labelComentario'+data+'"><div class="user-profile"> <img src="'+fotoAutor+'" alt="" data-src="" data-src-retina="assets/img/profiles/h2x.jpg" width="35" height="35"> </div><div class="message-wrapper"><div class="heading"> ' + nomeAutor + ' </div><div class="description">' + conteudoComentario + ' </div></div><div class="date pull-right"> Yesterday </div><div class="clearfix"></div><form name="deletaComentario" class="deletaComentario deletaComentario'+data+'" id="'+data+'" action="" method="POST" style="font-size:12px;"><input type="hidden" name="comentarioID" value="'+data+'"><input type="submit" class="btn btn-link btn-cons" value="Excluir Comentário"></form></div>');

            // limpa todos os campos do form

Messenger().post("Comentário Adicionado com sucesso!");

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
   message: 'Erro, você não tem permição para adicionar comentários neste box!',
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

// Adiciona Comentário