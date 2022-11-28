@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<h1 class="mt-4">Serviços</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Exportar</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
	</div>
	<div class="card-body">
		
		<div class="table-responsive">
			<table class="table table-bordered table-striped table-sm dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>ID</th>
						<th>OS</th>
						<th>OP</th>
                        <th>Fotos</th>
                        <th>Início</th>
						<th>Situação</th>
                        <th>Cliente</th>
                        <th>Produto</th>
					</tr>
				</thead>
				@if(count($albuns) > 5)
				<tfoot>
					<tr>
						<th>ID</th>
						<th>OS</th>
						<th>OP</th>
                        <th>Fotos</th>
                        <th>Início</th>
						<th>Situação</th>
                        <th>Cliente</th>
                        <th>Produto</th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($albuns) > 0)
					@foreach($albuns as $album)
					<tr>
						<td>{{ $album->id }}</td>
						<td>{{ $album->os }}</td>
						<td>{{ $album->op }}</td>
                        <td>{{ $album->quantidade }}</td>
                        <td>{{ $album->dt_processo_envio_impressao }}</td>
                        <td>
                            @if(in_array($album->id, $processando))
                                <span class="text-success">Processando</span>
                            @else
                                <span class="text-primary">Aguardando</span>
                            @endif
                        </td>
                        <td>{{ $album->cliente }}</td>
                        <td>{{ $album->produto }}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
        
    setInterval(()=>{
        location.reload();
    }, 30000);
});
</script>
@endsection