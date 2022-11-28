@extends('layouts.app')

@section('title', 'Print-One')

@section('content')
<h1 class="mt-4">Print-One</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/printone">Produtos</a></li>
	<li class="breadcrumb-item active">Produto</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($produto->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/printone_produto/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $produto->id }}'>
			
			<div class="form-row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="id_printone" class="small mb-1"><span class="text-danger">*</span> ID Print-One</label>
						<input type="number" id="id_printone" name="id_printone" value='{{ $produto->id_printone }}' placeholder="ID Print-One" class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-9">
					<div class="form-group">
						<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
						<input type="text" id="titulo" name="titulo" value='{{ $produto->titulo }}' placeholder="Título" class="form-control py-1" required>
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
	
	document.addEventListener("DOMContentLoaded", function(event) {
		
	});
</script>
@endsection