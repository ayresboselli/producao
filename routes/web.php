<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/log', [App\Http\Controllers\LogController::class, 'index']);

Route::get('/ferramentas_imposicao', [App\Http\Controllers\ImposicaoController::class, 'FerramentasImposicao'])->middleware('autorizacao:imp_ferr_ver');
Route::get('/ferramenta_imposicao', [App\Http\Controllers\ImposicaoController::class, 'FerramentaImposicao'])->middleware('autorizacao:imp_ferr_edit');
Route::get('/ferramenta_imposicao/{id?}', [App\Http\Controllers\ImposicaoController::class, 'FerramentaImposicao'])->middleware('autorizacao:imp_ferr_edit');
Route::post('/ferramenta_imposicao/salvar', [App\Http\Controllers\ImposicaoController::class, 'FerramentaImposicaoSalvar'])->middleware('autorizacao:imp_ferr_edit');
Route::post('/ferramenta_imposicao/deletar', [App\Http\Controllers\ImposicaoController::class, 'FerramentaImposicaoDeletar'])->middleware('autorizacao:imp_ferr_edit');

Route::get('/modelos_imposicao', [App\Http\Controllers\ImposicaoController::class, 'ModelosImposicao'])->middleware('autorizacao:imp_mod_ver');
Route::get('/modelo_imposicao', [App\Http\Controllers\ImposicaoController::class, 'ModeloImposicao'])->middleware('autorizacao:imp_mod_edit');
Route::get('/modelo_imposicao/{id?}', [App\Http\Controllers\ImposicaoController::class, 'ModeloImposicao'])->middleware('autorizacao:imp_mod_edit');
Route::post('/modelo_imposicao/salvar', [App\Http\Controllers\ImposicaoController::class, 'ModeloImposicaoSalvar'])->middleware('autorizacao:imp_mod_edit');
Route::post('/modelo_imposicao/deletar', [App\Http\Controllers\ImposicaoController::class, 'ModeloImposicaoDeletar'])->middleware('autorizacao:imp_mod_edit');

Route::get('/hotfolders_impressao', [App\Http\Controllers\ImpressaoController::class, 'Hotfolders'])->middleware('autorizacao:imp_hotf_ver');
Route::get('/hotfolder_impressao', [App\Http\Controllers\ImpressaoController::class, 'Hotfolder'])->middleware('autorizacao:imp_hotf_edit');
Route::get('/hotfolder_impressao/{id?}', [App\Http\Controllers\ImpressaoController::class, 'Hotfolder'])->middleware('autorizacao:imp_hotf_edit');
Route::post('/hotfolder_impressao/salvar', [App\Http\Controllers\ImpressaoController::class, 'HotfolderSalvar'])->middleware('autorizacao:imp_hotf_edit');
Route::post('/hotfolder_impressao/deletar', [App\Http\Controllers\ImpressaoController::class, 'HotfolderDeletar'])->middleware('autorizacao:imp_hotf_edit');

Route::get('/substratos_impressao', [App\Http\Controllers\ImpressaoController::class, 'Substratos'])->middleware('autorizacao:imp_substr_ver');
Route::get('/substrato_impressao', [App\Http\Controllers\ImpressaoController::class, 'Substrato'])->middleware('autorizacao:imp_substr_edit');
Route::get('/substrato_impressao/{id?}', [App\Http\Controllers\ImpressaoController::class, 'Substrato'])->middleware('autorizacao:imp_substr_edit');
Route::post('/substrato_impressao/salvar', [App\Http\Controllers\ImpressaoController::class, 'SubstratoSalvar'])->middleware('autorizacao:imp_substr_edit');
Route::post('/substrato_impressao/deletar', [App\Http\Controllers\ImpressaoController::class, 'SubstratoDeletar'])->middleware('autorizacao:imp_substr_edit');

Route::get('/produtos', [App\Http\Controllers\ProdutoController::class, 'Produtos'])->middleware('autorizacao:produto_ver');
Route::get('/produto', [App\Http\Controllers\ProdutoController::class, 'Produto'])->middleware('autorizacao:produto_edit');
Route::get('/produto/{id?}', [App\Http\Controllers\ProdutoController::class, 'Produto'])->middleware('autorizacao:produto_edit');
Route::post('/produto/salvar', [App\Http\Controllers\ProdutoController::class, 'ProdutoSalvar'])->middleware('autorizacao:produto_edit');
Route::post('/produto/deletar', [App\Http\Controllers\ProdutoController::class, 'ProdutoDeletar'])->middleware('autorizacao:produto_edit');
Route::get('/duplicar_produto/{id}', [App\Http\Controllers\ProdutoController::class, 'Duplicar'])->middleware('autorizacao:produto_edit');

Route::get('/clientes', [App\Http\Controllers\ClienteController::class, 'Clientes'])->middleware('autorizacao:cliente_ver');
Route::get('/cliente', [App\Http\Controllers\ClienteController::class, 'Cliente'])->middleware('autorizacao:cliente_edit');
Route::get('/cliente/{id?}', [App\Http\Controllers\ClienteController::class, 'Cliente'])->middleware('autorizacao:cliente_edit');
Route::post('/cliente/salvar', [App\Http\Controllers\ClienteController::class, 'Salvar'])->middleware('autorizacao:cliente_edit');
Route::post('/novo_cliente', [App\Http\Controllers\ClienteController::class, 'NovoCliente'])->middleware('autorizacao:cliente_edit');
Route::post('/usuario_ftp', [App\Http\Controllers\ClienteController::class, 'UsuarioFTP'])->middleware('autorizacao:cliente_edit');

Route::get('/servicos', [App\Http\Controllers\ServicoController::class, 'Servicos'])->middleware('autorizacao:servico_ver');
Route::get('/servico', [App\Http\Controllers\ServicoController::class, 'Servico'])->middleware('autorizacao:servico_edit');
Route::get('/servico/{id?}', [App\Http\Controllers\ServicoController::class, 'Servico'])->middleware('autorizacao:servico_edit');
Route::post('/servico/salvar', [App\Http\Controllers\ServicoController::class, 'ServicoSalvar'])->middleware('autorizacao:servico_edit');
Route::post('/servico/deletar', [App\Http\Controllers\ServicoController::class, 'ServicoDeletar'])->middleware('autorizacao:servico_edit');

Route::get('/tab_precos', [App\Http\Controllers\TabPrecosController::class, 'TabPrecos'])->middleware('autorizacao:tab_preco_ver');
Route::get('/tab_preco/{id?}', [App\Http\Controllers\TabPrecosController::class, 'TabPreco'])->middleware('autorizacao:tab_preco_edit');
Route::post('/tab_preco/salvar', [App\Http\Controllers\TabPrecosController::class, 'TabPrecoSalvar'])->middleware('autorizacao:tab_preco_edit');
Route::post('/tab_preco/deletar', [App\Http\Controllers\TabPrecosController::class, 'TabPrecoDeletar'])->middleware('autorizacao:tab_preco_edit');
Route::get('/tab_precos/csv', [App\Http\Controllers\TabPrecosController::class, 'ImportarCSV'])->middleware('autorizacao:tab_preco_edit');

Route::get('/tab_preco_produtos/{id}', [App\Http\Controllers\TabPrecosController::class, 'TabPrecoProdutos'])->middleware('autorizacao:tab_preco_ver');
Route::get('/tab_preco_produto/{id}/{id_produto?}', [App\Http\Controllers\TabPrecosController::class, 'TabPrecoProduto'])->middleware('autorizacao:tab_preco_ver');
Route::post('/tab_preco_produto/salvar', [App\Http\Controllers\TabPrecosController::class, 'TabPrecoProdutoSalvar'])->middleware('autorizacao:tab_preco_edit');
Route::post('/tab_preco_produto/deletar', [App\Http\Controllers\TabPrecosController::class, 'TabPrecoProdutoDeletar'])->middleware('autorizacao:tab_preco_edit');

Route::get('/uploads/{id_produto}', [App\Http\Controllers\UploadController::class, 'Index'])->middleware('autorizacao:upload_ver');
Route::get('/upload/{id_produto}/{id?}', [App\Http\Controllers\UploadController::class, 'Editar'])->middleware('autorizacao:upload_edit');
Route::post('/upload/salvar', [App\Http\Controllers\UploadController::class, 'Salvar'])->middleware('autorizacao:upload_edit');
Route::post('/upload/deletar', [App\Http\Controllers\UploadController::class, 'Deletar'])->middleware('autorizacao:upload_edit');

Route::get('/pedidos', [App\Http\Controllers\PedidoController::class, 'Index'])->middleware('autorizacao:pedido_ver');
Route::post('/novo_pedido', [App\Http\Controllers\PedidoController::class, 'NovoPedido'])->middleware('autorizacao:pedido_edit');
Route::get('/pedido/{id?}', [App\Http\Controllers\PedidoController::class, 'Editar'])->middleware('autorizacao:pedido_edit');
Route::post('/pedido/alt_copias', [App\Http\Controllers\PedidoController::class, 'AltCopias'])->middleware('autorizacao:pedido_edit');
Route::post('/pedido/reset', [App\Http\Controllers\PedidoController::class, 'Reset'])->middleware('autorizacao:pedido_edit');
Route::post('/pedido/imprimir', [App\Http\Controllers\PedidoController::class, 'Imprimir'])->middleware('autorizacao:pedido_edit');
Route::post('/pedido/reenviar', [App\Http\Controllers\PedidoController::class, 'Reenviar'])->middleware('autorizacao:pedido_edit');
Route::post('/pedido/deletar', [App\Http\Controllers\PedidoController::class, 'Deletar'])->middleware('autorizacao:pedido_edit');
Route::post('/pedido/reset_album', [App\Http\Controllers\PedidoController::class, 'ResetAlbum'])->middleware('autorizacao:pedido_edit');

Route::get('/arquivos/{id_item}/{id_album?}', [App\Http\Controllers\PedidoController::class, 'Arquivos'])->middleware('autorizacao:pedido_edit');
Route::post('/arquivos', [App\Http\Controllers\PedidoController::class, 'ArquivosPost'])->middleware('autorizacao:pedido_edit');

Route::get('/pedidos_itens', [App\Http\Controllers\PedidoItemController::class, 'Index'])->middleware('autorizacao:pedido_ver');

Route::get('/recortes', [App\Http\Controllers\RecorteController::class, 'Index'])->middleware('autorizacao:recorte_ver');
Route::get('/recorte/{id}', [App\Http\Controllers\RecorteController::class, 'Recorte'])->middleware('autorizacao:recorte_edit');
Route::post('/recorte/salvar', [App\Http\Controllers\RecorteController::class, 'Salvar'])->middleware('autorizacao:recorte_edit');

// Guias de OPs
Route::get('/guiasOP', [App\Http\Controllers\GuiaOPController::class, 'Index'])->middleware('autorizacao:guiasOP_ver');
Route::get('/guiaOP/{id}', [App\Http\Controllers\GuiaOPController::class, 'Guias'])->middleware('autorizacao:guiasOP_edit');
Route::get('/guiaOP/imprimir/{id}', [App\Http\Controllers\GuiaOPController::class, 'Imprimir'])->middleware('autorizacao:guiasOP_edit');

// Reimpress??o
Route::get('/reimpressoes_album', [App\Http\Controllers\ReimpressaoAlbumController::class, 'Index'])->middleware('autorizacao:reimp_ver');
Route::get('/reimpressao_album/{id?}', [App\Http\Controllers\ReimpressaoAlbumController::class, 'Editar'])->middleware('autorizacao:reimp_edit');
Route::post('/reimpressao_album/salvar', [App\Http\Controllers\ReimpressaoAlbumController::class, 'Salvar'])->middleware('autorizacao:reimp_edit');
Route::post('/reimpressoes_album_deletar', [App\Http\Controllers\ReimpressaoAlbumController::class, 'DeletarReimpressao'])->middleware('autorizacao:reimp_edit');
Route::post('/reimpressoes_album_reimprimir', [App\Http\Controllers\ReimpressaoAlbumController::class, 'Reimprimir'])->middleware('autorizacao:reimp_edit');
Route::post('/reimpressao_album_deletar', [App\Http\Controllers\ReimpressaoAlbumController::class, 'DeletarLamina'])->middleware('autorizacao:reimp_edit');

// Gest??o de Sistemas Legados
Route::get('/gsl', [App\Http\Controllers\GSLController::class, 'Index'])->middleware('autorizacao:gsl_ver');
Route::post('/gsl/filtrar', [App\Http\Controllers\GSLController::class, 'Filtrar']);
Route::post('/gsl/reprocessar', [App\Http\Controllers\GSLController::class, 'Reprocessar']);
Route::post('/gsl/configuracoes', [App\Http\Controllers\GSLController::class, 'Configuracoes']);

// PrintOne
Route::get('/printone', [App\Http\Controllers\PrintOneController::class, 'Index'])->middleware('autorizacao:integ_ver');
Route::get('/printone_produto/{id?}', [App\Http\Controllers\PrintOneController::class, 'Editar'])->middleware('autorizacao:integ_edit');
Route::post('/printone_produto/salvar', [App\Http\Controllers\PrintOneController::class, 'Salvar'])->middleware('autorizacao:integ_edit');
Route::get('/printone_duplicar_produto/{id}', [App\Http\Controllers\PrintOneController::class, 'Duplicar'])->middleware('autorizacao:integ_edit');
Route::post('/printone/deletar', [App\Http\Controllers\PrintOneController::class, 'Deletar'])->middleware('autorizacao:integ_edit');

Route::get('/printone_clientes', [App\Http\Controllers\PrintOneController::class, 'Clientes'])->middleware('autorizacao:integ_ver');
Route::get('/printone_cliente/{id?}', [App\Http\Controllers\PrintOneController::class, 'EditarCliente'])->middleware('autorizacao:integ_edit');
Route::post('/printone_cliente/salvar', [App\Http\Controllers\PrintOneController::class, 'SalvarCliente'])->middleware('autorizacao:integ_edit');
Route::post('/printone_cliente/deletar', [App\Http\Controllers\PrintOneController::class, 'DeletarCliente'])->middleware('autorizacao:integ_edit');

Route::get('/printone_itens/{id_produto}', [App\Http\Controllers\PrintOneController::class, 'Itens'])->middleware('autorizacao:integ_edit');
Route::get('/printone_item/{id_produto}/{id?}', [App\Http\Controllers\PrintOneController::class, 'EditarItem'])->middleware('autorizacao:integ_edit');
Route::post('/printone_item/salvar', [App\Http\Controllers\PrintOneController::class, 'SalvarItem'])->middleware('autorizacao:integ_edit');
Route::post('/printone_item/deletar', [App\Http\Controllers\PrintOneController::class, 'DeletarItem'])->middleware('autorizacao:integ_edit');

// Arquivamento
Route::get('/arquivamento', [App\Http\Controllers\ArquivamentoController::class, 'Arquivamento'])->middleware('autorizacao:arquiv_edit');
Route::post('/arquivamento', [App\Http\Controllers\ArquivamentoController::class, 'Salvar'])->middleware('autorizacao:arquiv_edit');

Route::get('/rel_margem_precos', [App\Http\Controllers\RelatorioController::class, 'MargemPrecoProduto'])->middleware('autorizacao:rel_mg_precos');


Route::get('/perfis', [App\Http\Controllers\PerfilController::class, 'Index']);//->middleware('autorizacao:perfis_ver');
Route::get('/perfil/{id?}', [App\Http\Controllers\PerfilController::class, 'Editar']);//->middleware('autorizacao:perfis_edit');
Route::post('/perfil/salvar', [App\Http\Controllers\PerfilController::class, 'Salvar']);//->middleware('autorizacao:perfis_edit');
Route::post('/perfil/deletar', [App\Http\Controllers\PerfilController::class, 'Deletar']);//->middleware('autorizacao:perfis_edit');

Route::get('/usuarios', [App\Http\Controllers\UsuarioController::class, 'Index'])->middleware('autorizacao:usuarios_ver');
Route::get('/usuario/{id?}', [App\Http\Controllers\UsuarioController::class, 'Editar'])->middleware('autorizacao:usuarios_edit');
Route::post('/usuario/salvar', [App\Http\Controllers\UsuarioController::class, 'Salvar'])->middleware('autorizacao:usuarios_edit');
Route::get('/usuario_alt_senha/{id}', [App\Http\Controllers\UsuarioController::class, 'AltSenha'])->middleware('autorizacao:alt_senhas');
Route::post('/usuario_alt_senha/salvar', [App\Http\Controllers\UsuarioController::class, 'AltSenhaSalvar'])->middleware('autorizacao:alt_senhas');

// Servi??os de automa????o
Route::get('/servico_automacao/servico/{servico}', [App\Http\Controllers\ServicosAutomacaoController::class, 'Servico'])->middleware('autorizacao:status_servicos');




#### API ####
Route::post('/pedido_item_upload', [App\Http\Controllers\PedidoController::class, 'ItemUpload']);
Route::post('/upload', [App\Http\Controllers\PedidoController::class, 'Upload']);
Route::post('/mapa_arquivos', [App\Http\Controllers\PedidoController::class, 'MapaArquivos']);
Route::post('/salva_item_url', [App\Http\Controllers\PedidoController::class, 'SalvaURL']);
Route::post('/add_servico', [App\Http\Controllers\PedidoController::class, 'AddServico']);
Route::post('/usuario_ftp', [App\Http\Controllers\ClienteController::class, 'UsuarioFTP']);

Route::post('/statusservicos', [App\Http\Controllers\HomeController::class, 'StatusServicos']);
Route::post('/statusservicosresart', [App\Http\Controllers\HomeController::class, 'StatusServicosRestart']);
Route::post('/producaomensal', [App\Http\Controllers\HomeController::class, 'ProducaoMensal']);
Route::post('/perdamensal', [App\Http\Controllers\HomeController::class, 'PerdaMensal']);
Route::post('/opporcelula', [App\Http\Controllers\HomeController::class, 'OpPorCelula']);
Route::post('/tempoporcelula', [App\Http\Controllers\HomeController::class, 'TempoPorCelula']);
Route::post('/ProdutosMaisVendidos', [App\Http\Controllers\HomeController::class, 'ProdutosMaisVendidos']);
Route::post('/ProdutosMaisProduzidos', [App\Http\Controllers\HomeController::class, 'ProdutosMaisProduzidos']);
Route::post('/AlbunsMensais', [App\Http\Controllers\HomeController::class, 'AlbunsMensais']);
Route::post('/ImpressaoMensal', [App\Http\Controllers\HomeController::class, 'ImpressaoMensal']);
Route::post('/SituacaoOPsMensais', [App\Http\Controllers\HomeController::class, 'SituacaoOPsMensais']);
Route::post('/IndiceLiquidez', [App\Http\Controllers\HomeController::class, 'IndiceLiquidez']);
Route::post('/MediaPrazosRecebimento', [App\Http\Controllers\HomeController::class, 'MediaPrazosRecebimento']);
Route::post('/TotalPagarReceber', [App\Http\Controllers\HomeController::class, 'TotalPagarReceber']);
Route::post('/Top5Pagar', [App\Http\Controllers\HomeController::class, 'Top5Pagar']);
Route::post('/Top5Receber', [App\Http\Controllers\HomeController::class, 'Top5Receber']);
Route::post('/ValoresPorTipos', [App\Http\Controllers\HomeController::class, 'ValoresPorTipos']);
Route::post('/PerdasPorFaturamento', [App\Http\Controllers\HomeController::class, 'PerdasPorFaturamento']);
Route::post('/ComprasPorFaturamento', [App\Http\Controllers\HomeController::class, 'ComprasPorFaturamento']);
Route::post('/BoletosPorMes', [App\Http\Controllers\HomeController::class, 'BoletosPorMes']);
Route::post('/BoletosAVencerPorSemana', [App\Http\Controllers\HomeController::class, 'BoletosAVencerPorSemana']);
Route::post('/RankingIndicadores', [App\Http\Controllers\HomeController::class, 'RankingIndicadores']);
Route::post('/IndicadoresPorProduto', [App\Http\Controllers\HomeController::class, 'IndicadoresPorProduto']);
Route::post('/IndicadoresPorOP', [App\Http\Controllers\HomeController::class, 'IndicadoresPorOP']);

Route::get('/clientes_ftp', [App\Http\Controllers\ClientesFTPController::class, 'Clientes']);