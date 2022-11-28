<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produto;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->Autorizacao();
        $produtos = Produto::orderBy('id_externo', 'ASC')->get();

        return view(
            'home', 
            [
                'contador' => $this->Contadores(),
                'autorizacao' => session()->get('funcoes'),
                'produtos' => $produtos
            ]
        );
    }

    private function Autorizacao()
    {
        if(is_null(session()->get('funcoes')))
        {
            $sql = "SELECT DISTINCT f.chave FROM funcaos f
            JOIN perfis_funcoes pf ON pf.id_funcao = f.id
            JOIN perfils p ON p.id = pf.id_perfil
            JOIN usuarios_perfis up ON up.id_perfil = p.id
            WHERE up.id_usuario = :id_usuario";
            
            $funcoes = DB::select($sql, ['id_usuario' => Auth()->user()->id]);
            
            $lista = [];
            foreach($funcoes as $funcao)
            {
                $lista[] = $funcao->chave;
            }

            session()->put('funcoes',$lista);
        }
    }

    public function StatusServicos()
    {
        $sql = "SELECT 
            id, 
            servico, 
            descricao, 
            tempo, 
            reiniciar,
            DATE_FORMAT (ultimo_processo,'%d/%m/%Y %H:%i:%s') ultimo_processo,
            if(unix_timestamp(now()) - unix_timestamp(ultimo_processo) < tempo,1,0) ativo
        FROM servicos_status";
        return DB::select($sql);
    }

    public function StatusServicosRestart(Request $request){
        $sql = "UPDATE servicos_status SET reiniciar = 1 WHERE id = :id";
        return DB::select($sql, ['id' => $request->id]);
    }

    private function Contadores()
    {
        $sql = "SELECT * FROM
                (SELECT count(id) abertas FROM pedidos WHERE data_fechamento IS NULL) abertas,
                (SELECT count(id) arquivamento FROM pedidos WHERE data_fechamento IS NOT NULL AND arquivar IS NULL) arquivamento,
                (
                    SELECT count(id) imprimir FROM (
                        SELECT i.id, i.quantidade, i.imprimir, count(f.id) arquivos, count(r.id) recorte 
                        FROM pedido_items i
                        JOIN pedidos p ON p.id = i.id_pedido AND p.excluido = 0
                        LEFT JOIN pedido_albums a ON a.id_item = i.id
                        LEFT JOIN pedido_item_arquivos f ON a.id = f.id_album
                        LEFT JOIN recortes r ON r.id_arquivo = f.id
						WHERE i.id_externo IS NOT NULL AND i.imprimir = 0 AND i.quantidade > 0 AND i.data_importacao IS NOT NULL
                        GROUP BY i.id
                    ) t
                    WHERE arquivos > 0
                ) imprimir,
                (
                    SELECT count(id) recorte FROM (
                        SELECT i.id FROM pedido_items i
                        JOIN pedido_albums a ON a.id_item = i.id
                        JOIN pedido_item_arquivos f ON a.id = f.id_album
                        JOIN recortes r ON r.id_arquivo = f.id AND r.crop_pos_x IS NULL AND r.crop_pos_y IS NULL AND r.crop_largura IS NULL AND r.crop_altura IS NULL
                        GROUP BY i.id
                    ) t
                ) recorte";
        
        $contadores = DB::select($sql);

        return $contadores[0];
    }

    public function ImpressaoMensal()
    {
        $sql = "SELECT * FROM (
                    SELECT sum(pro.quantidade) quantidade, substring(orca.data_cadastro,1,7) mes 
                    FROM zangraf_xkey_principal.cad_orca orca
                    JOIN zangraf_xkey_producao.producoes pro ON pro.cod_producao = orca.codigo
                    GROUP BY substring(orca.data_cadastro,1,7)
                    ORDER BY substring(orca.data_cadastro,1,7) DESC
                    LIMIT 12
                ) T
                ORDER BY mes ASC";
        return DB::connection('mysqlXKey')->select($sql);
    }
    
    public function PerdaMensal()
    {
        $sql = "SELECT * FROM (
                    SELECT sum(perdido) quantidade, substring(data_cadastro,1,7) mes  FROM zangraf_xkey_producao.apontamento_detalhe
                    GROUP BY substring(data_cadastro,1,7)
                    ORDER BY substring(data_cadastro,1,7) DESC
                    LIMIT 12
                ) T
                ORDER BY mes ASC;";
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function ProducaoMensal()
    {
        $sql = "SELECT * FROM (
                    SELECT count(orca.codigo) quantidade, substring(orca.data_cadastro,1,7) mes 
                    FROM zangraf_xkey_principal.cad_orca orca
                    JOIN zangraf_xkey_producao.producoes pro ON pro.cod_producao = orca.codigo
                    GROUP BY substring(orca.data_cadastro,1,7)
                    ORDER BY substring(orca.data_cadastro,1,7) DESC
                    LIMIT 12
                ) T
                ORDER BY mes ASC";
        return DB::connection('mysqlXKey')->select($sql);
    }
    
    public function OpPorCelula()
    {
        $sql = "SELECT 
                    opr.DESCRICAO AS celula, COUNT(0) AS quantidade
                FROM
                    zangraf_xkey_producao.producoes pdc
                    JOIN zangraf_xkey_producao.operacao_producao opr ON opr.PRODUCAO = pdc.CODIGO
                    JOIN zangraf_xkey_producao.apontamento apt ON apt.OPERACAO_PRODUCAO_ID = opr.Id
                    JOIN zangraf_xkey_principal.cad_orca orc ON orc.CODIGO = pdc.COD_PRODUCAO
                WHERE
                    apt.SITUACAO = 'EM ANDAMENTO'
                    and orc.status != 'FI'
                GROUP BY opr.DESCRICAO
                ORDER BY quantidade DESC";
        return DB::connection('mysqlXKey')->select($sql);
    }
    
    public function TempoPorCelula()
    {
        $sql = "SELECT avg(
                        unix_timestamp(concat(apt.data_termino,' ', apt.hora_termino)) - 
                        unix_timestamp(concat(apt.data_inicio,' ', apt.hora_inicio))
                    ) tempo,
                    iop.descricao celula
                FROM zangraf_xkey_producao.apontamento apt
                JOIN zangraf_xkey_producao.itemoperacao iop ON iop.codigo = apt.operacao
                WHERE apt.situacao = 'FINALIZADO' 
                    AND length(iop.descricao) > 1
                    AND apt.data_inicio > date_sub(curdate(), interval 3 month)
                GROUP BY iop.descricao
                ORDER BY tempo DESC";
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function ProdutosMaisVendidos()
    {
        $sql = "SELECT
                    concat(prd.codigo, ' - ', prd.descricao) produto,
                    count(orc.codigo) quantidade
                FROM 
                    zangraf_xkey_principal.cad_orca orc
                    JOIN zangraf_xkey_principal.pro_orca prc ON prc.orcamento = orc.codigo
                    JOIN zangraf_xkey_publico.cad_prod prd ON prd.codigo = prc.produto
                WHERE 
                    orc.DATA_CADASTRO between '2021-01-01' and '2021-12-31'
                    and prd.grupo = 1
                GROUP BY prd.codigo
                ORDER BY count(orc.codigo) desc
                limit 5";
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function ProdutosMaisProduzidos()
    {
        $sql = "SELECT
                    concat(pro.PRODUTO, ' - ', prd.descricao) produto,
                    SUM(pro.QUANTIDADE) quantidade
                FROM 
                    zangraf_xkey_producao.producoes pro
                    JOIN zangraf_xkey_publico.cad_prod prd ON prd.codigo = pro.produto
                WHERE 
                    pro.inicio between '2021-01-01' and '2021-12-31'
                GROUP BY pro.PRODUTO
                ORDER BY SUM(pro.QUANTIDADE) desc
                limit 5";
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function AlbunsMensais()
    {
        $sql = "SELECT substring(created_at, 1, 7) mes, count(*) quantidade FROM (
                    SELECT DISTINCT id_pedido, codigo, created_at FROM pedido_albums
                    WHERE created_at >= '2021-06-01 00:00:00'
                ) T
                GROUP BY substring(created_at, 1, 7)";
        return DB::select($sql);
    }

    public function SituacaoOPsMensais()
    {
        $sql = "SELECT * FROM(
                    SELECT
                        mes, 
                        /*
                        SUM(FI) / (SUM(RE) + SUM(FI) + SUM(BM) + SUM(CA) + SUM(EA) + SUM(AI)) * 100 fi, 
                        SUM(CA) / (SUM(RE) + SUM(FI) + SUM(BM) + SUM(CA) + SUM(EA) + SUM(AI)) * 100 ca, 
                        SUM(BM) / (SUM(RE) + SUM(FI) + SUM(BM) + SUM(CA) + SUM(EA) + SUM(AI)) * 100 bm, 
                        SUM(EA) / (SUM(RE) + SUM(FI) + SUM(BM) + SUM(CA) + SUM(EA) + SUM(AI)) * 100 ea, 
                        SUM(AI) / (SUM(RE) + SUM(FI) + SUM(BM) + SUM(CA) + SUM(EA) + SUM(AI)) * 100 ai,
                        SUM(RE) / (SUM(RE) + SUM(FI) + SUM(BM) + SUM(CA) + SUM(EA) + SUM(AI)) * 100 re 
                        */
                        SUM(FI) fi, 
                        SUM(CA) ca, 
                        SUM(BM) bm, 
                        SUM(EA) ea, 
                        SUM(AI) ai,
                        SUM(RE) re 
                        
                    FROM(
                        SELECT substring(DATA_CADASTRO, 1, 7) mes, count(SITUACAO) RE, 0 FI, 0 BM, 0 CA, 0 EA, 0 AI
                        FROM zangraf_xkey_principal.cad_orca
                        WHERE SITUACAO = 'RE'
                        GROUP BY substring(DATA_CADASTRO, 1, 7), situacao
                    
                        UNION
                    
                        SELECT substring(DATA_CADASTRO, 1, 7) mes, 0 RE, count(SITUACAO) FI, 0 BM, 0 CA, 0 EA, 0 AI
                        FROM zangraf_xkey_principal.cad_orca
                        WHERE SITUACAO = 'FI'
                        GROUP BY substring(DATA_CADASTRO, 1, 7), situacao
                    
                        UNION
                    
                        SELECT substring(DATA_CADASTRO, 1, 7) mes, 0 RE, 0 FI, count(SITUACAO) BM, 0 CA, 0 EA, 0 AI
                        FROM zangraf_xkey_principal.cad_orca
                        WHERE SITUACAO = 'BM'
                        GROUP BY substring(DATA_CADASTRO, 1, 7), situacao
                    
                        UNION
                    
                        SELECT substring(DATA_CADASTRO, 1, 7) mes, 0 RE, 0 FI, 0 BM, count(SITUACAO) CA, 0 EA, 0 AI
                        FROM zangraf_xkey_principal.cad_orca
                        WHERE SITUACAO = 'CA'
                        GROUP BY substring(DATA_CADASTRO, 1, 7), situacao
                    
                        UNION
                    
                        SELECT substring(DATA_CADASTRO, 1, 7) mes, 0 RE, 0 FI, 0 BM, 0 CA, count(SITUACAO) EA, 0 AI
                        FROM zangraf_xkey_principal.cad_orca
                        WHERE SITUACAO = 'EA'
                        GROUP BY substring(DATA_CADASTRO, 1, 7), situacao
                    
                        UNION
                    
                        SELECT substring(DATA_CADASTRO, 1, 7) mes, 0 RE, 0 FI, 0 BM, 0 CA, 0 EA, count(SITUACAO) AI
                        FROM zangraf_xkey_principal.cad_orca
                        WHERE SITUACAO = 'AI'
                        GROUP BY substring(DATA_CADASTRO, 1, 7), situacao
                    ) T
                    GROUP BY MES
                    ORDER BY MES DESC
                    LIMIT 12
                ) L
                ORDER BY MES ASC";
        
        return DB::connection('mysqlXKey')->select($sql);
    }
    
    // Financeiro
    public function IndiceLiquidez()
    {
        $sql = "SELECT * FROM (
                    SELECT * FROM indice_liquidez 
                    ORDER BY data DESC 
                    LIMIT 12
                ) T 
                ORDER BY data";
        
        return DB::select($sql);
    }

    public function MediaPrazosRecebimento()
    {
        $sql = "SELECT data, (mp / valor) media FROM (
                    SELECT
                        substring(DATA_PGTO, 1,7) data,
                        sum(VALOR_PAGO) valor,
                        sum(((unix_timestamp(DATA_PGTO) - unix_timestamp(VENCIMENTO)) / 60 / 60 / 24) * VALOR_PAGO) mp
                    FROM zangraf_xkey_financeiro.ct_receb
                    WHERE DATA_PGTO BETWEEN date_sub(current_date(), interval 12 month) AND current_date()
                    AND AGRUP_DESTINO = 0
                    GROUP BY substring(DATA_PGTO, 1,7)
                    ORDER BY substring(DATA_PGTO, 1,7) DESC
                    LIMIT 24
                ) T
                ORDER BY data ASC";
        
        return DB::connection('mysqlXKey')->select($sql);
    }
    
    public function TotalPagarReceber()
    {
        $sql = "SELECT
                    (sum(ct_pagar) + sum(ch_pagar) + sum(cartao_pagar)) a_pagar,
                    (sum(ct_receb) + sum(ch_receb)) a_receber
                FROM (
                    SELECT sum(valor) ct_pagar, 0 ct_receb, 0 ch_pagar, 0 ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.ct_pagar WHERE valor_pago = 0 AND data_pgto < '1900-01-01' AND AGRUP_DESTINO = 0
                    UNION
                    SELECT 0 ct_pagar, sum(valor) ct_receb, 0 ch_pagar, 0 ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.ct_receb WHERE DATA_PGTO < '1900-01-01' AND AGRUP_DESTINO = 0 AND INADIMPLENCIA < '1900-01-01' AND CARTORIO < '1900-01-01' AND CAUCAO < '1900-01-01'
                    UNION
                    SELECT 0 ct_pagar, 0 ct_receb, sum(valor) ch_pagar, 0 ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.che_emit WHERE USADO_CT_PAGAR = 'S' AND SITUACAO = 'A'
                    UNION
                    SELECT 0 ct_pagar, 0 ct_receb, 0 ch_pagar, sum(valor) ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.cheques WHERE situacao = 'C' 
                    UNION
                    SELECT 0 ct_pagar, 0 ct_receb, 0 ch_pagar, 0 ch_receb, sum(valor_liq) cartao_pagar FROM zangraf_xkey_financeiro.cartao_mvtos WHERE COD_ANTECIPACAO = 0 and BAIXADO = 'N'
                ) T";

        return DB::connection('mysqlXKey')->select($sql);
    }

    public function Top5Pagar()
    {
        $sql = "SELECT sum(valor) valor, concat(forn.codigo, ' - ', forn.nome_fantasia) fornecedor FROM (
                    SELECT valor, fornecedor FROM zangraf_xkey_financeiro.ct_pagar WHERE data_pgto = '0000-00-00' AND AGRUP_DESTINO = 0
                    UNION
                    SELECT valor, fornecedor FROM zangraf_xkey_financeiro.che_emit WHERE USADO_CT_PAGAR = 'S' AND SITUACAO = 'A'
                ) pagar
                JOIN zangraf_xkey_publico.cad_forn forn ON forn.codigo = pagar.fornecedor
                GROUP BY fornecedor
                ORDER BY valor DESC
                LIMIT 5";
        
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function Top5Receber()
    {
        $sql = "SELECT sum(valor) valor, concat(clie.codigo, ' - ', clie.nome) cliente FROM(
                    SELECT valor, cliente FROM zangraf_xkey_financeiro.ct_receb WHERE VALOR_PAGO = 0 AND AGRUP_DESTINO = 0
                    UNION
                    SELECT valor, cliente FROM zangraf_xkey_financeiro.cheques WHERE situacao = 'C' 
                    UNION
                    SELECT valor_liq valor, cliente FROM zangraf_xkey_financeiro.cartao_mvtos WHERE COD_ANTECIPACAO = 0 and BAIXADO = 'N'
                ) receber
                JOIN zangraf_xkey_publico.cad_clie clie ON clie.codigo = receber.cliente
                GROUP BY cliente
                ORDER BY valor DESC
                LIMIT 5";
        
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function ValoresPorTipos()
    {
        $sql = "SELECT
                    sum(ct_pagar) ct_pagar,
                    sum(ct_receb) ct_receb,
                    sum(ch_pagar) ch_pagar,
                    sum(ch_receb) ch_receb,
                    sum(cartao_pagar) cartao_pagar
                FROM (
                    SELECT sum(valor) ct_pagar, 0 ct_receb, 0 ch_pagar, 0 ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.ct_pagar WHERE valor_pago = 0 AND data_pgto < '1900-01-01' AND AGRUP_DESTINO = 0
                    UNION
                    SELECT 0 ct_pagar, sum(valor) ct_receb, 0 ch_pagar, 0 ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.ct_receb WHERE DATA_PGTO < '1900-01-01' AND AGRUP_DESTINO = 0 AND INADIMPLENCIA < '1900-01-01' AND CARTORIO < '1900-01-01' AND CAUCAO < '1900-01-01'
                    UNION
                    SELECT 0 ct_pagar, 0 ct_receb, sum(valor) ch_pagar, 0 ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.che_emit WHERE USADO_CT_PAGAR = 'S' AND SITUACAO = 'A'
                    UNION
                    SELECT 0 ct_pagar, 0 ct_receb, 0 ch_pagar, sum(valor) ch_receb, 0 cartao_pagar FROM zangraf_xkey_financeiro.cheques WHERE situacao = 'C' 
                    UNION
                    SELECT 0 ct_pagar, 0 ct_receb, 0 ch_pagar, 0 ch_receb, sum(valor_liq) cartao_pagar FROM zangraf_xkey_financeiro.cartao_mvtos WHERE COD_ANTECIPACAO = 0 and BAIXADO = 'N'
                ) T";
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function PerdasPorFaturamento()
    {
        $sql = "SELECT * FROM (
                    SELECT
                        mes, 
                        sum(valor) valor,
                        sum(produzido) produzido,
                        sum(perdido) perdido,
                        sum(perdido) * 100 / (sum(perdido) + sum(produzido)) perdas_percentual
                    FROM (
                        SELECT
                            substring(orc.data_cadastro, 1, 7) mes, 
                            orc.codigo,
                            crec.valor,
                            0 produzido,
                            0 perdido
                        FROM
                            zangraf_xkey_principal.cad_orca orc
                            JOIN zangraf_xkey_principal.pedidos_nf pnf ON orc.codigo = pnf.pedido
                            JOIN zangraf_xkey_principal.cad_nf cnf ON cnf.codigo = pnf.nf
                            JOIN zangraf_xkey_financeiro.ct_receb crec ON cnf.codigo = crec.chave_mvto
                        WHERE orc.data_cadastro > '2020-01-01'
                        GROUP BY orc.codigo

                        UNION

                        SELECT
                            substring(orc.data_cadastro, 1, 7) mes, 
                            orc.codigo,
                            0 valor,
                            coalesce(sum(apd.produzido), 0) produzido,
                            coalesce(sum(apd.perdido), 0) perdido
                        FROM
                            zangraf_xkey_principal.cad_orca orc
                            JOIN zangraf_xkey_producao.producoes pdc ON pdc.cod_producao = orc.codigo
                            JOIN zangraf_xkey_producao.apontamento apt ON apt.producao = pdc.codigo
                            JOIN zangraf_xkey_producao.apontamento_detalhe apd ON apt.cod_barras = apd.lig_id_operacao
                        WHERE orc.data_cadastro > '2020-01-01'
                        GROUP BY orc.codigo
                    ) T
                    GROUP BY mes
                    ORDER BY mes DESC
                LIMIT 6) T
                ORDER BY mes";
        
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function ComprasPorFaturamento()
    {
        $sql = "SELECT * FROM (
                    SELECT mes, sum(compras) compras, sum(vendas) vendas FROM (
                        --  VENDAS  --
                        SELECT substring(DATA_EMISSAO,1,7) mes, sum(TOTAL_NF) compras, 0 vendas 
                        FROM zangraf_xkey_principal.cad_nf
                        WHERE CLI_FOR = 'C' AND CANCELADA = 'N' AND TRANSACAO NOT IN(29, 36)
                        GROUP BY substring(DATA_EMISSAO,1,7)
                        
                        UNION
                        
                        --  COMPRAS  --
                        SELECT substring(DATA_EMISSAO,1,7) mes, 0 compras, sum(TOTAL_NF) vendas 
                        FROM zangraf_xkey_principal.cad_nf
                        WHERE CLI_FOR = 'F' AND CANCELADA = 'N'
                        GROUP BY substring(DATA_EMISSAO,1,7)
                    ) T
                    GROUP BY mes
                    ORDER BY mes DESC
                    LIMIT 12) t
                ORDER BY mes";
        
        $lista = DB::connection('mysqlXKey')->select($sql);
        
        $total_compras = 0;
        $total_vendas = 0;
        foreach($lista as $item)
        {
            $total_compras += $item->compras;
            $total_vendas += $item->vendas;
        }

        $saida = [];
        foreach($lista as $item)
        {
            $saida[] = [
                'mes' => $item->mes,
                'compras' => $item->compras!=0?($item->vendas/$item->compras*100):0,
                'comp_fat' => $total_vendas/$total_compras*100
            ];
        }

        return $saida;
    }

    public function BoletosPorMes()
    {
        $sql = "SELECT tipo, valor FROM(
                    -- RECEBIDO
                    SELECT 'Recebidos' tipo, SUM(valor) valor FROM zangraf_xkey_financeiro.ct_receb
                    WHERE tipo_recebimento = 23 AND YEAR(vencimento) = YEAR(CURRENT_DATE()) AND MONTH(vencimento) = MONTH(CURRENT_DATE())
                    AND valor_pago > 0
                
                    UNION
                
                    -- VENCIDOS
                    SELECT 'Vencidos' tipo, SUM(valor) valor FROM zangraf_xkey_financeiro.ct_receb
                    WHERE tipo_recebimento = 23 AND YEAR(vencimento) = YEAR(CURRENT_DATE()) AND MONTH(vencimento) = MONTH(CURRENT_DATE()) AND WEEK(vencimento) < WEEK(CURRENT_DATE())
                    AND DAY(vencimento) < DAY(CURRENT_DATE()) AND valor_pago < valor AND cartorio = '0000-00-00'
                
                    UNION
                
                    -- A VENCER
                    SELECT 'A vencer' tipo, SUM(valor) valor FROM zangraf_xkey_financeiro.ct_receb
                    WHERE tipo_recebimento = 23 AND YEAR(vencimento) = YEAR(CURRENT_DATE()) AND MONTH(vencimento) = MONTH(CURRENT_DATE())
                    AND DAY(vencimento) >= DAY(CURRENT_DATE()) AND valor_pago < valor AND cartorio = '0000-00-00'
                ) T";
        
        return DB::connection('mysqlXKey')->select($sql);
    }

    public function BoletosAVencerPorSemana()
    {
        $sql = "SELECT SUM(valor) valor 
                FROM zangraf_xkey_financeiro.ct_receb
                WHERE 
                    YEAR(vencimento) = YEAR(CURRENT_DATE()) AND 
                    WEEK(vencimento) = WEEK(CURRENT_DATE()) AND 
                    valor_pago < valor AND 
                    tipo_recebimento = 23 AND 
                    cartorio = '0000-00-00'";
        return DB::connection('mysqlXKey')->select($sql);
    }

    ##### INDICADORES #####
    // Ranking de indicadores
    public function RankingIndicadores(Request $request)
    {
        $sql = "SELECT l.descricao indicador, count(a.id) quantidade FROM indicadores_apontamentos a
                JOIN indicadores_listas l ON a.id_indicador = l.id
                WHERE a.created_at BETWEEN :dt_inicio AND :dt_fim
                GROUP BY id_indicador";
        $result = DB::select($sql, ['dt_inicio' => $request->dt_inicio, 'dt_fim' => $request->dt_fim]);

        return $result;
    }

    // Indicadores por OP
    public function IndicadoresPorOP(Request $request)
    {
        $sql = "SELECT i.descricao indicador, count(a.id) quantidade 
                FROM indicadores_apontamentos a
                JOIN indicadores_listas i ON a.id_indicador = i.id
                JOIN reimpressao_album_laminas l ON l.id = a.id_lamina
                JOIN reimpressao_album_pedidos p ON p.id = l.id_reimpressao
                WHERE p.ordem_producao = :op
                GROUP BY id_indicador";
        $result = DB::select($sql, ['op' => $request->op]);

        return $result;
    }

    // Indicadores por produto
    public function IndicadoresPorProduto(Request $request)
    {
        $sql = "SELECT i.descricao indicador, count(a.id) quantidade 
                FROM indicadores_apontamentos a
                JOIN indicadores_listas i ON a.id_indicador = i.id
                JOIN reimpressao_album_laminas l ON l.id = a.id_lamina
                JOIN reimpressao_album_pedidos p ON p.id = l.id_reimpressao
                JOIN pedido_items pi ON pi.id_externo = p.ordem_producao
                WHERE a.created_at BETWEEN :dt_inicio AND :dt_fim AND pi.id_produto = :produto
                GROUP BY id_indicador";
        $result = DB::select($sql, ['dt_inicio' => $request->dt_inicio, 'dt_fim' => $request->dt_fim, 'produto' => $request->produto]);

        return $result;
    }

}
