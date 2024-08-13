<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcao extends Model
{
    use HasFactory;

    protected $table = "funcoes";

	protected $fillable = ['chave','descricao'];

	public function perfis()
    {
        return $this->belongsToMany(Perfil::class);
    }
}
