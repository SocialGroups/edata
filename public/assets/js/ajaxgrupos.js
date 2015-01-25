  // Seguir Grupo 
  jQuery(document).ready(function(){
    jQuery('#SeguirGrupo').live("submit",function(){
      var dados = jQuery( this ).serialize();

		// ID do box
		var ID = $(this).attr("id");

		var boxID = '#boxID';

		var grupoID = document.getElementById('grupoID').value;

		// tipoBox
		var tipoExclusao = $('.tipoExclusao'+ID).val();
 
      jQuery.ajax({
        type: "POST",
        url: "/seguirgrupo/",
    	data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

            setTimeout(function () { location.reload(true); }, 3000);

          	$('#SeguirGrupo').hide("slow");

          	 $('.formFollowUnfollow').append('<form name="unfollow" id="unfollow" action="" method="POST" class="unfollow"><input type="hidden" name="tipoFollow" value="unfollow"><input type="hidden" name="grupoID" value="'+grupoID+'"><input type="submit" class="btn btn-primary btn-small" id="buttonSeguirGrupo" value="Deixar Grupo" ></form>');

          	// limpa todos os campos do form

Messenger().post("Parabéns, agora você esta seguindo este grupo e pode acompanhar os seus boxes a partir do seu feed de notícias");

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

// Seguir Grupo !-->

// Unfollow Grupo !-->
  jQuery(document).ready(function(){
    jQuery('#unfollow').live("submit",function(){
      var dados = jQuery( this ).serialize();

		// ID do box
		var ID = $(this).attr("id");

		var boxID = '#boxID';

		var grupoID = document.getElementById('grupoID').value;

		// tipoBox
		var tipoExclusao = $('.tipoExclusao'+ID).val();
 
      jQuery.ajax({
        type: "POST",
        url: "/seguirgrupo/",
    	data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

          	$('#unfollow').hide("slow");

            $('#adicionarbox').remove();

          	 $('.formFollowUnfollow').append('<form name="SeguirGrupo" id="SeguirGrupo" action="" method="POST" class="formSeguir"><input type="hidden" name="grupoID" id="grupoID" value="'+grupoID+'"><input type="submit" class="btn btn-primary btn-small" id="buttonSeguirGrupo" value="+ Seguir grupo" ></form>');

          	// limpa todos os campos do form

Messenger().post("Você não esta mais seguindo este grupo!");

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
	 message: 'Erro, você não segue este grupo!',
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

// Unfollow Grupo !-->


// Excluir Box !-->
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
	// Excluir Box !-->

// Compartilhamento !-->
  jQuery(document).ready(function(){
    jQuery('.compartilhou').live("submit",function(){
      var dados = jQuery( this ).serialize();

		// ID do box
		var ID = $(this).attr("id");

		var boxID = '#boxID';

		// tipoBox
		var tipoExclusao = $('.tipoExclusao'+ID).val();

		var divBoxID = boxID+ID;
 
      jQuery.ajax({
        type: "POST",
        url: "/compartilhar/"+ID,
    	data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

          	$('#myModal'+ID).modal('hide');


Messenger().post("Box compartilhando com sucesso!");

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

          	$('#myModal'+ID).modal('hide');


          	Messenger().post({
	 message: 'Erro, você só pode compartihar um box com um grupo que você siga!',
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
// Compartilhamento !-->


$('#grupoIDCompartilhamento').ajaxChosen({
                   dataType: 'json',
                   type: 'POST',
                   url:'/ajax/'
           },{
                   loadingImg: 'loading.gif'
           });


// Get Comentários !-->
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
//Get Comentários !-->


<!-- Adiciona Comentário !-->
<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('.adicionaComentatio').live("submit",function(){
      var dados = jQuery( this ).serialize();

		// ID do box
		var ID = $(this).attr("id");

		var boxID = '#boxID';

		// tipoBox
		var tipoExclusao = $('.tipoExclusao'+ID).val();

		var conteudoComentario 	= document.getElementById('conteudoComentarioBox'+ID).value;
		var nomeAutor 			= document.getElementById('nomeAutor'+ID).value;
		var fotoAutor 			= document.getElementById('fotoAutor'+ID).value;

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

// Adiciona Comentário !-->


// Denuncía box !-->
  jQuery(document).ready(function(){
    jQuery('.denunciaBox').live("submit",function(){
      var dados = jQuery( this ).serialize();

    // ID do box
    var ID = $(this).attr("id");

    var boxID = '#boxID';

    // tipoBox
    var tipoExclusao = $('.tipoExclusao'+ID).val();

    var divBoxID = boxID+ID;
 
      jQuery.ajax({
        type: "POST",
        url: "/denuncia/"+ID,
      data: dados,
        success: function( data )
        {
          //alert(  );

          if(data == 'true'){

            $('#denunciar'+ID).modal('hide');


Messenger().post("Denuncia efetuada com sucesso!");

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

          }else if(data == 'repetida'){

            $('#myModal'+ID).modal('hide');


            Messenger().post({
   message: 'Erro, você não pode denunciar o mesmo box mais de uma vez!',
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

            $('#myModal'+ID).modal('hide');


            Messenger().post({
   message: 'Erro, você precisa selecionar o motivo da denuncia!',
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
// Denuncía box !-->


// Adiciona Foto Capa Grupo !-->
  jQuery(document).ready(function(){
    jQuery('.adicionarGrupo').live("submit",function(){

            Messenger().post({
   message: 'Estamos enviando sua foto para nossos servidores. Por favor aguarde...',
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

});
});
</script>


<!-- Alterar foto de capa !-->
{% if returnacao|raw == 'alterarCapaGrupo' %}
<script>
var stateObj = { foo: "bar" };
history.pushState(stateObj, "", "/grupos/get/{{grupoID|raw}}");

Messenger().post("Foto de capa alterada com sucesso!");

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

{% endif %}
// Alterar foto de capa !-->


<!-- Ajax Carrega mais seguidores na modal !-->
<script type="text/javascript">
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
</script>
<!-- Ajax Carrega mais seguidores na modal !-->


<!-- deleta Comentário !-->
<script>
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
</script>
<!-- Deleta Comentário !-->