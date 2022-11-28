@extends('layouts.app')

@section('title', 'Print-One')

@section('content')
<h1 class="mt-4">Print-One</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/printone">Produtos</a></li>
	<li class="breadcrumb-item active">Ítens</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-4">
				<label>Código: </label>
				<b>{{ $produto->id_printone }}</b>
			</div>
			<div class="col-sm-8">
				<label>Título: </label>
				<b>{{ $produto->titulo }}</b>
			</div>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		<a href="/printone_item/{{ $produto->id }}" class="btn btn-primary btn-sm">Adicionar</a>
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
			<table class="table table-bordered table-sm table-striped dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Produto SAX</th>
						<th>Intervalo de páginas</th>
						<th>Unid. Medida</th>
						<th>Preço (R$)</th>
						<th></th>
					</tr>
				</thead>
				@if(count($itens) > 5)
				<tfoot>
					<tr>
						<th>Produto SAX</th>
						<th>Intervalo de páginas</th>
						<th>Unid. Medida</th>
						<th>Preço (R$)</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($itens) > 0)
					@foreach($itens as $item)
					<tr>
						<td>{{ $item->produto }}</td>
						<td>{{ $item->intervalo }}</td>
						<td>
							@if($item->unid_medida == 1)
								Unitário
							@else
								Foto
							@endif
						</td>
						<td>{{ $item->preco }}</td>
						<td style="width:90px">
							@if(in_array('integ_edit', session()->get('funcoes')))
							<div class='row'>
								<div class='col-sm-4'>
									<a href="/printone_item/{{ $produto->id }}/{{ $item->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
								</div>
								<div class='col-sm-4'>
									<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $item->id }},'{{ $item->produto }}')" class='text-danger'><i class='fa fa-trash'></i></a>
								</div>
							</div>
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


<!-- Modal Deletar -->
<div class="modal" id="modalDeletar">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Ítem</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar o ítem <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/printone_item/deletar" method='post'>
					@csrf
					<input type='hidden' id='id_deletar' name='id_item'>
					<input type='hidden' name='id_produto' value="{{ $produto->id }}">
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
</script>
@endsection