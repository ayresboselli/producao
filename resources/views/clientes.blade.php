@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<h1 class="mt-4">Clientes</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Clientes</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalNovoCliente">Adicionar</button>
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
						<th>ID</th>
						<th>Nome</th>
						<th>usuário FTP</th>
						<th></th>
					</tr>
				</thead>
				@if(count($clientes) > 5)
				<tfoot>
					<tr>
						<th>ID</th>
						<th>Nome</th>
						<th>usuário FTP</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($clientes) > 0)
					@foreach($clientes as $cliente)
					<tr>
						<td>{{ $cliente->id_externo }}</td>
						<td>{{ $cliente->nome }}</td>
						<td>{{ $cliente->ftp_usuario }}</td>
						<td>
							<a href="/cliente/{{ $cliente->id }}" title='Alterar' class="btn btn-primary btn-sm"><i class='fa fa-edit'></i></a>
						</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>


<!-- Modal Novo Cliente -->
<div class="modal" id="modalNovoCliente">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Cliente</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<form action="/novo_cliente" method='post'>
				@csrf
				
				<div class="modal-body">
					<div class="form-group">
						<label for="id_externo" class="small mb-1">Código</label>
						<input type="number" id="id_cliente" name="id_cliente" placeholder="Código do cliente" class="form-control py-1" />
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

@endsection