@extends('layouts.app')

@section('title', 'Reimpressões')

@section('content')
<h1 class="mt-4">Reimpressões</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Reimpressões</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a class="btn btn-primary btn-sm" href="/reimpressao_album">Adicionar</a>
	</div>
	<div class="card-body">
		
		@if (session('sucesso'))
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('sucesso') }}</p>
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
						<th>Título</th>
						<th>O.P.</th>
						<th>Produto</th>
						<th>Situação</th>
						<th></th>
					</tr>
				</thead>
				@if(count($pedidos) > 5)
				<tfoot>
					<tr>
						<th>Título</th>
						<th>O.P.</th>
						<th>Produto</th>
						<th>Situação</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($pedidos) > 0)
					@foreach($pedidos as $pedido)
					<tr>
						<td>{{ $pedido->titulo }}</td>
						<td>{{ $pedido->ordem_producao }}</td>
						<td>{{ $pedido->produto }}</td>
						<td>
						@if($pedido->imprimir)
							@if($pedido->processada)
							Processada em {{ $pedido->processada }}
							@else
							Aguardando processamento
							@endif
						@else
							Salvo
						@endif
						</td>
						<td>
							<a href="/reimpressao_album/{{ $pedido->id }}"><i class="fas fa-edit"></i></a>
							<a href="javascript:void(0)" onclick="Deletar({{ $pedido->id }}, '{{ $pedido->titulo }}')"><i class="fas fa-trash text-danger"></i></a>
							<a href="javascript:void(0)" onclick="Reimprimir({{ $pedido->id }}, '{{ $pedido->titulo }}')"><i class="fas fa-redo text-success"></i></a>
						</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- Modal Deletar -->
<div class="modal" id="modalDeletar">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Deletar</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar a reimpressão <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/reimpressoes_album_deletar" method='post'>
					@csrf
					<input type='hidden' id='id_deletar' name='id'>
					<button type="submit" class="btn btn-danger">Deletar</button>
				</form>
			</div>

		</div>
	</div>
</div>

<!-- Modal Reimprimir -->
<div class="modal" id="modalReimprimir">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Reimpressão</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja reimprimir <span id='titulo_reimprimir'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/reimpressoes_album_reimprimir" method='post'>
					@csrf
					<input type='hidden' id='id_reimprimir' name='id'>
					<button type="submit" class="btn btn-success">Reimprimir</button>
				</form>
			</div>

		</div>
	</div>
</div>

<script>
function Deletar(id, titulo){
	$('#id_deletar').val(id)
	$('#titulo_deletar').text(titulo);
	$('#modalDeletar').modal('show');
}

function Reimprimir(id, titulo){
	$('#id_reimprimir').val(id)
	$('#titulo_reimprimir').text(titulo);
	$('#modalReimprimir').modal('show');
}
</script>
@endsection