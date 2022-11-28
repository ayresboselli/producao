@extends('layouts.app')

@section('title', 'Guia de OP')

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

<h1 class="mt-4">Guia de OP</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href='/guiasOP'>Guias de OP</a></li>
	<li class="breadcrumb-item active">Guia de OP</li>
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
					<label for="titulo" class="small mb-1">Data de Entrada</label>
					<p><b>{{ date('d/m/Y', strtotime($pedido->data_entrada)) }}</b></p>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="titulo" class="small mb-1">Previsão de Entrega</label>
					<p><b>{{ date('d/m/Y', strtotime($pedido->previsao_entrega)) }}</b></p>
				</div>
			</div>
		</div>

		<div class="form-row">
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
						<th>Produto</th>
						<th>Álbuns</th>
						<th>Arquivos</th>
						<th>Impressões</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				@if(count($itens) > 0)
					@foreach($itens as $item)
					<tr @if($item->impressoes == 0)class='table-success' @endif>
						<td>{{ $item->id_externo }}</td>
						<td>{{ $item->produto }}</td>
						<td>{{ $item->albuns }}</td>
						<td>{{ $item->arquivos }}</td>
						<td>{{ $item->impressoes }}</td>
						<td style="width:30px">
							<a href="/guiaOP/imprimir/{{$item->id}}" target="_blank" title='Imprimir guia de OP' class="text-primary"><i class='fa fa-print'></i></a>
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

@endsection