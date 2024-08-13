@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<h1 class="mt-4">Serviços</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Serviços</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<a href="/servico" class="btn btn-primary btn-sm">Adicionar</a>
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
			<table class="table table-bordered table-sm dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nome</th>
						<th></th>
					</tr>
				</thead>
				@if(count($servicos) > 5)
				<tfoot>
					<tr>
						<th>ID</th>
						<th>Nome</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($servicos) > 0)
					@foreach($servicos as $servico)
					<tr>
						<td>{{ $servico->id_externo }}</td>
						<td>{{ $servico->titulo }}</td>
						<td style="width:80px">
							@if(in_array('servico_edit', session()->get('funcoes')))
							<div class='row'>
								<div class='col-sm-6'>
									<a href="/servico/{{ $servico->id }}" title='Alterar' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>
								</div>
								<div class='col-sm-6'>
									@if(count($servico->itens) == 0)
                                    <a href="javascript:void(0)" title='Excluir' onclick="Deletar({{ $servico->id }},'{{ $servico->titulo }}')" class='btn btn-danger btn-sm'>
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
				<h4 class="modal-title">Serviço</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h4>Tem certeza que deseja deletar o serviço <span id='titulo_deletar'></span>?</h4>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/servico/deletar" method='post'>
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
