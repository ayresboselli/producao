from threading import Thread

import mysql.connector
import shutil
import time
import os

root = ""
root_fotos = "/sistema/fotos"
root_thumbs = "/sistema/thumbs"
root_servicos = "/sistema/servicos"


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


vCPU = 48


thread_erro = 0
thread_cnt = 0
class ExportarImagem(Thread):
    def __init__ (self, origem, destino, tipo):
        Thread.__init__(self)
        self.origem = origem
        self.destino = destino
        self.tipo = tipo
    
    def run(self):
        global root
        global thread_erro
        global thread_cnt

        try:
            if self.tipo == 1:
                shutil.copyfile(self.origem, root+'/producao/'+self.destino)
            else:
                shutil.copyfile(self.origem, root+'/pedido/'+self.destino)
        
        except:
            thread_erro += 1

            thdMydbZangraf = mysql.connector.connect(
                host="192.168.10.50",
                user="root",
                password="1234",
                database="producao",
                raise_on_warnings=True
            )
            thdMyZangraf = thdMydbZangraf.cursor()

            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'ExportarProducao','Erro ao exportar o arquivo de "+str(self.origem)+" para "+str(self.destino)+"')"
            thdMyZangraf.execute(sql)
            thdMydbZangraf.commit()
            thdMydbZangraf.close()
        
        thread_cnt -= 1


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
        directory += '/'+pasta.strip()
        if not os.path.exists(directory):
            os.mkdir(directory)


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


#               0              1                2               3              4          5                       6           7             8
sql  = "SELECT i.id, p.id_externo os, p.tipo_contrato, i.id_externo op, p.id_cliente, p.cliente, r.id_externo id_produto, r.renomear, i.corrigir "
sql += "FROM pedidos p JOIN pedido_items i ON p.id = i.id_pedido JOIN produtos r ON r.id = i.id_produto "
sql += "WHERE i.imprimir = 1 AND i.data_envio_impressao IS NULL "
myZangraf.execute(sql)
itens = myZangraf.fetchall()

for item in itens:
    thread_erro = 0
    
    try:
        #                   0           1           2           3               4               5           6              7                8
        sql  = "SELECT cli.codigo, cli.apelido, orc.codigo, orc.contato, orc.tipo_pedido, po.sequencia, prd.codigo, prd.descricao, prd.descr_reduz "
        sql += "FROM zangraf_xkey_principal.cad_orca orc "
        sql += "JOIN zangraf_xkey_publico.cad_clie cli ON cli.codigo = orc.cliente "
        sql += "JOIN zangraf_xkey_principal.pro_orca po ON po.orcamento = orc.codigo "
        sql += "JOIN zangraf_xkey_publico.cad_prod prd ON prd.codigo = po.produto "
        sql += "WHERE po.orcamento = "+str(item[1])+" AND po.produto = "+str(item[6])
        mySax.execute(sql)
        strct = mySax.fetchone()

        if strct != None:
            
            # informa data e hora do início do processo
            sql = "UPDATE pedido_items SET dt_processo_envio_impressao = now() WHERE id = "+str(item[0])
            myZangraf.execute(sql)
            mydbZangraf.commit()
            

            sql = "SELECT id, codigo FROM pedido_albums WHERE id_item = "+str(item[0])
            myZangraf.execute(sql)
            albuns = myZangraf.fetchall()

            for album in albuns:
                path_base = "["+str(strct[0])+"]"+str(strct[1])+"/"+str(item[1])+"/["+str(strct[5])+"]"+str(strct[7])+"/"+str(album[1])+"/"
                
                if item[2] == 1: #Contrato
                    MakeDirectory('/producao/'+path_base)
                else:
                    MakeDirectory('/pedido/'+path_base)
                

                sql = "SELECT url_imagem FROM pedido_item_arquivos WHERE id_album = "+str(album[0])+" ORDER BY nome_arquivo"
                myZangraf.execute(sql)
                fotos = myZangraf.fetchall()

                cnt = 0
                #for foto in fotos:
                while cnt < len(fotos):

                    # if item[7] == 0:
                    #     nome = fotos[cnt][0].split('/')
                    #     nome_file = str(nome[len(nome)-1]).replace('_',' ')

                    #     filename = str(strct[0])+'_'+str(strct[2])+'_'+str(strct[5])+'_'+str(item[3])+'_'+str(album[1])+'_'+nome_file
                    # else: 
                    #     filename = str(strct[0])+'_'+str(strct[2])+'_'+str(strct[5])+'_'+str(item[3])+'_'+str(album[1])+'_'+Add0(cnt+1)+'.jpg'
                        
                    # origem = '/sistema/fotos/'+fotos[cnt][0]
                    # destino = path_base+filename

                    # if item[2] == 1:
                    #     shutil.copyfile(origem, root+'/producao/'+destino)
                    # else:
                    #     shutil.copyfile(origem, root+'/pedido/'+destino)
                    

                    if thread_cnt < vCPU:
                        if item[7] == 0:
                            nome = fotos[cnt][0].split('/')
                            nome_file = str(nome[len(nome)-1]).replace('_',' ')

                            filename = str(strct[0])+'_'+str(strct[2])+'_'+str(strct[5])+'_'+str(item[3])+'_'+str(album[1])+'_'+nome_file
                        else: 
                            filename = str(strct[0])+'_'+str(strct[2])+'_'+str(strct[5])+'_'+str(item[3])+'_'+str(album[1])+'_'+Add0(cnt+1)+'.jpg'
                        
                        origem = '/sistema/fotos/'+fotos[cnt][0]
                        destino = path_base+filename
                        
                        thread = ExportarImagem(origem, destino, item[2])
                        thread.start()

                        cnt += 1
                        thread_cnt += 1
                    
                    
                


                path_fluxo = "["+str(strct[0])+"]"+str(strct[1])+"/"+str(item[1])+"/["+str(strct[5])+"]"+str(strct[7])+"/"
                album_name = str(strct[0])+'_'+str(strct[2])+'_'+str(strct[5])+'_'+str(item[3])+'_'+str(album[1])

                sql = "SELECT id FROM gsls WHERE nome_album = '"+album_name+"'"
                myZangraf.execute(sql)
                albuns_gsl = myZangraf.fetchall()

                for alb_gsl in albuns_gsl:
                    myZangraf.execute("DELETE FROM gsls WHERE id = "+str(alb_gsl[0]))

                sql  = "INSERT INTO gsls (id_pedido_item, nome_album, cliente, ordem_servico, ordem_producao, tipo_pedido, path, album, quantidade, correcao) "
                sql += "VALUES ('"+str(item[0])+"', '"+album_name+"', '"+str(strct[1])+"', "+str(item[1])+", '"+str(item[3])+"', "+str(item[2])+", '"+path_fluxo+"', '"+str(album[1])+"', "+str(cnt-1)+", "+str(item[8])+")"
                myZangraf.execute(sql)

            if thread_erro == 0:
                sql = "UPDATE pedido_items SET data_envio_impressao = now() WHERE id = "+str(item[0])
                myZangraf.execute(sql)

                mydbZangraf.commit()
            else:
                mydbZangraf.rollback()

    except:
        mydbZangraf.rollback()

        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'ExportarProducao','Erro ao exportar os arquivos da OP "+str(item[3])+"')"
        myZangraf.execute(sql)
        mydbZangraf.commit()
    

time.sleep(60)
