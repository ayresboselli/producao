@extends('layouts.app')

@section('title', 'Usuário')

@section('content')
<h1 class="mt-4">Usuário</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/usuarios">Usuários</a></li>
	<li class="breadcrumb-item active">Usuário</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		@if(is_null($usuario->id))
		Cadastro
		@else
		Alteração
		@endif
	</div>
	<div class="card-body">
		<form action='/usuario/salvar' method='post'>
			@csrf
			<input type='hidden' id='id' name='id' value='{{ $usuario->id }}'>
			<input type="hidden" id="id_perfis" name="id_perfis" value="{{ $id_perfis }}">
			
			<div class="form-row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="nome" class="small mb-1"><span class="text-danger">*</span> Nome</label>
						<input type="text" id="nome" name="nome" placeholder="Nome" value='{{ $usuario->name }}' class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="email" class="small mb-1"><span class="text-danger">*</span> E-Mail</label>
						<input type="email" id="email" name="email" placeholder="E-Mail" value='{{ $usuario->email }}' class="form-control py-1" required>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<br>
						<div class='custom-control custom-checkbox'>
							<input class='custom-control-input' id='ativo' name='ativo' type='checkbox' @if($usuario->ativo) checked @endif>
							<label class='custom-control-label' for='ativo'>Ativo</label>
						</div>
					</div>
				</div>
			</div>

			<br>
			<div class="form-row">
				<div class="col-sm-5">
					<p class="text-center">Todas as funções</p>
					<ul id="perfis_inativos" class="list-group multilista" ondrop="DropInativa(event)" ondragover="allowDrop(event)">
					@foreach($perfis as $perfil)
						@if(is_null($perfil->id_usuario))
						<li id="item_func_{{ $perfil->id }}" value="{{ $perfil->id }}" class="list-group-item" draggable="true" ondragstart="DragAtiva(event)">
							{{ $perfil->titulo }}
						</li>
						@endif
					@endforeach
					</ul>
				</div>
				<div class="col-sm-2" style="margin:auto">
					<div class="align-middle text-center">
						<button type="button" class="btn btn-default" onclick="HabilitaPerfil()">>></button>
						<br><br>
						<button type="button" class="btn btn-default" onclick="DesabilitaPerfil()"><<</button>
					</div>
				</div>
				<div class="col-sm-5">
					<p class="text-center">Funções do perfil</p>
					<ul id="perfis_ativos" class="list-group multilista" ondrop="DropAtiva(event)" ondragover="allowDrop(event)">
					@foreach($perfis as $perfil)
						@if(!is_null($perfil->id_usuario))
						<li id="item_func_{{ $perfil->id }}" value="{{ $perfil->id }}" class="list-group-item" draggable="true" ondragstart="DragInativa(event)">
							{{ $perfil->titulo }}
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
	function AddIdPerfil(id) {
		id_perfis = document.getElementById('id_perfis').value.split(',');
		id_perfis.push(id)
		document.getElementById('id_perfis').value = id_perfis.join(',');
	}

	function RemoveIdPerfil(id) {
		id_perfis = document.getElementById('id_perfis').value.split(',');
		id_perfis.pop(id)
		document.getElementById('id_perfis').value = id_perfis.join(',');
	}

	function allowDrop(ev) {
		ev.preventDefault();
	}

	function DragAtiva(ev) {
		ev.dataTransfer.setData("ativo", ev.target.id);
	}

	function DragInativa(ev) {
		ev.dataTransfer.setData("inativo", ev.target.id);
	}

	function DropAtiva(ev) {
		ev.preventDefault();
		var data = ev.dataTransfer.getData("ativo");

		AddIdPerfil(document.getElementById(data).value);
		
		document.getElementById('perfis_ativos').appendChild(document.getElementById(data));
	}

	function DropInativa(ev) {
		ev.preventDefault();
		var data = ev.dataTransfer.getData("inativo");

		RemoveIdPerfil(document.getElementById(data).value);

		document.getElementById('perfis_inativos').appendChild(document.getElementById(data));
	}

	function HabilitaPerfil() {
		var lista = document.getElementById('perfis_inativos').children;

		for(var i = 0; i < lista.length; i++){
			AddIdPerfil(lista[i].value);
			document.getElementById('perfis_ativos').appendChild(lista[i]);
		}
	}
	
	function DesabilitaPerfil() {
		var lista = document.getElementById('perfis_ativos').children;

		for(var i = 0; i < lista.length; i++){
			RemoveIdPerfil(lista[i].value);
			document.getElementById('perfis_inativos').appendChild(lista[i]);
		}
	}
</script>
@endsection