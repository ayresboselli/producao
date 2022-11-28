<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Recorte;

class RecorteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Index()
	{
		$sql = "SELECT 
					i.id id_item,
					p.id_externo os,
					prd.id id_produto,
					prd.id_externo cod_produto,
					prd.titulo produto,
					concat(prd.largura+prd.sangr_dir+prd.sangr_esq, 'x', prd.altura+prd.sangr_sup+prd.sangr_inf) tamanho,
					count(r.id) arquivos
				FROM
					pedidos p
					JOIN pedido_items i ON p.id = i.id_pedido
					JOIN produtos prd ON prd.id = i.id_produto
					JOIN pedido_item_arquivos a ON i.id = a.id_item
					JOIN recortes r ON a.id = r.id_arquivo
				WHERE
					r.crop_pos_x IS NULL AND 
					r.crop_pos_y IS NULL AND 
					r.crop_largura IS NULL AND 
					r.crop_altura IS NULL
				GROUP BY i.id";
		
		$recortes = DB::select($sql);
		
		return view('recortes', ['recortes' => $recortes]);
	}
	
	public function Recorte($id)
	{
		$sql = "
		SELECT
			r.*,
			i.id id_item,
			a.url_imagem, 
			(p.largura + p.sangr_dir + p.sangr_esq) largura,
			(p.altura + p.sangr_sup + p.sangr_inf) altura
		FROM
			pedido_item_arquivos a
			JOIN recortes r ON a.id = r.id_arquivo
			JOIN pedido_items i ON i.id = a.id_item
			JOIN produtos p ON p.id = i.id_produto
		WHERE 
			crop_pos_x IS NULL
			AND crop_pos_y IS NULL
			AND crop_largura IS NULL
			AND crop_altura IS NULL
			AND i.id = :id_item
		ORDER BY a.url_imagem";
		
		$recortes = DB::select($sql, ['id_item' => $id]);
		
		if(count($recortes) > 0)
			return view('recorte', ['recortes' => $recortes]);
		else
			return redirect('recortes');
	}
	
	public function Salvar(Request $request)
	{
		$recorte = Recorte::find($request->id);
		if(!is_null($recorte))
		{
			$recorte->update([
				'id_usuario' => Auth()->user()->id,
				'crop_pos_x' => $request->pos_x,
				'crop_pos_y' => $request->pos_y,
				'crop_largura' => $request->largura,
				'crop_altura' => $request->altura
			]);
			
			$value = $request->session()->flash('sucesso', 'Recorte salvo com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Erro ao salvar o recorte!');
		}
		
		return redirect('recorte/'.$request->id_item)->with($value);
	}
	
}
