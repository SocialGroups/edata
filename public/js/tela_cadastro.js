$("#buttonVoltar").on("click", function() {
	window.history.back();
});

var changed_cadastro = false;
var changed_permissoes = false;
var oTable1;

$(document).ready(function() {

	$('#form_cadastro input').on('change', function() {
		changed_cadastro = true;
	});

	$('#form_configuracoes input').on('change', function() {
		changed_permissoes = true;
	});

	$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
		_title: function(title) {
			var $title = this.options.title || '&nbsp;'
			if( ("title_html" in this.options) && this.options.title_html == true )
				title.html($title);
			else title.text($title);
		}
	}));

	$( ".botaoAba").on("click", function(event) {
			event.preventDefault();
			aba = $(this);
			

			if(id_tela == '') {
				$.gritter.add({
					title: 'Atenção',
					text: 'É necessário incluir a tela antes de acessar as permissões',
					class_name: 'gritter-error'
				});
				return false;
			}
			// return false;
			if((changed_cadastro && aba.attr("href") == "#permissoes") || (changed_permissoes && aba.attr("href") == "#tela")) {
				$( "#dialog-confirm" ).removeClass('hide').dialog({
					resizable: false,
					modal: true,
					title_html: true,
					title: "<div class='widget-header' style='width: 100% !important;'><h4 class='smaller'><i class='icon-warning-sign red'></i> ATENÇÃO</h4></div>",
					buttons: [
						{
							html: "<i class='icon-trash bigger-110'></i>&nbsp; Confirmar",
							"class" : "btn btn-danger btn-xs",
							click: function() {
								$(this).dialog( "close" );
								aba.tab('show');
								changed = false;
								return false;						
							}
						},
						{
							html: "<i class='icon-remove bigger-110'></i>&nbsp; Cancelar",
							"class" : "btn btn-xs",
							click: function() {
								$( this ).dialog( "close" );
								return false;
							}
						}
					]
				});
				return false;
				
			} else {
				$(this).tab('show');
				return false;
			}
		
			//alert('teste');

			// 
		
	});

	$('.submit-cadastro').on("click", function() {
		if( $('#form_cadastro').valid() ) {
			// console.log($('.widget-telefone .input-group .input-mask-phone'));
			
				

			$.ajax({
				url: '/telas/',
				type: 'PUT',
				data: $('#form_cadastro').serialize(),
				dataType: 'json'
			}).done(function(retorno) {

				// oTable1.fnDraw();
				// console.log(retorno);

				if(retorno.result == true) {

					// console.log(id_unidade);

					if(id_tela == '') {
						$.gritter.add({
							title: 'ATENÇÃO',
							text: 'Não esqueça de acessar a aba de permissões da tela.',
							class_name: 'gritter-info gritter-center gritter-light'
						});
					}
					
					changed_cadastro = false;
					id_tela = retorno.item.id;

					$("#id_tela").val(id_tela);
					$("#tela_id_permissao").val(id_tela);

					// DADOS EDITADOS COM SUCESSO
					$.gritter.add({
						title: 'Dados gravados com sucesso',
						text: '' ,
						class_name: 'gritter-success'
					});
				
				} else {

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
		
			

		} else {
			$.gritter.add({
				title: 'Atenção',
				text: 'Corrija os erros do formulário' ,
				class_name: 'gritter-error'
			});
		}
		
	});

	$("#btn-cadastrar-permissao").on("click", function() {
		$("#form_permissao").submit();
		
	});

	$("#form_permissao").validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: true,
		rules: {
			nome: 'required'			
		},
		messages: {
			nome: {
				required: " "
			}
		},

		highlight: function (e) {
			$(e).closest('.form-group').addClass('has-error');
		},

		success: function (e) {
			campo = e.attr("for");
			$("#"+campo).closest('div.form-group').removeClass('has-error');
			$(e).remove();
		},

		errorPlacement: function (error, element) {
			name = element.attr("name");
			error.appendTo($("#errorContainer ."+name));
		},

		submitHandler: function (form) {

			idPermissao = $("#permissao_id").val();
			if(idPermissao != '') {
				urlAjax = '/telas/permissao/edit';
			} else {
				urlAjax = '/telas/permissao/insert';
			}
			// console.log(urlAjax);

			$.ajax({
				url: urlAjax,
				type: 'POST',
				data: $("#form_permissao").serialize(),
				dataType: 'json'
			}).done(function(retorno) {
				if(retorno.result == true) {
						
					console.log('teste');
					// DADOS EDITADOS COM SUCESSO
					$.gritter.add({
						title: 'Dados gravados com sucesso',
						text: '' ,
						class_name: 'gritter-success'
					});

					$("#permissao_id").val("");
					$("#divBtnCancelar").hide();

					oTable1.fnDraw();
					$("#form_permissao").each( function () {
						this.reset();
					});
				
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

	$("#btn-cancelar-permissao").on("click", function() {
		cancelarEdicaoPermissao();
	});


	$('#form_cadastro').validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: false,
		rules: {
			nome: 'required'			
		},

		messages: {
			nome: {
				required: " "
			}
		},

		invalidHandler: function (event, validator) { //display error alert on form submit   
			// $.gritter.add({
			// 	title: 'Atenção',
			// 	text: 'Corrija os erros do formulário',
			// 	class_name: 'gritter-error'
			// });
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
		},
		invalidHandler: function (form) {
		}
	});



	oTable1 = $('#table_results').dataTable( {
		"bFilter": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/telas/permissao/ajax/"+id_tela,
		"fnDrawCallback": function () {
			
			$("#table_results_filter").hide();	
		    $('.btn_button_edit').on("click", function() {  // an action to trigger editing
		    	
		    	editarPermissao(this);
			});
			$('.btn_button_delete').on("click", function(e) {  // an action to trigger editing
		    	
		    	apagarPermissao(this, e);
			});

        },
		"oLanguage": {
            "sUrl": "/assets/js/dataTable_ptBR.js"
        },
		"aoColumns": [
	      
		      null, { "bSortable": false }, null, { "bSortable": false }
			] 
		}
		
	);


});

apagarPermissao = function(item, e) {

	e.preventDefault();

	var mensagem = 'Essa ação não poderá ser desfeita e irá ';
	mensagem += 'apagar';
	mensagem += ' esse registro?';
	$( "#msgDialog-apagar" ).html(mensagem);

	id = $(item).attr("idDelete");
		
	$( "#dialog-confirm-apagar" ).removeClass('hide').dialog({
		resizable: false,
		modal: true,
		title_html: true,
		title: "<div class='widget-header' style='width: 100% !important;'><h4 class='smaller'><i class='icon-warning-sign red'></i> ATENÇÃO</h4></div>",
		buttons: [
			{
				html: "<i class='icon-trash bigger-110'></i>&nbsp; Confirmar",
				"class" : "btn btn-danger btn-xs",
				click: function() {

					$.ajax({
						url: '/telas/permissao/',
						type: 'DELETE',
						data: { idItem: id },
						dataType: 'json'
					}).done(function(retorno) {

						// oTable1.fnDraw();
						// console.log(retorno);

						if(retorno.result == true) {
							
							msg = "Registro apagado com sucesso";
							
							// DADOS EDITADOS COM SUCESSO
							$.gritter.add({
								title: msg,
								text: '' ,
								class_name: 'gritter-success'
							});

							oTable1.fnDraw();
						
						} else {

							// ERRO AO INSERIR OS DADOS
							var display_errors = "";
							$.each(retorno.errors, function(i, val) {
								if(val != "") {
									display_errors += val+"<br />";
								}
							});

							$.gritter.add({
								title: 'Dados não alterados:',
								text: display_errors ,
								sticky: true,
								class_name: 'gritter-error'
							});
						}
						
					});

					
					$( this ).dialog( "close" );
				}
			}
			,
			{
				html: "<i class='icon-remove bigger-110'></i>&nbsp; Cancelar",
				"class" : "btn btn-xs",
				click: function() {
					$( this ).dialog( "close" );
				}
			}
		]
	});
};

cancelarEdicaoPermissao = function() {
	$("#permissao_id").val("");
	$("#nome_permissao").val("");

	$("#divBtnCancelar").hide();
};
editarPermissao = function(el)
{
	id = $(el).attr("idEditar");
	nome = $(el).attr("nome");

	$("#permissao_id").val(id);
	$("#nome_permissao").val(nome);

	$("#divBtnCancelar").show();
};