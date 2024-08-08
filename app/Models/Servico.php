<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_externo',
        'titulo'
    ];

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItemServico::class);
    }
}
