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

sql = "SELECT id, id_externo FROM pedidos WHERE data_fechamento IS NULL"
myZangraf.execute(sql)
result = myZangraf.fetchall()

for row in result:
    sql = "SELECT situacao FROM zangraf_xkey_principal.cad_orca WHERE codigo = "+str(row[1])
    mySax.execute(sql)
    situacao = mySax.fetchone()

    if situacao[0] == 'FI':
        sql = "UPDATE pedidos SET data_fechamento = now() WHERE id = "+str(row[0])
        myZangraf.execute(sql)
        mydbZangraf.commit()
