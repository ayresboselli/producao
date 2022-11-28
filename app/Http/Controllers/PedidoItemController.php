<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PedidoItem;

class PedidoItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Index()
	{
		$sql = "SELECT 
                    i.id,
                    p.id_externo os, 
                    concat(p.id_cliente, ' - ', p.cliente) cliente,
                    i.id_externo op,
                    i.url_origem,
                    i.id_produto,
                    i.copias,
                    i.id_produto_externo,
                    concat(pro.id_externo, ' - ', pro.titulo) produto,
                    i.data_importacao,
                    i.imprimir,
                    i.data_envio_impressao,
                    count(a.id) arquivos,
                    count(r.id) recortes,
                    sum(a.situacao) situacao_arquivos,
                    u.name usuario,
                    
                    if(count(a.id) = 0,
                        if(i.url_origem IS NULL,
                            0/*Aguardando envio de arquivos*/
                        ,
                            1/*Aguardando processamento de arquivos*/
                        )
                    ,
                        if(count(a.id) != sum(a.situacao) or i.data_importacao IS NULL,
                            1/*Aguardando processamento de arquivos*/
                        ,
                            if(i.id_externo IS NULL,
                                2/*Aguardando a criação de O.P.*/
                            ,
                                if(i.data_envio_impressao IS NULL,
                                    if(i.imprimir = 0,
                                        3/*Pronto para impressão*/
                                    ,
                                        4/*Exportando*/
                                    )
                                ,
                                    5/*Em produção*/
                                )
                            )
                        )
                    ) situacao
                
                FROM pedidos p
                JOIN pedido_items i ON p.id = i.id_pedido
                LEFT JOIN produtos pro ON pro.id = i.id_produto
                LEFT JOIN users u ON u.id = p.id_usuario
                LEFT JOIN pedido_item_arquivos a ON i.id = a.id_item
                LEFT JOIN recortes r ON a.id = r.id_arquivo 
                    AND a.situacao = 0 AND 
                    r.crop_pos_x IS NULL AND 
                    r.crop_pos_y IS NULL AND 
                    r.crop_largura IS NULL AND 
                    r.crop_altura IS NULL
                
                WHERE p.data_fechamento IS NULL
                GROUP BY i.id
                ORDER BY p.id_externo DESC
                ";
		$itens = DB::select($sql);

		return view('pedidos_itens', ['itens' => $itens]);
	}
	
}
