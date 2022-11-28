<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\PedidoItem;

class GuiaOPController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function Index()
	{
        $sql = "SELECT
                    p.id,
                    p.id_externo,
                    u.name usuario,
                    p.id_cliente,
                    p.cliente,
                    p.contrato,
                    p.data_entrada,
                    p.previsao_entrega,
                    p.tipo_contrato,
                    count(i.id) impressoes
                FROM pedidos p
                    LEFT JOIN users u ON u.id = p.id_usuario
                    LEFT JOIN pedido_items i ON i.id_pedido = p.id AND i.impressoes = 0
                WHERE p.data_fechamento IS NULL
                GROUP BY p.id
                ORDER BY p.id DESC";

        $pedidos = DB::select($sql);

        return view('guias_op', ['pedidos' => $pedidos]);
    }

    public function Guias($id)
	{
        $sql = "SELECT
                    p.id,
                    p.id_externo,
                    p.id_cliente,
                    p.cliente,
                    p.contrato,
                    p.data_entrada,
                    p.previsao_entrega,
                    p.tipo_contrato,
                    u.name usuario
                FROM pedidos p
                    LEFT JOIN users u ON u.id = p.id_usuario
                WHERE p.id = :id";

		$pedido = DB::select($sql, ['id' => $id]);

		if(count($pedido) > 0){
            $pedido = $pedido[0];

            $sql = "SELECT
                        i.id,
                        i.id_externo,
                        i.id_produto,
                        concat(p.id_externo,' - ',p.titulo) produto,
                        count(album.id) albuns,
                        sum(album.arquivos) arquivos,
                        i.impressoes
                    FROM pedido_items i
                    JOIN produtos p ON i.id_produto = p.id
                    LEFT JOIN(
                        SELECT a.id, a.id_item, count(f.id) arquivos FROM pedido_albums a
                        LEFT JOIN pedido_item_arquivos f ON f.id_album = a.id
                        GROUP BY a.id
                    ) album ON album.id_item = i.id
                    WHERE i.id_pedido = :id_pedido AND i.id_externo IS NOT NULL
                    GROUP BY i.id";

            $itens = DB::select($sql, ['id_pedido' => $pedido->id]);
        }

        return view('guia_op', ['pedido' => $pedido, 'itens' => $itens]);
    }

    public function Imprimir($id){
        // Dados da OP
        $sql = "SELECT
                    p.id,
                    p.id_externo os,
                    concat(p.id_cliente, ' - ', p.cliente) cliente,
                    p.contrato,
                    p.data_entrada,
                    p.previsao_entrega,
                    p.tipo_contrato,
                    u.name usuario,
                    i.id id_item,
                    i.id_externo op,
                    prd.id_externo id_produto,
                    prd.titulo titulo_produto,
                    count(album.id) albuns,
                    sum(album.arquivos) arquivos
                FROM pedidos p
                JOIN pedido_items i ON p.id = i.id_pedido
                JOIN produtos prd ON i.id_produto = prd.id
                LEFT JOIN users u ON u.id = p.id_usuario
                LEFT JOIN(
                    SELECT a.id, a.id_item, count(f.id) arquivos FROM pedido_albums a
                    LEFT JOIN pedido_item_arquivos f ON f.id_album = a.id
                    GROUP BY a.id
                ) album ON album.id_item = i.id
                WHERE i.id = :id
                GROUP BY i.id";
        $pedido = DB::select($sql, ['id' => $id]);
        
        if(count($pedido) > 0){
            $pedido = $pedido[0];

            # Itens
            $sql = "SELECT
                        i.id_externo op,
                        p.id_externo id_produto,
                        p.titulo titulo_produto,
                        count(album.id) albuns,
                        sum(album.arquivos) arquivos
                    FROM pedido_items i
                    JOIN produtos p ON i.id_produto = p.id
                    LEFT JOIN(
                        SELECT a.id, a.id_item, count(f.id) arquivos FROM pedido_albums a
                        LEFT JOIN pedido_item_arquivos f ON f.id_album = a.id
                        GROUP BY a.id
                    ) album ON album.id_item = i.id
                    WHERE i.id_pedido = :id_pedido AND i.id != :id
                    GROUP BY i.id";
            $itens = DB::select($sql, ['id_pedido' => $pedido->id, 'id' => $pedido->id_item]);
            
            # Observações
            $observacoes = DB::connection('mysqlXKey')->select("SELECT OBSERVACOES obs FROM zangraf_xkey_principal.cad_orca WHERE codigo = :codigo", ['codigo' => $pedido->os]);
            
            # Código de barras
            # Planilha de álbuns
            $sql = "SELECT 
                        a.codigo album, count(f.id) fotos 
                    FROM 
                        pedido_items i
                        LEFT JOIN pedido_albums a ON i.id = a.id_item
                        LEFT JOIN pedido_item_arquivos f ON a.id = f.id_album
                    WHERE i.id = :id
                    GROUP BY a.id";
            $planilhas = DB::select($sql, ['id' => $pedido->id_item]);


            return view(
                'guia_op_html', 
                [
                    'pedido' => $pedido, 
                    'itens' => $itens,
                    'observacoes' => $observacoes[0]->obs,
                    'planilhas' => $planilhas
                ]
            );
        }
    }
}
