<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Funcao extends Model
{
    use HasFactory;

    protected $table = "funcoes";

	protected $fillable = ['chave','descricao'];

	public function perfis(): BelongsTo
    {
        return $this->belongsTo(Perfil::class);
    }
}
