<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReimpressaoAlbumPedido;
use App\Models\ReimpressaoAlbumLamina;
use App\Models\Produto;
use App\Models\Celula;
use App\Models\IndicadorLista;
use App\Models\IndicadorApontamento;
use Session;

class ReimpressaoAlbumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function Index()
    {
        $sql = "SELECT r.* FROM 
                reimpressao_album_pedidos r
                JOIN pedido_items i ON i.id_externo = r.ordem_producao
                JOIN pedidos p ON i.id_pedido = p.id
                WHERE p.data_fechamento IS NULL";
        $pedidos = DB::select($sql);
        
        return view('reimpressoes_album', ['pedidos' => $pedidos]);
    }

    public function Editar($id = null)
    {
        $laminas = [];
        $cliente = null;
        $produto = null;
        $pedido = ReimpressaoAlbumPedido::find($id);
        if(!is_null($pedido))
        {
            // Lâminas
            $sql = "SELECT l.*, c.nome celula, i.descricao indicador FROM reimpressao_album_laminas l
                    LEFT JOIN indicadores_apontamentos a ON l.id = a.id_lamina
                    LEFT JOIN  indicadores_listas i ON i.id = a.id_indicador
                    LEFT JOIN  celulas c ON c.id = a.id_celula_falha
                    WHERE l.id_reimpressao = :id_reimpressao";
            $laminas = DB::select($sql, ['id_reimpressao' => $pedido->id]);
            //$laminas = ReimpressaoAlbumLamina::where('id_reimpressao','=',$pedido->id)->get();

            // Cliente
            $sql = "SELECT codigo, apelido nome
                    FROM zangraf_xkey_publico.cad_clie
                    WHERE codigo = :cliente";
            $cliente = DB::connection('mysqlXKey')->select($sql,['cliente' => $pedido->cliente])[0];

            // Produto
            $produto = Produto::where('id_externo','=',$pedido->produto)->get()[0];
        }
        else
        {
            $pedido = new ReimpressaoAlbumPedido();
            Session::put('reimp_indicador', null);
            Session::put('reimp_celula', null);
        }

        $celulas = Celula::get();
        $indicadores = IndicadorLista::get();

        //return view('reimpressao_album', ['pedido' => $pedido]);
        return view(
            'reimpressao_album', 
            [
                'pedido' => $pedido, 
                'cliente' => $cliente, 
                'produto' => $produto, 
                'laminas' => $laminas, 
                'celulas' => $celulas,
                'indicadores' => $indicadores
            ]
        );
    }

    public function Salvar(Request $request)
    {
        $pedido = ReimpressaoAlbumPedido::find($request->id);
        $dados_pedido['imprimir'] = $request->imprimir;
        
        if(!is_null($request->id_album))
        {
            $album = DB::select("SELECT * FROM gsls WHERE id = :id_album", ['id_album' => $request->id_album]);
            if(count($album) == 0){
                $sql = "SELECT id, 
                            nome nome_album, 
                            ordemProducao ordem_servico 
                        FROM zangraf_flow.albuns 
                        WHERE id = :id_album";
                $album = DB::connection('mysqlXKey')->select($sql,['id_album' => $request->id_album]);
            }
        }
        else
        {
            $album = [];
        }

        if(count($album) > 0)
        {
            $estrutura = explode('_', $album[0]->nome_album);
            
            // Cliente
            $sql = "SELECT codigo, apelido nome
                    FROM zangraf_xkey_publico.cad_clie
                    WHERE codigo = :id_album";
            $cliente = DB::connection('mysqlXKey')->select($sql,['id_album' => $estrutura[0]])[0];

            // Produto
            $sql = "SELECT pd.codigo, pd.produto
                    FROM zangraf_xkey_principal.pro_orca po
                    JOIN zangraf_xkey_producao.producoes pd ON po.produto = pd.produto AND pd.cod_producao = po.orcamento
                    WHERE po.orcamento = :os AND po.sequencia = :seq";
            $producao = DB::connection('mysqlXKey')->select($sql,['os' => $album[0]->ordem_servico, 'seq' => $estrutura[2]])[0];
            
            if(is_null($request->id_produto) || $request->id_produto == $producao->produto)
            {
                $produto = Produto::where('id_externo','=',$producao->produto)->get()[0];
                
                $dados_pedido = [
                    'titulo' => 'Reimpressao_'.$producao->codigo.'_'.date('YmdHis'), 
                    'cliente' => $cliente->codigo,
                    'produto' => $producao->produto, 
                    'ordem_producao' => $producao->codigo, 
                    'imprimir' => $request->imprimir
                ];

                if(!is_null($pedido)){
                    $pedido->update($dados_pedido);
                }else{
                    $pedido = ReimpressaoAlbumPedido::create($dados_pedido);
                }

                if($produto->disposicao == 'Duplex')
                {
                    if($request->id_foto % 2 == 0){
                        $dados = [
                            'foto_frente' => $request->id_foto -1, 
                            'foto_verso' => (int)$request->id_foto
                        ];
                    }else{
                        $dados = [
                            'foto_frente' => (int)$request->id_foto, 
                            'foto_verso' => $request->id_foto +1
                        ];
                    }
                }
                else
                {
                    $dados = ['foto_frente' => $request->id_foto];
                }

                $dados['id_reimpressao'] = $pedido->id;
                $dados['album'] = $album[0]->nome_album;
                $lamina = ReimpressaoAlbumLamina::create($dados);

                $indicador = IndicadorApontamento::create([
                    'id_lamina' => $lamina->id,
                    'id_indicador' => $request->id_indicador, 
                    'id_celula_ident' => 4, 
                    'id_celula_falha' => $request->id_celula, 
                ]);

                Session::put('reimp_indicador', $indicador->id_indicador);
                Session::put('reimp_celula', $indicador->id_celula_falha);


                $value = $request->session()->flash('sucesso', 'Álbum adicionado comsucesso!');
            }
            else
            {
                // Álbum incompatível com o produto
                $value = $request->session()->flash('erro', 'Álbum incompatível com o produto!');
            }
        }
        else if(!$request->imprimir)
        {
            $value = $request->session()->flash('erro', 'Não encontrei o álbum!');
        }

        if(!is_null($pedido)){
            $pedido->update($dados_pedido);
        }else{
            $pedido = ReimpressaoAlbumPedido::create($dados_pedido);
        }

        if($request->imprimir){
            $value = $request->session()->flash('sucesso', 'Pedido finalizado com sucesso!');
            return redirect('reimpressoes_album')->with($value);
        }else{
            return redirect('reimpressao_album/'.$pedido->id)->with($value);
        }
    }

    public function Reimprimir(Request $request)
    {
        $pedido = ReimpressaoAlbumPedido::find($request->id);
        if(!is_null($pedido))
        {
            $pedido->update([
                'processada' => null
            ]);

            $value = $request->session()->flash('sucesso', 'Reimpressão salva com sucesso!');
            return redirect('reimpressoes_album')->with($value);
        }

        $value = $request->session()->flash('erro', 'Erro ao salvar a reimpressão!');
        return redirect('reimpressoes_album')->with($value);
    }

    public function DeletarLamina(Request $request)
    {
        $pedido = ReimpressaoAlbumPedido::find($request->id_pedido);
        if(!is_null($pedido))
        {
            $lamina = ReimpressaoAlbumLamina::where('id_reimpressao','=',$pedido->id)->find($request->id_lamina);
            
            $indicador = IndicadorApontamento::where('id_lamina', $lamina->id)->first();
            $indicador->delete();

            if($lamina->delete())
                return ['success' => true];

        }

        return ['success' => false];
    }

    public function DeletarReimpressao(Request $request)
    {
        $pedido = ReimpressaoAlbumPedido::find($request->id);
        if(!is_null($pedido))
        {
            if($pedido->delete())
            {
                $value = $request->session()->flash('sucesso', 'Reimpressão deletada com sucesso!');
                return redirect('reimpressoes_album')->with($value);
            }

        }

        $value = $request->session()->flash('erro', 'Erro ao deletar a reimpressão!');
        return redirect('reimpressoes_album')->with($value);
    }
}
