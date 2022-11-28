<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = ['id_externo', 'nome', 'ftp_usuario', 'ftp_senha', 'ftp_atualizado'];
}
