<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
	protected $fillable = [
		'id_externo',
		'tipo_contrato',
		'id_cliente',
		'id_usuario',
		'cliente',
		'contrato',
		'data_entrada',
		'previsao_entrega',
		'deletar_origem',
		'data_fechamento',
		'arquivar',
		'data_exclusao',
		'processado',
		'excluido'
	];
}
