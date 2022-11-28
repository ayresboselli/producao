@extends('layouts.app')

@section('title', 'Arquivamento')

@section('content')
<h1 class="mt-4">Arquivamento</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Arquivamento</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		
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
			<table id="tblPedidos" class="table table-bordered table-striped table-sm" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>O.S.</th>
						<th>Cliente</th>
						<th>Tipo</th>
						<th>Contrato</th>
						<th>Entrada</th>
						<th>Finalizado</th>
						<th>Deletar</th>
						<th>Arquivamento</th>
					</tr>
				</thead>
				@if(count($pedidos) > 5)
				<tfoot>
					<tr>
						<th>O.S.</th>
						<th>Cliente</th>
						<th>Tipo</th>
						<th>Contrato</th>
						<th>Entrada</th>
						<th>Finalizado</th>
						<th>Deletar</th>
						<th>Arquivamento</th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@foreach($pedidos as $pedido)
					<tr>
						<td>{{ $pedido->id_externo }}</td>
						<td>{{ $pedido->id_cliente }} - {{ $pedido->cliente }}</td>
						<td>
							@if($pedido->tipo_contrato == 1)
								Contrato
							@else
								Pedido
							@endif
						</td>
						<td>{{ $pedido->contrato }}</td>
						<td>{{ date('d/m/Y', strtotime($pedido->data_entrada)) }}</td>
						<td>{{ date('d/m/Y', strtotime($pedido->data_fechamento)) }}</td>
						<td>@if(!is_null($pedido->data_exclusao)){{ date('d/m/Y', strtotime($pedido->data_exclusao)) }}@endif</td>
						<td>
						@if(is_null($pedido->arquivar))
							@if(!is_null($pedido->clienteFTP))
							<button class="btn btn-primary btn-sm" onclick="ModalMoverFTP({{ $pedido->id }}, '{{ $pedido->id_externo }}')">FTP</button>
							@endif
							<button class="btn btn-danger btn-sm" onclick="ModalExcluir({{ $pedido->id }}, '{{ $pedido->id_externo }}')">Excluir</button>
						@else
							@switch($pedido->arquivar)
								@case(1) <span class="text-danger">Excluído</span> @break
								@case(2) <span class="text-primary">FTP</span> @break
							@endswitch
						@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal Mover para FTP -->
<div class="modal" id="modalMoverFTP">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Mover para FTP</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<form action="/arquivamento" method='post'>
				@csrf
				<input type="hidden" id="modalFTPId" name="modalId">
				<input type="hidden" name="arquivar" value="2">
				
				<div class="modal-body">
					<p>Tem certeza que deseja mover a OS <span id="modalFTPOS"></span> para o FTP?</p>
					<p>Para mover, digite <i><b>mover para ftp</b></i> no campo a baixo.</p>

					<input type="text" id="modalFTPText" placeholder="mover para ftp" onkeyup="AtivarBotaoModal(this.value, 'mover para ftp', 'btn_mover_ftp')" class="form-control py-1" />
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" id="btn_mover_ftp" class="btn btn-danger" disabled>Mover</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Excluir permanentemente -->
<div class="modal" id="modalExcluir">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Excluir permanentemente</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<form action="/arquivamento" method='post'>
				@csrf
				<input type="hidden" id="modalExcId" name="modalId">
				<input type="hidden" name="arquivar" value="1">
				
				<div class="modal-body">
					<p>Tem certeza que deseja excluir permanentemente a OS <span id="modalExcOS"></span>?</p>
					<p>Para mover, digite <i><b>excluir permanentemente</b></i> no campo a baixo.</p>

					<input type="text" id="modalExcText" placeholder="excluir permanentemente" onkeyup="AtivarBotaoModal(this.value, 'excluir permanentemente', 'btn_excluir')" class="form-control py-1" />
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" id="btn_excluir" class="btn btn-danger" disabled>Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
function AtivarBotaoModal(valor, texto, id){
	if(valor == texto){
		$('#'+id).removeAttr('disabled');
	}else{
		$('#'+id).attr('disabled','disabled');
	}
}

function ModalMoverFTP(id, os){
	$('#modalFTPId').val(id);
	$('#modalFTPOS').text(os);
	$('#modalFTPText').val('');

	$('#modalMoverFTP').modal('show');
}

function ModalExcluir(id, os){
	$('#modalExcId').val(id);
	$('#modalExcOS').text(os);
	$('modalExcText').val('');

	$('#modalExcluir').modal('show');
}

document.addEventListener("DOMContentLoaded", function(event) {
	//setTimeout(function(){ location.reload(); }, 600000);
});
</script>
@endsection