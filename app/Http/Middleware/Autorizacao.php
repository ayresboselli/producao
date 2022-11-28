<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Autorizacao
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $funcao)
    {
        if(!is_null(session()->get('funcoes')))
        {
            foreach(session()->get('funcoes') as $funcUser)
            {
                if(in_array($funcUser, explode('.',$funcao)))
                    return $next($request);
            }
        }
        else
        {
            return $next($request);
        }
        
        return redirect('/');
    }
}
