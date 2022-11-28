@extends('layouts.app')

@section('title', 'Guias de OPs')

@section('content')
<h1 class="mt-4">Guias de OPs</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Guias de OPs</li>
</ol>

<style>
#tblPedidos tbody tr td a {
	color: #000;
}
</style>
<div class="card mb-4">
	<div class="card-header">
		
	</div>
	<div class="card-body">
		
		@if (session('status'))
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('status') }}</p>
			</div>
		@endif
		@if (session('erro'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('erro') }}</p>
			</div>
		@endif
		
		<div class="table-responsive">
			<table id="tblPedidos" class="table table-bordered table-striped table-sm" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>O.S.</th>
						<th>Cliente</th>
						<th>Tipo</th>
						<th>Contrato</th>
						<th>Entrada</th>
						<th>Previsão</th>
						<th>Usuário</th>
					</tr>
				</thead>
				@if(count($pedidos) > 5)
				<tfoot>
					<tr>
						<th>O.S.</th>
						<th>Cliente</th>
						<th>Tipo</th>
						<th>Contrato</th>
						<th>Entrada</th>
						<th>Previsão</th>
						<th>Usuário</th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($pedidos) > 0)
					@foreach($pedidos as $pedido)
					<tr @if($pedido->impressoes > 0)class='table-success' @endif>
						@if(in_array('guiasOP_edit', session()->get('funcoes')))
							<td><a href="/guiaOP/{{ $pedido->id }}">{{ $pedido->id_externo }}</a></td>
							<td><a href="/guiaOP/{{ $pedido->id }}">{{ $pedido->id_cliente }} - {{ $pedido->cliente }}</a></td>
							<td>
								<a href="/guiaOP/{{ $pedido->id }}">
								@if($pedido->tipo_contrato == 1)
									Contrato
								@else
									Pedido
								@endif
								</a>
							</td>
							<td><a href="/guiaOP/{{ $pedido->id }}">{{ $pedido->contrato }}</a></td>
							<td><a href="/guiaOP/{{ $pedido->id }}">{{ date('d/m/Y', strtotime($pedido->data_entrada)) }}</a></td>
							<td><a href="/guiaOP/{{ $pedido->id }}">{{ date('d/m/Y', strtotime($pedido->previsao_entrega)) }}</a></td>
							<td><a href="/guiaOP/{{ $pedido->id }}">{{ $pedido->usuario }}</a></td>
						@else
							<td>{{ $pedido->id_externo }}</td>
							<td>{{ $pedido->id_cliente }} - {{ $pedido->cliente }}</td>
							<td>
								@if($pedido->tipo_contrato == 1)
									Contrato
								@else
									Pedido
								@endif
							</td>
							<td>{{ $pedido->contrato }}</td>
							<td>{{ date('d/m/Y', strtotime($pedido->data_entrada)) }}</td>
							<td>{{ date('d/m/Y', strtotime($pedido->previsao_entrega)) }}</td>
							<td>{{ $pedido->usuario }}</td>
						@endif
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>


<script>
function Deletar(id, titulo){
	$('#id_deletar').val(id)
	$('#titulo_deletar').text(titulo);
	$('#modalDeletar').modal('show');
}

document.addEventListener("DOMContentLoaded", function(event) {
	//setTimeout(function(){ location.reload(); }, 60000);
});
</script>
@endsection