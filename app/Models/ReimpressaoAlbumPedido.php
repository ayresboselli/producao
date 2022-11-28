<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReimpressaoAlbumPedido extends Model
{
    use HasFactory;
    protected $fillable = ['titulo', 'cliente', 'produto', 'ordem_producao', 'imprimir', 'processada'];
}
