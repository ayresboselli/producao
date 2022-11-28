@extends('layouts.app')

@section('title', 'Uploads')

@section('content')
<h1 class="mt-4">Uploads</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href='/produtos'>Produtos</a></li>
	<li class="breadcrumb-item active">Uploads</li>
</ol>

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

<div class="card mb-4">
	<div class="card-header">
		Produto
	</div>
	<div class="card-body">
		<div class="form-row">
			<div class="col-sm-2">
				<label>ID:</label>
				<b>{{ $produto->id_externo }}</b>
			</div>
			<div class="col-sm-10">
				<label>Título:</label>
				<b>{{ $produto->titulo }}</b>
			</div>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		<a href="/upload/{{ $produto->id }}" class="btn btn-primary btn-sm">Adicionar</a>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Título</th>
						<th>Tipo</th>
						<th>Replicar</th>
						<th></th>
					</tr>
				</thead>
				@if(count($uploads) > 5)
				<tfoot>
					<tr>
						<th>Título</th>
						<th>Tipo</th>
						<th>Replicar</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($uploads) > 0)
					@foreach($uploads as $upload)
					<tr>
						<td>{{ $upload->titulo }}</td>
						<td>
						@if($upload->tipo_upload == 'P')
							Pasta
						@else
							Arquivo
						@endif
						</td>
						<td>
						@switch($upload->replicar)
							@case('S') Sim @break
							@case('N') Não @break
							@case('?') Decidir no momento @break
						@endswitch
						</td>
						<td>
							<div class='row' style="max-width:60px">
								<div class='col-sm-6'>
									<a href="/upload/{{ $produto->id }}/{{ $upload->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
								</div>
								<div class='col-sm-6'>
									<a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $upload->id }},'{{ $upload->titulo }}')" class='text-danger'><i class='fa fa-trash'></i></a>
								</div>
							</div>
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
				<form action="/upload/deletar" method='post'>
					@csrf
					<input type='hidden' id='id_deletar' name='id'>
					<input type='hidden' id='id_produto' name='id_produto' value="{{ $produto->id }}">
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