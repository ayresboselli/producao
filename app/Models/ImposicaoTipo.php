<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImposicaoTipo extends Model
{
    use HasFactory;
	protected $fillable = ['titulo', 'descricao'];

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }

    public function nomes(): HasMany
    {
        return $this->hasMany(ImposicaoNome::class);
    }
}
