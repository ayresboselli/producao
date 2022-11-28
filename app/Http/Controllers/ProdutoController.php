<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Models\Produto;
use \App\Models\Upload;
use \App\Models\ImposicaoTipo;
use \App\Models\ImposicaoNome;
use \App\Models\ImpressaoHotfolder;
use \App\Models\ImpressaoSubstrato;

class ProdutoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	# Produtos
	public function Produtos()
	{
		$sql = "SELECT 
					p.*, 
					count(i.id) itens, 
					t.titulo tp_imposicao,
					n.titulo mod_imposicao
				FROM 
					produtos p
					LEFT JOIN pedido_items i ON p.id = i.id_produto
					LEFT JOIN imposicao_tipos t ON t.id = p.id_imposicao_tipo
					LEFT JOIN imposicao_nomes n ON n.id = p.id_imposicao_nome
				GROUP BY p.id";
		$produtos = DB::select($sql);
		
		return view('produtos.produtos',['produtos' => $produtos]);
	}
	
	public function Produto($id = null)
	{
		$produto = Produto::find($id);
		if(is_null($produto)){
			$produto = new Produto();
			
			$produto->sangr_sup = 0;
			$produto->sangr_inf = 0;
			$produto->sangr_esq = 0;
			$produto->sangr_dir = 0;
			$produto->renomear = 1;
		}
		
		$ferramentas = ImposicaoTipo::get();
		$modelos = ImposicaoNome::get();
		$hotfolders = ImpressaoHotfolder::get();
		$substratos = ImpressaoSubstrato::get();
		
		return view(
			'produtos.produto', 
			[
				'produto' => $produto,
				'ferramentas' => $ferramentas,
				'modelos' => $modelos,
				'hotfolders' => $hotfolders,
				'substratos' => $substratos
			]
		);
	}
	
	public function Duplicar($id)
	{
		$produto = Produto::find($id);
		if(!is_null($produto))
		{
			$dados = [
				'id_imposicao_tipo' => $produto->id_imposicao_tipo,
				'id_imposicao_nome' => $produto->id_imposicao_nome,
				'id_impressao_hotfolder' => $produto->id_impressao_hotfolder,
				'id_impressao_substrato' => $produto->id_impressao_substrato,
				'titulo' => $produto->titulo.' - Cópia',
				'sem_dimensao' => $produto->sem_dimensao,
				'largura' => $produto->largura,
				'altura' => $produto->altura,
				'sangr_sup' => $produto->sangr_sup,
				'sangr_inf' => $produto->sangr_inf,
				'sangr_esq' => $produto->sangr_esq,
				'sangr_dir' => $produto->sangr_dir,
				'disposicao' => $produto->disposicao,
				'renomear' => $produto->renomear,
			];
			
			$prod = Produto::create($dados);
			$id = $prod->id;
			/*
			$uploads = DB::select("SELECT * FROM uploads WHERE id_produto = :id", ['id' => $produto->id]);
			foreach($uploads as $upload)
			{
				Upload::create([
					'id_produto' => $id,
					'titulo' => $upload->titulo,
					'tipo_upload' => $upload->tipo_upload,
					'replicar' => $upload->replicar
				]);
			}
			*/
		}

		return redirect('produto/'.$id);
	}

	public function ProdutoSalvar(Request $request)
	{
		$renomear = false;
		$sem_dimensao = false;
		$largura = $request->largura;
		$altura = $request->altura;
		$sangr_sup = $request->sangr_sup;
		$sangr_inf = $request->sangr_inf;
		$sangr_esq = $request->sangr_esq;
		$sangr_dir = $request->sangr_dir;

		if($request->renomear == 'on')
			$renomear = true;

		if($request->sem_dimensao == 'on'){
			$sem_dimensao = true;

			if($largura == ''  && $altura == ''){
				$largura = null;
				$altura = null;
				$sangr_sup = null;
				$sangr_inf = null;
				$sangr_esq = null;
				$sangr_dir = null;
			}
		}
		
		$dados = [
			'id_externo' => $request->id_externo,
			'id_imposicao_tipo' => $request->imposicao,
			'id_imposicao_nome' => $request->modelo,
			'id_impressao_hotfolder' => $request->hotfolder,
			'id_impressao_substrato' => $request->substrato,
			'titulo' => $request->titulo,
			'sem_dimensao' => $sem_dimensao,
			'largura' => $largura,
			'altura' => $altura,
			'sangr_sup' => $sangr_sup,
			'sangr_inf' => $sangr_inf,
			'sangr_esq' => $sangr_esq,
			'sangr_dir' => $sangr_dir,
			'disposicao' => $request->disposicao,
			'renomear' => $renomear
		];
		
		$produto = Produto::find($request->id);
		if(is_null($produto))
		{
			$produto = Produto::create($dados);
		}
		else
		{
			$produto->update($dados);
		}

		$sql = "UPDATE pedido_items SET id_produto = :id_produto WHERE id_produto_externo = :id_externo AND id_produto IS NULL";
		DB::select($sql, ['id_produto' => $produto->id, 'id_externo' => $produto->id_externo]);
		
		$value = $request->session()->flash('status', 'Produto salvo com sucesso!');
		
        return redirect('produtos')->with($value);
	}
	
	public function ProdutoDeletar(Request $request)
	{
		$produto = Produto::find($request->id);
		if(!is_null($produto))
		{
			$produto->delete();
			$value = $request->session()->flash('status', 'Produto excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o produto!');
		}
		
		return redirect('produtos')->with($value);
	}
	
}
