@extends('layouts.app')

@section('title', 'Substrato')

@section('content')
<h1 class="mt-4">Substrato</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/hotfolders_impressao">Substratos</a></li>
	<li class="breadcrumb-item active">Substrato</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($substrato->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/substrato_impressao/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $substrato->id }}'>
			
			<div class="form-group">
				<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
				<input type="text" id="titulo" name="titulo" placeholder="Título" value='{{ $substrato->titulo }}' class="form-control py-1" required>
			</div>
			
			<div class="form-group">
				<label for="descricao" class="small mb-1">Descrição</label>
				<textarea id="descricao" name="descricao" placeholder="Descrição" class="form-control py-1">{{ $substrato->descricao }}</textarea>
			</div>
			
			<!--
			<div class="row">
				<div class="col-md-6">
					<div class="form-group mt-4 mb-0 text-left">
						<a href="/hotfolders_impressao" class="btn btn-default">Voltar</a>
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