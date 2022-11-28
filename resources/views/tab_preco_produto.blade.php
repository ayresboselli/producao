@extends('layouts.app')

@section('title', 'Produto')

@section('content')
<h1 class="mt-4">Produto</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/tab_precos">Tabela de preços</a></li>
	<li class="breadcrumb-item"><a href="/tab_preco_produtos/{{ $tabela->id }}">Produtos</a></li>
	<li class="breadcrumb-item active">Produto</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
		<div class="form-row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="small mb-1">ID Sax</label>
					<p><b>{{ $tabela->id_sax }}</b></p>
				</div>
			</div>
			<div class="col-md-10">
				<div class="form-group">
					<label class="small mb-1">Titulo</label>
					<p><b>{{ $tabela->titulo }}</b></p>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($produto->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/tab_preco_produto/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $tabela->id }}'>
			<input type='hidden' id='id_prod' name='id_prod' value='{{ $produto->id }}'>
			
			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="id_produto" class="small mb-1"><span class="text-danger">*</span> ID Produto</label>
						<select id="id_produto" name="id_produto" class="form-control py-1" required>
							<option value=""></option>
							@foreach($lista as $item)
							<option value="{{ $item->id }}" @if($item->id == $produto->id_produto) selected @endif>{{ $item->id_externo }} - {{ $item->titulo }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="preco" class="small mb-1"><span class="text-danger">*</span> Preço</label>
						<input type="text" id="preco" name="preco" placeholder="Preço" value='{{ $produto->preco }}' class="form-control py-1" required>
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