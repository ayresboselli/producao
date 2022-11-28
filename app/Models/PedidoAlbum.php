<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoAlbum extends Model
{
    use HasFactory;
	protected $fillable = ['id_externo', 'id_pedido', 'id_item', 'id_arquivo', 'codigo'];
}
