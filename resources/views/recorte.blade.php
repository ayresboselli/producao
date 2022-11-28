@extends('layouts.app')

@section('title', 'Recorte')

@section('content')
<style>
	#scroll {
		width: auto;
		height: 120px;
		overflow-x: scroll;
		overflow-y: hidden;
		white-space: nowrap;
		border: 1px solid #888;
		padding: 15px;
		text-align: center;
	}
	.miniaturas {
		max-width: 100px;
		display: inline-block;
	}
	.miniaturas:hover {
		border: 1px solid #000;
		opacity: 0.5;
	}
	.miniaturas img {
		width: 100%;
	}
	
	#dv_crop {
		max-width: 800px;
		margin: auto;
	}
	
	#dv_crop img {
		width: 100%;
	}
</style>

<h1 class="mt-4">Recorte</h1>
<ol class="breadcrumb mb-4">
	<li class="breadcrumb-item"><a href="/recortes">Recortes</a></li>
	<li class="breadcrumb-item active">Recorte</li>
</ol>

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

<div class="card mb-4">
	<div class="card-header">
		
	</div>
	<div class="card-body">
		<form action='/recorte/salvar' method='post'>
			@csrf
			
			<div id="dv_crop" class="form-group">
				<img id="crop_img" src="{{ asset('foto.php?filename=fotos/'.$recortes[0]->url_imagem) }}">
			</div>
			<br>
			
			<div class="form-group text-center">
				<button type="button" id="btnCropperReset" class="btn btn-danger btn-sm">Reset</button>
				<button type="button" id="btnCropperInverte" class="btn btn-primary btn-sm">Inverter</button>
				<button type="submit" class="btn btn-success btn-sm">Cortar</button>
			</div>
			
			<div id="scroll">
				@foreach($recortes as $recorte)
				<div class="miniaturas">
					<a href="javascript:void(0)" onclick="AlteraImagem({{ $recorte->id }},{{ $recorte->largura }},{{ $recorte->altura }}, '{{ $recorte->url_imagem }}')">
						<img src="{{ asset('foto.php?filename=thumbs/'.$recorte->url_imagem) }}">
					</a>
				</div>
				@endforeach
				
			</div>
			
			<input type="hidden" id="id" name="id" value="{{ $recortes[0]->id }}">
			<input type="hidden" id="id_item" name="id_item" value="{{ $recortes[0]->id_item }}">
			<input type="hidden" id="p_larg" value="{{ $recortes[0]->largura }}">
			<input type="hidden" id="p_alt" value="{{ $recortes[0]->altura }}">
			
			<input type="hidden" id="pos_x" name="pos_x">
			<input type="hidden" id="pos_y" name="pos_y">
			<input type="hidden" id="largura" name="largura">
			<input type="hidden" id="altura" name="altura">
		</form>
	</div>
</div>

<link  href="{{ asset('assets/cropper/cropper.css') }}" rel="stylesheet">
<script src="{{ asset('assets/cropper/cropper.js') }}"></script>

<script>
var inverter = false;
var image = null;
var options = null;
var cropper = null;

function AlteraImagem(id, largura, altura, url){
	cropper.destroy();
	
	var html = "<img id='crop_img' src='{{ asset('foto.php?filename=') }}fotos/"+url+"'>";
	$('#dv_crop').html(html);
	
	$('#id').val(id);
	$('#p_larg').val(largura);
	$('#p_alt').val(altura);
	
	image = $('#crop_img')[0];
	cropper = new Cropper(image, options);
}

document.addEventListener('DOMContentLoaded', function(){
	var largura = $('#p_larg').val();
	var altura = $('#p_alt').val();
	
	image = $('#crop_img')[0];
	options = {
		aspectRatio: largura / altura,
		autoCropArea: 1,
		crop(event) {
			$('#pos_x').val(event.detail.x);
			$('#pos_y').val(event.detail.y);
			$('#largura').val(event.detail.width);
			$('#altura').val(event.detail.height);
		},
	};
	cropper = new Cropper(image, options);
	
	$('#btnCropperReset').on('click',function(){
		cropper.reset();
	});
	
	$('#btnCropperInverte').on('click',function(){
		if(inverter){
			inverter = false;
			options.aspectRatio = largura / altura;
		}else{
			inverter = true;
			options.aspectRatio = altura / largura;
		}
		
		cropper.destroy();
        cropper = new Cropper(image, options);
	});
	
});

</script>
@endsection