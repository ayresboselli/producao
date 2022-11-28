@extends('layouts.app')

@section('title', 'Modelo de Imposição')

@section('content')
<h1 class="mt-4">Modelo de Imposição</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/modelos_imposicao">Modelos de Imposição</a></li>
	<li class="breadcrumb-item active">Modelo de Imposição</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($modelo->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/modelo_imposicao/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $modelo->id }}'>
			
			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="imposicao" class="small mb-1"><span class="text-danger">*</span> Ferramenta de imposição</label>
						<select id="imposicao" name="imposicao" class="form-control py-1" required>
							<option value=''>-- Selecione --</option>
							@foreach($ferramentas as $ferramenta)
							<option value='{{ $ferramenta->id }}' @if($ferramenta->id == $modelo->id_imposicao) selected @endif>{{ $ferramenta->titulo }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
						<input type="text" id="titulo" name="titulo" placeholder="Título" value='{{ $modelo->titulo }}' class="form-control py-1" required>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="descricao" class="small mb-1">Descrição</label>
				<textarea id="descricao" name="descricao" placeholder="Descrição" class="form-control py-1">{{ $modelo->descricao }}</textarea>
			</div>
			
			<!--
			<div class="row">
				<div class="col-md-6">
					<div class="form-group mt-4 mb-0 text-left">
						<a href="/modelos_imposicao" class="btn btn-default">Voltar</a>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group mt-4 mb-0 text-right">
						<button class="btn btn-primary">Salvar</button>
					</div>
				</div>
			</div>
			-->
			<div class="form-group mt-4 mb-0 text-right">
				<button class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</div>
@endsection