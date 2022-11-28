@extends('layouts.app')

@section('title', 'Print-One')

@section('content')
<h1 class="mt-4">Print-One</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/printone_clientes">Clientes</a></li>
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
		<form action='/printone_cliente/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id_item' value='{{ $cliente->id }}'>
			
			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="id_loja" class="small mb-1"><span class="text-danger">* </span>ID Loja</label>
						<input type="number" id="id_loja" name="id_loja" value='{{ $cliente->id_loja }}' placeholder="ID Loja" class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-6">
                    <div class="form-group">
						<label for="id_sax" class="small mb-1"><span class="text-danger">* </span>ID Sax</label>
						<input type="number" id="id_sax" name="id_sax" value='{{ $cliente->id_sax }}' placeholder="ID Sax" class="form-control py-1" required>
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