<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ImposicaoNome;

use App\Services\ImposicaoNomeService;
use App\Services\ImposicaoTipoService;

class ImposicaoModeloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista nomes de imposição
     */
	public function Listar()
	{
		return view(
            'produtos.modelosImposicao',
            [
                'modelos' => ImposicaoNomeService::Listar()
            ]
        );
	}

    /**
     * Carrega nome de imposição
     * @param int|null $id
     */
	public function Buscar($id = null)
	{
		$modelo = ImposicaoNomeService::Buscar($id);
		if(is_null($modelo)){
			$modelo = new ImposicaoNome();
		}

		return view(
            'produtos.modeloImposicao',
            [
                'ferramentas' => ImposicaoTipoService::Listar(),
                'modelo' => $modelo
            ]
        );
	}

    /**
     * Salva nome de imposição
     * @param Request $request
     */
	public function Salvar(Request $request)
	{
        try
        {
            $dados = ['titulo' => $request->titulo, 'descricao' => $request->descricao];

            $validated = $request->validate([
                'titulo' => 'required|max:80',
                'imposicao_tipo_id' => 'required',
                'descricao' => 'nullable',
            ]);

            ImposicaoNomeService::Salvar((object) $request->all());

            return redirect('modelos_imposicao')->with(
                $request->session()->flash('status', 'Modelo salva com sucesso!')
            );
        }
        catch(Except $e)
        {
            return view('produtos.modeloImposicao')->with(
                $request->session()->flash('erro', $e->getMessage())
            );
        }
	}

    /**
     * Deteta nome de imposição
     * @param Request $request
     */
	public function Deletar(Request $request)
	{
		if(ImposicaoNomeService::Deletar($request->id))
		{
			$value = $request->session()->flash('status', 'Modelo excluída com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir a modelo!');
		}

		return redirect('modelos_imposicao')->with($value);
	}
}
