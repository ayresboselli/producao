@extends('layouts.app')

@section('title', 'Upload')

@section('content')
<style>
	#dv_sangria{
		 max-width: 500px;
		 margin: auto;
	}
	#dv_tamanho{
		border: 1px solid black;
		margin: auto;
		width: 100px;
		height: 150px;
	}
</style>

<h1 class="mt-4">Upload</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href='/produtos'>Produtos</a></li>
	<li class="breadcrumb-item"><a href='/uploads/{{ $produto->id }}'>Uploads</a></li>
	<li class="breadcrumb-item active">Upload</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		Produto
	</div>
	<div class="card-body">
		<div class="form-row">
			<div class="col-sm-2">
				<label>ID:</label>
				<b>{{ $produto->id_externo }}</b>
			</div>
			<div class="col-sm-10">
				<label>Título:</label>
				<b>{{ $produto->titulo }}</b>
			</div>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($upload->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/upload/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $upload->id }}'>
			<input type='hidden' id='produto' name='produto' value='{{ $produto->id }}'>
			
			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
						<input type="text" id="titulo" name="titulo" value='{{ $upload->titulo }}' placeholder="Título" class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="tipo_upload" class="small mb-1"><span class="text-danger">*</span> Tipo</label>
						<select id="tipo_upload" name="tipo_upload" class="form-control py-1" required>
							<option value=''>-- Selecione --</option>
							<option value='A' @if($upload->tipo_upload == 'A') selected @endif>Arquivo</option>
							<option value='P' @if($upload->tipo_upload == 'P') selected @endif>Pasta</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="replicar" class="small mb-1"><span class="text-danger">*</span> Replicar arquivos para cada álbum</label>
						<select id="replicar" name="replicar" class="form-control py-1" required>
							<option value='' @if($upload->replicar == '') selected @endif>-- Selecione --</option>
							<option value='S' @if($upload->replicar == 'S') selected @endif>Sim</option>
							<option value='N' @if($upload->replicar == 'N') selected @endif>Não</option>
							<option value='?' @if($upload->replicar == '?') selected @endif>Decidir no momento</option>
						</select>
					</div>
				</div>
				
				<div class="col-md-7">
				</div>
			</div>
			<!--
			<div class="row">
				<div class="col-md-6">
					<div class="form-group mt-4 mb-0 text-left">
						<a href="/uploads" class="btn btn-default">Voltar</a>
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

<script>
	function TamanhoProduto(){
		var limite = 300;
		var largura = $("#largura").val();
		var altura = $("#altura").val();
		console.log(largura,altura);
		if(largura > 0 && altura > 0){
			if(largura > limite){
				altura -= (altura * ((largura - limite)/largura));
				largura = limite;
			}
			
			$("#dv_tamanho").width(largura);
			$("#dv_tamanho").height(altura);
			
		}
	}
	
	document.addEventListener("DOMContentLoaded", function(event) {
		TamanhoProduto();
	});
</script>
@endsection