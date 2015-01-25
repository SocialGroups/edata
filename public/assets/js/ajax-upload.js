$(function()
{

    // Variable to store your files
    var files;

    // Add events
    $('input[type=file]').on('change', prepareUpload);
    $('#adicionarbox').on('submit', uploadFiles);


    // Grab the files and set them to our variable
    function prepareUpload(event)
    {
        files = event.target.files;
    }

    // Catch the form submit and upload the files
    function uploadFiles(event)
    {

        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening

        // START A LOADING SPINNER HERE

        var verificaFile = document.getElementById('file_upload').value;
        var conteudo     = document.getElementById('conteudo').value;

        if(conteudo == ''){

        Messenger().post({
        message: 'Erro, você precisa preencher um conteúdo para adicionar um box!',
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


        if(verificaFile == ''){

            var dados = jQuery( this ).serialize();

            jQuery.ajax({
            type: "POST",
            url: "/insertbox/postbox",
            data: dados,
            success: function( data )
            {
          //alert(  );

          if(data == true){

            setTimeout(function () { location.reload(true); }, 1000);

Messenger().post("Box adicionado com sucesso!");

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

  $tsel.themeSelector({
    themes: ['flat', 'future', 'block', 'air', 'ice']
  }).on('update', function(theme){
    style = theme;

    update();
  });

}else{


            Messenger().post({
            message: 'Erro, você precisa preencher um conteúdo para adicionar um box!',
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

        }

        // Create a formdata object and add the files
        var data = new FormData();
        $.each(files, function(key, value)
        {
            data.append(key, value);
        });

        // Enviando

        Messenger().post({
            message: 'Estamos processando o post do seu box. Por favor aguarde...',
            type: 'error',
            delay: 1,
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

  


        // Enviando
        
        $.ajax({
            url: '/insertbox/uploads3',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(data, textStatus, jqXHR)
            {
                if(typeof data.error === 'undefined')
                {
                    // Success so call function to process the form
                    submitForm(event, data);
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                // STOP LOADING SPINNER
            }
        });
    }

    function submitForm(event, data)
    {
        // Create a jQuery object from the form
        $form = $(event.target);
        
        // Serialize the form data
        var formData = $form.serialize();
        
        // You should sterilise the file names
        $.each(data.files, function(key, value)
        {
            formData = formData + '&filenames[]=' + value;
        });

        $.ajax({
            url: '/insertbox/postbox',
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR)
            {
                if(typeof data.error === 'undefined')
                {   
                    setTimeout(function () { location.reload(true); }, 1000);
                    // Success so call function to process the form
                    console.log('SUCCESS: ' + data.success);

                    // aciona alert de sucesso jquery
                    Messenger().post("Box adicionado com sucesso!");

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
                    // aciona alert de sucesso jquery
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
            },
            complete: function()
            {
                // STOP LOADING SPINNER
            }
        });
    }
});