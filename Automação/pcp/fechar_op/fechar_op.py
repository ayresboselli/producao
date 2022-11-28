import mysql.connector

mydbSax = mysql.connector.connect(
	host="localhost",
	user="root",
	password="1234",
	database="zangraf_xkey_producao"
)
mySax = mydbSax.cursor()



def Perdas(op, ajuste_empenho):
    
    # busca por perdas na producao
    sql  = "SELECT sum(apd.perdido) FROM apontamento apt "
    sql += "JOIN apontamento_detalhe apd ON apt.cod_barras = apd.lig_id_operacao "
    sql += "WHERE apt.producao = "+str(op)

    mySax.execute(sql)
    result = mySax.fetchall()
    
    perdido = 0
    if result[0][0] != None and result[0][0] > 0:
        if result[0][0] == None:
            perdido = 0
        else:
            perdido = result[0][0]
        
        # busca por subproduto do produto
        sql  = "SELECT c.quantidade "
        sql += "FROM zangraf_xkey_producao.producoes p "
        sql += "JOIN zangraf_xkey_publico.com_prod c ON c.produto = p.produto "
        sql += "WHERE p.codigo = "+str(op)+" AND c.subproduto = "+str(ajuste_empenho[1])
        
        mySax.execute(sql)
        result = mySax.fetchall()
        if len(result) > 0:
            perdido = float(perdido) * float(result[0][0])
        
        # atualiza ajuste_empenho
        total = float(perdido) + float(ajuste_empenho[0])
        sql  = "UPDATE zangraf_xkey_producao.ajuste_empenho SET quantidade = "+str(total)+" WHERE producao = "+str(op)+" AND produto = "+str(ajuste_empenho[1])
        mySax.execute(sql)
        mydbSax.commit()
        
    return perdido



sql  = "SELECT DISTINCT pdc.codigo, pdc.quantidade "
sql += "FROM zangraf_xkey_producao.producoes pdc JOIN zangraf_xkey_producao.operacao_producao opr ON opr.producao = pdc.codigo JOIN zangraf_xkey_producao.apontamento apt ON apt.OPERACAO_PRODUCAO_ID = opr.Id "
sql += "WHERE pdc.situacao = 'A' AND opr.descricao = 'EXPEDIÇÃO' AND apt.situacao = 'FINALIZADO' "

mySax.execute(sql)
ordem_producao = mySax.fetchall()

# percorre as OPs filtradas
for item in ordem_producao:
    try:
        # busca insumos gastos na produção
        sql  = "SELECT quantidade, produto, setor "
        sql += "FROM zangraf_xkey_producao.ajuste_empenho "
        sql += "WHERE producao = "+str(item[0])
        mySax.execute(sql)
        result = mySax.fetchall()
        
        # atualiza insumos no estoque
        for row in result:
            perdido = Perdas(item[0],row)
            
            sql  = "SELECT codigo, estoque FROM zangraf_xkey_principal.prod_setor "
            sql += "WHERE produto = "+str(row[1])+" AND setor = "+str(row[2])
            mySax.execute(sql)
            estoque = mySax.fetchall()
        
            total = float(estoque[0][1]) - float(row[0]) - float(perdido)
            sql  = "UPDATE zangraf_xkey_principal.prod_setor SET estoque = "+str(total)+" WHERE codigo = "+str(estoque[0][0])
            mySax.execute(sql)
        
        # atualiza produtos no estoque
        sql  = "SELECT ps.codigo, (ps.estoque+pro.quantidade) total "
        sql += "FROM zangraf_xkey_producao.producoes pro JOIN zangraf_xkey_principal.prod_setor ps ON ps.produto = pro.produto "
        sql += "WHERE ps.setor = 1 AND pro.codigo = "+str(item[0])
        mySax.execute(sql)
        estoque = mySax.fetchall()
        
        sql = "UPDATE zangraf_xkey_principal.prod_setor SET estoque = "+str(estoque[0][1])+" WHERE codigo = "+str(estoque[0][0])
        mySax.execute(sql)
        
        sql = "UPDATE zangraf_xkey_producao.producoes SET situacao = 'E', termino = current_timestamp() WHERE codigo = "+str(item[0])
        mySax.execute(sql)

        mydbSax.commit()
    except mysql.connector.Error as err:
        mydbSax.rollback()
    
if mydbSax.is_connected():
    mySax.close()
    mydbSax.close()