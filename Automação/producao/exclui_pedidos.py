import mysql.connector

connZangraf = mysql.connector.connect(
    host="192.168.10.50",
    user="root",
    password="1234",
    database="producao"
)
cursorZangraf = connZangraf.cursor()

sql = "UPDATE pedidos SET arquivar = 1 WHERE data_fechamento <= DATE_SUB(now(), INTERVAL 15 DAY) /*AND tipo_contrato = 2*/ AND arquivar IS NULL"
cursorZangraf.execute(sql)
connZangraf.commit()