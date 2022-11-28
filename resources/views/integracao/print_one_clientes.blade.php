@extends('layouts.app')

@section('title', 'Print-One')

@section('content')
<h1 class="mt-4">Print-One</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Clientes</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a href="/printone_cliente" class="btn btn-primary btn-sm">Adicionar</a>
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
						<th>ID Loja</th>
						<th>ID Sax</th>
						<th></th>
					</tr>
				</thead>
				@if(count($clientes) > 5)
				<tfoot>
					<tr>
						<th>ID Loja</th>
						<th>ID Sax</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($clientes) > 0)
					@foreach($clientes as $cliente)
					<tr>
						<td>{{ $cliente->id_loja }}</td>
						<td>{{ $cliente->id_sax }}</td>
						<td style="width:130px">
							@if(in_array('integ_edit', session()->get('funcoes')))
							<div class='row'>
								<div class='col-sm-6'>
									<a href="/printone_cliente/{{ $cliente->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
								</div>
								<div class='col-sm-6'>
									<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $cliente->id }},'ID Loja: {{ $cliente->id_loja }} e ID Sax: {{ $cliente->id_sax }}')" class='text-danger'><i class='fa fa-trash'></i></a>
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
				<h4 class="modal-title">Cliente</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar o cliente <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/printone_cliente/deletar" method='post'>
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