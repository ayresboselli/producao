<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory;
	protected $fillable = [
		'id_externo',
		'imposicao_tipo_id',
		'imposicao_nome_id',
		'impressao_hotfolder_id',
		'impressao_substrato_id',
		'titulo',
		'sem_dimensao',
		'largura',
		'altura',
		'sangr_sup',
		'sangr_inf',
		'sangr_esq',
		'sangr_dir',
		'disposicao',
		'renomear'
	];

	public function imposicao_tipo(): BelongsTo
    {
        return $this->belongsTo(ImposicaoTipo::class);
    }

	public function imposicao_nome(): BelongsTo
    {
        return $this->belongsTo(ImposicaoNome::class);
    }

	public function impressao_hotfolder(): BelongsTo
    {
        return $this->belongsTo(ImpressaoHotfolder::class);
    }

	public function impressao_substrato(): BelongsTo
    {
        return $this->belongsTo(ImpressaoSubstrato::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function tabelas(): HasMany
    {
        return $this->hasMany(TabelaPrecoProduto::class);
    }
}
