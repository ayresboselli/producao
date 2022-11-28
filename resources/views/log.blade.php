@extends('layouts.app')

@section('title', 'Log')

@section('content')

<h1 class="mt-4">Log</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item active">Log</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
        <div class="table-responsive">
			<table id="tblLog" class="table table-bordered table-striped table-sm" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Data</th>
						<th>Tipo</th>
						<th>Módulo</th>
						<th>Mensagem</th>
					</tr>
				</thead>
				@if(count($logs) > 5)
				<tfoot>
					<tr>
						<th>Data</th>
						<th>Tipo</th>
						<th>Módulo</th>
						<th>Mensagem</th>
					</tr>
				</tfoot>
				@endif
				<tbody>
				@if(count($logs) > 0)
					@foreach($logs as $log)
					<tr class="@if($log->tipo == 1) text-danger @elseif($log->tipo == 2) text-info @endif">
						<td style="min-width: 150px;">{{ $log->created_at }}</td>
						<td>
                            @if($log->tipo == 1)
                                Erro
							@elseif($log->tipo == 2)
								Info
                            @endif
                        </td>
						<td>{{ $log->modulo }}</td>
						<td>{{ $log->mensagem }}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
    </div>
</div>
@endsection