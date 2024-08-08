<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfil extends Model
{
    use HasFactory;

    protected $table = "perfis";

	protected $fillable = ['titulo','descricao'];

	public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

	public function funcao(): BelongsTo
    {
        return $this->belongsTo(Funcoes::class);
    }
}
