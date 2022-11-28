<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Gsl;
use App\Models\GslConfig;
use App\Models\User;

class GSLController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /*
        $sql = "SELECT
                    gsls.id,
                    gsls.ordem_servico,
                    gsls.ordem_producao,
                    gsls.nome_album,
                    gsls.tipo_pedido,
                    if(gsls.correcao = 0, 0, 
                        if(gsls.dt_correcao_entrada IS NULL AND gsls.dt_correcao_saida IS NULL, 1,
                            if(gsls.dt_correcao_entrada IS NOT NULL AND gsls.dt_correcao_saida IS NULL, 2, 3)
                        )
                    ) correcao,
                    if(gsls.dt_imposicao_entrada IS NULL AND gsls.dt_imposicao_saida IS NULL, 1,
                        if(gsls.dt_imposicao_entrada IS NOT NULL AND gsls.dt_imposicao_saida IS NULL, 2, 3)
                    ) imposicao,
                    if(gsls.dt_impressao_entrada IS NULL, 1, 3) impressao,

                    gsls.dt_correcao_entrada,
                    gsls.dt_correcao_saida,
                    gsls.dt_imposicao_entrada,
                    gsls.dt_imposicao_saida,
                    gsls.dt_impressao_entrada
                FROM gsls
                JOIN pedido_items i ON i.id_externo = gsls.ordem_producao
                JOIN pedidos p ON p.id = i.id_pedido
                WHERE p.data_fechamento IS NULL";
                
        $gsls = DB::select($sql);
        */
        $config = GslConfig::get();

        return view('gsl', [/*'gsls' => $gsls, */'config' => $config]);
    }

    public function Filtrar(Request $request)
    {
        $sql = "SELECT
                    gsls.id,
                    gsls.ordem_servico,
                    gsls.ordem_producao,
                    gsls.nome_album,
                    gsls.tipo_pedido,
                    if(gsls.correcao = 0, 0, 
                        if(gsls.dt_correcao_entrada IS NULL AND gsls.dt_correcao_saida IS NULL, 1,
                            if(gsls.dt_correcao_entrada IS NOT NULL AND gsls.dt_correcao_saida IS NULL, 2, 3)
                        )
                    ) correcao,
                    if(gsls.dt_imposicao_entrada IS NULL AND gsls.dt_imposicao_saida IS NULL, 1,
                        if(gsls.dt_imposicao_entrada IS NOT NULL AND gsls.dt_imposicao_saida IS NULL, 2, 3)
                    ) imposicao,
                    if(gsls.dt_impressao_entrada IS NULL, 1, 3) impressao,

                    gsls.dt_correcao_entrada,
                    gsls.dt_correcao_saida,
                    gsls.dt_imposicao_entrada,
                    gsls.dt_imposicao_saida,
                    gsls.dt_impressao_entrada
                FROM gsls
                -- JOIN pedido_items i ON i.id_externo = gsls.ordem_producao
                JOIN pedido_items i ON i.id = gsls.id_pedido_item
                JOIN pedidos p ON p.id = i.id_pedido
                WHERE p.data_fechamento IS NULL ";

        $param = [];
        if(!is_null($request->os)){
            $sql .= "AND gsls.ordem_servico = :os ";
            $param['os'] = $request->os;
        }

        if(!is_null($request->op)){
            $sql .= "AND gsls.ordem_producao = :op ";
            $param['op'] = $request->op;
        }
        
        
        if(count($param) > 0){
            $gsls = DB::select($sql, $param);
        }else{
            $gsls = [];
        }
        
        
        return $gsls;
    }

    public function Reprocessar(Request $request)
    {
        $opc = $request->opc_reprocessar;
        $albuns = explode(',', $request->lista_reprocessar);
        $status = false;
        
        switch($opc)
        {
            // Correção
            case 1: 
                $sql = "UPDATE gsls 
                        SET
                            dt_correcao_entrada = NULL, 
                            dt_correcao_saida = NULL, 
                            dt_imposicao_entrada = NULL, 
                            dt_imposicao_saida = NULL, 
                            dt_impressao_entrada = NULL
                        WHERE id = :album";
                break;
            
            // Imposição
            case 2: 
                $sql = "UPDATE gsls 
                        SET
                            dt_imposicao_entrada = NULL, 
                            dt_imposicao_saida = NULL, 
                            dt_impressao_entrada = NULL
                        WHERE id = :album";
                break;
            
            // Impressão
            case 3:
                $sql = "UPDATE gsls SET dt_impressao_entrada = NULL WHERE id = :album";
                break;
            
            default:
                $sql = "";
        }

        if($sql != "")
        {
            foreach($albuns as $album)
            {
                $result = DB::update($sql, ['album' => $album]);
                if($result){
                    $status = true;
                }

                $sql_log = "INSERT INTO log(tipo, modulo, mensagem, created_at) VALUES(2, 'Reprocessar', 'Módulo: $opc, Álbum: $album, Usuário: ".Auth()->user()->id."', now())";
                DB::insert($sql_log);
            }
        }

        /*
        if($status)
            $value = $request->session()->flash('sucesso', 'Álbum(sn) Reprocessado(s)');
        else
            $value = $request->session()->flash('erro', 'Não consegui reprocessar o(s) album(ns)');

        return redirect('gsl')->with($value);
        */
        
        return ['success' => $status];
    }

    public function Configuracoes(Request $request)
    {
        $config = GslConfig::get();
        foreach($config as $c)
        {
            $name = "chk_$c->id";
            $c->update(['ativo' => $request->$name == 'on'?1:0]);
        }

        $value = $request->session()->flash('sucesso', 'Configurações salvas');
        return redirect('gsl')->with($value);
    }
}
