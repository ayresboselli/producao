<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ImposicaoTipo;

use App\Services\ImposicaoTipoService;

class ImposicaoFerramentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista tipos de imposição
     */
	public function Listar()
	{
		return view(
            'produtos.ferramentasImposicao',
            [
                'ferramentas' => ImposicaoTipoService::Listar()
            ]
        );
	}

    /**
     * Carrega tipo de imposição
     * @param int|null $id
     */
	public function Buscar($id = null)
	{
		$ferramenta = ImposicaoTipoService::Buscar($id);
		if(is_null($ferramenta)){
			$ferramenta = new ImposicaoTipo();
		}

		return view('produtos.ferramentaImposicao', ['ferramenta' => $ferramenta]);
	}

    /**
     * Salva tipo de imposição
     * @param Request $request
     */
	public function Salvar(Request $request)
	{
        try
        {
            $validated = $request->validate([
                'titulo' => 'required|max:80',
                'descricao' => 'nullable',
            ]);

            ImposicaoTipoService::Salvar((object) $request->all());

            return redirect('ferramentas_imposicao')->with(
                $request->session()->flash('status', 'Ferramenta salva com sucesso!')
            );
        }
        catch(Except $e)
        {
            return view('produtos.ferramentaImposicao')->with(
                $request->session()->flash('erro', $e->getMessage())
            );
        }
	}

    /**
     * Deteta tipo de imposição
     * @param Request $request
     */
	public function Deletar(Request $request)
	{
		if(ImposicaoTipoService::Deletar($request->id))
		{
			$value = $request->session()->flash('status', 'Ferramenta excluída com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir a ferramenta!');
		}

		return redirect('ferramentas_imposicao')->with($value);
	}

}
