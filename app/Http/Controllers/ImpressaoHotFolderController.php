<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ImpressaoHotfolder;
use \App\Services\ImpressaoHotfolderService;

class ImpressaoHotFolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function Listar()
	{
		return view(
            'produtos.hotfolders',
            [
                'hotfolders' => ImpressaoHotFolderService::Listar()
            ]
        );
	}

	public function Buscar($id = null)
	{
		$HotFolder = ImpressaoHotFolderService::Buscar($id);
		if(is_null($HotFolder)){
			$HotFolder = new ImpressaoHotFolder();
		}

		return view('produtos.hotfolder', ['hotfolder' => $HotFolder]);
	}

	public function Salvar(Request $request)
	{
        $validated = $request->validate([
            'titulo' => 'required|max:80',
            'descricao' => 'nullable',
        ]);

		ImpressaoHotFolderService::Salvar((object) $request->all());

        return redirect('hotfolders_impressao')->with(
            $request->session()->flash('status', 'HotFolder salvo com sucesso!')
        );
	}

	public function Deletar(Request $request)
	{
		if(ImpressaoHotFolderService::Deletar($request->id))
        {
			$value = $request->session()->flash('status', 'HotFolder excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o HotFolder!');
		}

		return redirect('hotfolders_impressao')->with($value);
	}
}
