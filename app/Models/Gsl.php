<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gsl extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome_album',
        'ordem_servico',
        'ordem_producao',
        'tipo_pedido',
        'path',
        'album',
        'quantidade',
        'correcao',
        'dt_correcao_entrada',
        'dt_correcao_saida',
        'dt_imposicao_entrada',
        'dt_imposicao_saida',
    ];
}
