@extends('layouts.app')

@section('title', 'Produto')

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

<h1 class="mt-4">Produto</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/produtos">Produtos</a></li>
	<li class="breadcrumb-item active">Produto</li>
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
		<form action='/produto/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $produto->id }}'>

			<div class="form-row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="id_externo" class="small mb-1">ID Externo</label>
						<input type="number" id="id_externo" name="id_externo" value='{{ $produto->id_externo }}' placeholder="ID Externo" class="form-control py-1" />
					</div>
				</div>
				<div class="col-md-9">
					<div class="form-group">
						<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
						<input type="text" id="titulo" name="titulo" value='{{ $produto->titulo }}' placeholder="Título" class="form-control py-1" required>
					</div>
				</div>

			</div>

			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="imposicao_tipo_id" class="small mb-1">Ferramenta de imposição</label>
						<select id="imposicao_tipo_id" name="imposicao_tipo_id" class="form-control py-1">
							<option value=''>Nenhum</option>
							@foreach($ferramentas as $ferramenta)
							<option value='{{ $ferramenta->id }}' @if($ferramenta->id == $produto->imposicao_tipo_id) selected @endif>{{ $ferramenta->titulo }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="imposicao_nome_id" class="small mb-1">Modelo de imposição</label>
						<select id="imposicao_nome_id" name="imposicao_nome_id" class="form-control py-1">
							<option value=''>Nenhum</option>
							@foreach($modelos as $modelo)
							<option value='{{ $modelo->id }}' @if($modelo->id == $produto->imposicao_nome_id) selected @endif>{{ $modelo->titulo }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="impressao_hotfolder_id" class="small mb-1">HotFolder de impressão</label>
						<select id="impressao_hotfolder_id" name="impressao_hotfolder_id" class="form-control py-1">
							<option value=''>Nenhum</option>
							@foreach($hotfolders as $hotfolder)
							<option value='{{ $hotfolder->id }}' @if($hotfolder->id == $produto->impressao_hotfolder_id) selected @endif>{{ $hotfolder->titulo }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="impressao_substrato_id" class="small mb-1">Substrato de impressão</label>
						<select id="impressao_substrato_id" name="impressao_substrato_id" class="form-control py-1">
							<option value=''>Nenhum</option>
							@foreach($substratos as $substrato)
							<option value='{{ $substrato->id }}' @if($substrato->id == $produto->impressao_substrato_id) selected @endif>{{ $substrato->titulo }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label for="disposicao" class="small mb-1"><span class="text-danger">*</span> Disposição</label>
						<select id="disposicao" name="disposicao" class="form-control py-1" required>
							<option value=''>-- Selecione --</option>
							<option value='Simplex' @if($produto->disposicao == 'Simplex') selected @endif>Simplex</option>
							<option value='Duplex' @if($produto->disposicao == 'Duplex') selected @endif>Duplex</option>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<br>
						<div class="custom-control custom-checkbox">
							&nbsp;&nbsp;
							<input class="custom-control-input" id="renomear" type="checkbox" name="renomear" {{ $produto->renomear ? 'checked' : '' }}>
							<label class="custom-control-label" for="renomear">Renomear fotos</label>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<br>
						<div class="custom-control custom-checkbox">
							&nbsp;&nbsp;
							<input class="custom-control-input" id="sem_dimensao" type="checkbox" name="sem_dimensao" onchange="SemDimensao()" {{ $produto->sem_dimensao ? 'checked' : '' }}>
							<label class="custom-control-label" for="sem_dimensao">Sem dimensões</label>
						</div>
					</div>
				</div>
			</div>



			<div id="dv_sangria">
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="largura" class="small mb-1"><span class="text-danger">*</span> Largura (mm)</label>
							<input type="number" id="largura" name="largura" value='{{ $produto->largura }}' placeholder="Largura" class="form-control py-1" onchange="TamanhoProduto()" require>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="altura" class="small mb-1"><span class="text-danger">*</span> Altura (mm)</label>
							<input type="number" id="altura" name="altura" value='{{ $produto->altura }}' placeholder="Altura" class="form-control py-1" onchange="TamanhoProduto()" require>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="small mb-1">Sangria (mm)</label>
					<table>
						<tr>
							<td></td>
							<td>
								<div class="form-group">
									<label for="sangr_sup" class="small mb-1">Superior</label>
									<input type="number" id="sangr_sup" name="sangr_sup" value='{{ $produto->sangr_sup }}' min='0' class="form-control py-1" />
								</div>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for="sangr_esq" class="small mb-1">Esquerda</label>
									<input type="number" id="sangr_esq" name="sangr_esq" value='{{ $produto->sangr_esq }}' min='0' class="form-control py-1" />
								</div>
							</td>
							<td>
								<div id="dv_tamanho"></div>
							</td>
							<td>
								<div class="form-group">
									<label for="sangr_dir" class="small mb-1">Direita</label>
									<input type="number" id="sangr_dir" name="sangr_dir" value='{{ $produto->sangr_dir }}' min='0' class="form-control py-1" />
								</div>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<div class="form-group">
									<label for="sangr_inf" class="small mb-1">Inferior</label>
									<input type="number" id="sangr_inf" name="sangr_inf" value='{{ $produto->sangr_inf }}' placeholder="mm" class="form-control py-1" />
								</div>
							</td>
							<td></td>
						</tr>
					</table>
				</div>
			</div>

			<div class="form-group mt-4 mb-0 text-right">
				<button class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</div>

<script>
	function SemDimensao(){
		var chk = $('#sem_dimensao')[0].checked;

		if(chk){
			$('#largura').removeAttr('required');
			$('#altura').removeAttr('required');
			$('#dv_sangria').attr('style','display:none');
		}else{
			$('#largura').attr('required','required');
			$('#altura').attr('required','required');
			$('#dv_sangria').removeAttr('style');
		}
	}

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
		SemDimensao();
		TamanhoProduto();
	});
</script>
@endsection
