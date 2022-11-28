from datetime import datetime
from threading import Thread
from PIL import Image

import xml.etree.cElementTree as ET
import mysql.connector
import shutil
import time
import os

mydbZangraf = mysql.connector.connect(
    host="localhost",
    user="root",
    password="1234",
    database="producao"
)
myZangraf = mydbZangraf.cursor()

mydbSax = mysql.connector.connect(
	host="localhost",
	user="root",
	password="1234",
	database="zangraf_xkey_principal"
)
mySax = mydbSax.cursor()

txt_correcao = "/root/automacao/correcao/correcao/"


def ListaJpg(path):
    lista = []
    try:
        arquivos = os.listdir(path.replace('//', '/'))
        for arquivo in arquivos:
            strct = arquivo.split('.')
            if len(strct) >= 2 and strct[1].lower() == 'jpg':
                lista.append(arquivo)
    except:
        lista = []

    return lista


def ListaPdf(path):
    lista = []
    arquivos = os.listdir(path)

    for arquivo in arquivos:
        strct = arquivo.split('.')
        if len(strct) >= 2 and strct[len(strct)-1].lower() == 'pdf':
            lista.append(arquivo)
    
    return lista


# Entrada Correção
def EntradaCorrecao():
    global txt_correcao
    global mydbZangraf
    global myZangraf

    #               0       1           2               3               4       5      6        7          8
    sql  = "SELECT id, nome_album, ordem_servico, ordem_producao, tipo_pedido, path, album, quantidade, cliente "
    sql += "FROM gsls WHERE correcao = 1 AND dt_correcao_entrada IS NULL"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()
    
    for row in result:
        salvar = True

        if row[4] != 1:
            try:
                conteudo  = "[Order]\n"
                conteudo += "OrderId="+row[1]+"\n"
                conteudo += "CustomerName="+row[8]+"\n"
                conteudo += "Address=\n"
                conteudo += "Phone=\n"
                conteudo += "Email=\n"
                conteudo += "Rush=0\n"
                conteudo += "Source=Digital Input\n"
                conteudo += "Product=Contrato\n"
                
                path = "/pedido/" + str(row[5]) + str(row[6])
                arquivos = os.listdir(path)
                
                for arquivo in arquivos:
                    ext = arquivo.split('.')
                    if ext[len(ext)-1].lower() == 'jpg' or ext[len(ext)-1].lower() == 'jpeg':

                        conteudo += "[Neg]\n"
                        conteudo += "NegNumber=V:\\\\"+str(row[5]).replace('/','\\') + str(row[6]) + '\\' + arquivo + "\n"
                        conteudo += "Retouch=0\n"
                        conteudo += "Orient=0\n"
                        conteudo += "Crop=\n"
                        conteudo += "Optimize=N\n"
                
                conteudo += "[Unit]\n"
                conteudo += "Code=\n"
                conteudo += "Qty=1\n"
                conteudo += "Color=C\n"
                
                file = open(txt_correcao + row[1]+'.txt', 'w')
                file.writelines(conteudo)
                file.close()

            except:
                salvar = False
                
                if os.path.exists(txt_correcao + row[1]+'.txt'):
                    os.remove(txt_correcao + row[1]+'.txt')
                
                sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'EntradaCorrecaoTxt','Erro ao criar o arquivo "+str(txt_correcao + row[1]+'.txt')+"')"
                myZangraf.execute(sql)
                mydbZangraf.commit()
        
        if salvar:
            sql = "UPDATE gsls SET dt_correcao_entrada = now() WHERE id = "+str(row[0])
            myZangraf.execute(sql)
            mydbZangraf.commit()
        

def TXTCorrecao():
    global txt_correcao

    destino = '/correcao/117/ADPCPrints/'
    #destino_tmp = "/home/administrador/gestao_software_legado/tmp/"

    try:
        arquivos = os.listdir(txt_correcao)
    except:
        arquivos = []
        
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'TXTCorrecao','Erro ao mapear os arquivos em "+str(txt_correcao)+"')"
        myZangraf.execute(sql)
        mydbZangraf.commit()

    for arquivo in arquivos:
        try:
            #shutil.copyfile(txt_correcao + arquivo, destino_tmp + arquivo)
            shutil.copyfile(txt_correcao + arquivo, destino + arquivo)
            os.remove(txt_correcao + arquivo)
        
        except:
            os.remove(destino + arquivo)

            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'EntradaCorrecao','Erro ao mover o arquivo "+str(txt_correcao + arquivo)+"')"
            myZangraf.execute(sql)
            mydbZangraf.commit()
        


# Saída Correção
def ArquivosSaidaCorrecao(path):
    try:
        pastas = os.listdir(path)

        for pasta in pastas:
            ##### Organiza as pastas #####
            if pasta[0] == 'A':
                arquivos = os.listdir(path + pasta + '/Contrato/')
                
                for arquivo in arquivos:
                    
                    strct = arquivo.split('_')
                    nome = strct[-1]
                    strct = strct[0:len(strct)-1]
                    diretorio = '_'.join(strct)
                    
                    if not os.path.exists(path + diretorio):
                        os.mkdir(path + diretorio)
                    
                    #shutil.move(path + pasta + '/Contrato/' + arquivo, path + diretorio + '/' + nome.lower())
                    shutil.move(path + pasta + '/Contrato/' + arquivo, path + diretorio + '/' + arquivo.lower())

            else:
                if os.path.exists(path + pasta + '/Contrato/'):
                    arquivos = os.listdir(path + pasta + '/Contrato/')
                    if len(arquivos) > 0:
                        for arquivo in arquivos:
                            os.rename(path + pasta + '/Contrato/' + arquivo, path + pasta + '/' + arquivo.lower())
                
                        
                ##### Busca por álbum #####
                #               0       1           2               3               4       5      6        7          8
                sql  = "SELECT id, nome_album, ordem_servico, ordem_producao, tipo_pedido, path, album, quantidade, cliente "
                sql += "FROM gsls WHERE correcao = 1 AND dt_correcao_entrada IS NOT NULL AND dt_correcao_saida IS NULL "
                myZangraf.execute(sql)
                result = myZangraf.fetchall()
                
                for row in result:
                    if pasta.find(row[1]) >= 0:
                        #saida = PastaSaida(row)
                        
                        # informa data e hora do início do processo
                        sql = "UPDATE gsls SET dt_processo_saida_correcao = now() WHERE id = "+str(row[0])
                        myZangraf.execute(sql)
                        mydbZangraf.commit()
            
                        p = ''
                        strct = row[5].split('/')
                        for s in strct:
                            p += '/'+s.strip()

                        if row[4] == 1:
                            saida = '/producao/' + p + '/' + str(row[6]) + '/'
                        else:
                            saida = '/pedido/' + p + '/' + str(row[6]) + '/'
                        
                        arq_saida = ListaJpg(saida)
                        arquivos = ListaJpg(path + pasta)
                        
                        # verifica se a quantidade corrigida corresponde a quantidade de fotos no pedido
                        if len(arquivos) >= len(arq_saida):
                            erro = False

                            # move arquivos
                            for arquivo in arquivos:
                                origem = path + pasta + '/' + arquivo
                                destino = saida + arquivo.lower()
                                
                                sucesso = False
                                cnt_trying = 0
                                while cnt_trying < 3:
                                    try:
                                        shutil.copyfile(origem, destino)
                                        cnt_trying = 3
                                        sucesso = True
                                    except:
                                        None
                                    cnt_trying += 1
                                
                                if not sucesso:
                                    sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'SaidaCorrecao','Erro ao mover o arquivo de "+str(origem)+" para "+str(destino)+"')"
                                    myZangraf.execute(sql)
                                    mydbZangraf.commit()
                                    erro = True
                            
                            if not erro:
                                #atualiza banco de dados
                                sql = "UPDATE gsls SET dt_correcao_saida = now() WHERE id = "+str(row[0])
                                myZangraf.execute(sql)
                                mydbZangraf.commit()

                                shutil.rmtree(path + pasta)

                            #copiar do produção para o arquivo


    except:
        None
        # sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'SaidaCorrecao','Erro ao mover os arquivos de "+str(origem)+" para "+str(destino)+"')"
        # myZangraf.execute(sql)
        # mydbZangraf.commit()


def SaidaCorrecao():
    path_116 = '/correcao/116/Contrato/'
    path_117 = '/correcao/117/Contrato/'
    path_118 = '/correcao/118/Contrato/'

    #116
    ArquivosSaidaCorrecao(path_116)
    #117
    ArquivosSaidaCorrecao(path_117)
    #118
    ArquivosSaidaCorrecao(path_118)




sql = "SELECT ativo FROM gsl_configs ORDER BY id"
myZangraf.execute(sql)
result = myZangraf.fetchall()


# Correção
if result[0][0] == 1:
    EntradaCorrecao()
    TXTCorrecao()
    SaidaCorrecao()




myZangraf.close()
mydbZangraf.close()

mySax.close()
mydbSax.close()

time.sleep(30)