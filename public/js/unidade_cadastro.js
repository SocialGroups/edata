$("#buttonVoltar").on("click", function() {
	window.history.back();
});


consultaCEPInternet = function(cep)
{
	$.ajax({
		url: 'http://cep.republicavirtual.com.br/web_cep.php?cep='+cep+'&formato=json',
		type: 'GET',
		dataType: 'json'
	}).done(function(retorno) {

		// console.log(retorno);
		if(retorno.resultado == '1') {
			novoRetorno = {};
			novoRetorno.endereco = retorno.tipo_logradouro+" "+retorno.logradouro;
			novoRetorno.bairro = retorno.bairro;
			novoRetorno.estado = retorno.uf;
			novoRetorno.cidade = retorno.cidade;
			preencheEndereco(novoRetorno);
		} else {
			habilitaEnderecoManual();
		}

	});
};

showLoader = function() {
	// console.log($(".ajaxloader"));
	$(".ajaxloader").removeClass("hide");
	// console.log($(".ajaxloader"));
};

hideLoader = function() {
	$(".ajaxloader").addClass("hide");
};

preencheEndereco = function(dados) {
	$("#endereco").val(dados.endereco);
	$("#bairro").val(dados.bairro);
	$("#estado").val(dados.estado);
	$("#cidade").val(dados.cidade);

	$("#endereco").prop("readonly", true);
	$("#bairro").prop("readonly", true);
	$("#estado").prop("readonly", true);
	$("#cidade").prop("readonly", true);

	$("#numero").focus();
	hideLoader();
};

habilitaEnderecoManual = function() {
	$("#endereco").prop("readonly", false);
	$("#bairro").prop("readonly", false);
	$("#estado").prop("readonly", false);
	$("#cidade").prop("readonly", false);

	$("#endereco").val("");
	$("#bairro").val("");
	$("#estado").val("");
	$("#cidade").val("");

	$("#endereco").focus();
	hideLoader();
};

consultaCEP = function()
{
	showLoader();
	cep = $("#cep").val();

	$.ajax({
		url: '/ajax/cep/'+cep,
		type: 'GET',
		dataType: 'json'
	}).done(function(retorno) {
		if(retorno == false) {
			consultaCEPInternet(cep);						
		} else {
			preencheEndereco(retorno);
		}

	});
};


adicionarTelefone = function()
{
	var divPrincipal = $(".widget-telefone");
	var firstDivClone = $("div:first", divPrincipal).clone();   

	divPrincipal.append(firstDivClone);
	$("input:text", firstDivClone).val("").mask("(99) 9999-9999?9").focusout(telefoneFocusOut).trigger('focusout');
	
	lastSpan = $("span:last", firstDivClone);
	botao = $("button:last", lastSpan);			
	botao.on("click", excluirTelefone);
}

excluirTelefone = function()
{
	if($('.widget-telefone .input-group').length == 1) {
		$.gritter.add({
			title: 'Atenção',
			text: 'É necessário ter ao menos um telefone cadastrado',
			class_name: 'gritter-error'
		});

	} else {
		$(this).closest('.input-group').popover('destroy');
		$(this).closest('.input-group').remove();
	}
}

cnpjFocusOut = function() 
{
	var cnpj, element;
    element = $(this);
    
    element.mask("999.999.999/9999-99");
}

telefoneFocusOut = function()
{
	var phone, element;
    element = $(this);
    element.unmask();
    phone = element.val().replace(/\D/g, '');
    if(phone.length > 10) {
        element.mask("(99) 99999-999?9");
    } else {
        element.mask("(99) 9999-9999?9");
    }
    $(this).closest(".input-group").removeClass('has-error');
}

var changed_cadastro = false;
var changed_configuracoes = false;
var oTable1;

$(document).ready(function() {


	$(".cep").mask("99999-999",{placeholder:"_",completed: consultaCEP});

	$('.input-mask-phone').focusout(telefoneFocusOut).trigger('focusout');

	$('.input-mask-phone').mask('(99) 9999-9999?9');
	$('.cnpj').mask('999.999.999/9999-99');
	$('.cnpj').focusout(cnpjFocusOut).trigger('focusout');

	$(".chosen-select").chosen(); 

	$('.btn-excluir-telefone').on("click", excluirTelefone);

	$('.adicionarTelefone').on("click", adicionarTelefone);

	$('.format_data').datepicker({
	    format: 'dd/mm/yyyy',
	    language: 'pt-BR',
	    autoclose: true
	}).prev().on(ace.click_event, function(){
		$(this).next().focus();
	});

	$('.format_hora').timepicker({
		minuteStep: 1,
		showSeconds: false,
		showMeridian: false,
		defaultTime: false
	}).next().on(ace.click_event, function(){
		$(this).prev().focus();
	});

	$('#form_cadastro input').on('change', function() {
		changed_cadastro = true;
	});

	$('#form_configuracoes input').on('change', function() {
		changed_configuracoes = true;
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
			

			if(id_unidade == '') {
				$.gritter.add({
					title: 'Atenção',
					text: 'É necessário incluir a unidade antes de acessar as configurações',
					class_name: 'gritter-error'
				});
				return false;
			}
			// return false;
			if((changed_cadastro && aba.attr("href") == "#configuracoes") || (changed_configuracoes && aba.attr("href") == "#unidade")) {
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
			var error_telefone = false;
			$('.widget-telefone .input-group .input-mask-phone').each( function(i) {
				if($(this).val() == '') {
					error_telefone = true;
					$(this).closest(".input-group").addClass('has-error');
					$.gritter.add({
						title: 'Atenção',
						text: 'Corrija os erros do formulário' ,
						class_name: 'gritter-error'
					});
				}
			});

			if(!error_telefone) {
				

				$.ajax({
					url: '/unidade/',
					type: 'PUT',
					data: $('#form_cadastro').serialize(),
					dataType: 'json'
				}).done(function(retorno) {

					// oTable1.fnDraw();
					// console.log(retorno);

					if(retorno.result == true) {

						// console.log(id_unidade);

						if(id_unidade == '') {
							$.gritter.add({
								title: 'ATENÇÃO',
								text: 'Não esqueça de acessar a aba de configurações da unidade.',
								class_name: 'gritter-info gritter-center gritter-light'
							});
						}
						
						changed_cadastro = false;
						id_unidade = retorno.item.id;

						$("#id_unidade").val(id_unidade);
						$("#unidade_id_manutencao").val(id_unidade);
						$("#unidade_id_configuracao").val(id_unidade);

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
			}
			

		} else {
			$.gritter.add({
				title: 'Atenção',
				text: 'Corrija os erros do formulário' ,
				class_name: 'gritter-error'
			});
		}
		
	});

	$("#btn-cadastrar-manutencao").on("click", function() {
		$("#form_manutencao").submit();
		
	});

	$("#form_manutencao").validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: true,
		rules: {
			data_inicio: 'required',
			hora_inicio: {
				required: true,
				maxlength: 5
			},
			hora_fim: {
				required: false,
				maxlength: 5
			},
			quantidade: 'required',
			tipo: 'required'
			
		},
		messages: {
			data_inicio: {
				required: " "
			},
			hora_inicio: {
				required: " ",
				maxlength: " "
			},
			quantidade: {
				required: " "
			},
			tipo: {
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

			idManutencao = $("#manutencao_id").val();
			if(idManutencao != '') {
				urlAjax = '/unidade/manutencao/edit';
			} else {
				urlAjax = '/unidade/manutencao/insert';
			}

			$.ajax({
				url: urlAjax,
				type: 'POST',
				data: $("#form_manutencao").serialize(),
				dataType: 'json'
			}).done(function(retorno) {
				if(retorno.result == true) {
						
					// changed_cadastro = false;
					// id_unidade = retorno.item.id;

					// $("#id_unidade").val(id_unidade);
					// $("#unidade_id_manutencao").val(id_unidade);

					// DADOS EDITADOS COM SUCESSO
					$.gritter.add({
						title: 'Dados gravados com sucesso',
						text: '' ,
						class_name: 'gritter-success'
					});

					$("#manutencao_id").val("");
					$("#divBtnCancelar").hide();

					oTable1.fnDraw();
					$("#form_manutencao").each( function () {
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

	$("#btn-gravar-configuracoes").on("click", function() {

		// $("#form_configuracoes").serialize();

		$.ajax({
			url: '/unidade/configuracao/update',
			type: 'POST',
			data: $("#form_configuracoes").serialize(),
			dataType: 'json'
		}).done(function(retorno) {
			if(retorno.result == true) {
					
				changed_configuracoes = false;
				// id_unidade = retorno.item.id;

				// $("#id_unidade").val(id_unidade);
				// $("#unidade_id_manutencao").val(id_unidade);

				// DADOS EDITADOS COM SUCESSO
				$.gritter.add({
					title: 'Dados gravados com sucesso',
					text: '' ,
					class_name: 'gritter-success'
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
	});

	$("#btn-cancelar-manutencao").on("click", function() {
		cancelarEdicaoManutencao();
	});


	$('#form_cadastro').validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: false,
		rules: {
			nome: 'required',
			cnpj: 'required',
			cep: 'required',
			endereco: 'required',
			numero: 'required',
			bairro: 'required',
			cidade: 'required',
			estado: 'required'
			
		},

		messages: {
			nome: {
				required: " "
			},
			cnpj: {
				required: " "
			},
			bairro: {
				required: " "
			},
			cep: {
				required: " "
			},
			endereco: {
				required: " "
			},
			cidade: {
				required: " "
			},
			estado: {
				required: " "
			},
			cep: {
				required: " "
			},
			numero: {
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
		"sAjaxSource": "/unidade/configuracao/ajax/"+id_unidade,
		"fnDrawCallback": function () {
			$("#table_results tbody td .btnAtivar").on("click", function(e) {
				// e.preventDefault()
		    	ativarDesativar($(this).attr('itemID'), $(this).context.checked, e, $(this));
		    	
		    	
		    });

			$("#table_results_filter").hide();	
		    $('.btn_button_edit').on("click", function() {  // an action to trigger editing
		    	
		    	editarManutencao(this);
			});
			$('.btn_button_delete').on("click", function(e) {  // an action to trigger editing
		    	
		    	apagarManutencao(this, e);
			});

        },
		"oLanguage": {
            "sUrl": "/assets/js/dataTable_ptBR.js"
        },
		"aoColumns": [
	      
		      null, null, null, null,
			  { "bSortable": false }
			] 
		}
		
	);


});

apagarManutencao = function(item, e) {

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
						url: '/unidade/manutencao/',
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

cancelarEdicaoManutencao = function() {
	$("#manutencao_id").val("");
	$("#data_inicio").val("");
	$("#hora_inicio").val("");
	$("#data_fim").val("");
	$("#hora_fim").val("");
	$("#quantidade").val("");
	$("#tipo").val("");

	$("#divBtnCancelar").hide();
};
editarManutencao = function(el)
{
	id = $(el).attr("idEditar");
	data_inicio = $(el).attr("data_inicio");
	hora_inicio = $(el).attr("hora_inicio");
	data_fim = $(el).attr("data_fim");
	hora_fim = $(el).attr("hora_fim");
	quantidade = $(el).attr("quantidade");
	tipo = $(el).attr("tipo");

	$("#manutencao_id").val(id);
	$("#data_inicio").val(data_inicio);
	$("#hora_inicio").val(hora_inicio);
	$("#data_fim").val(data_fim);
	$("#hora_fim").val(hora_fim);
	$("#quantidade").val(quantidade);
	$("#tipo").val(tipo);

	$("#divBtnCancelar").show();
};