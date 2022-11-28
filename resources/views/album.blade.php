@extends('layouts.app')

@section('title', 'Álbum')

@section('content')
<h1 class="mt-4">Álbum</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href='/pedidos'>Pedidos</a></li>
	<li class="breadcrumb-item"><a href='/albuns/{{ $album->id_pedido }}'>Álbuns</a></li>
	<li class="breadcrumb-item active">Álbum</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($album->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/album/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $album->id }}'>
			
			<div class="form-row">
				<div class="col-md-2">
					<div class="form-group">
						<label for="id_externo" class="small mb-1">O.S.</label>
						<p><b>{{ $pedido->id_externo }}</b></p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="small mb-1">Cliente</label>
						<p><b>{{ $pedido->id_cliente }} - {{ $pedido->cliente }}</b></p>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="small mb-1">Tipo de pedido</label>
						<p><b>@if($pedido->tipo_contrato == 1) Contrato @else Pedido @endif</b></p>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="small mb-1">Contrato</label>
						<p><b>{{ $pedido->contrato }}</b></p>
					</div>
				</div>
			</div>
			
			<div class="form-row">
				<div class="col-sm-6">
					<label for="id_arquivo" class="small mb-1"><span class="text-danger">*</span> Capa</label>
					<input type="id_arquivo" id="id_arquivo" name="id_arquivo" placeholder="Capa" value='{{ $album->id_arquivo }}' class="form-control py-1" required>
				</div>
				<div class="col-sm-6">
					<label for="codigo" class="small mb-1"><span class="text-danger">*</span> Código</label>
					<input type="text" id="codigo" name="codigo" placeholder="Código" value='{{ $album->codigo }}' class="form-control py-1" required>
				</div>
			</div>

			<div class="form-group mt-4 mb-0 text-right">
				<button class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</div>
@endsection