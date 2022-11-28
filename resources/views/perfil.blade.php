@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
<h1 class="mt-4">Perfil</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/perfis">Perfis</a></li>
	<li class="breadcrumb-item active">Perfil</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($perfil->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/perfil/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $perfil->id }}'>
			<input type="hidden" id="func_selecionada" name="func_selecionada" value="{{ $id_funcoes }}">
			
			<div class="form-group">
				<label for="titulo" class="small mb-1"><span class="text-danger">*</span> Título</label>
				<input type="text" id="titulo" name="titulo" placeholder="Título" value='{{ $perfil->titulo }}' class="form-control py-1" required>
			</div>
			
			<div class="form-group">
				<label for="descricao" class="small mb-1">Descrição</label>
				<textarea id="descricao" name="descricao" placeholder="Descrição" class="form-control py-1">{{ $perfil->descricao }}</textarea>
			</div>
			
			<br>
			<div class="form-row">
				<div class="col-sm-5">
					<p class="text-center">Todas as funções</p>
					<ul id="func_inativas" class="list-group multilista" ondrop="DropInativa(event)" ondragover="allowDrop(event)">
					@foreach($funcoes as $funcao)
						@if(is_null($funcao->id_perfil))
						<li id="item_func_{{ $funcao->id }}" value="{{ $funcao->id }}" class="list-group-item" draggable="true" ondragstart="DragAtiva(event)">
							{{ $funcao->chave }} - {{ $funcao->descricao }}
						</li>
						@endif
					@endforeach
					</ul>
				</div>
				<div class="col-sm-2" style="margin:auto">
					<div class="align-middle text-center">
						<button type="button" class="btn btn-default" onclick="HabilitaFuncao()">>></button>
						<br><br>
						<button type="button" class="btn btn-default" onclick="DesabilitaFuncao()"><<</button>
					</div>
				</div>
				<div class="col-sm-5">
					<p class="text-center">Funções do perfil</p>
					<ul id="func_ativas" class="list-group multilista" ondrop="DropAtiva(event)" ondragover="allowDrop(event)">
					@foreach($funcoes as $funcao)
						@if(!is_null($funcao->id_perfil))
						<li id="item_func_{{ $funcao->id }}" value="{{ $funcao->id }}" class="list-group-item" draggable="true" ondragstart="DragInativa(event)">
							{{ $funcao->chave }} - {{ $funcao->descricao }}
						</li>
						@endif
					@endforeach
					</ul>
				</div>
			</div>

			<div class="form-group mt-4 mb-0 text-right">
				<button class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</div>

<script>
	function AddIdFuncao(id) {
		id_funcoes = document.getElementById('func_selecionada').value.split(',');
		id_funcoes.push(id)
		document.getElementById('func_selecionada').value = id_funcoes.join(',');
	}

	function RemoveIdFuncao(id) {
		id_funcoes = document.getElementById('func_selecionada').value.split(',');
		
		var tmp = [];
		for(var i = 0; i < id_funcoes.length; i++){
			if(id_funcoes[i] != id)
				tmp.push(id_funcoes[i]);
		}

		id_funcoes = tmp;
		document.getElementById('func_selecionada').value = id_funcoes.join(',');
	}

	function allowDrop(ev) {
		ev.preventDefault();
	}

	function DragAtiva(ev) {
		ev.dataTransfer.setData("ativa", ev.target.id);
	}

	function DragInativa(ev) {
		ev.dataTransfer.setData("inativa", ev.target.id);
	}

	function DropAtiva(ev) {
		ev.preventDefault();
		var data = ev.dataTransfer.getData("ativa");

		AddIdFuncao(document.getElementById(data).value);
		
		document.getElementById('func_ativas').appendChild(document.getElementById(data));
	}

	function DropInativa(ev) {
		ev.preventDefault();
		var data = ev.dataTransfer.getData("inativa");

		RemoveIdFuncao(document.getElementById(data).value);

		document.getElementById('func_inativas').appendChild(document.getElementById(data));
	}

	function HabilitaFuncao() {
		var lista = document.getElementById('func_inativas').children;

		for(var i = 0; i < lista.length; i++){
			AddIdFuncao(lista[i].value);
			document.getElementById('func_ativas').appendChild(lista[i]);
		}
	}
	
	function DesabilitaFuncao() {
		var lista = document.getElementById('func_ativas').children;

		for(var i = 0; i < lista.length; i++){
			RemoveIdFuncao(lista[i].value);
			document.getElementById('func_inativas').appendChild(lista[i]);
		}
	}
</script>
@endsection