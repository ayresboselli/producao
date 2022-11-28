@extends('layouts.app')

@section('title', 'Perfis')

@section('content')
<h1 class="mt-4">Perfis</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Perfis</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a href="/perfil" class="btn btn-primary btn-sm">Adicionar</a>
	</div>
	<div class="card-body">
		
		@if (session('successo'))
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('successo') }}</p>
			</div>
		@endif
		@if (session('erro'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('erro') }}</p>
			</div>
		@endif
		
		<div class="table-responsive">
			<table class="table table-bordered dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Título</th>
						<th>Descrição</th>
						<th></th>
					</tr>
				</thead>
				@if(count($perfis) > 5)
				<tfoot>
					<tr>
						<th>Título</th>
						<th>Descrição</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@foreach($perfis as $perfil)
					<tr>
						<td>{{ $perfil->titulo }}</td>
						<td>{{ $perfil->descricao }}</td>
						<td>
							@if(in_array('perfis_edit', session()->get('funcoes')))
							<div class='row' style="max-width:60px">
								<div class='col-sm-6'>
									<a href="/perfil/{{ $perfil->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
								</div>
								<div class='col-sm-6'>
									<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $perfil->id }},'{{ $perfil->titulo }}')" class='text-danger'><i class='fa fa-trash'></i></a>
								</div>
							</div>
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
				<h4 class="modal-title">Perfil</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar o perfil <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/perfil/deletar" method='post'>
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