<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImposicaoNome extends Model
{
    use HasFactory;
	protected $fillable = [
        'imposicao_tipo_id',
        'titulo',
        'descricao'
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ImposicaoTipo::class, 'imposicao_tipo_id', 'id');
    }

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }
}
