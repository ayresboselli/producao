<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintOneProdutoItem extends Model
{
    use HasFactory;
    protected $fillable = ['id_produto', 'id_sax', 'intervalo', 'unid_medida','preco'];
}
