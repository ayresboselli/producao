@extends('layouts.app')

@section('title', 'Tabela de preços')

@section('content')
<h1 class="mt-4">Tabela de preços</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Tabela de preços</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a href="/tab_preco" class="btn btn-primary btn-sm">Adicionar</a>
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
						<th>Cod. Sax</th>
						<th>Titulo</th>
						<th>Qtd Itens</th>
						<th>Ativo</th>
						<th></th>
					</tr>
				</thead>
				@if(count($tabelas) > 5)
				<tfoot>
					<tr>
						<th>Cod. Sax</th>
						<th>Titulo</th>
						<th>Qtd Itens</th>
						<th>Ativo</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@foreach($tabelas as $tabela)
					<tr>
						<td>{{ $tabela->id_sax }}</td>
						<td>{{ $tabela->titulo }}</td>
						<td>{{ $tabela->itens }}</td>
						<td>
							@if($tabela->ativo)
							<span class='text-success'>Ativo</span>
							@else
							<span class='text-danger'>Inativo</span>
							@endif
						</td>
						<td style="width:60px">
                            @if(in_array('tab_preco_edit', session()->get('funcoes')))
                            <a href="/tab_preco/{{ $tabela->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
                            <a href="/tab_preco_produtos/{{ $tabela->id }}" title='Produtos' class='text-primary'><i class='fa fa-list'></i></a>
							@if($tabela->itens == 0)
							<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $tabela->id }},'{{ $tabela->titulo }}')" class='text-danger'><i class='fa fa-trash'></i></a>
                            @endif
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
				<h4 class="modal-title">Tabela de preços</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar a tabela <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/tab_preco/deletar" method='post'>
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
</script>
@endsection