<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PedidoAlbum;
use App\Models\Pedido;

class AlbumController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
	
	public function Index($id)
	{
		$pedido = Pedido::find($id);
		if(!is_null($pedido))
		{
			$albuns = DB::table('pedido_albums')->select('*')->where('id_pedido','=',$pedido->id)->get();
			return view('albuns', ['pedido' => $pedido, 'albuns' => $albuns]);
		}
		else
		{
			$value = $request->session()->flash('erro', 'Não encontrei o pedido');
			return redirect('pedidos')->with($value);
		}
	}

	public function Editar($id_pedido, $id)
	{
		$pedido = Pedido::find($id_pedido);
		if(!is_null($pedido))
		{
			if(!is_null($id))
			{
				$sql = "SELECT id FROM pedido_albums WHERE id_pedido = :id_pedido AND id = :id";
				$query = DB::select($sql, ['id_pedido' => $pedido->id, 'id' => $id]);
				$album = PedidoAlbum::find($query[0]->id);
			}
			else
			{
				$album = new PedidoAlbum();
			}
			
			return view('album', ['pedido' => $pedido, 'album' => $album]);
		}
		else
		{
			$value = $request->session()->flash('erro', 'Não encontrei o pedido');
			return redirect('pedidos')->with($value);
		}
	}
}
