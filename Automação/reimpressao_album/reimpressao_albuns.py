from pdf2image import pdfinfo_from_path,convert_from_path
from datetime import datetime
import xml.etree.cElementTree as ET
import mysql.connector
import img2pdf
import shutil
import time
import os

##### CONEXÕES COM BANCO DE DADOS #####
mydbSax = mysql.connector.connect(
	host="localhost",
	user="root",
	password="1234",
	database="zangraf_flow"
)
mySax = mydbSax.cursor()

mydbZangraf = mysql.connector.connect(
    host="192.168.10.50",
    user="root",
    password="1234",
    database="producao"
)
myZangraf = mydbZangraf.cursor()

tmp = None

##### FUNÇÕES #####
def Add0(val):
	val = str(val)
	if len(val) == 1:
		return '00'+str(val)
	elif len(val) == 2:
		return '0'+str(val)
	else:
		return str(val)

# lista todos os pedidos de reimpressão prontos para impressão, ainda não impressos
#               0         1          2            3             4               5           6
sql  = "SELECT rp.id, rp.titulo, rp.cliente, rp.produto, rp.ordem_producao, ih.titulo, isu.titulo "
sql += "FROM reimpressao_album_pedidos rp JOIN produtos p ON p.id_externo = rp.produto JOIN impressao_hotfolders ih ON ih.id = p.id_impressao_hotfolder JOIN impressao_substratos isu ON isu.id = p.id_impressao_substrato "
sql += "WHERE rp.imprimir = 1 AND rp.processada IS NULL "
myZangraf.execute(sql)
result = myZangraf.fetchall()

qtd_processos = len(result)
for row in result:
    try:
        #                   0                   1               2           3           4               5               6
        sql  = "SELECT p.id_externo os, p.tipo_contrato, p.id_cliente, p.cliente, p.contrato, i.id_externo op, i.id_produto_externo "
        sql += "FROM pedido_items i JOIN pedidos p ON p.id = i.id_pedido "
        sql += "WHERE i.id_externo = " + str(row[4])
        myZangraf.execute(sql)
        pedido = myZangraf.fetchone()
        
        if pedido != None:
            sql  = "SELECT por.sequencia, prd.descricao FROM zangraf_xkey_producao.producoes pdc "
            sql += "JOIN zangraf_xkey_principal.pro_orca por ON por.orcamento = pdc.cod_producao AND pdc.produto = por.produto "
            sql += "JOIN zangraf_xkey_publico.cad_prod prd ON prd.codigo = pdc.produto "
            sql += "WHERE pdc.codigo = "+str(pedido[5])+" AND pdc.produto = "+str(pedido[6])
            mySax.execute(sql)
            produto = mySax.fetchone()
            
            if pedido[1] == 1:
                path = "/producao/["+str(pedido[2])+"]"+str(pedido[3])+"/"+str(pedido[0])+"/["+str(produto[0])+"]"+str(produto[1])+"/"
            else:
                path = "/pedido/["+str(pedido[2])+"]"+str(pedido[3])+"/"+str(pedido[0])+"/["+str(produto[0])+"]"+str(produto[1])+"/"
            
            # criar pasta temporária
            tmp = '/root/automacao/reimpressao_album/tmp/'+str(pedido[5])
            if os.path.exists(tmp):
                shutil.rmtree(tmp, ignore_errors=True)

            if not os.path.exists(tmp):
                os.mkdir(tmp)
            

            sql  = "SELECT id, foto_frente, foto_verso, album "
            sql += "FROM reimpressao_album_laminas WHERE id_reimpressao = "+str(row[0])+" "
            sql += "ORDER BY album"
            myZangraf.execute(sql)
            fotos = myZangraf.fetchall()
            
            album = None
            images = []
            for foto in fotos:
                # extrai JPG do PDF
                filename = path + foto[3]+".pdf"
                
                album = filename
                try:
                    images = convert_from_path(filename, dpi=300, first_page = foto[1], last_page = foto[2])
                    if len(images) > 0:
                        images[0].save(tmp+'/'+ foto[3]+'_'+Add0(foto[1])+'.jpg', 'JPEG', dpi=(300, 300))
                    if len(images) > 1:
                        images[1].save(tmp+'/'+ foto[3]+'_'+Add0(foto[2])+'.jpg', 'JPEG', dpi=(300, 300))
                    
                    sql = "UPDATE reimpressao_album_laminas SET status = 0 WHERE id = "+str(foto[0])
                    myZangraf.execute(sql)
                    mydbZangraf.commit()
                    
                    cnt = 1
                    for image in images:
                        if cnt == foto[1] or cnt == foto[2]:
                            image.save(tmp+'/'+ foto[3]+'_'+Add0(cnt)+'.jpg', 'JPEG', dpi=(300, 300))
                        
                        cnt += 1
                        
                    
                except:
                    sql = "UPDATE reimpressao_album_laminas SET status = 1 WHERE id = "+str(foto[0])
                    myZangraf.execute(sql)
                    mydbZangraf.commit()
                
            
                    
            # compilar novo pdf
            arq_tmp = os.listdir(tmp)
            if len(arq_tmp) > 0:
                arq_tmp.sort()
                
                image_list = []
                
                for arq in arq_tmp:
                    ext = arq.split('.')
                    if len(ext) == 2 and ext[1] == 'jpg':
                        image_list.append(tmp+'/'+arq) 

                with open(path + row[1]+".pdf","wb") as f:
                    f.write(img2pdf.convert(image_list))
            
            
                #criar JDF
                data_jdf = str(datetime.now().date())+'T'+str(datetime.now().time()).split('.')[0]

                jdf = ET.Element("JDF", xmlns="http://www.CIP4.org/JDFSchema_1_1", Type="Combined", ID="rootNodeId", Status="Waiting", JobPartID="000.cdp.797", Version="1.3", Types="DigitalPrinting", DescriptiveName=row[1])
                AuditPool = ET.SubElement(jdf, "AuditPool")
                ET.SubElement(AuditPool, "Created", AgentName="Piovelli - HP DFE JDF Configurator", AgentVersion="8", TimeStamp=data_jdf)
                ET.SubElement(jdf, "Comment", Name="JobSpec").text = row[5]
                ET.SubElement(jdf, "NodeInfo", JobPriority="50")

                ResourcePool = ET.SubElement(jdf, "ResourcePool")
                ET.SubElement(ResourcePool, "Media", Class="Consumable", ID="M001", Status="Available", DescriptiveName=row[6])
                DigitalPrintingParams = ET.SubElement(ResourcePool, "DigitalPrintingParams", Class="Parameter", ID="DPP001", Status="Available", PageDelivery="ReverseOrderFaceUp")
                ET.SubElement(DigitalPrintingParams, "MediaRef", rRef="M001")
                RunList = ET.SubElement(ResourcePool, "RunList", ID="RunList_1", Status="Available", Class="Parameter")
                LayoutElement = ET.SubElement(RunList, "LayoutElement")
                ET.SubElement(LayoutElement, "FileSpec", MimeType="application/pdf", URL="FILE://192.168.10.12" + path + row[1]+".pdf")
                ET.SubElement(ResourcePool, "Component", ID="Component", ComponentType="FinalProduct", Status="Unavailable", Class="Quantity")
                Device = ET.SubElement(ResourcePool, "Device", Class="Implementation", ID="D001", Status="Available")
                ET.SubElement(Device, "GeneralID", IDUsage="QueueDestination", IDValue="Held")
                ET.SubElement(ResourcePool, "LayoutPreparationParams", Class="Parameter", ID="LPP001", Status="Available", Sides="TwoSidedFlipY")


                ResourceLinkPool = ET.SubElement(jdf, "ResourceLinkPool")
                ET.SubElement(ResourceLinkPool, "MediaLink", rRef="M001", Usage="Input")
                ET.SubElement(ResourceLinkPool, "DigitalPrintingParamsLink", rRef="DPP001", Usage="Input")
                ET.SubElement(ResourceLinkPool, "RunListLink", rRef="RunList_1", Usage="Input")
                ET.SubElement(ResourceLinkPool, "ComponentLink", Usage="Output", rRef="Component")
                ET.SubElement(ResourceLinkPool, "DeviceLink", rRef="D001", Usage="Input")
                ET.SubElement(ResourceLinkPool, "LayoutPreparationParamsLink", rRef="LPP001", Usage="Input")
                
                tree = ET.ElementTree(jdf)
                tree.write('/jobs/jdf/' + row[1] + '.jdf')


                # salvar data da impressão
                sql = "UPDATE reimpressao_album_pedidos SET processada = now() WHERE id = "+str(row[0])
                myZangraf.execute(sql)
                mydbZangraf.commit()
    
    except:
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'Reimpressão de álbuns','Erro ao reprocessar o álbum "+str(row[1])+"')"
        myZangraf.execute(sql)
        mydbZangraf.commit()
    

    # deleta pasta temporária
    if tmp != None and os.path.exists(tmp):
        shutil.rmtree(tmp, ignore_errors=True)


time.sleep(60)

myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'reimpressao_albuns'")
mydbZangraf.commit()