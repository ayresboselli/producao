<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produto;
use App\Models\PrintOneCliente;
use App\Models\PrintOneProduto;
use App\Models\PrintOneProdutoItem;

class PrintOneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function Index()
    {
        $sql = "SELECT
                    p.id,
                    p.id_printone,
                    p.titulo,
                    count(i.id) item,
                    sum(i.preco) preco
                FROM 
                    print_one_produtos p
                    LEFT JOIN print_one_produto_items i ON i.id_produto = p.id
                GROUP BY p.id";
        $produtos = DB::select($sql);
        //$produtos = PrintOneProduto::get();
        
        return view('integracao.print_one_produtos', ['produtos' => $produtos]);
    }

    public function Editar($id = null)
    {
        $produto = PrintOneProduto::find($id);
        if(is_null($produto))
        {
            $produto = new PrintOneProduto();
        }

        return view('integracao.print_one_produto', ['produto' => $produto]);
    }

    public function Salvar(Request $request)
    {
        $produto = PrintOneProduto::find($request->id);
        if(is_null($produto))
        {
            $produto = PrintOneProduto::create([
                'id_printone' => $request->id_printone, 
                'titulo' => $request->titulo, 
            ]);

        }
        else
        {
            $produto->update([
                'id_printone' => $request->id_printone, 
                'titulo' => $request->titulo, 
            ]);

        }
        
        $value = $request->session()->flash('sucesso', 'Produto salvo com sucesso');
        return redirect('printone')->with($value);
    }

    public function Duplicar($id)
    {
        $produto = PrintOneProduto::find($id);
        if(!is_null($produto))
        {
            $produto_novo = PrintOneProduto::create([
                'id_printone' => $produto->id_printone, 
                'titulo' => $produto->titulo, 
            ]);

            $itens = PrintOneProdutoItem::where('id_produto','=',$produto->id)->get();
            foreach($itens as $item)
            {
                PrintOneProdutoItem::create([
                    'id_produto' => $produto_novo->id, 
                    'id_sax' => $item->id_sax, 
                    'intervalo' => $item->intervalo
                ]);
            }
            
            return redirect('printone_produto/'.$produto_novo->id);
        }
        else
        {
            return redirect('printone');
        }
    }

    public function Deletar(Request $request)
    {
        $produto = PrintOneProduto::find($request->id);
        if(!is_null($produto))
        {
            $itens = PrintOneProdutoItem::where('id_produto','=',$produto->id)->get();
            foreach($itens as $item)
            {
                $item->delete();
            }
            
            $produto->delete();
            $value = $request->session()->flash('sucesso', 'Produto deletado com sucesso');
        }
        else
        {
            $value = $request->session()->flash('erro', 'Não encontrei o produto');
        }

        return redirect('printone')->with($value);
    }


    ##### CLIENTES #####
    public function Clientes()
    {
        $clientes = PrintOneCliente::get();
        
        return view('integracao.print_one_clientes', ['clientes' => $clientes]);
    }
    
    public function EditarCliente($id = null)
    {
        $cliente = PrintOneCliente::find($id);
        if(is_null($cliente))
        {
            $cliente = new PrintOneCliente();
        }

        return view('integracao.print_one_cliente', ['cliente' => $cliente]);
    }

    public function SalvarCliente(Request $request)
    {
        $cliente = PrintOneCliente::find($request->id);
        if(is_null($cliente))
        {
            $cliente = PrintOneCliente::create([
                'id_loja' => $request->id_loja, 
                'id_sax' => $request->id_sax, 
            ]);

        }
        else
        {
            $cliente->update([
                'id_loja' => $request->id_loja, 
                'id_sax' => $request->id_sax, 
            ]);

        }
        
        $value = $request->session()->flash('sucesso', 'Cliente salvo com sucesso');
        return redirect('printone_cliente')->with($value);
    }

    public function DeletarCliente(Request $request)
    {
        $cliente = PrintOneCliente::find($request->id);
        if(!is_null($cliente))
        {
            $cliente->delete();
            $value = $request->session()->flash('sucesso', 'Cliente deletado com sucesso');
        }
        else
        {
            $value = $request->session()->flash('erro', 'Não encontrei o cliente');
        }

        return redirect('printone_cliente')->with($value);
    }


    ##### ÍTENS #####
    public function Itens($id_produto)
    {
        $produto = PrintOneProduto::find($id_produto);
        if(!is_null($produto))
        {
            $sql = "SELECT popi.id, concat(p.id_externo, ' - ',p.titulo) produto, popi.intervalo, popi.unid_medida, popi.preco 
                    FROM print_one_produto_items popi
                    JOIN produtos p ON p.id_externo = popi.id_sax
                    WHERE popi.id_produto = :id_produto";
            $itens = DB::select($sql, ['id_produto' => $produto->id]);

            return view(
                'integracao.print_one_produto_itens', 
                [
                    'produto' => $produto,
                    'itens' => $itens,
                ]);
        }
        else
        {
            return redirect('printone');
        }
    }

    public function EditarItem($id_produto, $id = null)
    {
        $produto = PrintOneProduto::find($id_produto);
        if(!is_null($produto))
        {
            $item = PrintOneProdutoItem::where('id_produto','=',$produto->id)->find($id);
            if(!is_null($item)){
                $sql = "SELECT concat(id_externo, ' - ', titulo) produto FROM produtos WHERE id_externo = :id_externo";
                $prd = DB::select($sql, ['id_externo' => $item->id_sax])[0];
                $selecionado = $prd->produto;
            }else{
                $item = new PrintOneProdutoItem();
                $selecionado = '';
            }
            
            $produtos = Produto::get();

            return view(
                'integracao.print_one_produto_item', 
                [
                    'produto' => $produto,
                    'item' => $item,
                    'produtos' => $produtos,
                    'selecionado' => $selecionado,
                ]);
        }
        else
        {
            return redirect('printone');
        }
    }

    public function SalvarItem(Request $request)
    {
        $produto = PrintOneProduto::find($request->id_produto);
        if(!is_null($produto))
        {
            $item = PrintOneProdutoItem::where('id_produto','=',$produto->id)->find($request->id_item);
            $id_sax = explode(' - ',$request->prod)[0];
            
            if(is_null($item))
            {
                $item = PrintOneProdutoItem::create([
                    'id_produto' => $produto->id, 
                    'id_sax' => $id_sax, 
                    'intervalo' => $request->intervalo,
                    'unid_medida' => $request->unid_medida,
                    'preco' => $request->preco
                ]);
            }
            else
            {
                $item->update([
                    'id_sax' => $id_sax, 
                    'intervalo' => $request->intervalo,
                    'unid_medida' => $request->unid_medida,
                    'preco' => $request->preco
                ]);
            }

            $value = $request->session()->flash('success', 'Ítem salvo com sucesso');
            return redirect('printone_itens/'.$produto->id)->with($value);
        }
        else
        {
            $value = $request->session()->flash('erro', 'Não encontrei o produto');
            return redirect('printone')->with($value);
        }
    }

    public function DeletarItem(Request $request)
    {
        $produto = PrintOneProduto::find($request->id_produto);
        if(!is_null($produto))
        {
            $item = PrintOneProdutoItem::where('id_produto','=',$produto->id)->find($request->id_item);
            
            if(!is_null($item))
            {
                $item->delete();
                $value = $request->session()->flash('sucesso', 'Ítem deletado com sucesso');
            }
            else
            {
                $value = $request->session()->flash('erro', 'Não consegui deletar o ítem');
            }

            return redirect('printone_itens/'.$request->id_produto)->with($value);
        }
        else
        {
            $value = $request->session()->flash('erro', 'Não encontrei o produto');
            return redirect('printone')->with($value);
        }
    }

}
