<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PedidoAlbum;
use App\Models\PedidoItemArquivo;
use App\Models\Recorte;
use App\Models\Servico;
use App\Models\PedidoItemServico;
use App\Models\User;

class PedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function Index()
	{
		$pedidos = Pedido::get();
		$situacao = [];
		$usuario = [];
		$mapeamento = [];
		$lista_pedidos = [];
		$qtd_itens = [];

		foreach($pedidos as $pedido)
		{
			if(is_null($pedido->data_fechamento))
			{
				[$itens, $sit_item] = $this->ListaItens($pedido->id);

				$situacao[$pedido->id] = null;
				foreach($sit_item as $sitem)
				{
					if($situacao[$pedido->id] == null){
						$situacao[$pedido->id] = $sitem;
					}else{
						if($situacao[$pedido->id] > $sitem){
							$situacao[$pedido->id] = $sitem;
						}
					}
				}

				$qtd_itens[$pedido->id] = 0;
				$mapeamento[$pedido->id] = true;
				foreach($itens as $item)
				{
					$albuns = PedidoAlbum::where('id_item','=',$item->id)->get();
					$qtd_itens[$pedido->id] += count($albuns);

					if(is_null($item->id_produto))
						$mapeamento[$pedido->id] = false;
				}

				$lista_pedidos[] = $pedido;
			}

			$user = User::find($pedido->id_usuario);
			if(!is_null($user))
				$usuario[$pedido->id] = $user->name;
			else
				$usuario[$pedido->id] = '';
		}
		
		return view('pedidos',['pedidos' => $lista_pedidos, 'situacao' => $situacao, 'mapeamento' => $mapeamento, 'qtd_itens' => $qtd_itens, 'usuario' => $usuario]);
	}
	
	public function NovoPedido(Request $request)
	{
		$pedido = DB::table('pedidos')->select('*')->where('id_externo','=',$request->ordemServico)->get();
		if(count($pedido) == 0)
		{
			$sql = "
				SELECT
					o.codigo id_externo,
					o.tipo_pedido tipo_contrato,
					o.cliente id_cliente,
					c.apelido cliente,
					o.contato contrato,
					o.data_aprov data_entrada,
					o.previsao_entrega,
					o.observacoes
				FROM 
					zangraf_xkey_principal.cad_orca o
					JOIN zangraf_xkey_publico.cad_clie c ON c.codigo = o.cliente
				WHERE o.codigo = :os";
			$pedido = DB::connection('mysqlXKey')->select($sql,['os' => $request->ordemServico]);
			
			if(count($pedido) > 0)
			{
				
				if($pedido[0]->tipo_contrato == 1)
					$endereco = "Producao: /[".$pedido[0]->id_cliente."]".$pedido[0]->cliente."/[".$pedido[0]->id_cliente."][".$pedido[0]->id_externo."]".$pedido[0]->contrato."/";
				else
					$endereco = "Pedido: /[".$pedido[0]->id_cliente."]".$pedido[0]->cliente."/".$pedido[0]->id_externo."/";
				
				$observacoes = $endereco."\n\n".$pedido[0]->observacoes;

				$result = DB::connection('mysqlXKey')->
					table('zangraf_xkey_principal.cad_orca')->
					where('codigo','=',$request->ordemServico)->
					update(['observacoes' => $observacoes]);
				
				if($result){
					if($pedido[0]->data_entrada == '0000-00-00')
						$data_entrada = date('Y-m-d');
					else
						$data_entrada = $pedido[0]->data_entrada;
						
					$pedido = Pedido::create([
						'id_externo' => $pedido[0]->id_externo,
						'id_usuario' => Auth()->user()->id,
						'tipo_contrato' => $pedido[0]->tipo_contrato,
						'id_cliente' => $pedido[0]->id_cliente,
						'cliente' => $pedido[0]->cliente,
						'contrato' => trim($pedido[0]->contrato),
						'data_entrada' => $data_entrada,
						'previsao_entrega' => $pedido[0]->previsao_entrega,
						'deletar_origem' => false
					]);
					
				}else{
					$value = $request->session()->flash('erro', 'Não consegui criar o endereço dos arquivos');
					return redirect('pedidos')->with($value);
				}
			}
			else
			{
				$value = $request->session()->flash('erro', 'Não encontrei a ordem de serviço');
				return redirect('pedidos')->with($value);
			}
		}
		else
		{
			$pedido = $pedido[0];
		}
		

		$sql = "SELECT p.codigo id_externo, o.produto, coalesce(p.quantidade, o.quantidade) quantidade
		FROM zangraf_xkey_principal.pro_orca o
		LEFT JOIN zangraf_xkey_producao.producoes p ON p.cod_producao = o.orcamento AND o.produto = p.produto
		WHERE o.orcamento = :os";
		$itensSax = DB::connection('mysqlXKey')->select($sql,['os' => $request->ordemServico]);

		$itens = [];
		if(count($itensSax) > 0)
		{
			foreach($itensSax as $i)
			{
				$id_produto = null;
				$result = DB::table('produtos')->select('id')->where('id_externo','=',$i->produto)->get();
				
				if(count($result) > 0)
					$id_produto = $result[0]->id;
				
				$sql = "SELECT * FROM pedido_items WHERE id_pedido = :id_pedido AND id_produto_externo = :id_prod_ext";
				$old_item = DB::select($sql, ['id_pedido' => $pedido->id, 'id_prod_ext' => $i->produto]);

				if(count($old_item) == 0)
				{
					/*$itens[] = */PedidoItem::create([
						'id_pedido' => $pedido->id,
						'id_externo' => $i->id_externo!=null?$i->id_externo:null,
						'id_produto' => $id_produto,
						'id_produto_externo' => $i->produto,
						'quantidade' => $i->quantidade
					]);
				}
			}
		}


		[$itens, $situacao] = $this->ListaItens($pedido->id);

		$listaServicos = Servico::get();
		//return view('pedido', ['pedido' => $pedido, 'itens' => $itens, 'situacao' => $situacao, 'servicos' => []]);
		return view(
			'pedido', 
			[
				'pedido' => $pedido, 
				'itens' => $itens, 
				'situacao' => $situacao, 
				'servicos' => [], 
				'listaServicos' => $listaServicos
			]
		);
	}
	
	public function Editar($id)
	{
		$pedido = Pedido::find($id);
		if(!is_null($pedido)){
			[$itens, $situacao] = $this->ListaItens($pedido->id);
			
			//$itens = DB::table('pedido_items')->select('*')->where('id_pedido','=',$pedido->id)->get();
			foreach($itens as $item)
			{
				if(is_null($item->id_produto))
				{
					$result = DB::table('produtos')->select('id')->where('id_externo','=',$item->id_produto_externo)->get();
					if(count($result) > 0)
					{
						$pedidoItem = PedidoItem::find($item->id);
						$pedidoItem->update(['id_produto' => $result[0]->id]);
					}
				}
			}
			
			# Serviços
			$sql = "SELECT  
					i.id,
					i.id_externo,
					i.url_origem,
					i.id_servico,
					i.imprimir,
					i.data_envio_impressao,
					i.arquivos,
					s.titulo servico
				FROM 
					pedido_item_servicos i
					LEFT JOIN servicos s ON s.id = i.id_servico
				WHERE 
					i.id_pedido = :id_pedido
				GROUP BY i.id";
			$servicos = DB::select($sql, ['id_pedido' => $pedido->id]);

			$listaServicos = Servico::get();

			return view(
				'pedido', 
				[
					'pedido' => $pedido, 
					'itens' => $itens, 
					'situacao' => $situacao, 
					'servicos' => $servicos, 
					'listaServicos' => $listaServicos
				]
			);
		}
		
		return redirect('pedidos');
	}
	
	private function ListaItens($id_pedido)
	{
		$sql = "SELECT  
					i.id,
					i.id_produto_externo,
					i.id_externo,
					i.url_origem,
					i.id_produto,
					i.copias,
					i.tentativa_importar,
					i.data_importacao,
					i.imprimir,
					i.data_envio_impressao,
					concat(p.id_externo, ' - ', p.titulo) produto,
					'' albuns,
					count(a.id) arquivos,
                    count(r.id) recortes,
					sum(a.situacao) situacao
				FROM 
					pedido_items i
					LEFT JOIN produtos p ON p.id = i.id_produto
					LEFT JOIN pedido_item_arquivos a ON i.id = a.id_item
                    LEFT JOIN recortes r ON a.id = r.id_arquivo 
						AND a.situacao = 0 AND 
						r.crop_pos_x IS NULL AND 
						r.crop_pos_y IS NULL AND 
						r.crop_largura IS NULL AND 
						r.crop_altura IS NULL
				WHERE 
					i.id_pedido = :id_pedido
				GROUP BY i.id";
		$itens = DB::select($sql, ['id_pedido' => $id_pedido]);

		for($i = 0; $i < count($itens); $i++){
			DB::select("SELECT count(*) albuns FROM pedido_albums WHERE id_item = :id_item", ['id_item' => $itens[$i]->id]);
		}

		$situacao = [];
		foreach($itens as $item){
			if($item->arquivos == 0)
			{
				if(is_null($item->url_origem)){
					$situacao[$item->id] = 0;//"Aguardando envio de arquivos";
				}else{
					$situacao[$item->id] = 1;//"Aguardando processamento de arquivos";
				}
			}
			else
			{
				if($item->arquivos != $item->situacao || is_null($item->data_importacao)){
					$situacao[$item->id] = 1;//"Aguardando processamento de arquivos";
				}else{
					if(is_null($item->id_externo)){
						$situacao[$item->id] = 2;//"Aguardando a criação de O.P.";
					}else{
						if(is_null($item->data_envio_impressao)){
							if(!$item->imprimir)
								$situacao[$item->id] = 3;//"Pronto para impressão";
							else
								$situacao[$item->id] = 4;//"Exportando";
						}else{
							$situacao[$item->id] = 5;//"Em produção";
						}
					}
				}
			}
		}
		
		return [$itens, $situacao];
	}

	public function Upload(Request $request)
	{
		// https://blog.especializati.com.br/upload-de-arquivos-no-laravel-com-request/
		
		if($request->tipo == 'S')
		{
			if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid())
			{
				try{
					$dir = '/sistema/servicos/'.$request->pedido.'/'.$request->item;
					$this->MakeDirectory($dir);

					$imagem = imagecreatefromjpeg($request->arquivo);
					imagejpeg($imagem, $dir.'/'.$request->arquivo->getClientOriginalName(), 100);
				}catch(Exception $e){
					return ['success' => false, 'msg' => $e->getMessage()];
				}

				return ['success' => true];
			}else{
				$msg = "Falha no upload";
			}
		}
		else
		{
			//if (!$request->session()->exists('renomear') || $request->session()->get('renomear') != $request->item)
			//{
				//$request->session()->put('renomear', $request->item);
				$pedido_item = PedidoItem::find($request->item);
				if(!is_null($pedido_item)){
					$pedido_item->update(['data_importacao' => date('Y-m-d H:i:s'),'renomear' => $request->renomear]);
				}
			//}

			$sql = "
				SELECT p.* 
				FROM pedido_items i
				JOIN produtos p ON p.id = i.id_produto
				WHERE i.id = :id_item";
			$produto = DB::select($sql, ['id_item' => $request->item]);
			
			if(count($produto) > 0)
			{
				//$dpi = 11.81102362204724;
				$dpi = 12;
				$produto = $produto[0];
				
				if($produto->sem_dimensao == 0)
				{
					// Lê dimensões do produto
					$prod_largura = ($produto->sangr_esq + $produto->largura + $produto->sangr_dir) * $dpi;
					$prod_altura = ($produto->sangr_sup + $produto->altura + $produto->sangr_inf) * $dpi;
				}
				
				// cria nome do arquivo
				if(!is_null($request->filename))
				{
					$fn = explode('/',$request->filename);
					if(count($fn) > 2){
						$filename = $fn[count($fn)-2].'/'.$fn[count($fn)-1];
					}else{
						$filename = $request->filename;
					}
				}
				else
				{
					$filename = $request->album.'/'.$request->arquivo->getClientOriginalName();
				}
				
				
				$url_imagem = $request->pedido.'/'.$request->item.'/'.$filename;
				
				// Busca se o arquivo existe
				$id = DB::select(
					'SELECT id FROM pedido_item_arquivos WHERE id_item = :id_item AND url_imagem = :url_imagem',
					['id_item' => $request->item, 'url_imagem' => $url_imagem]
				);
				
				$arquivo = null;
				if(count($id) > 0)
					$arquivo = PedidoItemArquivo::find($id[0]->id);
				
				if(is_null($arquivo))
				{
					// verifica se o arquivo foi enviado
					if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid())
					{
						$recorte = false;
						if($produto->sem_dimensao == 0)
						{
							try{
								// carrega a imagem
								list($largura, $altura) = getimagesize($request->arquivo);
								$imagem = imagecreatefromjpeg($request->arquivo);
								
								// verifica se precisa rotacionar
								$rotacionar = false;
								if(($prod_largura > $prod_altura && $largura < $altura) || ($prod_largura < $prod_altura && $largura > $altura))
								{
									// rotaciona a imagem
									$imagem = imagerotate($imagem, 90, 0);
									$tmp = $largura;
									$largura = $altura;
									$altura = $tmp;
								}
								
								
								// redimensiona a imagem
								if($prod_largura != $largura || $prod_altura != $altura)
								{
									// precisa redimencionar
									$margem = 0.1;
									if($largura > $altura)
									{
										$prop_produto = $prod_altura / $prod_largura;
										$prop_imagem = $altura / $largura;
									}
									else
									{
										$prop_produto = $prod_largura / $prod_altura;
										$prop_imagem = $largura / $altura;
									}
									
									if($prop_produto > $prop_imagem-($prop_imagem*$margem) && $prop_produto < $prop_imagem+($prop_imagem*$margem))
									{
										// redimenciona
										$imagem_p = imagecreatetruecolor($prod_largura, $prod_altura);
										imagecopyresampled($imagem_p, $imagem, 0, 0, 0, 0, $prod_largura, $prod_altura, $largura, $altura);
										$imagem = $imagem_p;
									}
									else
									{
										// cria info de recorte
										$recorte = true;
									}
									
								}
							}catch(Exception $e){
								return ['success' => false, 'msg' => 'Erro no processamento de imagem: '.$e->getMessage()];
							}
						}else{
							// carrega a imagem
							list($largura, $altura) = getimagesize($request->arquivo);
							$imagem = imagecreatefromjpeg($request->arquivo);

							$prod_largura = $largura; 
							$prod_altura = $altura;
						}

						// Cria estrutura
						$dir_fotos = '/sistema/fotos/'.$request->pedido.'/'.$request->item;
						$dir_thumbs = '/sistema/thumbs/'.$request->pedido.'/'.$request->item;
						
						try{
							$fn = explode('/',$filename);
							if(count($fn) > 1)
							{
								$codigo = $fn[count($fn)-2];
								//Storage::makeDirectory('public/fotos/'.$request->pedido.'/'.$request->item.'/'.$codigo);
								//Storage::makeDirectory('public/thumbs/'.$request->pedido.'/'.$request->item.'/'.$codigo);

								$this->MakeDirectory($dir_fotos.'/'.$codigo);
								$this->MakeDirectory($dir_thumbs.'/'.$codigo);
							}
							else
							{
								$codigo = '001';
								//Storage::makeDirectory('public/fotos/'.$request->pedido.'/'.$request->item);
								//Storage::makeDirectory('public/thumbs/'.$request->pedido.'/'.$request->item);

								$this->MakeDirectory($dir_fotos);
								$this->MakeDirectory($dir_thumbs);
							}
						}catch(Exception $e){
							return ['success' => false, 'msg' => 'Erro na criação de pastas: '.$e->getMessage()];
						}
						

						// criar álbum
						$id = DB::select(
							'SELECT id FROM pedido_albums WHERE id_item = :id_item AND codigo = :codigo',
							['id_item' => $request->item, 'codigo' => $codigo]
						);
						
						if(count($id) > 0)
						{
							$album = PedidoAlbum::find($id[0]->id);
						}
						else
						{
							$album = PedidoAlbum::create(['id_pedido' => $request->pedido, 'id_item' => $request->item, 'codigo' => $codigo]);
						}
						

						try
						{
							// thumbnails
							$image_thumb = imagecreatetruecolor($prod_largura*0.1, $prod_altura*0.1);
							imagecopyresampled($image_thumb, $imagem, 0, 0, 0, 0, $prod_largura*0.1, $prod_altura*0.1, $prod_largura, $prod_altura);
							imagejpeg($image_thumb, $dir_thumbs.'/'.$filename, 100);
							
							//Salva a imagem
							if(imagejpeg($imagem, $dir_fotos.'/'.$filename, 100))
							{
								// Salva arquivo
								$dados = [
									'id_item' => $request->item,
									'id_album' => $album->id,
									'url_imagem' => $url_imagem,
									'nome_arquivo' => $request->arquivo->getClientOriginalName(),
									'largura' => $largura,
									'altura' => $altura,
									'situacao' => $recorte ? 0 : 1
								];
								
								$arquivo = PedidoItemArquivo::create($dados);
								
								
								// Salva info de recorte
								if($recorte){
									// https://fengyuanchen.github.io/cropperjs/
									Recorte::create(['id_arquivo' => $arquivo->id]);
								}
								
								return ['success' => true];
							}
							
							$msg = 'Erro ao salvar a imagem';
							imagedestroy($imagem);
						}catch(Exception $e){
							return ['success' => false, 'msg' => 'Erro ao salvar a imagem: '.$e->getMessage()];
						}
					}
					else
					{
						$msg = 'Houve algum problema no upload';
					}
				}
				else
				{
					return ['success' => true];
				}
			}
			else
			{
				$msg = 'Não identifiquei o produto';
			}
		}

		return ['success' => false, 'msg' => $msg];
	}
	
	public function MapaArquivos(Request $request)
	{
		$reservados = ['.','..','PrintOne','Sistema'];
		if(!is_null($request->dir) && in_array(explode('/', $request->dir)[1], ['arquivos','brutos','ftp']))
		{
			$dir = $request->dir;
			$reservados = ['.','..'];
		}
		
		$lista = [];
		$directories = scandir($dir);
		
		foreach($directories as $diretorio)
		{
			if(is_dir($dir.'/'.$diretorio) && !in_array($diretorio, $reservados))
			{
				$sub = $this->MapaArquivosCount($dir.'/'.$diretorio);
				$lista[] = ['dir' => $diretorio, 'sub' => $sub];
			}
		}
		
		return $lista;
	}
	
	public function AddServico(Request $request)
	{
		$pedido = Pedido::find($request->id_pedido);
		$servico = Servico::find($request->id_servico);
		if(!is_null($pedido))
		{
			PedidoItemServico::create([
				'id_pedido' => $pedido->id,
				'id_servico' => $servico->id
			]);
			return ['success' => true];
		}
		else
		{
			return ['success' => false];
		}
	}
	
	public function SalvaURL(Request $request)
	{
		if($request->tipo == 'S')
			$item = PedidoItemServico::find($request->id_item);
		else
			$item = PedidoItem::find($request->id_item);
		
		if(!is_null($item))
		{
			$item->update(['url_origem' => $request->url]);
			return ['success' => true];
		}
		else
		{
			return ['success' => false];
		}
	}
	
	public function Reset(Request $request)
	{
		if($request->tipo == 'P')
		{
			$item = PedidoItem::find($request->id);
			if(!is_null($item))
			{
				try
				{
					//DB::transaction(function () {
						// deleta aruivos
						$arquivos = DB::table('pedido_item_arquivos')->select('*')->where('id_item','=',$item->id)->get();
						foreach($arquivos as $arquivo)
						{
							// deleta arquivos
							if(file_exists('/sistema/fotos/'.$arquivo->url_imagem))
								unlink('/sistema/fotos/'.$arquivo->url_imagem);
							if(file_exists('/sistema/thumbs/'.$arquivo->url_imagem))
								unlink('/sistema/thumbs/'.$arquivo->url_imagem);
							
							// deleta registros de recortes
							DB::table('recortes')->where('id_arquivo','=',$arquivo->id)->delete();
						}
						DB::table('pedido_item_arquivos')->where('id_item','=',$item->id)->delete();
						

						// deleta álbuns
						$albuns = DB::table('pedido_albums')->select('*')->where('id_item','=',$item->id)->get();
						foreach($albuns as $album)
						{
							// deleta pastas
							if(is_dir('/sistema/fotos/'.$item->id_pedido.'/'.$item->id.'/'.$album->codigo))
								rmdir('/sistema/fotos/'.$item->id_pedido.'/'.$item->id.'/'.$album->codigo);
							if(is_dir('/sistema/thumbs/'.$item->id_pedido.'/'.$item->id.'/'.$album->codigo))
								rmdir('/sistema/thumbs/'.$item->id_pedido.'/'.$item->id.'/'.$album->codigo);
						}
						DB::table('pedido_albums')->where('id_item','=',$item->id)->delete();

						DB::table('gsls')->where('ordem_producao','=',$item->id_externo)->delete();
					//});
				
					$sql = "UPDATE pedido_items SET
							url_origem = NULL,
							dt_processo_importacao = NULL,
							data_importacao = NULL, 
							tentativa_importar = 0,
							imprimir = 0, 
							dt_processo_envio_impressao = NULL,
							data_envio_impressao = NULL
							WHERE id = :id
					";
					DB::update($sql, ['id' => $request->id]);
				
					// $item->update([
					// 	'url_origem' => null, 
					// 	'dt_processo_importacao' => null,
					// 	'data_importacao' => null, 
					// 	'tentativa_importar' => 0,
					// 	'imprimir' => 0, 
					// 	'dt_processo_envio_impressao' => null,
					// 	'data_envio_impressao' => null
					// ]);

				}catch(Except $e){
					dd($e);
				}

				return redirect('pedido/'.$item->id_pedido);
			}
			else
			{
				$value = $request->session()->flash('erro', 'Não encontrei a o ítem');
				return redirect('pedido/'.$item->id_pedido)->with($value);
			}
		}
		else
		{
			$item = PedidoItemServico::find($request->id);
			if(!is_null($item))
			{
				$this->ListarPasta('/brutos/Sistema/servicos/'.$item->id_pedido.'/'.$item->id);
				$item->update([
					'arquivos' => 0,
					'imprimir' => 0,
					'data_importacao' => null,
					'data_envio_impressao' => null,
				]);
				return redirect('pedido/'.$item->id_pedido);
			}
			else
			{
				$value = $request->session()->flash('erro', 'Não encontrei a o ítem');
				return redirect('pedido/'.$item->id_pedido)->with($value);
			}
		}
	}

	public function Arquivos($id_item, $id_album = null)
	{
		$sql = "SELECT i.id, i.id_pedido, p.disposicao FROM pedido_items i
				JOIN produtos p ON i.id_produto = p.id
				WHERE i.id = :id";
		$result = DB::select($sql,['id' => $id_item]);
		
		$item = null;
		if(count($result) > 0)
			$item = $result[0];
		
		if(!is_null($item))
		{
			$sql = "SELECT * FROM pedido_albums WHERE id_item = :id_item";
			$albuns = DB::select($sql, ['id_item' => $id_item]);

			$album = null;
			if(count($albuns) > 0)
				$album = $albuns[0];
			
			if(!is_null($id_album))
			{
				foreach($albuns as $row)
				{
					if($row->id == $id_album){
						$album = $row;
						break;
					}
				}
			}
			
			$arquivos = [];
			if(!is_null($album))
			{
				$sql = "SELECT * FROM pedido_item_arquivos WHERE id_item = :id_item AND id_album = :id_album ORDER BY nome_arquivo";
				$arquivos = DB::select($sql, ['id_item' => $item->id, 'id_album' => $album->id]);
			}
			
			return view('arquivos', ['item' => $item, 'albuns' => $albuns, 'arquivos' => $arquivos, 'id_album' => $id_album]);
		}
		else
		{
			$value = $request->session()->flash('erro', 'Não encontrei a o ítem');
			return redirect('pedido/'.$item->id_pedido)->with($value);
		}
	}

	public function ArquivosPost(Request $request)
	{
		return redirect('arquivos/'.$request->id.'/'.$request->album);
	}

	public function Deletar(Request $request)
	{
		$deletar  = true;
		$value = null;

		$pedido = Pedido::find($request->id);
		if(!is_null($pedido))
		{
			$itens = PedidoItem::where('id_pedido','=',$pedido->id)->get();
			foreach($itens as $item)
				$item->delete();
			
			$itens = PedidoItemServico::where('id_pedido','=',$pedido->id)->get();
			foreach($itens as $item)
				$item->delete();
			
			if($pedido->delete()){
				$value = $request->session()->flash('status', 'Pedido deletado');
			}else{
				$value = $request->session()->flash('erro', 'Erro ao deletar');
			}
		}
		
		return redirect('pedidos')->with($value);
	}

	public function Imprimir(Request $request)
	{
		if($request->tipo == 'P')
		{
			$item = PedidoItem::find($request->id_item);
			$dados = ['imprimir' => 1, 'corrigir' => $request->corrigir, 'data_envio_impressao' => null];
		}
		else
		{
			$item = PedidoItemServico::find($request->id_item);
			$dados = ['imprimir' => 1];
		}
		
		if(!is_null($item))
		{
			$item->update($dados);
			return ['success' => true];
		}

		return ['success' => false];
	}

	private function MapaArquivosCount($dir)
	{
		$cnt = 0;
		$directories = scandir($dir);

		foreach($directories as $diretorio)
		{
			if(is_dir($dir.'/'.$diretorio) && !in_array($diretorio, ['.','..']))
			{
				$cnt++;
			}
		}
		
		return $cnt;
	}
	
	private function MakeDirectory($path)
	{
		$url = '/';
		$folders = explode('/', $path);
		foreach($folders as $dir)
		{
			if($dir != '')
			{
				if(!is_dir($url.$dir))
				{
					if(!mkdir($url.$dir))
						return false;
				}

				$url .= $dir.'/';
			}
		}

		return true;
	}

	private function ListarPasta($path)
	{
		$pastas = scandir($path);
		
		foreach($pastas as $pasta)
			if(!in_array($pasta,['.','..']))
				if(is_dir($path.'/'.$pasta))
				{
					$this->ListarPasta($path.'/'.$pasta);

					//if(is_dir($path.'/'.$pasta))
						rmdir($path.'/'.$pasta);
				}
				else
					//if(file_exists($path.'/'.$pasta))
						unlink($path.'/'.$pasta);
		
	}

	public function ResetAlbum(Request $request)
	{
		$sql = "SELECT a.* 
				FROM pedido_albums l
				JOIN pedido_item_arquivos a ON a.id_album = l.id
				WHERE l.id_pedido = :id_pedido AND l.id_item = :id_item AND l.id = :id_album";
		
		$arquivos = DB::select($sql, ['id_pedido' => $request->id_pedido, 'id_item' => $request->id_item, 'id_album' => $request->id_album]);
		foreach($arquivos as $arquivo)
		{
			// deleta arquivos
			if(file_exists('/sistema/fotos/'.$arquivo->url_imagem))
				unlink('/sistema/fotos/'.$arquivo->url_imagem);
			if(file_exists('/sistema/thumbs/'.$arquivo->url_imagem))
				unlink('/sistema/thumbs/'.$arquivo->url_imagem);

			// deleta registros de recortes
			DB::table('recortes')->where('id_arquivo','=',$arquivo->id)->delete();
			DB::table('pedido_item_arquivos')->where('id','=',$arquivo->id)->delete();
		}

		$album = PedidoAlbum::find($request->id_album);
		if(!is_null($album))
		{
			// deleta pastas
			if(is_dir('/sistema/fotos/'.$request->id_pedido.'/'.$request->id_item.'/'.$album->codigo))
				rmdir('/sistema/fotos/'.$request->id_pedido.'/'.$request->id_item.'/'.$album->codigo);
			if(is_dir('/sistema/thumbs/'.$request->id_pedido.'/'.$request->id_item.'/'.$album->codigo))
				rmdir('/sistema/thumbs/'.$request->id_pedido.'/'.$request->id_item.'/'.$album->codigo);
			
			$album->delete();
		}
		
		return redirect('arquivos/'.$request->id_item);
	}

	public function AltCopias(Request $request)
	{
		$item = PedidoItem::find($request->id_item);
		if(!is_null($item)){
			if($item->update(['copias' => $request->copias])){
				return ['success' => true];
			}
		}

		return ['success' => false];
	}
}
