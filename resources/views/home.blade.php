@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="mt-4">Dashboard</h1>
	<ol class="breadcrumb mb-4">
		<li class="breadcrumb-item active">Dashboard</li>
	</ol>
	<div class="row">
		<div class="col-xl-3 col-md-6">
			<div class="card bg-primary text-white mb-4">
				<div class="card-body">
					OSs abertas
					<h1>{{ $contador->abertas }}</h1>
				</div>
				<div class="card-footer d-flex align-items-center justify-content-between">
					<a class="small text-white stretched-link" href="/pedidos">Ver detalhes</a>
					<div class="small text-white"><i class="fas fa-angle-right"></i></div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-warning text-white mb-4">
				<div class="card-body">
					OSs aguardando arquivamento
					<h1>{{ $contador->arquivamento }}</h1>
				</div>
				<div class="card-footer d-flex align-items-center justify-content-between">
					<a class="small text-white stretched-link" href="/arquivamento">Ver detalhes</a>
					<div class="small text-white"><i class="fas fa-angle-right"></i></div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-success text-white mb-4">
				<div class="card-body">
					OPs prontas para imprimir
					<h1>{{ $contador->imprimir }}</h1>
				</div>
				<div class="card-footer d-flex align-items-center justify-content-between">
					<a class="small text-white stretched-link" href="/pedidos_itens">Ver detalhes</a>
					<div class="small text-white"><i class="fas fa-angle-right"></i></div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-danger text-white mb-4">
				<div class="card-body">
					OPs aguardando recorte
					<h1>{{ $contador->recorte }}</h1>
				</div>
				<div class="card-footer d-flex align-items-center justify-content-between">
					<a class="small text-white stretched-link" href="/recortes">Ver detalhes</a>
					<div class="small text-white"><i class="fas fa-angle-right"></i></div>
				</div>
			</div>
		</div>
	</div>
	
	@if(in_array('status_servicos', $autorizacao))
	<div class="card mb-4">
		<div class="card-header">
			<i class="fas fa-chart-area mr-1"></i>
			Status de Serviços
		</div>
		<div class="card-body">
			<table class="table table-bordered table-striped table-sm">
				<thead>
					<tr>
						<th>Serviço</th>
						<th>Último Processo</th>
						<th>Ativo</th>
					</tr>
				</thead>
				<tbody id='tb_status_servicos'>
					<tr>
						<td colspan='3'><div class="spinner-border"></div></td>
					</tr>
				</tbody>
			</table>


			<table class="table table-bordered table-striped table-sm">
				<thead>
					<tr>
						<th>Serviço</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<a href="/servico_automacao/servico/importar">Importar arquivos</a>
						</td>
					<tr>
					</tr>
						<td>
							<a href="/servico_automacao/servico/exportar">Exportar arquivos</a>
						</td>
					</tr>
					</tr>
						<td>
							<a href="/servico_automacao/servico/saida_correcao">Saída da correção</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	@endif

	<div class="card mb-4">
		<div class="card-header">
			<i class="fas fa-chart-area mr-1"></i>
			Gráficos de Produção
		</div>
		<div class="card-body">
		
			<div class="row">
				<div class="col-xl-6">
					<div id="chart_op_celula">
						<div class="spinner-border"></div>
					</div>
				</div>
				<div class="col-xl-6">
					<div id="chart_tempo_medio_celula">
						<div class="spinner-border"></div>
					</div>
				</div>
			</div>
	
			<div class="row">
				<div class="col-xl-6">
					<div id="chart_impressao_mensal">
						<div class="spinner-border"></div>
					</div>
				</div>
				<div class="col-xl-6">
					<div id="chart_perda_pensal">
						<div class="spinner-border"></div>
					</div>
				</div>
			</div>
			<br>
			
			<div class="row">
				<div class="col-xl-6">
					<div id="chart_producao_mensal">
						<div class="spinner-border"></div>
					</div>
				</div>
				<div class="col-xl-6">
					<div id="chart_albuns_mensais">
						<div class="spinner-border"></div>
					</div>
				</div>
			</div>
			<br>

			<div id="chart_situacao_op">
				<div class="spinner-border"></div>
			</div>
			
		</div>
	</div>

	<div class="card mb-4">
		<div class="card-header">
			<i class="fas fa-chart-area mr-1"></i>
			Gráficos de Venda
		</div>
		<div class="card-body">
			<div id="chart_ProdutosMaisVendidos">
				<div class="spinner-border"></div>
			</div>
			<div id="chart_ProdutosMaisProduzidos">
				<div class="spinner-border"></div>
			</div>

			<div class="row">
				<div class="col-xl-6">
					
				</div>
				<div class="col-xl-6">
					
				</div>
			</div>

		</div>
	</div>

	@if(in_array('dsh_fin', $autorizacao))
	<div class="card mb-4">
		<div class="card-header">
			<i class="fas fa-chart-area mr-1"></i>
			Administrativo e Financeiro
		</div>
		<div class="card-body">
			<div id="chart_indice_liquidez">
				<div class="spinner-border"></div>
			</div>
			<div id="chart_mediaPrazosRecebimento">
				<div class="spinner-border"></div>
			</div>

			<div class="row">
				<div class="col-xl-4">
					<div id="chart_totalPagarReceber">
						<div class="spinner-border"></div>
					</div>
				</div>
				<div class="col-xl-4">
					<div id="chart_top5pagar">
						<div class="spinner-border"></div>
					</div>
				</div>
				<div class="col-xl-4">
					<div id="chart_top5receber">
						<div class="spinner-border"></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xl-6">
					<div id="chart_valoresPorTipos">
						<div class="spinner-border"></div>
					</div>
				</div>
				<div class="col-xl-6">
					<div id="chart_perdasPorFaturamento">
						<div class="spinner-border"></div>
					</div>
				</div>
			</div>
			
			<div id="chart_comprasPorFaturamento">
				<div class="spinner-border"></div>
			</div>
			
			<br>
			<div class="row">
				<div class="col-xl-8">
					<div id="chart_boletosPorMes">
						<div class="spinner-border"></div>
					</div>
				</div>
				<div class="col-xl-4">
					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="card bg-primary text-white mb-4">
								<div class="card-body text-center">
									Boletos a vencer na semana
									<h4 id="boletoAVencerPorSemana"></h4>
								</div>
							</div>
						</div>
						<div class="col-xl-2"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif

	<div class="card mb-4">
		<div class="card-header">
			Ranking de Indicadores de Falhas
		</div>
		<div class="card-body">
			<div class="form-group">
				<label>Período:</label>
				<div class="row">
					<div class="col-sm-5">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">De</i></span>
							</div>
							<input type="date" id="dt_inicio_RankingIndicadores" class="form-control">
						</div>
					</div>
					<div class="col-sm-5">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Até</i></span>
							</div>
							<input type="date" id="dt_fim_RankingIndicadores" class="form-control">
						</div>
					</div>
					<div class="col-sm-2">
						<button class="btn btn-primary btn-sm" onclick="RankingIndicadores('{{ asset('/RankingIndicadores') }}')">Filtrar</button>
					</div>
				</div>
			</div>

			<div id="chart_RankingIndicadores">
				<div class="spinner-border"></div>
			</div>
		</div>
	</div>
	
	
	<div class="card mb-4">
		<div class="card-header">
			Indicadores de Falhas por Oordem de Produção
		</div>
		<div class="card-body">
			<div class="form-group">
				<div class="row">
					<div class="col-sm-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">OP</i></span>
							</div>
							<input type="number" id="op" class="form-control">
						</div>
					</div>

					<div class="col-sm-2">
						<button class="btn btn-primary btn-sm" onclick="IndicadoresPorOP('{{ asset('/IndicadoresPorOP') }}')">Filtrar</button>
					</div>
				</div>
			</div>

			<div id="chart_IndicadoresPorOP">
				<p>Insira uma OP</p>
			</div>
		</div>
	</div>
	

	<div class="card mb-4">
		<div class="card-header">
			Indicadores de Falhas por Produto
		</div>
		<div class="card-body">
			<div class="form-group">
				<label>Período:</label>
				<div class="row">
					<div class="col-sm-3">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">De</i></span>
							</div>
							<input type="date" id="dt_inicio_IndicadoresPorProduto" class="form-control">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Até</i></span>
							</div>
							<input type="date" id="dt_fim_IndicadoresPorProduto" class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Produto</i></span>
							</div>
							<select id="produto" class="form-control">
								<option value=""></option>
								@foreach($produtos as $produto)
								<option value="{{$produto->id}}">{{$produto->id_externo}} - {{$produto->titulo}}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="col-sm-2">
						<button class="btn btn-primary btn-sm" onclick="IndicadoresPorProduto('{{ asset('/IndicadoresPorProduto') }}')">Filtrar</button>
					</div>
				</div>
			</div>

			<div id="chart_IndicadoresPorProduto">
				<p>Selecione o produto</p>
			</div>
		</div>
	</div>
	

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="{{ asset('js/dashboard.js') }}"></script>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		var inicio = Data(-1);
        var fim = Data();

		StatusServicos("{{ asset('/statusservicos') }}");
		ProducaoMensal("{{ asset('/producaomensal') }}");
		PerdaMensal("{{ asset('/perdamensal') }}");
		ImpressaoMensal("{{ asset('/ImpressaoMensal') }}");
		OpPorCelula("{{ asset('/opporcelula') }}");
		TempoPorCelula("{{ asset('/tempoporcelula') }}");
		AlbunsMensais("{{ asset('/AlbunsMensais') }}");
		SituacaoOPsMensais("{{ asset('/SituacaoOPsMensais') }}");

		ProdutosMaisVendidos("{{ asset('/ProdutosMaisVendidos') }}");
		ProdutosMaisProduzidos("{{ asset('/ProdutosMaisProduzidos') }}");

		IndiceLiquidez("{{ asset('/IndiceLiquidez') }}");
		MediaPrazosRecebimento("{{ asset('/MediaPrazosRecebimento') }}");
		TotalPagarReceber("{{ asset('/TotalPagarReceber') }}");
		Top5Pagar("{{ asset('/Top5Pagar') }}");
		Top5Receber("{{ asset('/Top5Receber') }}");
		ValoresPorTipos("{{ asset('/ValoresPorTipos') }}");
		PerdasPorFaturamento("{{ asset('/PerdasPorFaturamento') }}");
		
		ComprasPorFaturamento("{{ asset('/ComprasPorFaturamento') }}");
		BoletosPorMes("{{ asset('/BoletosPorMes') }}");
		BoletosAVencerPorSemana("{{ asset('/BoletosAVencerPorSemana') }}");

		
        $('#dt_inicio_RankingIndicadores').val(inicio);
        $('#dt_fim_RankingIndicadores').val(fim);
		RankingIndicadores("{{ asset('/RankingIndicadores') }}");
		
		$('#dt_inicio_IndicadoresPorProduto').val(inicio);
        $('#dt_fim_IndicadoresPorProduto').val(fim);
		//IndicadoresPorProduto("{{ asset('/IndicadoresPorProduto') }}");

		setInterval(()=>{
			StatusServicos("{{ asset('/statusservicos') }}");
		}, 30000);
	});
	</script>
@endsection