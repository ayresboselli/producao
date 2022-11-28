<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintOneCliente extends Model
{
    use HasFactory;
    protected $fillable = ['id_loja', 'id_sax'];
}
