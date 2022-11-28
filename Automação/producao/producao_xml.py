from threading import Thread
from datetime import datetime
#from shutil import copyfile
from PIL import Image

import xml.etree.cElementTree as ET
import mysql.connector
import shutil
import time
import os

#dpi = 11.811023622047244
dpi = 12

margem_proporcao = 0.1
vCPU = 24

#root = "/brutos/Sistema/teste"
root = ""
root_fotos = "/brutos/Sistema/fotos"
root_thumbs = "/brutos/Sistema/thumbs"
root_servicos = "/brutos/Sistema/servicos"
arquivo_log = "/var/log/producao/producao.log"

##### CONEXÕES COM BANCO DE DADOS #####
mydbSax = mysql.connector.connect(
	host="192.168.10.178",
	user="root",
	password="1234",
	database="zangraf_xkey_principal"
)
mySax = mydbSax.cursor()

mydbZangraf = mysql.connector.connect(
    host="192.168.10.160",
    user="root",
    password="1234",
    database="producao"
)
myZangraf = mydbZangraf.cursor()



def ExportarXML():
    global mydbZangraf
    global myZangraf
    global mydbSax
    global mySax

    #                0             1               2               3              4           5
    sql  = "SELECT DISTINCT p.id , p.id_externo os, p.tipo_contrato, p.data_entrada, p.id_cliente, p.cliente, "
    #        6          7               8                   9               10
    sql += "i.id, i.id_externo op, i.quantidade, i.id_produto_externo, i.corrigir, "
    #                                                           11
    sql += "if((r.largura + r.sangr_dir + r.sangr_esq) > (r.altura + r.sangr_sup + r.sangr_inf), 'PAISAGEM', if((r.largura + r.sangr_dir + r.sangr_esq) < (r.altura + r.sangr_sup + r.sangr_inf), 'RETRATO', 'QUADRADO')) orientacao, "
    #               12                      13              14                    15                      16          17
    sql += "it.titulo imp_tipo, ino.titulo imp_nome, upper(r.disposicao), h.titulo hotfolder, s.titulo substrato, l.codigo album "

    sql += "FROM pedidos p JOIN pedido_items i ON p.id = i.id_pedido JOIN produtos r ON r.id = i.id_produto JOIN pedido_albums l ON i.id = l.id_item "
    sql += "LEFT JOIN imposicao_tipos it ON it.id = r.id_imposicao_tipo LEFT JOIN imposicao_nomes ino ON ino.id = r.id_imposicao_nome LEFT JOIN impressao_hotfolders h ON h.id = r.id_impressao_hotfolder LEFT JOIN impressao_substratos s ON s.id = r.id_impressao_substrato "
    sql += "WHERE i.exportar_xml = 1 AND i.data_envio_impressao IS NOT NULL ORDER BY l.codigo"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    strct = None
    album = None
    albuns = myZangraf.rowcount

    for row in result:
        if album != row[17]:
            album = row[17]

            #                   0           1           2           3               4               5           6              7                8
            sql  = "SELECT cli.codigo, cli.apelido, orc.codigo, orc.contato, orc.tipo_pedido, po.sequencia, prd.codigo, prd.descricao, prd.descr_reduz "
            sql += "FROM zangraf_xkey_principal.cad_orca orc "
            sql += "JOIN zangraf_xkey_publico.cad_clie cli ON cli.codigo = orc.cliente "
            sql += "JOIN zangraf_xkey_principal.pro_orca po ON po.orcamento = orc.codigo "
            sql += "JOIN zangraf_xkey_publico.cad_prod prd ON prd.codigo = po.produto "
            sql += "WHERE po.orcamento = "+str(row[1])+" AND po.produto = "+str(row[9])
            mySax.execute(sql)
            strct = mySax.fetchone()

            if strct != None:
                xml = ET.Element("ordemProducao")
                ET.SubElement(xml, "dataCadastro").text = str(row[3])
                ET.SubElement(xml, "numero").text = str(row[7])
                ET.SubElement(xml, "orcamento").text = str(row[1])
                ET.SubElement(xml, "descricao").text = str(row[7])
                ET.SubElement(xml, "quantidade").text = str(row[8])
                
                if strct[4] == 1:
                    path = "/["+str(strct[0])+"]"+str(strct[1])+"/["+str(strct[0])+"]["+str(strct[2])+"]"+str(strct[3])+"/["+str(strct[5])+"]"+str(strct[7])
                    ET.SubElement(xml, "path").text = path
                    ET.SubElement(xml, "curso").text = "CONTRATO"
                else:
                    path = "/["+str(strct[0])+"]"+str(strct[1])+"/"+str(strct[2])+"/["+str(strct[5])+"]"+str(strct[7])
                    ET.SubElement(xml, "path").text = path
                    ET.SubElement(xml, "curso").text = "PEDIDO"
                
                
                cliente = ET.SubElement(xml, "cliente")
                ET.SubElement(cliente, "codigo").text = str(row[4])
                ET.SubElement(cliente, "nome").text = str(row[5])

                produto = ET.SubElement(xml, "produto")
                ET.SubElement(produto, "codigo").text = str(strct[6])
                ET.SubElement(produto, "sequencia").text = str(strct[5])
                ET.SubElement(produto, "copia").text = "1"
                ET.SubElement(produto, "nome").text = str(strct[7])
                ET.SubElement(produto, "descReduz").text = str(strct[8])
                '''
                if albuns == 1:
                    ET.SubElement(produto, "tipo").text = "ARQUIVO"
                    arquivos = ET.SubElement(produto, "arquivos")
                    files = os.listdir('/producao'+path+'/001')
                    for file in files:
                        try:
                            imagem = Image.open('/producao'+path+'/001/'+file)
                            ET.SubElement(arquivos, "arquivo", copia="1").text = '/001/'+file
                        except:
                            imagem = None

                else:
                '''
                ET.SubElement(produto, "tipo").text = "DIRETORIO"
                arquivos = ET.SubElement(produto, "arquivos")
                ET.SubElement(arquivos, "arquivo", copia="1").text = str(row[17])+"/"
                
                
                processo = ET.SubElement(xml, "processo")
                if row[10] == 1:
                    ET.SubElement(processo, "correcao", fonte="Digital Input").text = "true"
                else:
                    ET.SubElement(processo, "correcao", fonte="Digital Input").text = "false"

                ET.SubElement(processo, "imposicao").text = "true"
                ET.SubElement(processo, "otimizacao").text = "true"
                ET.SubElement(processo, "impressao").text = "true"

                imposicao = ET.SubElement(xml, "imposicao")
                ET.SubElement(imposicao, "tipo").text = str(row[12])
                if row[13] != None:
                    ET.SubElement(imposicao, "nome").text = str(row[13])
                else:
                    ET.SubElement(imposicao, "nome").text = " "
                ET.SubElement(imposicao, "disposicao").text = str(row[14])
                ET.SubElement(imposicao, "orientacao").text = str(row[11])
                ET.SubElement(imposicao, "multPrintJob").text = "false"
                if row[15] != None:
                    ET.SubElement(imposicao, "hotfolder").text = str(row[15])
                else:
                    ET.SubElement(imposicao, "hotfolder").text = " "
                ET.SubElement(imposicao, "substrato").text = str(row[16])
                ET.SubElement(imposicao, "secagem").text = "false"
                ET.SubElement(imposicao, "rotacao").text = " "
                
                propriedades = ET.SubElement(xml, "propriedades")
                ET.SubElement(propriedades, "rotacionar").text = "false"
                ET.SubElement(propriedades, "prioridade").text = "Medium"

                if strct[4] == 1:
                    #contrato
                    if albuns == 1:
                        path_xml = "/robo/FLUXO_ENTRADA/"+str(strct[0])+"_"+str(row[1])+"_"+str(strct[5])+"_"+str(strct[3])+"_001.xml"
                    else:
                        path_xml = "/robo/FLUXO_ENTRADA/"+str(strct[0])+"_"+str(row[1])+"_"+str(strct[5])+"_"+str(strct[3])+"_"+str(row[17])+".xml"
                    
                else:
                    #pedido
                    if albuns == 1:
                        path_xml = "/robo/FLUXO_ENTRADA/"+str(strct[0])+"_PEDIDO_"+str(strct[5])+"_"+str(row[8])+"_001.xml"
                    else:
                        path_xml = "/robo/FLUXO_ENTRADA/"+str(strct[0])+"_PEDIDO_"+str(strct[5])+"_"+str(row[8])+"_"+str(row[17])+".xml"
                
                tree = ET.ElementTree(xml)
                tree.write(path_xml)
                path_xml = None

                
                sql = "UPDATE pedido_items SET exportar_xml = 0 WHERE id = " + str(row[6])
                myZangraf.execute(sql)
                mydbZangraf.commit()
                



ExportarXML()
