@extends('layouts.app')

@section('title', 'Arquivos')

@section('content')
<style>
.lista_fotos{
	/*text-align: center;*/
}
.lista_fotos a{
	max-width: 200px;
	display: inline-table;
}
.lista_fotos a img{
	width: 100%;
}
.lista_fotos a:hover{
	border: 2px solid #aaa;
	opacity: 0.5;
}
.scroll{
	width: auto;
	height: 150px;
	overflow-x: scroll;
	overflow-y: hidden;
	white-space: nowrap;
	border: 1px solid #888;
	padding: 15px;
	text-align: center;
}
.scroll img{
	max-height: 100px;
}

#livro{
	width: 80%;
	margin: auto;
	border: 1px solid #888;
	margin-bottom: 15px;
}
#livro td{
	width: 50%;
	padding: 0;
}
#livro img{
	width: 100%;
}
</style>

<h1 class="mt-4">Arquivos</h1>

<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href='/pedidos'>Pedidos</a></li>
	<li class="breadcrumb-item"><a href='/pedido/{{ $item->id_pedido }}'>Pedido</a></li>
	<li class="breadcrumb-item active">Arquivos</li>
</ol>

<div class="card mb-4">
	<div class="card-header">
		<form action="/arquivos" method="post" id="album-form">
			@csrf
			<input type="hidden" name="id" value="{{ $item->id }}">

			<div class="row">
				<div class="col-sm-2">
					<div class="input-group">
						<div class="input-group-append">
							<label for="album">Álbum:&nbsp;</label>
						</div>

						<select id="album" class="form-control form-control-sm" name="album" onchange="event.preventDefault(); document.getElementById('album-form').submit();"">
						@foreach($albuns as $album)
							<option value="{{ $album->id }}" @if($album->id == $id_album) selected @endif>{{ $album->codigo }}</option>
						@endforeach
						</select>
					</div>
				</div>
				<div class="col-sm-10">
					<div class="input-group">
						<button type="button" class="btn btn-danger btn-sm" onclick="ResetarAlbum({{$item->id_pedido}}, {{$item->id}})">Resetar</button>
					</div>
				</div>
			</div>
		</form>
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
		
		@if($item->disposicao == 'Simplex')
			<div id="listaFotosSimplex" class="lista_fotos"></div>
		@else
			<table id="livro">
				<tr>
					<td>
						<a href='javascript:void(0)' id="aImgPagImpar"><img id="imgPagImpar"></a>
					</td>
					<td>
						<a href='javascript:void(0)' id="aImgPagPar"><img id="imgPagPar"></a>
					</td>
				</tr>
			</table>

			<div id="listaFotosDuplex" class="lista_fotos scroll"></div>
		@endif
	</div>
</div>


<div class="modal" id="modalVerArquivo">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Arquivo</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div id='modalUploadForm' class="modal-body">
				<img id="fotoModal" width="100%">
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button class="btn btn-primary" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Reset -->
<div class="modal" id="modalReset">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Resetar Álbum</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<p>Ao resetar o álbum, todos os arquivos serão excluídos, sendo necessário novo envio.</p>
				<p>Tem certeza que deseja resetar o álbum<span id='titulo_reset'></span>?</p>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form action="/pedido/reset_album" method="post">
					@csrf
					<input type='hidden' id='id_pedido' name='id_pedido'>
					<input type='hidden' id='id_item' name='id_item'>
					<input type='hidden' id='id_album' name='id_album'>
					<button type="submit" class="btn btn-danger">Resetar</button>
				</form>
			</div>

		</div>
	</div>
</div>

<script>
var root = "{{ asset('foto.php?filename=') }}";

var lista_arquivos = [];
@for($i = 0; $i < count($arquivos); $i++)
lista_arquivos[{{$i}}] = '{{$arquivos[$i]->url_imagem}}';
@endfor

function VerArquivo(url){
	$('#fotoModal').attr('src', '');
	$('#fotoModal').attr('src', root+'fotos/'+url);
	$('#modalVerArquivo').modal('show');
}

function Livro(index){
	var urlPar = '';
	var urlImar = '';
	var preview = 0;
	var next = 0;

	if (index % 2 == 0) {
		if(index > 0){
			urlImar = root+'fotos/'+lista_arquivos[index-1];
			preview = index-2;
		}
		
		urlPar = root+'fotos/'+lista_arquivos[index];

		if(index < lista_arquivos.length-1){
			next = index+1;
		}
	}else{
		if(index > 0){
			preview = index-1;
		}

		urlImar = root+'fotos/'+lista_arquivos[index];
		
		if(index < lista_arquivos.length-1){
			urlPar = root+'fotos/'+lista_arquivos[index+1];
			next = index+2;
		}
	}

	$('#imgPagPar').attr('src', urlPar);
	$('#imgPagImpar').attr('src', urlImar);

	$('#aImgPagImpar').attr('onclick', 'Livro('+preview+')');
	$('#aImgPagPar').attr('onclick', 'Livro('+next+')');
}

function ListaFotosSimplex(){
	var html = '';
	for(var i = 0; i < lista_arquivos.length; i++){
		html += "<a href='javascript:void(0)' onclick=\"VerArquivo('"+lista_arquivos[i]+"')\"><img src='"+root+"thumbs/"+lista_arquivos[i]+"'></a>";
	}
	
	$('#listaFotosSimplex').html(html);
}

function ListaFotosDuplex(){
	var html = '';
	for(var i = 0; i < lista_arquivos.length; i++){
		html += "<a href='javascript:void(0)' onclick='Livro("+i+")'><img src='"+root+"thumbs/"+lista_arquivos[i]+"'></a>";
	}
	
	$('#listaFotosDuplex').html(html);
}

function ResetarAlbum(id_pedido, id_item){
	var id_album = $('#album').val();
	
	$('#id_pedido').val(id_pedido);
	$('#id_item').val(id_item);
	$('#id_album').val(id_album);
	
	$('#modalReset').modal('show');
}

document.addEventListener('DOMContentLoaded',()=>{
	ListaFotosSimplex();
	ListaFotosDuplex();
	Livro(0);
});
</script>
@endsection