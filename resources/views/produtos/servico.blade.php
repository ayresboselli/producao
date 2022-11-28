@extends('layouts.app')

@section('title', 'Serviço')

@section('content')
<h1 class="mt-4">Serviço</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/servicos">Serviços</a></li>
	<li class="breadcrumb-item active">Serviço</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($servico->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/servico/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $servico->id }}'>
			
			<div class="form-row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="id_externo" class="small mb-1">ID Externo</label>
						<input type="number" id="id_externo" name="id_externo" value='{{ $servico->id_externo }}' placeholder="ID Externo" class="form-control py-1" />
					</div>
				</div>
				<div class="col-md-9">
					<div class="form-group">
						<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
						<input type="text" id="titulo" name="titulo" value='{{ $servico->titulo }}' placeholder="Título" class="form-control py-1" required>
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