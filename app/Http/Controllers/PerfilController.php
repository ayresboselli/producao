<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Perfil;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Index()
	{
		$perfis = Perfil::get();
		return view('perfis',['perfis' => $perfis]);
	}
	
    public function Editar($id = null)
    {
        $perfil = Perfil::find($id);
        if(is_null($perfil))
        {
            $perfil = new Perfil();
        }

        $sql = "
            SELECT f.id, f.chave, f.descricao, pf.id_perfil 
            FROM funcaos f 
            LEFT JOIN perfis_funcoes pf 
                ON f.id = pf.id_funcao 
                AND pf.id_perfil = :id_perfil
            ORDER BY f.chave";
        
        $funcoes = DB::select($sql, ['id_perfil' => $perfil->id]);

        $id_funcoes = [];
        foreach($funcoes as $funcao)
        {
            if(!is_null($funcao->id_perfil))
            {
                $id_funcoes[] = $funcao->id;
            }
        }

        return view('perfil', ['perfil' => $perfil, 'funcoes' => $funcoes, 'id_funcoes' => implode(',', $id_funcoes)]);
    }

    public function Salvar(Request $request)
    {
        $perfil = Perfil::find($request->id);
        if(is_null($perfil))
        {
            $perfil = Perfil::create([
                'titulo' => $request->titulo,
                'descricao' => $request->descricao
            ]);
        }
        else
        {
            $perfil->update([
                'titulo' => $request->titulo,
                'descricao' => $request->descricao
            ]);
        }

        DB::table('perfis_funcoes')->where('id_perfil','=',$perfil->id)->delete();
        $funcoes = array_unique(explode(',',$request->func_selecionada));
        foreach($funcoes as $funcao)
        {
            if(is_numeric($funcao))
            {
                DB::table('perfis_funcoes')->insert([
                    'id_perfil' => $perfil->id, 
                    'id_funcao' => $funcao
                ]);
            }
        }

        $value = $request->session()->flash('successo', 'Perfil salvo com sucesso!');
        return redirect('/perfis')->with($value);
    }

    public function Deletar(Request $request)
    {
        $perfil = Perfil::find($request->id);
        if(!is_null($perfil))
        {
            $perfil->delete();
            $value = $request->session()->flash('successo', 'Perfil excluído com sucesso!');
        }
        else
        {
            $value = $request->session()->flash('erro', 'Erro ao excluir o perfil!');
        }

        return redirect('/perfis')->with($value);
    }
}
