<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\TabelaPreco;
use App\Models\TabelaPrecoProduto;
use App\Models\Produto;

class TabPrecosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function TabPrecos()
    {
        //$tabelas = TabelaPreco::get();
        $sql = "SELECT tp.*, count(tpp.id) itens 
                FROM tabela_precos tp
                LEFT JOIN tabela_preco_produtos tpp ON tp.id = tpp.id_tabela
                GROUP BY tp.id";
        $tabelas = DB::select($sql);
        return view('tab_precos', ['tabelas' => $tabelas]);
    }
    
    public function TabPreco($id = null)
    {
        $tabela = TabelaPreco::find($id);
        if(is_null($tabela)){
            $tabela = new TabelaPreco();
        }

        return view('tab_preco', ['tabela' => $tabela]);
    }

    public function TabPrecoSalvar(Request $request)
    {
        $tabela = TabelaPreco::find($request->id);
        if(!is_null($tabela)){
            $tabela->update([
                'id_sax' => $request->id_sax,
                'titulo' => $request->titulo,
                'ativo' => $request->ativo=='on'?1:0
            ]);
        }else{
            $tabela = TabelaPreco::create([
                'id_sax' => $request->id_sax,
                'titulo' => $request->titulo,
                'ativo' => $request->ativo=='on'?1:0
            ]);
        }

        if(!is_null($tabela)){
            $value = $request->session()->flash('sucesso', 'Tabela salva com sucesso!');
        }else{
            $value = $request->session()->flash('erro', 'Erro ao salvar a tabela!');
        }

        return redirect('tab_precos')->with($value);
    }

    public function TabPrecoDeletar(Request $request)
    {
        $tabela = TabelaPreco::find($request->id);
        if(!is_null($tabela)){
            $tabela->delete();
            $value = $request->session()->flash('sucesso', 'Tabela deletada com sucesso!');
        }else{
            $value = $request->session()->flash('erro', 'Não encontrei a tabela!');
        }

        return redirect('tab_precos/')->with($value);
    }
    
    public function TabPrecoProdutos($id)
    {
        $tabela = TabelaPreco::find($id);
        if(is_null($tabela)){
            $tabela = new TabelaPreco();
        }

        //$produtos = TabelaPrecoProduto::where('id_tabela','=',$id)->get();
        $sql = "SELECT 
                    tpp.id, 
                    concat(p.id_externo,' - ', p.titulo) produto, 
                    tpp.preco 
                FROM tabela_preco_produtos tpp
                JOIN produtos p ON p.id = tpp.id_produto
                WHERE tpp.id_tabela = :id_tabela";
        $produtos = DB::select($sql, ['id_tabela' => $tabela->id]);

        return view('tab_preco_produtos', ['tabela' => $tabela, 'produtos' => $produtos]);
    }
    
    public function TabPrecoProduto($id, $id_prod = null)
    {
        $tabela = TabelaPreco::find($id);
        if(!is_null($tabela))
        {
            $produto = TabelaPrecoProduto::where('id_tabela','=',$tabela->id)->find($id_prod);
            if(is_null($produto)){
                $produto = new TabelaPrecoProduto();
            }

            $lista = Produto::get();
            
            return view('tab_preco_produto', ['tabela' => $tabela, 'produto' => $produto, 'lista' => $lista]);
        }
        else
        {
            return redirect('tab_precos');
        }
        
    }

    public function TabPrecoProdutoSalvar(Request $request)
    {
        $tabela = TabelaPreco::find($request->id);
        if(!is_null($tabela))
        {
            $produto = TabelaPrecoProduto::where('id_tabela','=',$tabela->id)->find($request->id_prod);
            if(!is_null($produto)){
                $produto->update([
                    'id_produto' => $request->id_produto, 
                    'preco' => str_replace(',','.',$request->preco)
                ]);
            }else{
                $produto = TabelaPrecoProduto::create([
                    'id_tabela' => $tabela->id, 
                    'id_produto' => $request->id_produto, 
                    'preco' => str_replace(',','.',$request->preco)
                ]);
            }
            
            if(!is_null($produto)){
                $value = $request->session()->flash('sucesso', 'Produto salvo com sucesso!');
            }else{
                $value = $request->session()->flash('erro', 'Erro ao salvar o produto!');
            }

            return redirect('tab_preco_produtos/'.$tabela->id)->with($value);

        }else{
            $value = $request->session()->flash('erro', 'Não encontrei a tabela de preços!');
            return redirect('tab_precos')->with($value);
        }
    }

    public function TabPrecoProdutoDeletar(Request $request)
    {
        $produto = TabelaPrecoProduto::find($request->id);
        if(!is_null($produto)){
            $produto->delete();
            $value = $request->session()->flash('sucesso', 'Produto deletado com sucesso!');
        }else{
            $value = $request->session()->flash('erro', 'Não encontrei o produto!');
        }

        return redirect('tab_preco_produtos/'.$request->id_tabela)->with($value);
    }

    public function ImportarCSV()
    {
        $url = "https://docs.google.com/spreadsheets/d/e/2PACX-1vTJDRwxGlbBQsWxSC8RRZQPxBUm2PNoBYW5JsjkqVcjcgjc7mHy-brW7Wyv2y8uD6QdWpRjbysc5Zke/pub?gid=1360114689&single=true&output=tsv";
        //$url = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRRkYCZLR7muW5KfjocdERKuYOzj7dmx1q3lf1eDmuak6b_Xx5x2o7EZnJ8Ao42PJPi8KCZRm-4fUKD/pub?gid=1360114689&single=true&output=tsv";
        $file = file_get_contents($url);
        $file = explode("\r\n", $file);

        $cnt = 0;
        //echo "<pre>";
        foreach($file as $linha){
            $linha = explode("\t", $linha);

            $sql = "SELECT tc.cod_tabela FROM zangraf_xkey_publico.cad_clie c
            JOIN zangraf_xkey_publico.tabprecos_cliente tc On tc.cliente = c.codigo
            WHERE c.nome like \"%".$linha[0]."%\" OR c.apelido like \"%".$linha[0]."%\"";
            $tabela = $cod = DB::connection('mysqlXKey')->select($sql);
            
            if(count($tabela) > 0){
                $tabela = $tabela[0]->cod_tabela;
                $linha[4] = $tabela;
            
                $sql = "SELECT codigo FROM zangraf_xkey_publico.cad_prod WHERE descricao = :nome ";
                $cod = DB::connection('mysqlXKey')->select($sql, ['nome' => $linha[1]]);
                if($linha[0] != '' && count($cod) > 0 && substr($linha[1],0,5) != 'SERVI'/* && strstr($linha[1], 'LINHO') != ''*/
                /*&& strstr($linha[1], '23X30') != ''*/){
                    $cod = $cod[0]->codigo;
                    $linha[1] = $cod.' - '.$linha[1];
                    
                    print_r($linha[0].' - '.$linha[1].' - '.$linha[2].' - '.$linha[4]."<br>\n");
                    
                    $cnt++;
                }
            }
        }
        //echo "</pre>";
        echo $cnt;

        /*
        $csv = Storage::disk('local')->get('tabelas.csv');
        $array = array_map('str_getcsv', explode(PHP_EOL, $csv));

        foreach($array as $item)
        {
            $tabela = DB::select("SELECT id FROM tabela_precos WHERE id_sax = ".$item[0])[0]->id;
            $produto = DB::select("SELECT id FROM produtos WHERE id_externo = ".$item[1]);
            /*
            if(count($produto) > 0){
                $produto = $produto[0]->id;
                
                print_r($produto);
                print_r($item);
                echo '<br>';
            }
            *
            if(count($produto) == 0){
                echo $item[1].'<br>';
            }

        }
        */
    }
}
