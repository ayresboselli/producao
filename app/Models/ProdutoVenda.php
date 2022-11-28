<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoVenda extends Model
{
    public $cliente;
    public $descricao;
    public $custo;
    public $preco;

    public function __construct($cliente = null, $descricao = null, $preco = null){
        $this->cliente = $cliente;
        $this->descricao = $descricao;
        $this->preco = trim(str_replace('R$ ','',$preco));
    }

    public function Margem(){
        return round(100 - ($this->custo * 100 / $this->preco), 2);
    }
}