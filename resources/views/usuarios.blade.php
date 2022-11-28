@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<h1 class="mt-4">Usuários</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Usuários</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a href="/usuario" class="btn btn-primary btn-sm">Adicionar</a>
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
			<table class="table table-bordered dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Nome</th>
						<th>E-mail</th>
						<th>Ativo</th>
						<th></th>
					</tr>
				</thead>
				@if(count($usuarios) > 5)
				<tfoot>
					<tr>
						<th>Nome</th>
						<th>E-mail</th>
						<th>Ativo</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@foreach($usuarios as $usuario)
					<tr>
						<td>{{ $usuario->name }}</td>
						<td>{{ $usuario->email }}</td>
						<td>
							@if($usuario->ativo)
								Sim
							@else
								Não
							@endif
						</td>
						<td>
							<div class='row' style="width:60px">
								<div class='col-sm-6'>
									@if(in_array('usuarios_edit', session()->get('funcoes')))
									<a href="/usuario/{{ $usuario->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
									@endif
								</div>
								<div class='col-sm-6'>
									@if(in_array('alt_senhas', session()->get('funcoes')))
									<a href="/usuario_alt_senha/{{ $usuario->id }}" title='Alterar Senha' class='text-primary'><i class='fa fa-key'></i></a>
									@endif
								</div>
							</div>
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
				<h4>Tem certeza que deseja deletar o usuário <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/usuario/deletar" method='post'>
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