<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Models\ImpressaoSubstrato;
use App\Services\ImpressaoSubstratoService;

class ImpressaoSubstratoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function Listar()
	{
		return view(
            'produtos.substratos',
            [
                'substratos' => ImpressaoSubstratoService::Listar()
            ]
        );
	}

	public function Buscar($id = null)
	{
		$substrato = ImpressaoSubstratoService::Buscar($id);
		if(is_null($substrato)){
			$substrato = new ImpressaoSubstrato();
		}

		return view('produtos.substrato', ['substrato' => $substrato]);
	}

	public function Salvar(Request $request)
	{
        $validated = $request->validate([
            'titulo' => 'required|max:80',
            'descricao' => 'nullable',
        ]);

		ImpressaoSubstratoService::Salvar((object) $request->all());

        return redirect('substratos_impressao')->with(
            $request->session()->flash('status', 'Substrato salvo com sucesso!')
        );
	}

	public function Deletar(Request $request)
	{
		if(ImpressaoSubstratoService::Deletar($request->id))
        {
			$value = $request->session()->flash('status', 'Substrato excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o substrato!');
		}

		return redirect('substratos_impressao')->with($value);
	}
}
