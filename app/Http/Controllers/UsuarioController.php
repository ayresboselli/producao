<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Index()
	{
		$usuarios = User::get();
		return view('usuarios', ['usuarios' => $usuarios]);
	}
	
    public function Editar($id = null)
    {
        $usuario = User::find($id);
        if(is_null($usuario))
        {
            $usuario = new User();
        }

        $perfis = [];
        $sql = "
            SELECT p.id, p.titulo, p.descricao, up.id_usuario 
            FROM perfils p
            LEFT JOIN usuarios_perfis up ON p.id = up.id_perfil AND up.id_usuario = :id_usuario
            ORDER BY p.titulo";
        
        $perfis = DB::select($sql, ['id_usuario' => $usuario->id]);

        $id_perfis = [];
        foreach($perfis as $perfil)
        {
            if(!is_null($perfil->id_usuario))
            {
                $id_perfis[] = $perfil->id;
            }
        }

        return view('usuario', ['usuario' => $usuario, 'perfis' => $perfis, 'id_perfis' => implode(',', $id_perfis)]);
    }

    public function Salvar(Request $request)
    {
        $usuario = User::find($request->id);
        if(is_null($usuario))
        {
            $usuario = User::create([
                'name' => $request->nome,
                'email' => $request->email,
                'password' => sha1(uniqid()).uniqid(),
                'ativo' => $request->ativo=='on'?true:false
            ]);
        }
        else
        {
            $usuario->update([
                'name' => $request->nome,
                'email' => $request->email,
                'ativo' => $request->ativo=='on'?true:false
            ]);
        }

        DB::table('usuarios_perfis')->where('id_usuario','=',$usuario->id)->delete();
        $perfis = array_unique(explode(',',$request->id_perfis));
        foreach($perfis as $perfil)
        {
            if(is_numeric($perfil))
            {
                DB::table('usuarios_perfis')->insert([
                    'id_usuario' => $usuario->id,
                    'id_perfil' => $perfil, 
                ]);
            }
        }

        $value = $request->session()->flash('sucesso', 'Usuário salvo com sucesso!');
        return redirect('/usuarios')->with($value);
    }

    public function AltSenha($id)
    {
        $usuario = User::find($id);
        if(is_null($usuario))
        {
            $value = $request->session()->flash('erro', 'Usuário não encontrado!');
            return redirect('/usuarios')->with($value);
        }

        return view('usuario_alt_senha', ['usuario' => $usuario]);
    }

    public function AltSenhaSalvar(Request $request)
    {
        $usuario = User::find($request->id);
		
		if($request->senha_n == $request->senha_r){
			$usuario->update(['password' => Hash::make($request->senha_n)]);
			$value = $request->session()->flash('sucesso', 'Senha alterada com sucesso');
			return redirect('usuarios')->with($value);
		}else{
			$value = $request->session()->flash('senha_r', 'As senhas não conferem.');
			return view('usuario_alt_senha', ['usuario' => $usuario])->with($value);
		}

        $value = $request->session()->flash('erro', 'Usuário não encontrado!');
        return redirect('/usuarios')->with($value);
    }

}
