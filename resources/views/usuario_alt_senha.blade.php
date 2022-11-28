@extends('layouts.app')

@section('title', 'Alterar senha')

@section('content')
<h1 class="mt-4">Alterar senha</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/usuarios">Usuários</a></li>
	<li class="breadcrumb-item active">Alterar senha</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		Alteração
	</div>
	<div class="card-body">
		@if (session('erro'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('erro') }}</p>
			</div>
		@endif

		<form action='/usuario_alt_senha/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $usuario->id }}'>

			<div class="form-row">
				<div class="col-md-4">
					<div class="form-group">
						<label>Nome: </label>
						<b>{{ $usuario->name }}</b>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>E-Mail: </label>
						<b>{{ $usuario->email }}</b>
					</div>
				</div>
				<div class="col-md-4">
				</div>
			</div>

			<div class="form-row">
				<div class="col-md-5">
					<div class="form-group">
						<label for="senha_n" class="small mb-1"><span class="text-danger">*</span> Nova senha</label>
						<input type="password" id="senha_n" name="senha_n" class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-1">
				</div>
				<div class="col-md-5">
					<div class="form-group">
					<label for="senha_r" class="small mb-1"><span class="text-danger">*</span> Repita a senha</label>
						<input type="password" id="senha_r" name="senha_r" class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-1">
					<div class="form-group text-right">
						<br>
						<button class="btn btn-primary">Salvar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection