<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;

    protected $table = "perfis";

	protected $fillable = ['titulo','descricao'];

	public function users()
    {
        return $this->belongsToMany(User::class);
    }

	public function funcoes()
    {
        return $this->belongsToMany(Funcao::class);
    }
}
