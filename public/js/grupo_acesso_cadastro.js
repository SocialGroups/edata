$("#buttonVoltar").on("click", function() {
	window.history.back();
});

var changed_cadastro = false;
var changed_permissoes = false;
var oTable1;

// var selectTreeItem = function ($treeEl, item, $parentEl) {
// 		var $parentEl = $parentEl || $treeEl;
// 		if (item.type == "item") {
// 				var $itemEl = $parentEl.find("div.tree-item-name").filter(function (_, treeItem) {
// 						return $(treeItem).text() == item.name && !$(treeItem).parent().is(".tree-selected");
// 				}).parent();
// 				$treeEl.tree("selectItem", $itemEl);
// 		}
// 		else if (item.type == "folder") {
// 				selectTreeFolder($treeEl, item, $parentEl);
// 		}
// };

// var selectTreeFolder = function($treeEl, folder, $parentEl) {
// 		var $parentEl = $parentEl || $treeEl;
// 		if (folder.type == "folder") {
// 				var $folderEl = $parentEl.find("div.tree-folder-name").filter(function (_, treeFolder) {
// 						return $(treeFolder).text() == folder.name;
// 				}).parent();
// 				$treeEl.one("loaded", function () {
// 						$.each(folder.children, function (i, item) {
// 								selectTreeFolder($treeEl, item, $folderEl.parent());
// 						});
// 				});
// 				$treeEl.tree("selectFolder", $folderEl);
// 		}
// 		else {
// 				selectTreeItem($treeEl, folder, $parentEl);
// 		}
// };

$(document).ready(function() {

	$.ajax({
		url: '/ajax/telas-permissoes',
		type: 'POST',
		data: {grupo_id: grupo_acesso_id},
		dataType: 'json'
	}).done(function(retorno) {

		var treeDataSource = new DataSourceTree({data: retorno});
		// console.log(treeDataSource);

		$('#tree1').ace_tree({
			dataSource: treeDataSource ,
			multiSelect:true,
			loadingHTML:'<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
			'open-icon' : 'icon-minus',
			'close-icon' : 'icon-plus',
			'selectable' : true,
			'selected-icon' : 'icon-ok',
			'unselected-icon' : 'icon-remove'
		});

		// Abrir todas as opções
		$("#tree1 .icon-plus").trigger("click");

	
		$("#tree1 .tree-folder-header").on("click", function(e) {
			return false;
			e.preventDefault();
		});

	});


	$('#form_cadastro input').on('change', function() {
		changed_cadastro = true;
	});

	$('.submit-permissoes').on('click', function() {
		var selecionados = $("#tree1").tree('selectedItems');

		var dataArray = {};
		dataArray['id_grupo_acesso'] = grupo_acesso_id;
		dataArray['selecionados'] = new Array();
		for( i = 0; i < selecionados.length; i++) {
			dataArray['selecionados'][selecionados[i].additionalParameters.id] = selecionados[i].additionalParameters.id;
		}

		$.ajax({
			url: '/grupo-acesso/permissao',
			type: 'PUT',
			data: dataArray,
			dataType: 'json'
		}).done(function(retorno) {
			if(retorno.result == true) {				
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
			

			if(grupo_acesso_id == '') {
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
				
	});

	$('.submit-cadastro').on("click", function() {
		if( $('#form_cadastro').valid() ) {
			
			$.ajax({
				url: '/grupo-acesso/',
				type: 'PUT',
				data: $('#form_cadastro').serialize(),
				dataType: 'json'
			}).done(function(retorno) {

				if(retorno.result == true) {

					if(grupo_acesso_id == '') {
						$.gritter.add({
							title: 'ATENÇÃO',
							text: 'Não esqueça de acessar a aba de permissões da tela.',
							class_name: 'gritter-info gritter-center gritter-light'
						});
					}
					
					changed_cadastro = false;
					id_grupo_acesso = retorno.item.id;

					$("#grupo_acesso_id").val(id_grupo_acesso);
					$("#grupo_acesso_id_permissao").val(id_grupo_acesso);

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
						url: '/grupo-acesso/permissao/',
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