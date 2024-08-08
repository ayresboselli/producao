<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItemAlbum extends Model
{
    use HasFactory;

    protected $table = "pedido_item_albuns";

	protected $fillable = [
        'pedido_item_id',
        'id_externo',
        'codigo'
    ];

	public function item(): BelongsTo
    {
        return $this->belongsTo(PedidoItem::class);
    }
}
