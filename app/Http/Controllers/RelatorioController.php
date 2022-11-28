<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProdutoVenda;

class RelatorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function MargemPrecoProduto()
    {
        
        $sql = "SELECT 
                    prd.codigo, 
                    prd.descricao,
                    round(sum(com.quantidade * sub.ult_custo),2) custo
                FROM 
                    zangraf_xkey_publico.cad_prod prd
                    JOIN zangraf_xkey_publico.com_prod com ON com.produto = prd.codigo
                    JOIN zangraf_xkey_publico.cad_prod sub ON sub.codigo = com.subproduto
                WHERE 
                    prd.ativo = 'S' 
                GROUP BY prd.codigo";
        $produtos_custo = DB::connection('mysqlXKey')->select($sql);
        
/*
					<tr>
						<td>{{ $produto->codigo }}</td>
						<td>{{ $produto->descricao }}</td>
                        <td>{{ $produto->custo }}</td>
                        <td>{{ $produto->venda }}</td>
                        <td>{{ $produto->margem }}</td>
					</tr>
					
        
*/

        ### Geral ###
        // aqui temos a URL que precisamos fazer o request
        //$url = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRRkYCZLR7muW5KfjocdERKuYOzj7dmx1q3lf1eDmuak6b_Xx5x2o7EZnJ8Ao42PJPi8KCZRm-4fUKD/pub?gid=288430433&single=true&output=csv";
        $url = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRRkYCZLR7muW5KfjocdERKuYOzj7dmx1q3lf1eDmuak6b_Xx5x2o7EZnJ8Ao42PJPi8KCZRm-4fUKD/pub?gid=288430433&single=true&output=tsv";
        $file = file_get_contents($url);
        $file = explode("\r\n", $file);

        $produtos = [];
        foreach($file as $linha)
        {
            $linha = explode("\t", $linha);
            $produto = new ProdutoVenda($linha[0],$linha[1],$linha[2]);
            if(is_numeric($produto->preco))
            {
                foreach($produtos_custo as $pc){
                    if(strstr($pc->descricao,$produto->descricao))
                    {
                        $produto->custo = $pc->custo;
                        $produtos[] = $produto;
                        break;
                    }
                }
            }
        }


        ### Clientes ###
        //$url = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRRkYCZLR7muW5KfjocdERKuYOzj7dmx1q3lf1eDmuak6b_Xx5x2o7EZnJ8Ao42PJPi8KCZRm-4fUKD/pub?gid=1360114689&single=true&output=csv";
        $url = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRRkYCZLR7muW5KfjocdERKuYOzj7dmx1q3lf1eDmuak6b_Xx5x2o7EZnJ8Ao42PJPi8KCZRm-4fUKD/pub?gid=1360114689&single=true&output=tsv";
        $file = file_get_contents($url);
        $file = explode("\r\n", $file);

        foreach($file as $linha)
        {
            $linha = explode("\t", $linha);
            $produto = new ProdutoVenda($linha[0],$linha[1],$linha[2]);
            if(is_numeric($produto->preco))
            {
                foreach($produtos_custo as $pc){
                    if(strstr($pc->descricao,$produto->descricao))
                    {
                        $produto->custo = $pc->custo;
                        $produtos[] = $produto;
                        break;
                    }
                }
            }
        }

        return view('relatorios.margem_preco_produtos', ['produtos' => $produtos]);

    }
}
