# https://marquesfernandes.com/desenvolvimento/criando-um-servico-linux-com-systemd/

from threading import Thread
#from shutil import copyfile
from PIL import Image

import xml.etree.cElementTree as ET
import mysql.connector
import time
import os

dpi = 11.811023622047244

margem_proporcao = 0.1
vCPU = 48

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

lista_arquivos = []
'''
lista_arquivos
    0       1        2        3         4        5        6       7
id_item, id_album, origem, destino, filename, largura, altura, recortar,
'''

thread_recortes = []
thread_import = 0
class ImportaImagem(Thread):

    def __init__ (self, cnt, row):
        global lista_arquivos

        Thread.__init__(self)
        
        self.cnt = cnt
        self.row = row
        self.id_album = lista_arquivos[cnt][1] # id_album
        self.origem = lista_arquivos[cnt][2] # origem
        self.destino = lista_arquivos[cnt][3] # filename


    def run(self):
        global root_fotos
        global root_thumbs
        global dpi
        global margem_proporcao
        global thread_import
        global lista_arquivos

        try:
            img = Image.open(self.origem)
            (largura, altura) = img.size
            lista_arquivos[self.cnt][5] = largura
            lista_arquivos[self.cnt][6] = altura


            if self.row[7] == 0:
                prod_largura = self.row[3] * dpi
                prod_altura = self.row[4] * dpi


                ##### ROTACIONA #####
                if (prod_largura > prod_altura and largura < altura) or (prod_largura < prod_altura and largura > altura):
                    img = img.transpose(Image.ROTATE_90)
                    (largura, altura) = img.size
                    lista_arquivos[self.cnt][5] = largura
                    lista_arquivos[self.cnt][6] = altura
                
            
                ##### REDIMENCIONA #####
                if largura != prod_largura or altura != prod_altura:
                    if largura > altura:
                        prop_produto = prod_altura / prod_largura
                        prop_imagem = altura / largura
                    else:
                        prop_produto = prod_largura / prod_altura
                        prop_imagem = largura / altura
                
                    prop_imagem_variacao = prop_imagem * margem_proporcao
                
                    if prop_produto > prop_imagem - prop_imagem_variacao and prop_produto < prop_imagem + prop_imagem_variacao:
                        img = img.resize((int(prod_largura), int(prod_altura)), Image.ANTIALIAS)
                    else:
                        # cadastrar recorte
                        lista_arquivos[self.cnt][7] = True # recortar
            
                (largura, altura) = img.size
                lista_arquivos[self.cnt][5] = largura
                lista_arquivos[self.cnt][6] = altura
            
        
            ##### SALVA #####
            img.save(root_fotos + '/' + self.destino, 'JPEG', dpi=(300, 300))
            
            if largura > altura:
                i = 300 / altura
            else:
                i = 300 / largura
            
            img_thumb = img.resize((int(largura * i), int(altura * i)), Image.ANTIALIAS)
            img_thumb.save(root_thumbs + '/' + self.destino)
            
            thread_import -= 1
        except:
            mydbConn = mysql.connector.connect(
                host="192.168.10.50",
                user="root",
                password="1234",
                database="producao"
            )
            myConn = mydbConn.cursor()

            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'ImportaImagem Pedido','Erro ao importar o arquivo "+str(self.origem)+" para a OS "+str(self.row[8])+"')"
            myConn.execute(sql)
            mydbConn.commit()
            mydbConn.close()
            
            thread_erro += 1


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




myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'producao-importar-pedidos'")
mydbZangraf.commit()


sql  = "SELECT * FROM ( "
sql += "SELECT i.id, i.id_pedido, i.url_origem, (coalesce(p.largura,0) + coalesce(p.sangr_dir,0) + coalesce(p.sangr_esq,0)) largura, (coalesce(p.altura,0) +coalesce( p.sangr_sup,0) + coalesce(p.sangr_inf,0)) altura, count(a.id) arquivos, i.quantidade, p.sem_dimensao, e.id_externo, i.tentativa_importar "
sql += "FROM pedido_items i JOIN produtos p ON p.id = i.id_produto JOIN pedidos e ON e.id = i.id_pedido LEFT JOIN pedido_item_arquivos a ON i.id = a.id_item "
sql += "WHERE i.url_origem IS NOT NULL AND i.url_origem != '' /*AND i.data_importacao IS NULL*/ AND e.tipo_contrato != 1 AND i.tentativa_importar < 3 "
sql += "GROUP BY i.id ORDER BY i.quantidade "
sql += ") t WHERE arquivos = 0"
myZangraf.execute(sql)
result = myZangraf.fetchall()

qtd_processos = len(result)
for row in result:
    thread_erro = 0

    try:
        path = row[2]
        if os.path.exists(path):
            pastas = os.listdir(path)
            pastas.sort()
            album = None
            id_album = None
            
            lista_arquivos = []
            for pasta in pastas:
                if os.path.isdir(path+'/'+pasta):
                    
                    # cadastrar album
                    sql = "SELECT id FROM pedido_albums WHERE id_item = "+str(row[0])+" AND codigo = '"+str(pasta)+"'"
                    myZangraf.execute(sql)
                    id_album = myZangraf.fetchone()
                    
                    if id_album == None:
                        sql  = "INSERT INTO pedido_albums(id_item, id_pedido, codigo, created_at, updated_at) "
                        sql += "VALUES ("+str(row[0])+", "+str(row[1])+", '"+str(pasta)+"', now(), now())"
                        myZangraf.execute(sql)
                        
                        sql = "SELECT max(id) FROM pedido_albums WHERE id_pedido = "+str(row[1])+" AND codigo = '"+str(pasta)+"'"
                        myZangraf.execute(sql)
                        id_album = myZangraf.fetchone()[0]
                        mydbZangraf.commit()
                    else:
                        id_album = id_album[0]

                    
                    ##### CRIA ESTRUTURA DE PASTAS #####
                    if not os.path.exists(root_fotos+'/'+str(row[1])):
                        os.mkdir(root_fotos+'/'+str(row[1]))
                    if not os.path.exists(root_fotos+'/'+str(row[1])+"/"+str(row[0])):
                        os.mkdir(root_fotos+'/'+str(row[1])+"/"+str(row[0]))
                    if not os.path.exists(root_fotos+'/'+str(row[1])+"/"+str(row[0])+'/'+pasta):
                        os.mkdir(root_fotos+'/'+str(row[1])+"/"+str(row[0])+'/'+pasta)
                    
                    if not os.path.exists(root_thumbs+'/'+str(row[1])):
                        os.mkdir(root_thumbs+'/'+str(row[1]))
                    if not os.path.exists(root_thumbs+'/'+str(row[1])+"/"+str(row[0])):
                        os.mkdir(root_thumbs+'/'+str(row[1])+"/"+str(row[0]))
                    if not os.path.exists(root_thumbs+'/'+str(row[1])+"/"+str(row[0])+'/'+pasta):
                        os.mkdir(root_thumbs+'/'+str(row[1])+"/"+str(row[0])+'/'+pasta)
                    
                    # mapeia os arquivos
                    arquivos = os.listdir(path+'/'+pasta)
                    for arquivo in arquivos:
                        nome_arq = arquivo.split('.')
                        if len(nome_arq) > 1 and (nome_arq[1].lower() == 'jpg' or nome_arq[1].lower() == 'jpeg'):

                            origem = row[2] + '/' + pasta + '/' + arquivo

                            filename = pasta + '/' + nome_arq[0] + '.jpg'
                            destino = str(row[1])+"/"+str(row[0])+"/"+str(filename)
                            
                            item = [row[0],id_album,origem,destino,filename,None,None,False]
                            lista_arquivos.append(item)
                            
                else:
                    #if pasta.split('.')[-1].lower() == 'jpg':
                    if id_album == None:
                        album = '001'

                        sql = "SELECT count(*) FROM pedido_albums WHERE id_item = "+str(row[0])+" AND codigo = '"+str(album)+"'"
                        myZangraf.execute(sql)
                        qtd_album = myZangraf.fetchone()[0]

                        if qtd_album == 0:
                            lista_arquivos = []
                            sql  = "INSERT INTO pedido_albums(id_item, id_pedido, codigo, created_at, updated_at) "
                            sql += "VALUES ("+str(row[0])+", "+str(row[1])+", '"+str(album)+"', now(), now())"
                            myZangraf.execute(sql)
                        
                        sql = "SELECT max(id) FROM pedido_albums WHERE id_pedido = "+str(row[1])+" AND codigo = '"+str(album)+"'"
                        myZangraf.execute(sql)
                        id_album = myZangraf.fetchone()[0]
                        mydbZangraf.commit()

                        ##### CRIA ESTRUTURA DE PASTAS #####
                        if not os.path.exists(root_fotos+'/'+str(row[1])):
                            os.mkdir(root_fotos+'/'+str(row[1]))
                        if not os.path.exists(root_fotos+'/'+str(row[1])+"/"+str(row[0])):
                            os.mkdir(root_fotos+'/'+str(row[1])+"/"+str(row[0]))
                        if not os.path.exists(root_fotos+'/'+str(row[1])+"/"+str(row[0])+'/'+album):
                            os.mkdir(root_fotos+'/'+str(row[1])+"/"+str(row[0])+'/'+album)
                        
                        if not os.path.exists(root_thumbs+'/'+str(row[1])):
                            os.mkdir(root_thumbs+'/'+str(row[1]))
                        if not os.path.exists(root_thumbs+'/'+str(row[1])+"/"+str(row[0])):
                            os.mkdir(root_thumbs+'/'+str(row[1])+"/"+str(row[0]))
                        if not os.path.exists(root_thumbs+'/'+str(row[1])+"/"+str(row[0])+'/'+album):
                            os.mkdir(root_thumbs+'/'+str(row[1])+"/"+str(row[0])+'/'+album)

                    nome_arq = pasta.split('.')
                    if len(nome_arq) > 1 and (nome_arq[1].lower() == 'jpg' or nome_arq[1].lower() == 'jpeg'):

                        origem = row[2] + '/' + pasta

                        filename = nome_arq[0] + '.jpg'
                        destino = str(row[1])+"/"+str(row[0])+"/"+str(filename)
                        
                        item = [row[0],id_album,origem,destino,filename,None,None,False]
                        lista_arquivos.append(item)
            
            # processa os arquivos
            cnt_arqivos = 0
            while cnt_arqivos < len(lista_arquivos):
                if thread_import < vCPU:
                    thread = ImportaImagem(cnt_arqivos, row)
                    thread.start()

                    thread_import += 1
                    cnt_arqivos += 1
            
            # espera o fim de todas as thereads
            '''
            while thread_import > 0:
                continue
            '''
            time.sleep(60)
            
            # salva informações dos arquivos
            if thread_erro == 0:
                for item in lista_arquivos:
                    situacao = 1
                    if item[7] == True: # recortar
                        situacao = 0
                
                    try:
                        sql  = "INSERT INTO pedido_item_arquivos(id_item, id_album, url_imagem, nome_arquivo, largura, altura, situacao, created_at, updated_at) "
                        sql += "VALUES ("+str(row[0])+", "+str(item[1])+", '"+item[3]+"', '"+item[4]+"', "+str(item[5])+", "+str(item[6])+", "+str(situacao)+", now(), now())"
                        myZangraf.execute(sql)

                        if item[7] == True:
                            sql = "SELECT max(id) FROM pedido_item_arquivos WHERE url_imagem = '"+item[3]+"'"
                            myZangraf.execute(sql)
                            id_arquivo = myZangraf.fetchone()[0]

                            sql  = "INSERT INTO recortes(id_arquivo, created_at, updated_at) "
                            sql += "VALUES("+str(id_arquivo)+", now(), now())"
                            myZangraf.execute(sql)
                    
                        mydbZangraf.commit()
                    except:
                        mydbZangraf.rollback()

            if len(lista_arquivos) > 0:
                sql = "UPDATE pedido_items SET data_importacao = now() WHERE id = "+str(row[0])
                myZangraf.execute(sql)
                mydbZangraf.commit()
        
    
    except:
        pastas = None

    sql = "UPDATE pedido_items SET tentativa_importar = "+str(row[9]+1)+" WHERE id = "+str(row[0])
    myZangraf.execute(sql)
    mydbZangraf.commit()



time.sleep(60)
