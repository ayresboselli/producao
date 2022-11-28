@extends('layouts.app')

@section('title', 'Reimpressão')

@section('content')
<h1 class="mt-4">Reimpressão</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/reimpressoes_album">Reimpressões</a></li>
	<li class="breadcrumb-item active">Reimpressão</li>
</ol>

@if (session('sucesso'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<p>{{ session('sucesso') }}</p>
	</div>
@endif
@if (session('erro'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<p>{{ session('erro') }}</p>
	</div>
@endif

<div class="card mb-4">
	<div class="card-header">
		<div class="row">
			<div class="col-sm-2">
				O.P.: <b>{{ $pedido->ordem_producao }}</b>
			</div>
			<div class="col-sm-8">
			{{ $pedido->titulo }}
			</div>
			<div class="col-sm-2 text-right">
			{{ $pedido->created_at }}
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-5">
				<div class="form-group">
					Cliente: @if(!is_null($cliente)) {{ $cliente->codigo }} - {{ $cliente->nome }} @endif
				</div>
			</div>
			<div class="col-sm-5">
				<div class="form-group">
					Produto: @if(!is_null($produto)) {{ $produto->id_externo }} - {{ $produto->titulo }} @endif
				</div>
			</div>
			<div class="col-sm-1"></div>
		</div>

		<form action='/reimpressao_album/salvar' method='post' id="formPedido">
			@if(!$pedido->imprimir)

			@csrf
			<input type="hidden" id="id" name="id" value="{{ $pedido->id }}">
			<input type="hidden" id="imprimir" name="imprimir" value="0">
			@if(!is_null($produto))
			<input type="hidden" id="id_produto" name="id_produto" value="{{ $produto->id_externo }}">
			@endif

			<div class="row">
				<div class="col-sm-2">
					<div class="form-group">
						<label for="id_album">Álbum</label>
						<input type="number" id="id_album" name="id_album" class="form-control form-control-sm" autofocus required>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<label for="id_foto">Foto</label>
						<input type="number" id="id_foto" name="id_foto" class="form-control form-control-sm" required>
					</div>
					</div>
				<div class="col-sm-1">
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label for="id_celula">Célula de Origem</label>
						<select id="id_celula" name="id_celula" class="form-control form-control-sm" required>
							<option value=""></option>
							@foreach($celulas as $celula)
							<option value="{{$celula->id}}" @if(Session::get('reimp_celula') == $celula->id) selected @endif>{{$celula->nome}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<!--
				<div class="col-sm-3">
					<div class="form-group">
						<label for="id_indicador">Indicador</label>
						<select id="id_indicador" name="id_indicador" class="form-control form-control-sm" required>
							<option value=""></option>
							@foreach($indicadores as $indicador)
							<option value="{{$indicador->id}}" @if(Session::get('reimp_indicador') == $indicador->id) selected @endif>{{$indicador->descricao}}</option>
							@endforeach
						</select>
					</div>
				</div>
-->
				<div class="col-sm-1">
					<div class="form-group">
						<label>&nbsp;</label><br>
						<button type="submit" class="btn btn-primary btn-sm">Buscar</button>
					</div>
				</div>
			</div>
			@endif

			<div class="table-responsive">
				<table class="table table-striped table-sm">
					<thead>
						<tr>
							<th>Foto frente</th>
							<th>Foto verso</th>
							<th>Álbum</th>
							<th>Célula</th>
							<th>Falha</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					@foreach($laminas as $lamina)
						<tr>
							<td>{{ $lamina->foto_frente }}</td>
							<td>{{ $lamina->foto_verso }}</td>
							<td>{{ $lamina->album }}</td>
							<td>{{ $lamina->celula }}</td>
							<td>{{ $lamina->indicador }}</td>
							<td>
								<a href="javascript:void(0)" onclick="Deletar({{ $pedido->id }}, {{ $lamina->id }})"><i class="fas fa-trash text-danger"></i></a>
							</td>
						</tr>
					@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th colspan="5">{{ count($laminas)*2 }} fotos</th>
						</tr>
					</tfoot>
				</table>
			</div>
		
			@if(!$pedido->imprimir)
			<div class="text-right">
				<button type="button" class="btn btn-success" onclick="Imprimir()">Imprimir</button>
			</div>
			@endif
		</form>

	</div>
</div>

<link  href="{{ asset('assets/cropper/cropper.css') }}" rel="stylesheet">
<script src="{{ asset('assets/cropper/cropper.js') }}"></script>

<script>
var inverter = false;
var image = null;
var options = null;
var cropper = null;

function Imprimir(){
	$('#imprimir').val(1);
	$('#formPedido').submit();
}

function Deletar(id_pedido, id_lamina){
	var _token = document.getElementsByName('_token')[0].value;

	$.ajax({
		url: '/reimpressao_album_deletar',
		method: 'post',
		data: {
			_token,
			id_pedido,
			id_lamina
		},
		success: function (result) {
			if(result.success){
				location.reload();
			}else{
				console.log('success', result)
			}
		},
		error: function (result) {
			console.log('Erro', result);
		}
	});
}
</script>
@endsection