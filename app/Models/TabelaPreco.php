<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TabelaPreco extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_sax',
        'titulo',
        'ativo'
    ];

    public function produtos(): HasMany
    {
        return $this->hasMany(TabelaPrecoProduto::class);
    }
}
