<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Upload;
use App\Models\Produto;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Index($id_produto)
	{
		$produto = Produto::find($id_produto);
		$uploads = DB::table('uploads')->select('*')->where('id_produto','=',$produto->id)->get();
		/*
		$uploads = DB::table('uploads as u')
			->join('produtos as p', 'p.id', '=', 'u.id_produto')
			->select('u.*', 'p.titulo as produto')
			->where('u.id_produto','=',$id_produto)
			->get();
		*/
		return view('produtos.uploads',['produto' => $produto, 'uploads' => $uploads]);
	}
	
	public function Editar($id_produto, $id = null)
	{
		$produto = Produto::find($id_produto);
		
		$upload = Upload::find($id);
		if(is_null($upload)){
			$upload = new Upload();
		}
		
		$produtos = Produto::get();
		
		return view('produtos.upload', ['upload' => $upload, 'produto' => $produto]);
	}
	
	public function Salvar(Request $request)
	{
		$dados = [
			'id_produto' => $request->produto,
			'titulo' => $request->titulo,
			'tipo_upload' => $request->tipo_upload,
			'replicar' => $request->replicar,
		];
		
		$upload = Upload::find($request->id);
		if(is_null($upload))
		{
			$upload = Upload::create($dados);
		}
		else
		{
			$upload->update($dados);
		}
		
		$value = $request->session()->flash('status', 'Upload salvo com sucesso!');
		
        return redirect('uploads/'.$request->produto)->with($value);
	}
	
	public function Deletar(Request $request)
	{
		$upload = Upload::find($request->id);
		if(!is_null($upload))
		{
			$upload->delete();
			$value = $request->session()->flash('status', 'Upload excluído com sucesso!');
		}
		else
		{
			$value = $request->session()->flash('erro', 'Falha ao excluir o upload!');
		}
		
		return redirect('uploads/'.$request->id_produto)->with($value);
	}
}
