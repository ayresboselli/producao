@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<h1 class="mt-4">Produtos</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/tab_precos">Tabela de preços</a></li>
	<li class="breadcrumb-item active">Produtos</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
		<div class="form-row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="small mb-1">ID Sax</label>
					<p><b>{{ $tabela->id_sax }}</b></p>
				</div>
			</div>
			<div class="col-md-10">
				<div class="form-group">
					<label class="small mb-1">Titulo</label>
					<p><b>{{ $tabela->titulo }}</b></p>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		<a href="/tab_preco_produto/{{$tabela->id}}" class="btn btn-primary btn-sm">Adicionar</a>
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
			<table class="table table-bordered table-striped table-sm dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Produto</th>
						<th>Preço</th>
						<th></th>
					</tr>
				</thead>
				@if(count($produtos) > 5)
				<tfoot>
					<tr>
						<th>Produto</th>
						<th>Preço</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@foreach($produtos as $produto)
					<tr>
						<td>{{ $produto->produto }}</td>
						<td>{{ $produto->preco }}</td>
						<td style="width:60px">
                            @if(in_array('tab_preco_edit', session()->get('funcoes')))
                            <a href="/tab_preco_produto/{{ $tabela->id }}/{{ $produto->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
							<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $produto->id }},'{{ $produto->produto }}')" class='text-danger'><i class='fa fa-trash'></i></a>
                            @endif
						</td>
					</tr>
				@endforeach
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
				<h4 class="modal-title">Produto</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar produto <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/tab_preco_produto/deletar" method='post'>
					@csrf
					<input type='hidden' id='id_deletar' name='id'>
					<input type='hidden' name='id_tabela' value="{{$tabela->id}}">
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