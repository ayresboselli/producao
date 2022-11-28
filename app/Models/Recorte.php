<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recorte extends Model
{
    use HasFactory;
	protected $fillable = ['id_arquivo', 'id_usuario', 'crop_pos_x', 'crop_pos_y', 'crop_largura', 'crop_altura', 'situacao'];
}
