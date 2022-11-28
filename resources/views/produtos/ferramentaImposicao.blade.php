@extends('layouts.app')

@section('title', 'Ferramenta de Imposição')

@section('content')
<h1 class="mt-4">Ferramenta de Imposição</h1>
	<ol class="breadcrumb mb-4">
		<li class="breadcrumb-item"><a href="/ferramentas_imposicao">Ferramentas de Imposição</a></li>
		<li class="breadcrumb-item active">Ferramenta de Imposição</li>
	</ol>
	
	<div class="card mb-4">
		<div class="card-header">
			@if(is_null($ferramenta->id))
			Cadastro
			@else
			Alteração
			@endif
		</div>
		<div class="card-body">
			<form action='/ferramenta_imposicao/salvar' method='post'>
				@csrf
				<input type='hidden' id='id' name='id' value='{{ $ferramenta->id }}'>
				
				<div class="form-group">
					<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
					<input type="text" id="titulo" name="titulo" placeholder="Título" value='{{ $ferramenta->titulo }}' class="form-control py-1" required>
				</div>
				
				<div class="form-group">
					<label for="descricao" class="small mb-1">Descrição</label>
					<textarea id="descricao" name="descricao" placeholder="Descrição" class="form-control py-1">{{ $ferramenta->descricao }}</textarea>
				</div>
				
				<!--
				<div class="row">
					<div class="col-md-6">
						<div class="form-group mt-4 mb-0 text-left">
							<a href="/ferramentas_imposicao" class="btn btn-default">Voltar</a>
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