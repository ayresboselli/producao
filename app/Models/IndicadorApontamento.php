<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorApontamento extends Model
{
    use HasFactory;

    protected $table = 'indicadores_apontamentos';

    protected $fillable = ['id_lamina', 'id_indicador', 'id_celula_ident', 'id_celula_falha'];
}
