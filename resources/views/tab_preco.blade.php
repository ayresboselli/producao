@extends('layouts.app')

@section('title', 'Tabela de preço')

@section('content')
<h1 class="mt-4">Tabela de preço</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/tab_precos">Tabela de preços</a></li>
	<li class="breadcrumb-item active">Tabela de preço</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($tabela->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/tab_preco/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $tabela->id }}'>
			
			<div class="form-row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="id_sax" class="small mb-1"><span class="text-danger">*</span> ID Sax</label>
						<input type="number" id="id_sax" name="id_sax" placeholder="ID Sax" value='{{ $tabela->id_sax }}' class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Titulo</label>
						<input type="text" id="titulo" name="titulo" placeholder="Titulo" value='{{ $tabela->titulo }}' class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<div class="custom-control custom-checkbox">
							<br>
							&nbsp;&nbsp;
							<input class="custom-control-input" id="ativo" type="checkbox" name="ativo" {{ $tabela->ativo ? 'checked' : '' }}>
							<label class="custom-control-label" for="ativo">Ativo</label>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group mt-4 mb-0 text-right">
				<button class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</div>
@endsection