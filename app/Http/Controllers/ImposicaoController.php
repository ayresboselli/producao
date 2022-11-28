<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Models\ImposicaoTipo;
use \App\Models\ImposicaoNome;

class ImposicaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	# Ferramentas de imposição
	public function FerramentasImposicao()
	{
		$sql = "SELECT i.*, count(p.id) produtos FROM imposicao_tipos i
				LEFT JOIN produtos p ON i.id = p.id_imposicao_tipo GROUP BY i.id";
		$ferramentas = DB::select($sql);

		return view('produtos.ferramentasImposicao', ['ferramentas' => $ferramentas]);
	}
	
	public function FerramentaImposicao($id = null)
	{
		$ferramenta = ImposicaoTipo::find($id);
		if(is_null($ferramenta)){
			$ferramenta = new ImposicaoTipo();
		}
		
		return view('produtos.ferramentaImposicao', ['ferramenta' => $ferramenta]);
	}
	
	public function FerramentaImposicaoSalvar(Request $request)
	{
		$dados = ['titulo' => $request->titulo, 'descricao' => $request->descricao];
		
		$ferramenta = ImposicaoTipo::find($request->id);
		if(is_null($ferramenta))
		{
			$ferramenta = ImposicaoTipo::create($dados);
		}
		else
		{
			$ferramenta->update($dados);
		}
		
		$value = $request->session()->flash('status', 'Ferramenta salva com sucesso!');
		
        return redirect('ferramentas_imposicao')->with($value);
	}
	
	public function FerramentaImposicaoDeletar(Request $request)
	{
		$ferramenta = ImposicaoTipo::find($request->id);
		if(!is_null($ferramenta))
		{
			$ferramenta->delete();
			$value = $request->session()->flash('status', 'Ferramenta excluída com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir a ferramenta!');
		}
		
		return redirect('ferramentas_imposicao')->with($value);
	}
	
	
	# Modelos de imposução
	public function ModelosImposicao()
	{
		$sql = "SELECT n.*, t.titulo imposicao, count(p.id) produtos 
				FROM imposicao_nomes n
				JOIN imposicao_tipos t ON t.id = n.id_imposicao
				LEFT JOIN produtos p ON t.id = p.id_imposicao_nome
				GROUP BY n.id;";
		$modelos = DB::select($sql);

		return view('produtos.modelosImposicao', ['modelos' => $modelos]);
	}
	
	public function ModeloImposicao($id = null)
	{
		$ferramentas = ImposicaoTipo::get();
		
		$modelo = ImposicaoNome::find($id);
		if(is_null($modelo)){
			$modelo = new ImposicaoNome();
		}
		
		return view('produtos.modeloImposicao', ['modelo' => $modelo, 'ferramentas' => $ferramentas]);
	}
	
	public function ModeloImposicaoSalvar(Request $request)
	{
		$dados = ['titulo' => $request->titulo, 'id_imposicao' => $request->imposicao, 'descricao' => $request->descricao];
		
		$modelo = ImposicaoNome::find($request->id);
		if(is_null($modelo))
		{
			$modelo = ImposicaoNome::create($dados);
		}
		else
		{
			$modelo->update($dados);
		}
		
		$value = $request->session()->flash('status', 'Modelo salvo com sucesso!');
		
        return redirect('modelos_imposicao')->with($value);
	}
	
	public function ModeloImposicaoDeletar(Request $request)
	{
		$modelo = ImposicaoNome::find($request->id);
		if(!is_null($modelo))
		{
			$modelo->delete();
			$value = $request->session()->flash('status', 'Modelo excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o modelo!');
		}
		
		return redirect('modelos_imposicao')->with($value);
	}
	
}
