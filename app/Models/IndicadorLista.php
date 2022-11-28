<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorLista extends Model
{
    use HasFactory;
    
    protected $table = 'indicadores_listas';

    protected $fillable = ['descricao'];
}
