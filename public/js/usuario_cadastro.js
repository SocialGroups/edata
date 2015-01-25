$("#buttonVoltar").on("click", function() {
	window.history.back();
});

$(document).ready(function() {

	$(".chosen-select").chosen(); 

	$(".submit-cadastro").on("click", function() {
		$("#form_cadastro").submit();
		
	});

	var rulesForm = {};
	if(id_usuario == "") {
		rulesForm = {
			nome: 'required',
			login: 'required',
			grupo_acesso_id: 'required',
			email: {required: true, email: true},
			senha: 'required',
			confirmacao_senha: {equalTo: '#senha'}
			
		};
	} else {
		rulesForm = {
			nome: 'required',
			login: 'required',
			grupo_acesso_id: 'required',
			email: {required: true, email: true},
			//senha: 'required',
			confirmacao_senha: {equalTo: '#senha'}
			
		};
	}

	
	$('#form_cadastro').validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: false,
		rules: rulesForm,

		messages: {
			nome: {
				required: " "
			},
			login: {
				required: " "
			},
			grupo_acesso_id: {
				required: " "
			},
			email: {
				required: " ",
				email: "Forneça um email válido"
			},
			senha: {
				required: " "
			},
			confirmacao_senha: {
				equalTo: "As senhas não conferem"
			}
		},

		highlight: function (e) {
			$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
		},

		success: function (e) {
			$(e).closest('div.form-group').removeClass('has-error');
			$(e).remove();
		},

		errorPlacement: function (error, element) {
			if(element.is(':checkbox') || element.is(':radio')) {
				var controls = element.closest('div[class*="col-"]');
				if(controls.find(':checkbox,:radio').length > 1) controls.append(error);
				else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
			}
			else if(element.is('.chosen-select')) {
				error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
			}
			else error.insertAfter(element);
		},

		submitHandler: function (form) {

			$.ajax({
				url: '/usuario/',
				type: 'PUT',
				data: $("#form_cadastro").serialize(),
				dataType: 'json'
			}).done(function(retorno) {
				if(retorno.result == true) {
						
					

					// DADOS EDITADOS COM SUCESSO
					$.gritter.add({
						title: 'Dados gravados com sucesso',
						text: '' ,
						class_name: 'gritter-success'
					});

					$("#id_usuario").val(retorno.id);
									
				} else {
					// console.log(retorno.errors);
					// ERRO AO INSERIR OS DADOS
					var display_errors = "";
					$.each(retorno.errors, function(i, val) {
						if(val != "") {
							display_errors += val+"<br />";
						}
					});

					$.gritter.add({
						title: 'Dados não gravados:',
						text: display_errors ,
						sticky: true,
						class_name: 'gritter-error'
					});
				}
			});
		},
		invalidHandler: function (form) {
			$.gritter.add({
				title: 'Atenção',
				text: 'Corrija os erros do formulário' ,
				class_name: 'gritter-error'
			});
		}
	});




});