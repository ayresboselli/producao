<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Produto;

use App\Services\ProdutoService;
use App\Services\ImposicaoTipoService;
use App\Services\ImposicaoNomeService;
use App\Services\ImpressaoHotfolderService;
use App\Services\ImpressaoSubstratoService;

class ProdutoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function Produtos()
	{
		return view(
            'produtos.produtos',
            [
                'produtos' => ProdutoService::Listar()
            ]
        );
	}

	public function Produto($id = null)
	{
		$produto = ProdutoService::Buscar($id);
		if(is_null($produto)){
			$produto = new Produto();

			$produto->sangr_sup = 0;
			$produto->sangr_inf = 0;
			$produto->sangr_esq = 0;
			$produto->sangr_dir = 0;
			$produto->renomear = true;
		}

		return view(
			'produtos.produto',
			[
				'produto' => $produto,
				'ferramentas' => ImposicaoTipoService::Listar(),
				'modelos' => ImposicaoNomeService::Listar(),
				'hotfolders' => ImpressaoHotfolderService::Listar(),
				'substratos' => ImpressaoSubstratoService::Listar()
			]
		);
	}

	public function Duplicar($id)
	{
		$produto = ProdutoService::Duplicar($id);
		return redirect('produto/'.$produto->id);
	}

	public function ProdutoSalvar(Request $request)
	{
        try
        {
            $validated = $request->validate([
                'id_externo' => 'nullable',
                'imposicao_tipo_id' => 'nullable',
                'imposicao_nome_id' => 'nullable',
                'impressao_hotfolder_id' => 'nullable',
                'impressao_substrato_id' => 'nullable',
                'titulo' => 'required',
                'largura' => 'required',
                'altura' => 'required',
                'sangr_sup' => 'nullable',
                'sangr_inf' => 'nullable',
                'sangr_esq' => 'nullable',
                'sangr_dir' => 'nullable',
                'disposicao' => 'required',
                'renomear' => 'nullable',
                'sem_dimensao' => 'nullable',
            ]);

            ProdutoService::Salvar((object) $request->all());

            return redirect('produtos')->with(
                $request->session()->flash('status', 'Produto salvo com sucesso!')
            );
        }
        catch(Excepton $e)
        {
            return redirect('produto/'.$request->id)->with(
                $request->session()->flash('erro', 'Erro ao salvar o produto!')
            );
        }
	}

	public function ProdutoDeletar(Request $request)
	{
		if(ProdutoService::Delete($request->id))
		{
			$value = $request->session()->flash('status', 'Produto excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o produto!');
		}

		return redirect('produtos')->with($value);
	}
}
