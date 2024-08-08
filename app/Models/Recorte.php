<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recorte extends Model
{
    use HasFactory;

	protected $fillable = [
        'pedido_item_arquivo_id',
        'user_id',
        'crop_pos_x',
        'crop_pos_y',
        'crop_largura',
        'crop_altura',
        'situacao'
    ];

	public function arquivo(): BelongsTo
    {
        return $this->belongsTo(PedidoItemArquivo::class);
    }

	public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
