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
	$("#logradouro").val(dados.endereco);
	$("#bairro").val(dados.bairro);
	$("#estado").val(dados.estado);
	$("#cidade").val(dados.cidade);

	$("#logradouro").prop("readonly", true);
	$("#bairro").prop("readonly", true);
	$("#estado").prop("readonly", true);
	$("#cidade").prop("readonly", true);

	$("#numero").focus();
	hideLoader();
};

habilitaEnderecoManual = function() {
	$("#logradouro").prop("readonly", false);
	$("#bairro").prop("readonly", false);
	$("#estado").prop("readonly", false);
	$("#cidade").prop("readonly", false);

	$("#logradouro").val("");
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

cpfFocusOut = function() 
{
	var element;
    element = $(this);
    
    element.mask("999.999.999-99");
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
var oTableOrcamento;

calculaIMC = function () {
	peso = $("#peso").val().replace('.','').replace(',','.');
	altura = $("#altura").val().replace('.','').replace(',','.');

	altura2 = altura * altura;
	imc = peso / altura2;
	imc = imc.toFixed(2);
	$("#imc").val(imc)

	// alert(peso);
	// alert(altura);


};

$(document).ready(function() {

	$(".dinheiro").maskMoney({showSymbol:true, symbol:"R$ ", decimal:",", thousands:"."});
	$(".numero").maskMoney({showSymbol:false, decimal:",", thousands:"."});

	$(".imc").on("click", calculaIMC );

	$(".cep").mask("99999-999",{placeholder:"_",completed: consultaCEP});

	$('.input-mask-phone').focusout(telefoneFocusOut).trigger('focusout');

	$('.input-mask-phone').mask('(99) 9999-9999?9');
	$('.cpf').mask('999.999.999-99');
	$('.cpf').focusout(cpfFocusOut).trigger('focusout');

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
			

			if(id_cliente == '') {
				$.gritter.add({
					title: 'Atenção',
					text: 'É necessário incluir o cliente unidade antes de acessar as outras abas',
					class_name: 'gritter-error'
				});
				return false;
			}
			// return false;
			if(
				(changed_cadastro && aba.attr("href") == "#configuracoes") || 
				(changed_configuracoes && aba.attr("href") == "#unidade")
			) {
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

	$(".submit-cadastro").on("click", function() {
		$("#form_cadastro").submit();	
	});
	
	$("#form_cadastro").validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: true,
		rules: {
			nome: 'required',
			data_nascimento: 'required',
			unidade_id: 'required',
			email: 'email',
			email_secundario: 'email'
			
		},
		messages: {
			nome: {
				required: " "
			},
			data_nascimento: {
				required: " "
			},
			unidade_id: {
				required: " "
			},
			email: {
				email: " "
			},
			email_secundario: {
				email: " "
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
					return false;
				}
			});


			$.ajax({
				url: '/cliente/',
				type: 'PUT',
				data: $("#form_cadastro").serialize(),
				dataType: 'json'
			}).done(function(retorno) {
				if(retorno.result == true) {
						
					changed_cadastro = false;
					id_cliente = retorno.item.id;


					// DADOS EDITADOS COM SUCESSO
					$.gritter.add({
						title: 'Dados gravados com sucesso',
						text: '' ,
						class_name: 'gritter-success'
					});

					$("#id_cliente").val(id_cliente);

				
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
	




	oTableOrcamento = $('#table_results').dataTable( {
		"bFilter": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/orcamento/ajax/"+id_cliente,
		"fnDrawCallback": function () {
			
			btnNovoOrcamento = 
							'<button type="button" id="btn-novo-orcamento" class="btn btn-sm btn-success" style="margin-top: -2px;">'+
							'	Novo Orçamento <i class="icon-arrow-right icon-on-right bigger-110"></i>'+
							'</button>';
			$("#table_results_filter").html(btnNovoOrcamento);	
			$("#btn-novo-orcamento").on("click", function () {
				$("#idClienteOrcamento").val(id_cliente);
				$("#fmrNovoOrcamento").submit();
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
