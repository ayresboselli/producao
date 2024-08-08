<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PedidoItem extends Model
{
    use HasFactory;

    protected $table = "pedido_itens";

	protected $fillable = [
        'pedido_id',
        'produto_id',
        'id_externo',
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

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
    public function albuns(): HasMany
    {
        return $this->hasMany(PedidoItemAlbum::class);
    }
}
