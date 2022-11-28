<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItemServico extends Model
{
    use HasFactory;
    protected $fillable = ['id_externo', 'id_pedido', 'id_servico', 'id_servico_externo', 'url_origem', 'arquivos', 'imprimir', 'data_envio_impressao'];
}
