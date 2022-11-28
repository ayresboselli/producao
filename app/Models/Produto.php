<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;
	protected $fillable = [
		'id_externo',
		'id_imposicao_tipo',
		'id_imposicao_nome',
		'id_impressao_hotfolder',
		'id_impressao_substrato',
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
	
}
