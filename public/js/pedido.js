var id_item = null;

function ModalUpload(id, url, tipo){
	id_item = id;
	$('#upload_files').val('');
	$('#upload_tipo').val(tipo);
	//$('#modalUploadForm').html(MapaArquivos(id));
	$('#uploadFolder').val(url);
	if(tipo == 'S')
		$('#spTipoUpload').text('Serviço');
	else
		$('#spTipoUpload').text('Produto');

	$('#modalUpload').modal('show');
}

function Resetar(id, titulo, tipo){
	if(tipo == 'P'){
		titulo = 'produto '+titulo;
	}else{
		titulo = 'serviço '+titulo;
	}
	
	$('#id_reset').val(id);
	$('#tipo_reset').val(tipo);
	$('#titulo_reset').text(titulo);
	$('#modalReset').modal('show');
}

function ModalAddServico(){
	$('#modalAddServico').modal('show');
}

function ModalImprimir(id, tipo){
	id_item = id;
	
	$('#msgReimprimir').attr('style','display:none');
	if(tipo == 'P'){
		$('#tipo_impr').val(tipo);
		$('#modalImprimir').modal('show');
	}else{
		Imprimir(tipo);
	}
}

function ModalReimprimir(id){
	id_item = id;
	$('#msgReimprimir').removeAttr('style');
	$('#tipo_impr').val('P');
	$('#modalImprimir').modal('show');
}

function ModalXML(id){
	id_item = id;
	$('#modalXML').modal('show');
}

function Enviar(){
	var _token = document.getElementsByName('_token')[0].value;
	var url = $('#uploadFolder').val();
	var tipo = $('#upload_tipo').val();
	var album = $('#upload_album').val();
	var files = $('#upload_files')[0].files;
	var id_pedido = $('#id').val();
	var renomear = $('#renomear')[0].checked?1:0;
	
	var total = files.length;
	var enviados = 0;
	
	if(album == ''){
		album = '001';
	}
	
	if(total > 0)
	{
		var html = "<div class='progress-bar' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width:0%'>0 de"+total+"</div>";
		$('#progresspar_files').addClass('progress');
		$('#progresspar_files').html(html);
		
		for(var i = 0; i < total; i++){
			var formData = new FormData();
			
			formData.append("arquivo", files[i]);
			formData.append("album", album);
			//formData.append("filename", files[i].webkitRelativePath);
			formData.append('_token', _token);
			formData.append('pedido', id_pedido);
			formData.append('item', id_item);
			formData.append('tipo', tipo);
			formData.append('renomear', renomear);
			
			$.ajax({
				url: '/upload',
				method: 'post',
				processData: false,
				contentType: false,
				data: formData,
				success: function (result) {
					if(result.success){
						enviados += 1;
						
						var percentual = 100 / total * enviados;
						
						html = "<div class='progress-bar' role='progressbar' aria-valuenow='"+percentual+"' aria-valuemin='0' aria-valuemax='100' style='width:"+percentual+"%'>";
						html += enviados+"de"+total+"</div>";
						
						$('#progresspar_files').html(html);
						
						if(enviados == total){
							//$('#modalUpload').modal('hide');
							location.reload();
						}
					}else{
						console.log('success', result)
					}
				},
				error: function (result) {
					console.log('Erro', result);
				}
			});
		}
	}
	else
	{
		SalvaURL(url, tipo);
	}
}

function GeraLista(lista, id_lista, root, id_item){
	var chaves = Object.keys(lista);
	var html = "<ul class='lista_pastas "+id_lista+"'>";
	
	for(var i = 0; i < chaves.length; i++){
		var nome = chaves[i].split('/');
		var prox_id = nome.join('');
		nome = nome[nome.length-1];
		
		html += "<li>";
		html += "<a href='javascript:void(0)' onclick=\"$('#uploadFolder').val('"+root+"/"+chaves[i]+"')\">"+nome+"</a>&nbsp;";
		
		if(lista[chaves[i]].length != 0){
			html += "<a href='javascript:void(0)' id='"+prox_id+"_sinal' onclick=\"MostraLista('"+prox_id+"')\">+</a>";
		}
		html += "</li>";
		html += "<li>";
		html += GeraLista(lista[chaves[i]], prox_id, root, id_item);
		html += "</li>";
	}
	
	html += '</ul>';
	
	return html;
}

function MostraLista(id, dir){
	if($('#'+id+'_ul').is(":hidden")){
		$('#'+id+'_ul').attr('style','display:block');
		$('#'+id+'_sinal').text('-');
		
		var _token = document.getElementsByName('_token')[0].value;
		var html = '';
		
		$.ajax({
			url: '/mapa_arquivos',
			method: 'post',
			async: false,
			data: {
				_token,
				dir
			},
			success: function (result) {
				var html = '';
				
				result.map(function(r){
					var sub_id = id+r.dir.replaceAll(' ','');
					
					html += "<li><a href='javascript:void(0)'  onclick=\"$('#uploadFolder').val('"+dir+'/'+r.dir+"')\">"+r.dir+"&nbsp;";
					if(r.sub > 0){
						html += "<a href='javascript:void(0)' id='bruto_sinal' onclick=\"MostraLista('"+sub_id+"','"+dir+'/'+r.dir+"')\">+</a>";
					}
					html += "<ul id='"+sub_id+"_ul' class='lista_pastas'></ul>";
					html += "</li>";
				});
				
				$('#'+id+'_ul').html(html);
			},
			error: function (result) {
				console.log('Erro', result);
			}
		});
	}else{
		console.log('+')
		$('#'+id+'_ul').removeAttr('style');
		$('#'+id+'_sinal').text('+');
	}
}


function AddServico(){
	var _token = document.getElementsByName('_token')[0].value;
	var id_pedido = $('#id').val();
	var id_servico = $('#modalSlcServico').val();
	
	$.ajax({
		url: '/add_servico',
		method: 'post',
		data: {
			_token,
			id_pedido,
			id_servico
		},
		success: function (result) {
			if(result.success){
				location.reload();
				//$('#modalImprimir').modal('hidde');
			}else{
				console.log(result);
			}
		},
		error: function (result) {
			console.log('Erro', result);
		}
	});
}

function SalvaURL(url, tipo){
	var _token = document.getElementsByName('_token')[0].value;
	
	$.ajax({
		url: '/salva_item_url',
		method: 'post',
		data: {
			_token,
			id_item,
			url,
			tipo
		},
		success: function (result) {
			if(result.success){
				location.reload();
			}else{
				console.log(result);
			}
		},
		error: function (result) {
			console.log('Erro', result);
		}
	});
}

function Imprimir(tipo = 'P'){
	var _token = document.getElementsByName('_token')[0].value;
	var corrigir = $('#modalImpCorrecao').val();
	
	$.ajax({
		url: '/pedido/imprimir',
		method: 'post',
		data: {
			_token,
			id_item,
			tipo,
			corrigir
		},
		success: function (result) {
			if(result.success){
				location.reload();
				//$('#modalImprimir').modal('hidde');
			}else{
				console.log(result);
			}
		},
		error: function (result) {
			console.log('Erro', result);
		}
	});
}
/*
function XML(){
	var _token = document.getElementsByName('_token')[0].value;
	var corrigir = $('#modalXMLCorrecao').val();
	
	$.ajax({
		url: '/pedido/reenviar',
		method: 'post',
		data: {
			_token,
			id_item,
			corrigir
		},
		success: function (result) {
			if(result.success){
				$('#modalXML').modal('hide');
				toastr.success("XML reenviado!");
			}else{
				toastr.danger('XML reenviado!')
			}
		},
		error: function (result) {
			console.log('Erro', result);
		}
	});
}
*/
function AltCopias(id_item, copias){
	var _token = document.getElementsByName('_token')[0].value;
	
	$.ajax({
		url: '/pedido/alt_copias',
		method: 'post',
		data: {
			_token,
			id_item,
			copias
		},
		success: function (result) {
			if(result.success){
				toastr.success("Número de cópias alterado!");
			}else{
				toastr.danger('Erro ao alterar o número de cópias!')
			}
		},
		error: function (result) {
			console.log('Erro', result);
		}
	});
}