<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpressaoHotfolder extends Model
{
    use HasFactory;
	protected $fillable = ['titulo', 'descricao'];
}
