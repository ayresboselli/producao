<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;

class ArquivamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function Arquivamento()
    {
        $sql = "SELECT p.*, c.id clienteFTP
                FROM pedidos p
                LEFT JOIN clientes c ON c.id_externo = p.id_cliente AND (c.ftp_usuario IS NOT NULL OR c.ftp_usuario = '')
                WHERE p.data_fechamento IS NOT NULL AND p.excluido = 0";
        $pedidos = DB::select($sql);
        
        $lista = [];
        foreach($pedidos as $pedido)
		{
			if(!is_null($pedido->data_fechamento))
			{
                $lista[] = $pedido;
            }
        }

        return view('arquivamento', ['pedidos' => $lista]);
    }


    public function Salvar(Request $request)
    {
        $pedido = Pedido::find($request->modalId);
        if(!is_null($pedido))
        {
            if($request->arquivar == 1){
                $data_exclusao = date('Y-m-d');
            }else{
                $data_exclusao = date('Y-m-d', strtotime('+30 days'));
            }

            $pedido->update([
                'arquivar' => $request->arquivar,
                'data_exclusao' => $data_exclusao,
                'processado' => 0,
                'excluido' => 0
            ]);
            
            $value = $request->session()->flash('sucesso', 'OS salva');
        }
        else
        {
            $value = $request->session()->flash('erro', 'Erro ao salvar a OS');
        }

        return redirect('arquivamento')->with($value);
    }
}
