<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicosAutomacaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function Servico($servico){
        switch($servico){
            case 'importar':
                $sql = "SELECT i.id, e.id_externo os, i.id_externo op, i.url_origem, i.quantidade, i.dt_processo_importacao, i.tentativa_importar, p.titulo produto
                        FROM pedido_items i JOIN produtos p ON p.id = i.id_produto JOIN pedidos e ON e.id = i.id_pedido 
                        WHERE i.url_origem IS NOT NULL AND i.url_origem != '' AND i.data_importacao IS NULL 
                        GROUP BY i.id ORDER BY i.id";
                $albuns = DB::select($sql);

                $id_contrato = DB::select("SELECT i.id FROM pedido_items i JOIN pedidos p ON p.id = i.id_pedido WHERE p.tipo_contrato = 1 ORDER BY i.dt_processo_importacao DESC LIMIT 1");
                $id_pedido = DB::select("SELECT i.id FROM pedido_items i JOIN pedidos p ON p.id = i.id_pedido WHERE p.tipo_contrato != 1 ORDER BY i.dt_processo_importacao DESC LIMIT 1");

                $processando = [
                    $id_contrato[0]->id,
                    $id_pedido[0]->id
                ];

                return view('servicos_automacao.importar', ['albuns' => $albuns, 'processando' => $processando]);
                break;

            case 'exportar':
                $sql = "SELECT i.id, p.id_externo os, i.id_externo op, i.quantidade, i.dt_processo_envio_impressao, concat(p.id_cliente, ' - ', p.cliente) cliente, concat(r.id_externo, ' - ', r.titulo) produto
                        FROM pedidos p JOIN pedido_items i ON p.id = i.id_pedido JOIN produtos r ON r.id = i.id_produto 
                        WHERE i.imprimir = 1 AND i.data_envio_impressao IS NULL";
                $albuns = DB::select($sql);

                $id_pedido = DB::select("SELECT i.id FROM pedido_items i JOIN pedidos p ON p.id = i.id_pedido ORDER BY i.dt_processo_envio_impressao DESC LIMIT 1");

                $processando = [
                    $id_pedido[0]->id
                ];

                return view('servicos_automacao.exportar', ['albuns' => $albuns, 'processando' => $processando]);
                break;
            
            case 'saida_correcao':
                $sql = "SELECT gsls.id, gsls.cliente, gsls.ordem_servico, gsls.ordem_producao, concat(gsls.path, ' - ', gsls.album) url, gsls.quantidade, gsls.dt_processo_saida_correcao
                        FROM gsls JOIN pedido_items i ON i.id = gsls.id_pedido_item JOIN pedidos p ON p.id = i.id_pedido
                        WHERE correcao AND dt_correcao_entrada IS NOT NULL AND dt_correcao_saida IS NULL AND p.data_fechamento IS NULL";
                $albuns = DB::select($sql);
                
                $id_pedido = DB::select("SELECT id FROM gsls ORDER BY dt_processo_saida_correcao DESC LIMIT 1");

                $processando = [
                    $id_pedido[0]->id
                ];

                return view('servicos_automacao.saida_correcao', ['albuns' => $albuns, 'processando' => $processando]);
                break;
        }

        return redirect('/');
    }
}