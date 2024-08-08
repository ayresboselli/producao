<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TabelaPrecoProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'tabela_preco_id',
        'produto_id',
        'preco'
    ];

	public function tabela(): BelongsTo
    {
        return $this->belongsTo(TabelaPreco::class);
    }

	public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
