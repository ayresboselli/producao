@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<h1 class="mt-4">Produtos</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Produtos</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a href="/produto" class="btn btn-primary btn-sm">Adicionar</a>
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
			<table class="table table-bordered table-sm table-striped dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nome</th>
						<th>Tipo de Imposição</th>
						<th>Modelo de Imposição</th>
						<th>Tamanho (mm)</th>
						<th>Renomear Arquivos</th>
						<th></th>
					</tr>
				</thead>
				@if(count($produtos) > 5)
				<tfoot>
					<tr>
						<th>ID</th>
						<th>Nome</th>
						<th>Tipo de Imposição</th>
						<th>Modelo de Imposição</th>
						<th>Tamanho (mm)</th>
						<th>Renomear Arquivos</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($produtos) > 0)
					@foreach($produtos as $produto)
					<tr>
						<td>{{ $produto->id_externo }}</td>
						<td>{{ $produto->titulo }}</td>
						<td>{{ $produto->tp_imposicao }}</td>
						<td>{{ $produto->mod_imposicao }}</td>
						<td>
							@if($produto->sem_dimensao)
							Sem dimensão
							@else
							{{ $produto->sangr_esq+$produto->largura+$produto->sangr_dir }} x {{ $produto->sangr_sup+$produto->altura+$produto->sangr_inf }}
							@endif
						</td>
						<td>
							@if($produto->renomear)
								Sim
							@else
								Não
							@endif
						</td>
						<td style="width:90px">
							@if(in_array('produto_edit', session()->get('funcoes')))
							<div class='row'>
								<div class='col-sm-4'>
									<a href="/produto/{{ $produto->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
								</div>
								<div class='col-sm-4'>
									<a href="/duplicar_produto/{{ $produto->id }}" title='Duplicar' class='text-primary'><i class='fa fa-copy'></i></a>
								</div>
								<div class='col-sm-4'>
									@if($produto->itens == 0 && $produto->uploads == 0)<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $produto->id }},'{{ $produto->titulo }}')" class='text-danger'><i class='fa fa-trash'></i></a>@endif
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
				<h4 class="modal-title">Produto</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar o produto <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/produto/deletar" method='post'>
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