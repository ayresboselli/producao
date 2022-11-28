<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelaPrecoProduto extends Model
{
    use HasFactory;
    protected $fillable = ['id_tabela', 'id_produto', 'preco'];
}
