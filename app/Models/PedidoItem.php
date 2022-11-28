<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;
	protected $fillable = [
        'id_externo', 
        'id_pedido', 
        'id_produto', 
        'id_produto_externo', 
        'quantidade', 
        'url_origem',
        'copias', 
        'data_importacao', 
        'renomear', 
        'imprimir', 
        'exportar_xml', 
        'corrigir', 
        'data_envio_impressao'
    ];
}
