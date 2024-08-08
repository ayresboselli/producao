<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImposicaoNome extends Model
{
    use HasFactory;
	protected $fillable = ['titulo', 'id_imposicao', 'descricao'];

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }
}
