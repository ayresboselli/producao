<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReimpressaoAlbumLamina extends Model
{
    use HasFactory;

    protected $fillable = [
        'reimpressao_album_pedido_id',
        'foto_frente',
        'foto_verso',
        'album',
        'defeito_celula',
        'defeito_descricao',
        'status'
    ];

    public function pedidoReimpressao(): BelongsTo
    {
        return $this->belongsTo(ReimpressaoAlbumPedido::class);
    }
}
