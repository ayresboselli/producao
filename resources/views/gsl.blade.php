@extends('layouts.app')

@section('title', 'Fluxo')

@section('content')
<h1 class="mt-4">Fluxo</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Fluxo</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
			Reprocessar <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li><a href="javascript:void(0)" onclick="Reprocessar(1)">Correção</a></li>
			<li><a href="javascript:void(0)" onclick="Reprocessar(2)">Imposição</a></li>
			<li><a href="javascript:void(0)" onclick="Reprocessar(3)">Impressão</a></li>
		</ul>
		@if(in_array('gsl_edit', session()->get('funcoes')))
		<div class="float-right">
			<button class="btn btn-default" data-toggle="modal" data-target="#modalConfig">Config</button>
		</div>
		@endif
	</div>
	<div class="card-body">
		
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
		
		
		
		<div class="row">
			<div class="col_sm-2">
				<div class="form-group">
					<label for="filtro_os">O.S.</label>
					<input type="number" id="filtro_os" class="form-control form-control-sm">
				</div>
			</div>
			<div class="col_sm-2">
				<div class="form-group">
					<label for="filtro_op">O.P.</label>
					<input type="number" id="filtro_op" class="form-control form-control-sm">
				</div>
			</div>
			<div class="col_sm-1">
				<div class="form-group">
					<br>
					<button class="btn btn-primary btn-sm mt-2" onclick="Filtrar(true)">Filtrar</button>
				</div>
			</div>
		</div>


		<div class="table-responsive">
			<table class="table table-bordered table-striped table-sm" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>OS</th>
                        <th>OP</th>
						<th>Nome Álbum</th>
                        <th>Tipo</th>
                        <th @if(!$config[0]->ativo) class="text-danger" @endif>Correção</th>
						<th @if(!$config[1]->ativo) class="text-danger" @endif>Imposição</th>
						<th @if(!$config[2]->ativo) class="text-danger" @endif>Impressão</th>
						<th><input type='checkbox' onclick="SelecionarTodos(this.checked)"></th>
					</tr>
				</thead>
				<!--
				<tfoot>
					<tr>
						<th>OS</th>
                        <th>OP</th>
						<th>Nome Álbum</th>
                        <th>Tipo</th>
                        <th @if(!$config[0]->ativo) class="text-danger" @endif>Correção</th>
						<th @if(!$config[1]->ativo) class="text-danger" @endif>Imposição</th>
						<th @if(!$config[2]->ativo) class="text-danger" @endif>Impressão</th>
						<th><input type='checkbox' class="checkbox" onclick="SelecionarTodos(this.checked)"></th>
					</tr>
				</tfoot>
				-->
				<tbody id="tbl_gsls">
					<tr><td colspan="8">Insira as informações no filtro</td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal Reprocessar -->
<div class="modal" id="modalReprocessar">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Reprocessar</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<p>Tem certeza que deseja reprocessar <span id='qtd_reprocessar'></span>?</p>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/gsl/reprocessar" method='post'>
					@csrf
					<input type='hidden' id='opc_reprocessar' name='opc_reprocessar'>
					<input type='hidden' id='lista_reprocessar' name='lista_reprocessar'>
					<!--<button type="submit" class="btn btn-danger">Reprocessar</button>-->
				</form>

				<button class="btn btn-danger" onclick="ReprocessarConfirmar()">Reprocessar</button>
			</div>

		</div>
	</div>
</div>

<!-- Modal Configurações -->
<div class="modal" id="modalConfig">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Configurações</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form action="/gsl/configuracoes" method='post'>
				@csrf

				<!-- Modal body -->
				<div class="modal-body">
					<table class="table table-bordered table-striped table-sm" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th>Função</th>
								<th>Ativo</th>
							</tr>
						</thead>
						<tbody>
							@foreach($config as $c)
							<tr>
								<td>{{ $c->funcao }}</td>
								<td>
									<input type="checkbox" id="chk_{{ $c->id }}" name="chk_{{ $c->id }}" @if($c->ativo) checked @endif>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-success">Salvar</button>
				</div>
			</form>

		</div>
	</div>
</div>

<script>
	var lista_reprocessar = [];

	function Filtrar(btn = false){
		var _token = document.getElementsByName('_token')[0].value;
		var os = $('#filtro_os').val();
		var op = $('#filtro_op').val();

		$.ajax({
			url: '{{ asset("/gsl/filtrar") }}',
			method: 'post',
			dataType: 'json',
			data: { _token, os, op },
			success: function (result) {
				var html = '';

				result.map((r)=>{
					if(r.tipo_pedido == 1){
						tipo_pedido = 'Contrato';
					}else{
						tipo_pedido = 'Pedido';
					}

					switch(r.correcao) {
						case 0:
							correcao = "<span class='text-default'>Inativo</span>";
						break;
						case 1:
							correcao = "<span class='text-secondary'>Aguardando</span>";
						break;
						case 2:
							correcao = "<span class='text-primary' title='"+r.dt_correcao_entrada+"'>Em andamento</span>";
						break;
						case 3:
							correcao = "<span class='text-success' title='"+r.dt_correcao_saida+"'>Finalizado</span>";
						break;
						default:
							correcao = "<span class='text-danger'>Falha</span>";
					}

					switch(r.imposicao) {
						case 0:
							imposicao = "<span class='text-default'>Inativo</span>";
						break;
						case 1:
							imposicao = "<span class='text-secondary'>Aguardando</span>";
						break;
						case 2:
							imposicao = "<span class='text-primary' title='"+r.dt_imposicao_entrada+"'>Em andamento</span>";
						break;
						case 3:
							imposicao = "<span class='text-success' title='"+r.dt_imposicao_saida+"'>Finalizado</span>";
						break;
						default:
						imposicao = "<span class='text-danger'>Falha</span>";
					}

					switch(r.impressao) {
						case 0:
							impressao = "<span class='text-default'>Inativo</span>";
						break;
						case 1:
							impressao = "<span class='text-secondary'>Aguardando</span>";
						break;
						case 2:
							impressao = "<span class='text-primary' title='"+r.dt_impressao_entrada+"'>Em andamento</span>";
						break;
						case 3:
							impressao = "<span class='text-success' title='"+r.dt_impressao_entrada+"'>Finalizado</span>";
						break;
						default:
							impressao = "<span class='text-danger'>Falha</span>";
					}

					var checked = '';
					if(lista_reprocessar.indexOf(r.id+'') >= 0){
						checked = ' checked';
					}

					html += "<tr>\n"; 
                    html += "    <td>"+r.ordem_servico+"</td>\n";
                    html += "    <td>"+r.ordem_producao+"</td>\n";
                    html += "    <td>"+r.nome_album+"</td>\n";
                    html += "    <td>"+tipo_pedido+"</td>\n";
                    html += "    <td>"+correcao+"</td>\n";
                    html += "    <td>"+imposicao+"</td>\n";
                    html += "    <td>"+impressao+"</td>\n";
					html += "    <td><input type='checkbox' id='chk_"+r.id+"' onchange='ReprocItem(this)' class='checkbox'"+checked+"></td>\n";
					html += "</tr>\n";
				});

				if(html == ''){
					if(btn){
						html = '<tr><td colspan="8">Nenhum registro encontrado</td></tr>';
					}else{
						html = '<tr><td colspan="8">Insira as informações no filtro</td></tr>';
					}
				}

				$('#tbl_gsls').html(html);
			},
			error: function (result) {
				console.log('Erro',result);
			}
		});
	}

	function SelecionarTodos(checked){
		var chk = $('.checkbox');

		lista_reprocessar = [];
		
		for(var i = 0; i < chk.length; i++){
			chk[i].checked = checked
			if(checked){
				lista_reprocessar.push(chk[i].id.split('_')[1]);
			}
		}
	}

	function ReprocItem(elem){
		var value = elem.id.split('_')[1];

		if(elem.checked){
			lista_reprocessar.push(value);
		}else{
			var index = lista_reprocessar.indexOf(value);
			if (index > -1) {
				lista_reprocessar.splice(index, 1); // 2nd parameter means remove one item only
			}
		}
	}

	function Reprocessar(opc){
		opcao_reprocessar = opc;

		elem = $('.checkbox');

		$('#opc_reprocessar').val(opc);
		/*
		for(var i = 0; i < elem.length; i++){
			if(elem[i].checked){
				lista_reprocessar.push(elem[i].id.split('_')[1]);
			}
		}
		*/
		if(lista_reprocessar.length >= 1){
			if(lista_reprocessar.length == 1){
				$('#qtd_reprocessar').text('1 álbum');
			}else{
				$('#qtd_reprocessar').text(lista_reprocessar.length + ' álbuns');
			}

			var saida = lista_reprocessar.join(',');
			$('#lista_reprocessar').val(saida);
			
			$('#modalReprocessar').modal('show');
		}
		
	}

	function ReprocessarConfirmar(){
		var _token = document.getElementsByName('_token')[0].value;
		var opc_reprocessar = $('#opc_reprocessar').val();
		var lista_reprocessar = $('#lista_reprocessar').val();

		$.ajax({
			url: '{{ asset("/gsl/reprocessar") }}',
			method: 'post',
			dataType: 'json',
			data: { 
				_token,
				opc_reprocessar,
				lista_reprocessar, 
			},
			success: function (result) {
				var html = '';

				lista_reprocessar = [];

				if(result.success){
					toastr.success('Álbum(ns) reporocessado(s) com sucesso');
					Filtrar();
				}else{
					toastr.success('Erro ao reprocessar o(s) álbum(ns)');
				}

				$('#modalReprocessar').modal('hide');
			},
			error: function (result) {
				console.log('Erro',result);
				toastr.success('Erro ao reprocessar o(s) álbum(ns)');
			}
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		SelecionarTodos(false);

		setInterval(()=>{
			Filtrar();
		}, 30000);
	});
	
</script>
@endsection