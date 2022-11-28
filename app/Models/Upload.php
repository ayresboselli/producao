<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
	protected $fillable = ['id_produto', 'titulo', 'tipo_upload', 'replicar'];
}
