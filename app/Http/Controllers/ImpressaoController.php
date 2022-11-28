<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Models\ImpressaoHotfolder;
use \App\Models\ImpressaoSubstrato;

class ImpressaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	# Hotfolders
	public function Hotfolders()
	{
		$sql = "SELECT h.*, count(p.id) produtos FROM impressao_hotfolders h
				LEFT JOIN produtos p ON h.id = p.id_impressao_hotfolder
				GROUP BY h.id";
		$hotfolders = DB::select($sql);
		return view('produtos.hotfolders', ['hotfolders' => $hotfolders]);
	}
	
	public function Hotfolder($id = null)
	{
		$hotfolder = ImpressaoHotfolder::find($id);
		if(is_null($hotfolder)){
			$hotfolder = new ImpressaoHotfolder();
		}
		
		return view('produtos.hotfolder', ['hotfolder' => $hotfolder]);
	}
	
	public function HotfolderSalvar(Request $request)
	{
		$dados = ['titulo' => $request->titulo, 'descricao' => $request->descricao];
		
		$hotfolder = ImpressaoHotfolder::find($request->id);
		if(is_null($hotfolder))
		{
			$hotfolder = ImpressaoHotfolder::create($dados);
		}
		else
		{
			$hotfolder->update($dados);
		}
		
		$value = $request->session()->flash('status', 'HotFolder salvo com sucesso!');
		
        return redirect('hotfolders_impressao')->with($value);
	}
	
	public function HotfolderDeletar(Request $request)
	{
		$hotfolder = ImpressaoHotfolder::find($request->id);
		if(!is_null($hotfolder))
		{
			$hotfolder->delete();
			$value = $request->session()->flash('status', 'HotFolder excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o HotFolder!');
		}
		
		return redirect('hotfolders_impressao')->with($value);
	}
	
	
	# Substratos
	public function Substratos()
	{
		$sql = "SELECT s.*, count(p.id) produtos FROM impressao_substratos s
				LEFT JOIN produtos p ON s.id = p.id_impressao_substrato
				GROUP BY s.id";
		$substratos = DB::select($sql);
		return view('produtos.substratos', ['substratos' => $substratos]);
	}
	
	public function Substrato($id = null)
	{
		$substrato = ImpressaoSubstrato::find($id);
		if(is_null($substrato)){
			$substrato = new ImpressaoSubstrato();
		}
		
		return view('produtos.substrato', ['substrato' => $substrato]);
	}
	
	public function SubstratoSalvar(Request $request)
	{
		$dados = ['titulo' => $request->titulo, 'descricao' => $request->descricao];
		
		$substrato = ImpressaoSubstrato::find($request->id);
		if(is_null($substrato))
		{
			$substrato = ImpressaoSubstrato::create($dados);
		}
		else
		{
			$substrato->update($dados);
		}
		
		$value = $request->session()->flash('status', 'Substrato salvo com sucesso!');
		
        return redirect('substratos_impressao')->with($value);
	}
	
	public function SubstratoDeletar(Request $request)
	{
		$substrato = ImpressaoSubstrato::find($request->id);
		if(!is_null($substrato))
		{
			$substrato->delete();
			$value = $request->session()->flash('status', 'Substrato excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o substrato!');
		}
		
		return redirect('substratos_impressao')->with($value);
	}
	
}
