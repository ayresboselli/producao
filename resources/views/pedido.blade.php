@extends('layouts.app')

@section('title', 'Pedido')

@section('content')
<style>
	.lista_pastas{
		list-style: none;
		display: none;
	}

	#modalUploadForm {
		border: 1px solid #ccc;
	}

	#apagaUploadFolder {
		position: relative;
		top: -35px;
		right: 10px;
	}
</style>

<h1 class="mt-4">Pedido</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href='/pedidos'>Pedidos</a></li>
	<li class="breadcrumb-item active">Pedido</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
		<input type='hidden' id='id' name='id' value='{{ $pedido->id }}'>
				
		<div class="form-row">
			<div class="col-md-2">
				<div class="form-group">
					<label for="id_externo" class="small mb-1">O.S.</label>
					<p><b>{{ $pedido->id_externo }}</b></p>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="titulo" class="small mb-1">Cliente</label>
					<p><b>{{ $pedido->id_cliente }} - {{ $pedido->cliente }}</b></p>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="titulo" class="small mb-1">Tipo de pedido</label>
					<p><b>@if($pedido->tipo_contrato == 1) Contrato @else Pedido @endif</b></p>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="titulo" class="small mb-1">Contrato</label>
					<p><b>{{ $pedido->contrato }}</b></p>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="card mb-4">
	<div class="card-header">
		Produtos
	</div>
	<div class="card-body">
		
		<div class="table-responsive">
			<table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>O.P.</th>
						<th>Produto SAX</th>
						<th>Produto</th>
						<th>Cópias</th>
						<th>Origem</th>
						<th>Arq. enviados</th>
						<th>Situação</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				@if(count($itens) > 0)
					@foreach($itens as $item)
					<tr @if(is_null($item->id_produto))class='table-danger' title='Produto não mapeado'@endif>
						<td>{{ $item->id_externo }}</td>
						<td>{{ $item->id_produto_externo }}</td>
						<td>{{ $item->produto }}</td>
						<td>
							<input type="number" min="1" size="3" class="form-control form-control-sm" value="{{ $item->copias }}" onchange=" AltCopias({{$item->id}}, this.value)"> 
						</td>
						<td>{{ $item->url_origem }}</td>
						<td>{{ $item->arquivos }}</td>
						<td>
							@switch($situacao[$item->id])
								@case(0) Aguardando envio de arquivos @break
								@case(1) Aguardando processamento de arquivos @break
								@case(2) Aguardando a criação de O.P. @break
								@case(3) Pronto para impressão @break
								@case(4) Exportando @break
								@case(5) Em produção @break
							@endswitch
						</td>
						<td style="width:100px">
						@if(!is_null($item->id_produto))
							<a href="javascript:void(0)" id='btn_upload_{{ $item->id }}' title='Upload' onclick="ModalUpload({{ $item->id }}, '{{ $item->url_origem }}', 'P')" class="text-primary"><i class='fa fa-upload'></i></a>
							@if($item->arquivos > 0 || $item->albuns > 0)
								&nbsp;<a href="/arquivos/{{ $item->id }}" title='Arquivos' class="text-primary"><i class='fa fa-file'></i></a>
								
								@if($item->recortes > 0)
								&nbsp;<a href="/recorte/{{ $item->id }}" class="text-primary"><i class="fas fa-crop-alt"></i></a>
								@endif

								&nbsp;<a href="javascript:void(0)" title='Resetar' onclick="Resetar({{ $item->id }}, '{{ $item->produto }}', 'P')" class="text-danger"><i class='fa fa-undo'></i></a>
								
								@if($item->arquivos == $item->situacao && !is_null($item->id_externo) && !$item->imprimir && !is_null($item->data_importacao))
								&nbsp;<a href="javascript:void(0)" title='Imprimir' onclick="ModalImprimir({{ $item->id }}, 'P')" class="text-success"><i class='fa fa-print'></i></a>
								@endif

								@if(!is_null($item->data_envio_impressao))
								<!--&nbsp;<a href="javascript:void(0)" title='Reimprimir' onclick="ModalReimprimir({{ $item->id }}, 'P')" class="text-success"><i class='fa fa-redo'></i></a>-->
								<!--&nbsp;<a href="javascript:void(0)" title='Reenviar XML' onclick="ModalXML({{ $item->id }})" class="text-success"><i class='fa fa-file-code'></i></a>-->
								@endif
							@else
								@if($item->tentativa_importar > 0)
								&nbsp;<a href="javascript:void(0)" title='Resetar' onclick="Resetar({{ $item->id }}, '{{ $item->produto }}', 'P')" class="text-danger"><i class='fa fa-undo'></i></a>
								@endif
							@endif
						@endif
						</td>
					</tr>
					@endforeach
				@else
					<tr>
						<td colspan='7'>Nenhum ítem</td>
					</tr>
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		Serviços <button onclick="ModalAddServico()" class="btn btn-primary btn-sm">Adicionar</button>
	</div>
	<div class="card-body">
		
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Serviço</th>
						<th>Origem</th>
						<th>Arq. enviados</th>
						<th>Situação</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				@if(count($servicos) > 0)
					@foreach($servicos as $item)
					<tr>
						<td>{{ $item->id_externo }}</td>
						<td>{{ $item->servico }}</td>
						<td>{{ $item->url_origem }}</td>
						<td>{{ $item->arquivos }}</td>
						<td>
						@if($item->arquivos == 0)
							Aguardando arquivos
						@else
							@if($item->imprimir)
								@if(is_null($item->data_envio_impressao))
									Exportando
								@else
									Em produção
								@endif
							@else
								Pronto para impressão
							@endif
							
						@endif
						</td>
						<td>
							<a href="javascript:void(0)" id='btn_upload_{{ $item->id }}' title='Upload' onclick="ModalUpload({{ $item->id }}, '{{ $item->url_origem }}', 'S')" class="btn btn-sm btn-light text-primary"><i class='fa fa-upload'></i></a>
							@if($item->arquivos > 0)
								<button type="button" title='Resetar' onclick="Resetar({{ $item->id }}, '{{ $item->servico }}', 'S')" class="btn btn-sm btn-danger"><i class='fa fa-undo'></i></button>
								@if($item->imprimir == 0)
								<button type="button" title='Imprimir' onclick="ModalImprimir({{ $item->id }}, 'S')" class="btn btn-sm btn-success"><i class='fa fa-print'></i></button>
								@endif
							@endif
							<div id='progresspar_{{ $item->id }}'></div> 
						</td>
					</tr>
					@endforeach
				@else
					<tr>
						<td colspan='6'>Nenhum ítem</td>
					</tr>
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>


<div class="modal" id="modalUpload">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Upload de <span id="spTipoUpload"></span></h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<input type='hidden' id='upload_tipo'>
				<input type='file' id='upload_files' accept='image/jpeg' class='form-control py-1' multiple>
				
				<div class="row">
					<div class="col-sm-6">
						<label for="">Nome do álbum:</label>
						<input type="text" id="upload_album" name="upload_album" class="">
					</div>
					<div class="col-sm-6">
						<div class="custom-control custom-checkbox">
							<input class="custom-control-input" id="renomear" type="checkbox" name="renomear">
							<label class="custom-control-label" for="renomear">Renomear arquivos</label>
						</div>
					</div>
				</div>
				

				<br><div id='progresspar_files'></div>
				
				<p>-- OU --</p>

				<label>Mapear arquivos</label>
				
				<div class='form-group'>
					<input id="uploadFolder" class="form-control">
					<button type="button" id="apagaUploadFolder" class="close" onclick="$('#uploadFolder').val('')">&times;</button>
				</div>

				<div id='modalUploadForm'>
					<ul class='lista_pastas' style='display: block; padding-left: 10px;'>
						<li>Arquivos&nbsp;
							<a href='javascript:void(0)' id='arquivos_sinal' onclick="MostraLista('arquivos','/arquivos')">+</a>
							<ul id='arquivos_ul' class='lista_pastas' style='display: none'></ul>
						</li>
						<li>Bruto&nbsp;
							<a href='javascript:void(0)' id='bruto_sinal' onclick="MostraLista('bruto','/brutos')">+</a>
							<ul id='bruto_ul' class='lista_pastas' style='display: none'></ul>
						</li>
						<!-- <li>FTP&nbsp;
							<a href='javascript:void(0)' id='ftp_sinal' onclick="MostraLista('ftp','/ftp')">+</a>
							<ul id='ftp_ul' class='lista_pastas' style='display: none'></ul>
						</li> -->
					</ul>
				</div>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<!--<button class="btn btn-primary" data-dismiss="modal">Ok</button>-->
				<button class="btn btn-primary" onclick="Enviar()">Enviar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Reset -->
<div class="modal" id="modalReset">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Resetar</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<p>Ao resetar o pedido, todos os arquivos serão excluídos, sendo necessário novo envio.</p>
				<p>Tem certeza que deseja resetar o <span id='titulo_reset'></span>?</p>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/pedido/reset" method='post'>
					@csrf
					<input type='hidden' id='id_reset' name='id'>
					<input type='hidden' id='tipo_reset' name='tipo'>
					<button type="submit" class="btn btn-danger">Resetar</button>
				</form>
			</div>

		</div>
	</div>
</div>


<!-- Modal Add Serviço -->
<div class="modal" id="modalAddServico">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Serviço</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<select id="modalSlcServico" class="form-control form-control-sm">
					@foreach($listaServicos as $item)
					<option value="{{ $item->id }}">{{ $item->titulo }}</option>
					@endforeach
				</select>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" onclick="AddServico()" class="btn btn-primary">Adicionar</button>
			</div>
		</div>
	</div>
</div>


<!-- Modal Imprimir -->
<div class="modal" id="modalImprimir">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Imprimir</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<div id="msgReimprimir" class="alert alert-danger" style="display:none">
					<p class="text-center">ATENÇÃO!</p>
					<p>Ao reenviar este pedido, todos os arquivos anteriormente enviados serão apagados.</p>
				</div>

				<div class="row">
					<div class="col-sm-8">
						<p>Enviar para correção?</p>
					</div>
					<div class="col-sm-4">
						<select id="modalImpCorrecao" class="form-control form-control-sm">
							<option value="1">Sim</option>
							<option value="0">Não</option>
						</select>
					</div>
				</div>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<button type="button" onclick="Imprimir()" class="btn btn-success">Imprimir</button>
			</div>
		</div>
	</div>
</div>

<script src="{{ asset('/js/pedido.js') }}"></script>
@endsection