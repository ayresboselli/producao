@extends('layouts.app')

@section('title', 'HotFolders')

@section('content')
<h1 class="mt-4">HotFolders</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">HotFolders</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a href="/hotfolder_impressao" class="btn btn-primary btn-sm">Adicionar</a>
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
			<table class="table table-bordered dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Título</th>
						<th>Descrição</th>
						<th></th>
					</tr>
				</thead>
				@if(count($hotfolders) > 5)
				<tfoot>
					<tr>
						<th>Título</th>
						<th>Descrição</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($hotfolders) > 0)
					@foreach($hotfolders as $hotfolder)
					<tr>
						<td>{{ $hotfolder->titulo }}</td>
						<td>{{ $hotfolder->descricao }}</td>
						<td style="max-width:60px">
							@if(in_array('imp_hotf_edit', session()->get('funcoes')))
							<div class='row'>
								<div class='col-sm-6'>
									<a href="/hotfolder_impressao/{{ $hotfolder->id }}" title='Alterar' class='text-primary'><i class='fa fa-edit'></i></a>
								</div>
								<div class='col-sm-6'>
									@if(count($hotfolder->produtos) == 0)
                                    <a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $hotfolder->id }},'{{ $hotfolder->titulo }}')" class='text-danger'>
                                        <i class='fa fa-trash'></i>
                                    </a>
                                    @endif
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
				<h4 class="modal-title">HotFolders</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar o hotfolder <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/hotfolder_impressao/deletar" method='post'>
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
