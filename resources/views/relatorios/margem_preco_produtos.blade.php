@extends('layouts.app')

@section('title', 'Margem de preços de produtos')

@section('content')
<h1 class="mt-4">Margem de preços de produtos</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Margem de preços de produtos</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		
	</div>
	<div class="card-body">
		
		<div class="table-responsive">
			<table class="table table-bordered table-sm table-striped dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Cliente</th>
						<th>Produto</th>
						<th>Custo (R$)</th>
						<th>Venda (R$)</th>
                        <th>Margem (%)</th>
					</tr>
				</thead>
				@if(count($produtos) > 5)
				<tfoot>
					<tr>
                        <th>Cliente</th>
						<th>Produto</th>
						<th>Custo (R$)</th>
						<th>Venda (R$)</th>
                        <th>Margem (%)</th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($produtos) > 0)
					@foreach($produtos as $produto)
					
					<tr>
						<td>{{ $produto->cliente }}</td>
						<td>{{ $produto->descricao }}</td>
						<td>R$ {{ $produto->custo }}</td>
						<td>R$ {{ $produto->preco }}</td>
						<td>{{ $produto->Margem() }}%</td>
					</tr>

					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection