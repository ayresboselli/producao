<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItemArquivo extends Model
{
    use HasFactory;
	protected $fillable = ['id_item', 'id_album', 'folha', 'url_imagem', 'nome_arquivo', 'largura', 'altura', 'situacao'];
}
