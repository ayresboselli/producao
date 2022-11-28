from threading import Thread
from datetime import datetime
from shutil import copyfile
from PIL import Image

import mysql.connector
import time
import os

dpi = 11.81102362204724
margem_proporcao = 0.1
vCPU = 12

#root = "/brutos/Sistema/teste"
root = ""
root_fotos = "/sistema/fotos"
root_thumbs = "/sistema/thumbs"
root_servicos = "/sistema/servicos"
arquivo_log = "/var/log/producao/producao.log"

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

count_files = 0
thread_import = 0
class ImportaServico(Thread):
    def __init__ (self, origem, destino):
        Thread.__init__(self)
        self.origem = origem
        self.destino = destino

    def run(self):
        global thread_import
        global count_files

        try:
            imagem = Image.open(self.origem)
            imagem.save(root_servicos + self.destino, 'JPEG', dpi=(300, 300))
        except:
            copyfile(self.origem, root_servicos + self.destino)
            
        # deleta self.origem
        count_files += 1
        thread_import -= 1


thread_export = 0
class ExportarImagemBrutos(Thread):
    def __init__ (self, origem, destino):
        Thread.__init__(self)
        self.origem = origem
        self.destino = destino

    def run(self):
        global thread_export

        try:
            imagem = Image.open(self.origem)
            imagem.save(root + self.destino, 'JPEG', dpi=(300, 300))
        except:
            copyfile(self.origem, root + self.destino)
            imagem = None
        
        thread_export -= 1


def Add0(val):
	val = str(val)
	if len(val) == 1:
		return '00'+str(val)
	elif len(val) == 2:
		return '0'+str(val)
	else:
		return str(val)


def MakeDirectory(folder):
    folder = root+folder
    directory = ''
    pastas = folder.split('/')
    for pasta in pastas:
        directory += '/'+pasta
        if not os.path.exists(directory):
            os.mkdir(directory)


def ListarPasta(root, path):
    pastas = os.listdir(root+path)
    lista = []
    for pasta in pastas:
        if os.path.isdir(root+path+'/'+pasta):
            lista += ListarPasta(root, path+'/'+pasta)
        else:
            lista.append(path+'/'+pasta)
    
    return lista


def CriaPastasServicos(path, arquivo = False):
    d = ''
    pastas = path.split('/')

    cnt = 0
    tam = len(pastas)
    if arquivo:
        tam -= 1
    while cnt < tam:
        if pastas[cnt] != '':
            d += '/'+pastas[cnt]
            if not os.path.exists(root_servicos + d):
                os.mkdir(root_servicos + d)
        cnt += 1


def CriaPastas(path):
    d = ''
    pastas = path.split('/')

    cnt = 0
    tam = len(pastas)-1
    while cnt < tam:
        if pastas[cnt] != '':
            d += '/'+pastas[cnt]
            if not os.path.exists(root + d):
                os.mkdir(root + d)
        cnt += 1


def ImportarServicos():
    global arquivo_log
    global mydbZangraf
    global myZangraf
    global thread_import
    global vCPU

    sql  = "SELECT i.id, i.id_pedido, i.url_origem "
    sql += "FROM pedido_item_servicos i JOIN servicos s ON s.id = i.id_servico "
    sql += "WHERE i.url_origem IS NOT NULL AND i.arquivos = 0 AND s.id = 1 LIMIT 1"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    cnt_processos = len(result)
    for row in result:

        arquivos = ListarPasta(row[2], '')

        cnt_arq = 0
        while cnt_arq < len(arquivos):
            if thread_import < vCPU:
                arq = '/'+str(row[1])+'/'+str(row[0]) + arquivos[cnt_arq]
                CriaPastasServicos(arq, True)

                thread = ImportaServico(row[2]+arquivos[cnt_arq], arq)
                thread.start()

                cnt_arq += 1
                thread_import += 1
        
        sql = "UPDATE pedido_item_servicos SET arquivos = "+str(count_files)+" WHERE id = "+str(row[0])
        myZangraf.execute(sql)
        mydbZangraf.commit()
    
    return cnt_processos


def ExportarServicos():
    global arquivo_log
    global mydbZangraf
    global myZangraf
    global mydbSax
    global mySax
    global thread_export
    global vCPU

    sql  = "SELECT i.id, i.id_pedido, p.id_externo FROM pedido_item_servicos i JOIN pedidos p ON p.id = i.id_pedido "
    sql += "WHERE i.data_envio_impressao IS NULL AND i.id_servico = 1 AND i.imprimir = 1"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()
    
    cnt_processos = len(result)
    for row in result:
        path = root_servicos+'/'+str(row[1])+'/'+str(row[0])
        arquivos = ListarPasta(path, '')

        cnt_arq = 0
        while cnt_arq < len(arquivos):
            if thread_export < vCPU:
                sql  = "SELECT cli.codigo, cli.apelido, orc.codigo, orc.contato, orc.tipo_pedido "
                sql += "FROM zangraf_xkey_principal.cad_orca orc JOIN zangraf_xkey_publico.cad_clie cli ON cli.codigo = orc.cliente "
                sql += "WHERE orc.codigo = "+str(row[2])
                mySax.execute(sql)
                strct = mySax.fetchone()

                if strct != None:
                    origem = path+arquivos[cnt_arq]

                    if strct[4] == 1:
                        destino = '/producao/['+str(strct[0])+']'+str(strct[1])+'/'+str(strct[2])+'/BRUTO' + arquivos[cnt_arq]
                    else:
                        destino = '/pedido/['+str(strct[0])+']'+str(strct[1])+'/'+str(strct[2]) + arquivos[cnt_arq]

                    cnt_arq += 1
                    CriaPastas(destino)
                    
                    thread = ExportarImagemBrutos(origem, destino)
                    thread.start()
                    
                    thread_export += 1
                    
            else:
                time.sleep(0.3)
        
        sql = "UPDATE pedido_item_servicos SET data_envio_impressao = now() WHERE id = "+str(row[0])
        myZangraf.execute(sql)
        mydbZangraf.commit()
    
    return cnt_processos



myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'producao-servicos'")
mydbZangraf.commit()


qtd_processos = ImportarServicos()
qtd_processos += ExportarServicos()


if qtd_processos == 0:
    time.sleep(60)
