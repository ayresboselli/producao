<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Clientes()
    {
        $clientes = Cliente::get();
        return view('clientes', ['clientes' => $clientes]);
    }

    public function NovoCliente(Request $request)
    {
        $cliente = Cliente::where('id_externo','=',$request->id_cliente)->get();
        if(count($cliente) == 0)
        {
            $sql = "SELECT codigo, nome 
                    FROM zangraf_xkey_publico.cad_clie
                    WHERE codigo = :codigo";
            $result = DB::connection('mysqlXKey')->select($sql, ['codigo' => $request->id_cliente]);
            
            if(count($result) > 0)
            {
                $cliente = Cliente::create([
                    'id_externo' => $result[0]->codigo,
                    'nome' => $result[0]->nome
                ]);

                return redirect('/cliente/'.$cliente->id);
            }
            else
            {
                $value = $request->session()->flash('erro', 'Não consegui encontrar o cliente');
                return redirect('clientes')->with($value);
            }
        }
        else
        {
            return redirect('/cliente/'.$cliente->id);
        }
    }

    public function Cliente($id)
    {
        $cliente = Cliente::find($id);
        if(!is_null($cliente))
        {
            return view('cliente', ['cliente' => $cliente]);
        }

        $value = $request->session()->flash('erro', 'Não consegui encontrar o cliente');
        return view('clientes')->with($value);
    }

    public function UsuarioFTP(Request $request)
    {
        $sql = "SELECT count(id) cnt FROM clientes WHERE ftp_usuario = :usuario AND id != :id";
        $result = DB::select($sql, ['id' => $request->id, 'usuario' => $request->usuario]);

        return $result[0]->cnt;
    }

    public function Salvar(Request $request)
    {
        $cliente = Cliente::find($request->id);
        if(!is_null($cliente))
        {
            $ftp_atualizado = $cliente->ftp_atualizado;
            if($cliente->ftp_usuario != $request->ftp_usuario || $cliente->ftp_senha != $request->ftp_senha)
                $ftp_atualizado = false;
            
            $cliente->update([
                'id_externo' => $request->id_externo, 
                'nome' => $request->nome, 
                'ftp_usuario' => $request->ftp_usuario, 
                'ftp_senha' => $request->ftp_senha,
                'ftp_atualizado' => $ftp_atualizado
            ]);
        }
        else
        {
            Cliente::create([
                'id_externo' => $request->id_externo, 
                'nome' => $request->nome, 
                'ftp_usuario' => $request->ftp_usuario, 
                'ftp_senha' => $request->ftp_senha
            ]);
        }
        
        $value = $request->session()->flash('sucesso', 'Cliente salvo com sucesso');
        return redirect('clientes')->with($value);
    }

}
