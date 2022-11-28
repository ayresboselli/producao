@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<h1 class="mt-4">Pedidos</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Pedidos</li>
</ol>

<style>
#tblPedidos tbody tr td a {
	color: #000;
}
</style>
<div class="card mb-4">
	<div class="card-header">
		<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalNovoPedido">Adicionar</button>
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
						<th>Situação</th>
						<th>Usuário</th>
						<th></th>
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
						<th>Situação</th>
						<th>Usuário</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($pedidos) > 0)
					@foreach($pedidos as $pedido)
					<tr  @if(!$mapeamento[$pedido->id])class='table-danger' title='Produto não mapeado'@endif>
						@if(in_array('pedido_edit', session()->get('funcoes')))
						<td><a href="/pedido/{{ $pedido->id }}">{{ $pedido->id_externo }}</a></td>
						<td><a href="/pedido/{{ $pedido->id }}">{{ $pedido->id_cliente }} - {{ $pedido->cliente }}</a></td>
						<td>
							<a href="/pedido/{{ $pedido->id }}">
							@if($pedido->tipo_contrato == 1)
								Contrato
							@else
								Pedido
							@endif
							</a>
						</td>
						<td><a href="/pedido/{{ $pedido->id }}">{{ $pedido->contrato }}</a></td>
						<td><a href="/pedido/{{ $pedido->id }}">{{ date('d/m/Y', strtotime($pedido->data_entrada)) }}</a></td>
						<td><a href="/pedido/{{ $pedido->id }}">{{ date('d/m/Y', strtotime($pedido->previsao_entrega)) }}</a></td>
						<td style="min-width: 200px">
							<a href="/pedido/{{ $pedido->id }}">
							@if(is_null($pedido->data_fechamento))
								@switch($situacao[$pedido->id])
									@case(0) Aguardando arquivos @break
									@case(1) Aguardando processamento @break
									@case(2) Aguardando a criação de O.P. @break
									@case(3) Pronto para impressão @break
									@case(4) Exportando @break
									@case(5) Em produção @break
								@endswitch
							@else
								Finalizado
							@endif
							</a>
						</td>
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
						<td style="min-width: 200px">
							@if(is_null($pedido->data_fechamento))
								@switch($situacao[$pedido->id])
									@case(0) Aguardando arquivos @break
									@case(1) Aguardando processamento @break
									@case(2) Aguardando a criação de O.P. @break
									@case(3) Pronto para impressão @break
									@case(4) Exportando @break
									@case(5) Em produção @break
								@endswitch
							@else
								Finalizado
							@endif
						</td>
						@endif
						<td>{{ $usuario[$pedido->id] }}</td>
						<td>
						@if($qtd_itens[$pedido->id] == 0)
							<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $pedido->id }},'{{ $pedido->id_externo }}')" class='text-danger'><i class='fa fa-trash'></i></a>
						@endif
						</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- Modal Novo Pedido -->
<div class="modal" id="modalNovoPedido">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Pedido</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<form action="/novo_pedido" method='post'>
				@csrf
				
				<div class="modal-body">
					<div class="form-group">
						<label for="id_externo" class="small mb-1">Ordem de Serviço</label>
						<input type="number" id="ordemServico" name="ordemServico" placeholder="Código da O.S." class="form-control py-1" />
					</div>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Buscar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Deletar -->
<div class="modal" id="modalDeletar">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Pedido</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar o pedido <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/pedido/deletar" method='post'>
					@csrf
					<input type='hidden' id='id_deletar' name='id'>
					<button type="submit" class="btn btn-danger">Deletar</button>
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

document.addEventListener("DOMContentLoaded", function(event) {
	//setTimeout(function(){ location.reload(); }, 60000);
});
</script>
@endsection