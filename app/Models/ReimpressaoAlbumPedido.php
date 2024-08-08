<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReimpressaoAlbumPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'cliente',
        'produto',
        'ordem_producao',
        'imprimir',
        'processada'
    ];

    public function laminas(): HasMany
    {
        return $this->hasMany(ReimpressaoAlbumLamina::class);
    }
}
