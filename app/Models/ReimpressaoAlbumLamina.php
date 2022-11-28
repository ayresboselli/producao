<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReimpressaoAlbumLamina extends Model
{
    use HasFactory;
    protected $fillable = ['id_reimpressao', 'foto_frente', 'foto_verso', 'album', 'defeito_celula', 'defeito_descricao', 'status'];
}
