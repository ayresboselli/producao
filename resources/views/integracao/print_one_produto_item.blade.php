@extends('layouts.app')

@section('title', 'Print-One')

@section('content')
<h1 class="mt-4">Print-One</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/printone">Produtos</a></li>
	<li class="breadcrumb-item"><a href="/printone_item/{{ $produto->id }}">Ítens</a></li>
	<li class="breadcrumb-item active">Ítem</li>
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
		<form action='/printone_item/salvar' method='post'>
			@csrf
			<input type='hidden' id='id_produto' name='id_produto' value='{{ $produto->id }}'>
			<input type='hidden' id='id_item' name='id_item' value='{{ $item->id }}'>
			
			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="prod" class="small mb-1"><span class="text-danger">* </span>Produto SAX</label>
						<input type='text' id="prod" name="prod" placeholder="Clique duas vezes para listar os produtos" value="{{ $selecionado }}" list="lista_produtos" class="form-control py-1" required>
						<datalist id="lista_produtos">
						@foreach($produtos as $prod)
							<option value="{{ $prod->id_externo }} - {{ $prod->titulo }}">
						@endforeach
						</datalist>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label for="unid_medida" class="small mb-1">Unidade de Medida</label>
						<select id="unid_medida" name="unid_medida" class="form-control py-1">
							<option value='1' @if($item->unid_medida == 1) selected @endif>Unitário</option>
							<option value='2' @if($item->unid_medida == 2) selected @endif>Foto</option>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label for="preco" class="small mb-1"><span class="text-danger">* </span>Preço (R$)</label>
						<input type="text" id="preco" name="preco" value='{{ $item->preco }}' placeholder="Preço (R$)" class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label for="intervalo" class="small mb-1">Intervalo de páginas</label>
						<input type="text" id="intervalo" name="intervalo" value='{{ $item->intervalo }}' placeholder="Intervalo de páginas" class="form-control py-1">
						<span class="text-secondary">Exemplo: 1,2,3-5</span>
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