<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Servico;
use App\Services\ServicoService;

class ServicoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function Servicos()
	{
		return view(
            'produtos.servicos',
            [
                'servicos' => ServicoService::Listar()
            ]
        );
	}

	public function Servico($id = null)
	{
		$servico = ServicoService::Buscar($id);
		if(is_null($servico)){
			$servico = new Servico();
		}

		return view('produtos.servico', ['servico' => $servico]);
	}

	public function ServicoSalvar(Request $request)
	{
        $validated = $request->validate([
            'id_externo' => 'nullable',
            'titulo' => 'required|max:80',

        ]);

		ServicoService::Salvar((object) $request->all());

        return redirect('servicos')->with(
            $request->session()->flash('status', 'Serviço salvo com sucesso!')
        );
	}

	public function ServicoDeletar(Request $request)
	{
		if(ServicoService::Deletar($request->id))
		{
			$value = $request->session()->flash('status', 'Serviço excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o serviço!');
		}

		return redirect('servicos')->with($value);
	}
}
