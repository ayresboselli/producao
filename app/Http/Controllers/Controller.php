<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\User;

use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected function Autorizacao()
    {
        if(is_null(session()->get('funcoes')))
        {
            $user = User::find(Auth()->user()->id);

            $lista = [];
            foreach ($user->perfis as $perfil)
                foreach ($perfil->funcoes as $funcao)
                    if (!in_array($funcao->chave, $lista))
                        $lista[] = $funcao->chave;

            session()->put('funcoes',$lista);
        }
    }

}
