<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;
	protected $fillable = [
		'user_id',
		'id_externo',
		'tipo_contrato',
		'id_cliente',
		'cliente',
		'contrato',
		'data_entrada',
		'previsao_entrega',
		'deletar_origem',
		'data_fechamento',
		'arquivar',
		'data_exclusao',
		'processado',
		'excluido'
	];

	public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
