@extends('layouts.app')

@section('title', 'Cliente')

@section('content')
<h1 class="mt-4">Cliente</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/clientes">Clientes</a></li>
	<li class="breadcrumb-item active">Cliente</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($cliente->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/cliente/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $cliente->id }}'>
			
			<div class="form-row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="id_externo" class="small mb-1">ID Externo</label>
						<input type="number" id="id_externo" name="id_externo" value='{{ $cliente->id_externo }}' placeholder="ID Externo" class="form-control py-1" />
					</div>
				</div>
				<div class="col-md-9">
					<div class="form-group">
						<label for="nome" class="small mb-1"><span class="text-danger">*</span> Nome</label>
						<input type="text" id="nome" name="nome" value='{{ $cliente->nome }}' placeholder="Nome" class="form-control py-1" required>
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="ftp_usuario" class="small mb-1">Usuário FTP</label>
						<input type="text" id="ftp_usuario" name="ftp_usuario" value='{{ $cliente->ftp_usuario }}' placeholder="Usuário FTP" onblur="UsuarioFTP(this.value)" class="form-control py-1" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="ftp_senha" class="small mb-1">Senha FTP</label>
						<input type="text" id="ftp_senha" name="ftp_senha" value='{{ $cliente->ftp_senha }}' placeholder="Senha FTP" class="form-control py-1">
					</div>
				</div>
			</div>
			
			<div class="form-group mt-4 mb-0 text-right">
				<button class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</div>

<script>
	function UsuarioFTP(usuario){
		var _token = document.getElementsByName('_token')[0].value;
		var id = $('#id').val();
		
		$.ajax({
			url: '/usuario_ftp',
			method: 'post',
			data: {
				_token,
				id,
				usuario
			},
			success: function (result) {
				if(result > 0){
					$('#ftp_usuario').addClass('is-invalid');
				}else{
					$('#ftp_usuario').removeClass('is-invalid');
				}
			},
			error: function (result) {
				console.log('Erro', result);
			}
		});
	}
</script>
@endsection