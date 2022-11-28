<?php
$con_sax = new PDO("mysql:host=localhost;dbname=zangraf_xkey_principal", "root", "1234");
$con_zangraf = new PDO("mysql:host=192.168.10.50;dbname=producao", "root", "1234");

// listar pedidos sem OS
$result = $con_zangraf->query("SELECT * FROM pedidos WHERE id_externo IS NULL AND updated_at IS NOT NULL");
$result->execute();
while($pedido = $result->fetchObject())
{
	$res_clie = $con_sax->query("SELECT * FROM zangraf_xkey_publico.cad_clie WHERE codigo = ".$pedido->id_cliente);
	$res_clie->execute();
	
	$cliente = $res_clie->fetchObject();
	
	// criar OSs
	$sql = "INSERT INTO zangraf_xkey_principal.cad_orca(STATUS, FILIAL, TIPO_PEDIDO, ID_INTERNA, CLIENTE, ENDERECO_CLI, NUMERO_CLI, COMPLEMENTO_CLI, BAIRRO_CLI, CIDADE_CLI, ESTADO_CLI, CEP_CLI, FORMA_PAGAMENTO, DATA_CADASTRO, HORA_CADASTRO, DATA_INICIO, HORA_INICIO, DATA_TERMINO, HORA_TERMINO, INICIO_VIGENCIA, FIM_VIGENCIA, SITUACAO, VENDEDOR, CONTATO, DATA_PEDIDO, HORA_PEDIDO, DATA_APROV, HORA_APROV, QTDE_PARCELAS, DIAS_PRIMEIRA, PREVISAO_ENTREGA, SETOR, observacoes2, frete, valor_frete, transportadora)
			VALUES(:status, :filial, :tipo_pedido, :id_interna, :cliente, :endereco_cli, :numero_cli, :complemento_cli, :bairro_cli, :cidade_cli, :estado_cli, :cep_cli, :forma_pagamento, :data_cadastro, :hora_cadastro, :data_inicio, :hora_inicio, :data_termino, :hora_termino, :inicio_vigencia, :fim_vigencia, :situacao, :vendedor, :contato, :data_pedido, :hora_pedido, :data_aprov, :hora_aprov, :qtde_parcelas, :dias_primeira, :previsao_entrega, :setor, :observacoes2, :frete, :valor_frete, :transportadora)";
	$strct = $con_sax->prepare($sql);

	$strct->bindValue(":status", 0, PDO::PARAM_INT );
	$strct->bindValue(":filial", 1, PDO::PARAM_INT );
	$strct->bindValue(":tipo_pedido", 2, PDO::PARAM_INT );
	$strct->bindValue(":id_interna", $pedido->id_printone, PDO::PARAM_INT );
	$strct->bindValue(":cliente", $pedido->id_cliente, PDO::PARAM_INT );
	$strct->bindValue(":endereco_cli", $cliente->ENDERECO, PDO::PARAM_STR );
	$strct->bindValue(":numero_cli", $cliente->NUMERO, PDO::PARAM_INT );
	$strct->bindValue(":complemento_cli", $cliente->COMPLEMENTO, PDO::PARAM_STR );
	$strct->bindValue(":bairro_cli", $cliente->BAIRRO, PDO::PARAM_STR );
	$strct->bindValue(":cidade_cli", $cliente->CIDADE, PDO::PARAM_STR );
	$strct->bindValue(":estado_cli", $cliente->ESTADO, PDO::PARAM_STR );
	$strct->bindValue(":cep_cli", $cliente->CEP, PDO::PARAM_STR );
	$strct->bindValue(":forma_pagamento", 1, PDO::PARAM_INT );
	$strct->bindValue(":data_cadastro", $pedido->data_entrada, PDO::PARAM_STR );
	$strct->bindValue(":hora_cadastro", '00:00:00', PDO::PARAM_STR );
	$strct->bindValue(":data_inicio", '1899-12-30', PDO::PARAM_STR );
	$strct->bindValue(":hora_inicio", '00:00:00', PDO::PARAM_STR );
	$strct->bindValue(":data_termino", '1899-12-30', PDO::PARAM_STR );
	$strct->bindValue(":hora_termino", '00:00:00', PDO::PARAM_STR );
	$strct->bindValue(":inicio_vigencia", '1899-12-30', PDO::PARAM_STR );
	$strct->bindValue(":fim_vigencia", '1899-12-30', PDO::PARAM_STR );
	$strct->bindValue(":situacao", 'EA', PDO::PARAM_STR ); // Orçamento
	//$strct->bindValue(":situacao", 'AI', PDO::PARAM_STR ); // Pedido
	$strct->bindValue(":vendedor", $pedido->id_vendedor, PDO::PARAM_INT );
	$strct->bindValue(":contato", $pedido->contrato, PDO::PARAM_STR );
	$strct->bindValue(":data_pedido", '0000-00-00', PDO::PARAM_STR );
	$strct->bindValue(":hora_pedido", '00:00:00', PDO::PARAM_STR );
	$strct->bindValue(":data_aprov", $pedido->data_entrada, PDO::PARAM_STR );
	$strct->bindValue(":hora_aprov", '00:00:00', PDO::PARAM_STR );
	$strct->bindValue(":qtde_parcelas", 1, PDO::PARAM_INT );
	$strct->bindValue(":dias_primeira", 30, PDO::PARAM_INT );
	$strct->bindValue(":previsao_entrega", $pedido->previsao_entrega, PDO::PARAM_STR );
	$strct->bindValue(":setor", 1, PDO::PARAM_INT );
	$strct->bindValue(":observacoes2", $pedido->observacoes, PDO::PARAM_LOB );
	$strct->bindValue(":frete", 'F', PDO::PARAM_STR );
	$strct->bindValue(":valor_frete", $pedido->valorFrete, PDO::PARAM_STR );
	$strct->bindValue(":transportadora", 15, PDO::PARAM_INT );

	if($strct->execute())
	{
		// pega codigo da OS
		$strct = $con_sax->prepare("SELECT codigo FROM zangraf_xkey_principal.cad_orca WHERE id_interna = :id_interna AND contato = :contato");
		$strct->bindValue(":id_interna", $pedido->id_printone, PDO::PARAM_INT );
		$strct->bindValue(":contato", $pedido->contrato, PDO::PARAM_STR );
		$strct->execute();

		$ordem = $strct->fetchObject();
		$id_os = $ordem->codigo;
		
		// atualiza pedido com o código da OS
		$strct = $con_zangraf->prepare("UPDATE pedidos SET id_externo = :id_os WHERE id = :id");
		$strct->bindValue(":id_os", $id_os, PDO::PARAM_INT );
		$strct->bindValue(":id", $pedido->id, PDO::PARAM_INT );
		$strct->execute();

		// listar itens
		$res_item = $con_zangraf->query("SELECT * FROM pedido_items WHERE id_pedido = ".$pedido->id);
		$res_item->execute();

		$sequencia = 1;
		$quantidade = 0;
		$total_produtos = 0;
		while($item = $res_item->fetchObject())
		{
			// busca custo do produto
			$st_prd = $con_sax->prepare("SELECT ult_custo, custo_medio FROM zangraf_xkey_publico.cad_prod WHERE codigo = :id_produto");
			$st_prd->bindValue(":id_produto", $item->id_produto_externo, PDO::PARAM_INT );
			$st_prd->execute();
			$prd_sax = $st_prd->fetchObject();

			$quantidade += $item->quantidade;
			$total_liq = $item->preco_unitario * $item->quantidade;
			$total_produtos += $total_liq;

			$sql = "INSERT INTO zangraf_xkey_principal.pro_orca(ORCAMENTO, SEQUENCIA, PRODUTO, UNIDADE, ITEM_UNID, QUANTIDADE, QTDE_SEPARADA, UNITARIO, TOTAL_LIQ, UNIT_ORIG, ULT_CUSTO, CUSTO_MEDIO) 
					VALUE(:orcamento, :sequencia, :produto, :unidade, :item_unid, :quantidade, :quantidade_separada, :unitario, :total_liq, :unit_orig, :ult_custo, :custo_medio)";
			$strct = $con_sax->prepare($sql);
			$strct->bindValue(":orcamento", $id_os, PDO::PARAM_INT );
			$strct->bindValue(":sequencia", $sequencia, PDO::PARAM_INT );
			$strct->bindValue(":produto", $item->id_produto_externo, PDO::PARAM_INT );
			$strct->bindValue(":unidade", 'UND', PDO::PARAM_STR );
			$strct->bindValue(":item_unid", 1, PDO::PARAM_INT );
			$strct->bindValue(":quantidade", $item->quantidade, PDO::PARAM_INT );
			$strct->bindValue(":quantidade_separada", $item->quantidade, PDO::PARAM_INT );
			$strct->bindValue(":unitario", $item->preco_unitario, PDO::PARAM_STR );
			$strct->bindValue(":total_liq", $total_liq, PDO::PARAM_STR );
			$strct->bindValue(":unit_orig", $item->preco_unitario, PDO::PARAM_STR );
			$strct->bindValue(":ult_custo", $prd_sax->ult_custo, PDO::PARAM_STR );
			$strct->bindValue(":custo_medio", $prd_sax->custo_medio, PDO::PARAM_STR );
			$strct->execute();

			$sequencia++;
		}

		// atualiza quantidade da OS
		$strct = $con_sax->prepare("UPDATE zangraf_xkey_principal.cad_orca SET quantidade = :quantidade, total_produtos = :total_produtos WHERE codigo = :codigo");
		$strct->bindValue(":codigo", $id_os, PDO::PARAM_INT );
		$strct->bindValue(":quantidade", $quantidade, PDO::PARAM_INT );
		$strct->bindValue(":total_produtos", $total_produtos, PDO::PARAM_STR );
		$strct->execute();
		
		// listar serviços
		$res_serv = $con_zangraf->query("SELECT * FROM pedido_item_servicos WHERE id_pedido = ".$pedido->id." AND id_servico_externo IS NOT NULL");
		$res_serv->execute();

		$sequencia = 1;
		while($serv = $res_serv->fetchObject())
		{
			$sql = "INSERT INTO zangraf_xkey_principal.ser_orca(ORCAMENTO, SEQUENCIA, SERVICO, QUANTIDADE, QTDE_MOV, QTDE_FATURADA, UNITARIO, UNIT_ORIG)
					VALUES(:orcamento, :sequencia, :servico, :quantidade, :qtde_mov, :qtde_faturada, :unitario, :unit_orig)";
			$strct = $con_sax->prepare($sql);
			$strct->bindValue(":orcamento", $id_os, PDO::PARAM_INT );
			$strct->bindValue(":sequencia", $sequencia, PDO::PARAM_INT );
			$strct->bindValue(":servico", $serv->id_servico_externo, PDO::PARAM_INT );
			$strct->bindValue(":quantidade", 1, PDO::PARAM_INT );
			$strct->bindValue(":qtde_mov", 1, PDO::PARAM_INT );
			$strct->bindValue(":qtde_faturada", 1, PDO::PARAM_INT );
			$strct->bindValue(":unitario", $serv->valor, PDO::PARAM_STR );
			$strct->bindValue(":unit_orig", $serv->valor, PDO::PARAM_STR );
			$strct->execute();

			$sequencia++;
		}
	}
}
