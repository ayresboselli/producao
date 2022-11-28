<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Models\Servico;

class ServicoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Servicos()
	{
        $sql = "SELECT s.*, count(i.id) itens FROM servicos s
                LEFT JOIN pedido_item_servicos i ON i.id_servico = s.id
                GROUP BY s.id";
        $servicos = DB::select($sql);
		return view('produtos.servicos',['servicos' => $servicos]);
	}
	
	public function Servico($id = null)
	{
		$servico = Servico::find($id);
		if(is_null($servico)){
			$servico = new Servico();
		}
		
		return view('produtos.servico', ['servico' => $servico]);
	}
	
	public function ServicoSalvar(Request $request)
	{
		$dados = [
			'id_externo' => $request->id_externo,
			'titulo' => $request->titulo
		];
		
		$servico = Servico::find($request->id);
		if(is_null($servico))
		{
			$servico = Servico::create($dados);
		}
		else
		{
			$servico->update($dados);
		}

		$value = $request->session()->flash('status', 'Serviço salvo com sucesso!');
        return redirect('servicos')->with($value);
	}
	
	public function ServicoDeletar(Request $request)
	{
		$servico = Servico::find($request->id);
		if(!is_null($servico))
		{
			$servico->delete();
			$value = $request->session()->flash('status', 'Serviço excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o serviço!');
		}
		
		return redirect('servicos')->with($value);
	}
}
