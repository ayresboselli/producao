<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItemArquivo extends Model
{
    use HasFactory;
	protected $fillable = [
        'pedido_item_id',
        'pedido_item_album_id',
        'folha',
        'url_imagem',
        'nome_arquivo',
        'largura',
        'altura',
        'situacao'
    ];

	public function item(): BelongsTo
    {
        return $this->belongsTo(PedidoItem::class);
    }
	public function album(): BelongsTo
    {
        return $this->belongsTo(PedidoItemAlbum::class);
    }
}
