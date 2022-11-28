@extends('layouts.app')

@section('title', 'Recortes')

@section('content')
<h1 class="mt-4">Recortes</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Recortes</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
		
		@if (session('status'))
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('status') }}</p>
			</div>
		@endif
		@if (session('erro'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{{ session('erro') }}</p>
			</div>
		@endif
		
		<div class="table-responsive">
			<table class="table table-bordered dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>O.S.</th>
						<th>Produto</th>
						<th>Tamanho</th>
						<th>Arquivos</th>
						<th></th>
					</tr>
				</thead>
				@if(count($recortes) > 5)
				<tfoot>
					<tr>
						<th>O.S.</th>
						<th>Produto</th>
						<th>Tamanho (mm)</th>
						<th>Arquivos</th>
						<th></th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@foreach($recortes as $recorte)
					<tr>
						<td>{{ $recorte->os }}</td>
						<td>{{ $recorte->cod_produto }} - {{ $recorte->produto }}</td>
						<td>{{ $recorte->tamanho }}</td>
						<td>{{ $recorte->arquivos }}</td>
						<td>
						@if(in_array('recorte_edit', session()->get('funcoes')))
							<a href="/recorte/{{ $recorte->id_item }}"><i class="fas fa-crop-alt"></i></a>
						@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection