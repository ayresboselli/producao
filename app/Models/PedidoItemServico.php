<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItemServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'servico_id',
        'id_externo',
        'id_servico_externo',
        'url_origem',
        'arquivos',
        'imprimir',
        'data_envio_impressao'
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class);
    }
}
