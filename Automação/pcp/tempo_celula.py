import time
import mysql.connector
from datetime import datetime


mydbSax = mysql.connector.connect(
	host="localhost",
	user="root",
	password="1234",
	database="zangraf_xkey_producao"
)
mySax = mydbSax.cursor(buffered=True)


lista = {}


sql = "SELECT codigo, quantidade, inicio FROM zangraf_xkey_producao.producoes WHERE inicio BETWEEN '2022-02-01' AND '2022-02-28' order by codigo desc"
mySax.execute(sql)
producoes = mySax.fetchall()

for producao in producoes:
    inicio = producao[2]
    #                   0                                                   1                   2           3
    sql = "SELECT a.producao, concat(a.data_termino,' ', a.hora_termino) termino, o.descricao celula, cod_recurso "
    sql += "FROM zangraf_xkey_producao.apontamento a JOIN zangraf_xkey_producao.operacao_producao o ON o.codigo_op = a.operacao AND a.producao = o.producao "
    sql += "WHERE a.producao = "+str(producao[0])+" AND a.data_termino != '0000-00-00' ORDER BY a.producao, a.item "
    mySax.execute(sql)
    apontamentos = mySax.fetchall()

    for apontamento in apontamentos:
        d1 = str(inicio)[8:10]
        d2 = apontamento[1][8:10]

        dias = int(d2)-int(d1)

        tempo = time.mktime(datetime.strptime(apontamento[1], '%Y-%m-%d %H:%M:%S').timetuple()) - time.mktime(datetime.strptime(str(inicio), '%Y-%m-%d %H:%M:%S').timetuple())
        tempo -= dias*50400 # para dias diferentes

        tempo /= 60*60 # Horas

        if tempo > 0:
            #print(inicio, apontamento[1], apontamento[2], tempo)

            if apontamento[2] in lista:
                tmp_tempo = lista[apontamento[2]][0] + tempo
                contador = lista[apontamento[2]][1] + 1 
                lista[apontamento[2]][0] = tmp_tempo
                lista[apontamento[2]][1] = contador
                
            else:
                lista[apontamento[2]] = [tempo, 1]
                
        
        inicio = apontamento[1]

for l in lista:
    print(l, lista[l][0] / lista[l][1])

print()