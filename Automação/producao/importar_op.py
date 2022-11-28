import mysql.connector

##### CONEXÕES COM BANCO DE DADOS #####
mydbSax = mysql.connector.connect(
	host="localhost",
	user="root",
	password="1234",
	database="zangraf_xkey_principal"
)
mySax = mydbSax.cursor()

mydbZangraf = mysql.connector.connect(
    host="192.168.10.50",
    user="root",
    password="1234",
    database="producao"
)
myZangraf = mydbZangraf.cursor()


sql  = "SELECT i.id, p.id_externo os, i.id_produto_externo id_produto "
sql += "FROM pedidos p JOIN pedido_items i ON i.id_pedido = p.id "
sql += "WHERE i.id_externo IS NULL AND p.id_externo IS NOT NULL"
myZangraf.execute(sql)
result = myZangraf.fetchall()

for row in result:
    try:
        sql  = "SELECT codigo FROM zangraf_xkey_producao.producoes "
        sql += "WHERE cod_producao = "+str(row[1])+" AND produto = "+str(row[2])
        mySax.execute(sql)
        op = mySax.fetchone()

        if op != None:
            sql = "UPDATE pedido_items SET id_externo = "+str(op[0])+" WHERE id = "+str(row[0])
            myZangraf.execute(sql)
            mydbZangraf.commit()
    except:
        None