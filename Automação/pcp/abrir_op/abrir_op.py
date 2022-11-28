import mysql.connector


mydbSax = mysql.connector.connect(
	host="localhost",
	user="root",
	password="1234",
	database="zangraf_xkey_producao"
)
mySax = mydbSax.cursor(buffered=True)

sql  = "SELECT o.codigo, p.produto, p.quantidade, concat(o.data_inicio, ' ', o.hora_inicio) inicio, concat(o.previsao_entrega, ' ', o.hora_previsao) previsao, pdc.codigo "
sql += "FROM zangraf_xkey_principal.cad_orca o JOIN zangraf_xkey_principal.pro_orca p ON p.orcamento = o.codigo LEFT JOIN zangraf_xkey_producao.producoes pdc ON pdc.cod_producao = o.codigo AND p.produto = pdc.produto "
sql += "WHERE o.situacao = 'AI' AND pdc.codigo IS NULL GROUP BY p.orcamento, p.sequencia ORDER BY o.codigo, produto"
mySax.execute(sql)
produtos = mySax.fetchall()

for produto in produtos:
    
    sql = "SELECT max(codigo) FROM zangraf_xkey_producao.producoes"
    mySax.execute(sql)
    ordem = mySax.fetchone()
    if ordem != None:
        ordem = ordem[0] + 1
    
    
    # Composição do produto
    sql  = "SELECT cp.subproduto, cp.seq, prd.custo_medio, prd.local_produto, cp.quantidade "
    sql += "FROM zangraf_xkey_publico.com_prod cp JOIN zangraf_xkey_publico.cad_prod prd ON prd.codigo = cp.subproduto "
    sql += "WHERE produto = "+str(produto[1])+" ORDER BY seq"
    mySax.execute(sql)
    composicao = mySax.fetchall()
    

    # Operações
    sql  = "SELECT o.codigo "
    sql += "FROM zangraf_xkey_producao.operacoes o LEFT JOIN zangraf_xkey_producao.itemoperacao i ON o.codigo = i.operacao "
    sql += "WHERE o.cod_produto = "+str(produto[1])+" GROUP BY o.codigo ORDER BY count(i.codigo) DESC LIMIT 1"
    mySax.execute(sql)
    operacoes = mySax.fetchone()
    
    itemoperacao = None
    if operacoes != None:
        #               0           1           2           3           4            5       6          7           8           9           10          11              12          13          14           15
        sql  = "SELECT CODIGO, OPERACAO, TEMPO_PREV, COD_RECURSO, COD_CELULA_TER, CEL_TER, SETUP, LOTE_PADRAO, FERRAMENTA, DESCRICAO, TIPO_LINHA, LINHA_PRODUCAO, TEMPO_PADRAO, SEQUENCIA, SOBREPOSICAO, TEMPO_SOBRE "
        sql += "FROM zangraf_xkey_producao.itemoperacao WHERE operacao = "+str(operacoes[0])+" ORDER BY codigo"
        mySax.execute(sql)
        itemoperacao = mySax.fetchall()


    if composicao != None and itemoperacao != None:
        try:
            # cria OP
            sql  = "INSERT INTO zangraf_xkey_producao.producoes(codigo, produto, quantidade, inicio, previsao, cod_producao, filial, observacoes) "
            sql += "VALUES("+str(ordem)+","+str(produto[1])+", "+str(produto[2])+", now(), '"+str(produto[4])+"', "+str(produto[0])+", 1, '')"
            mySax.execute(sql)
            
            sql = "SELECT codigo FROM zangraf_xkey_producao.producoes WHERE cod_producao = "+str(produto[0])+" AND produto = "+str(produto[1])
            mySax.execute(sql)
            op = mySax.fetchone()
            
            if op != None:
                op = op[0]
                
                for comp_item in composicao:
                    #Cria Ajuste de Empenho
                    if len(comp_item[3]) > 0:
                        setor = comp_item[3]
                    else:
                        setor = 1
                    
                    quantidade = comp_item[4] * produto[2]
                    sql = "SELECT count(*) FROM zangraf_xkey_producao.ajuste_empenho WHERE producao = "+str(op)+" AND produto = "+str(comp_item[0])+" AND item = "+str(comp_item[1])+""
                    mySax.execute(sql)
                    cnt_ajuste = mySax.fetchone()
                    
                    if cnt_ajuste[0] == 0:
                        sql  = "INSERT INTO zangraf_xkey_producao.ajuste_empenho(producao, produto, item, custo, quantidade, setor) "
                        sql += "VALUES("+str(op)+", "+str(comp_item[0])+", "+str(comp_item[1])+", "+str(comp_item[2])+", "+str(quantidade)+", "+str(setor)+")"
                        mySax.execute(sql)
                    
                
                sequencia = 1
                for item_op in itemoperacao:
                    codebar = str(op)
                    cnt = len(codebar) + len(str(item_op[0]))
                    while cnt < 13:
                        codebar += str('0')
                        cnt += 1

                    codebar += str(item_op[0])
                    
                    sql = "SELECT max(Id) FROM zangraf_xkey_producao.operacao_producao"
                    mySax.execute(sql)
                    id = mySax.fetchone()[0] +1
                    
                    sql  = "INSERT INTO zangraf_xkey_producao.operacao_producao(Id, PRODUCAO, COD_BARRAS, SEQUENCIA, CODIGO_OP, TEMPO_PREV, COD_RECURSO, COD_CELULA_TER, SETUP, LOTE_PADRAO, FERRAMENTA, DESCRICAO, TIPO_LINHA, LINHA_PRODUCAO, TEMPO_PADRAO, SOBREPOSICAO, TEMPO_SOBRE) "
                    sql += "VALUES("+str(id)+", "+str(op)+", '"+codebar+"', "+str(item_op[13])+", "+str(item_op[0])+", '"+str(item_op[2])+"', "+str(item_op[3])+", "+str(item_op[4])+", '"+str(item_op[6])+"', "+str(item_op[7])+", "+str(item_op[8])+", '"+str(item_op[9])+"', "+str(item_op[10])+", "+str(item_op[11])+", '"+str(item_op[12])+"', '"+str(item_op[14])+"', '"+str(item_op[15])+"')"
                    mySax.execute(sql)
                    
                    sql = "SELECT id FROM zangraf_xkey_producao.operacao_producao WHERE producao = "+str(op)+" AND codigo_op = "+str(item_op[0])
                    mySax.execute(sql)
                    op_prod_id = mySax.fetchone()

                    if op_prod_id != None:
                        op_prod_id = op_prod_id[0]
                        
                        sql = "SELECT max(ID_REGISTRO) FROM zangraf_xkey_producao.apontamento"
                        mySax.execute(sql)
                        id = mySax.fetchone()[0] +1
                        
                        sql  = "INSERT INTO zangraf_xkey_producao.apontamento(ID_REGISTRO, PRODUCAO, ITEM, COD_BARRAS, OPERACAO, TEMPO_PREV, PRIORIDADE, OPERACAO_PRODUCAO_ID) "
                        sql += "VALUES("+str(id)+", "+str(op)+", "+str(item_op[13])+", '"+codebar+"', "+str(item_op[0])+", '"+str(item_op[2])+"', "+str(item_op[13])+", "+str(op_prod_id)+")"
                        mySax.execute(sql)
                    
                    sequencia += 1
                    codebar = None

            mydbSax.commit()
        except mysql.connector.Error as err:
            mydbSax.rollback()
    

if mydbSax.is_connected():
    mySax.close()
    mydbSax.close()

