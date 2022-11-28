from datetime import datetime
from threading import Thread
from PIL import Image

import xml.etree.cElementTree as ET
import mysql.connector
import shutil
import time
import os

mydbZangraf = mysql.connector.connect(
    host="192.168.10.50",
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

root_path = '/root/automacao/'
xml_impostrip = root_path + "gestao_software_legado/impostrip/"
jdf_dfe = root_path + "gestao_software_legado/jdf/"
txt_correcao = root_path + "gestao_software_legado/correcao/"

vCPU = 48



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
    destino_tmp = "/home/administrador/gestao_software_legado/tmp/"

    try:
        arquivos = os.listdir(txt_correcao)
    except:
        arquivos = []
        
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'TXTCorrecao','Erro ao mapear os arquivos em "+str(txt_correcao)+"')"
        myZangraf.execute(sql)
        mydbZangraf.commit()

    for arquivo in arquivos:
        try:
            shutil.copyfile(txt_correcao + arquivo, destino_tmp + arquivo)
            shutil.copyfile(txt_correcao + arquivo, destino + arquivo)
            os.remove(txt_correcao + arquivo)
        
        except:
            os.remove(destino + arquivo)

            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'EntradaCorrecao','Erro ao mover o arquivo "+str(txt_correcao + arquivo)+"')"
            myZangraf.execute(sql)
            mydbZangraf.commit()
        


# Saída Correção
def ArquivosSaidaCorrecao(path):
    global thread_cnt
    global vCPU

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
                                try:
                                    origem = path + pasta + '/' + arquivo
                                    destino = saida + arquivo.lower()
                                    
                                    shutil.copyfile(origem, destino)
                                except:
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
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'SaidaCorrecao','Erro ao mover os arquivos de "+str(origem)+" para "+str(destino)+"')"
        myZangraf.execute(sql)
        mydbZangraf.commit()


def SaidaCorrecao():
    path_116 = '/correcao/116/Contrato/'
    path_117 = '/correcao/117/Contrato/'

    #116
    ArquivosSaidaCorrecao(path_116)
    #117
    ArquivosSaidaCorrecao(path_117)


# Entrada Imposição
def Impostrip(album):
    global xml_impostrip

    try:
        #if id_album != None:
        path = '/'+str(album[4])
        if album[3] == 1:
            org_path = '/producao' + path + '/' + album[5]
            imp_path = 'X:' + path + '/' + album[5] + '/'
            #imp_path = 'W:' + path + '/' + album[5] + '/'
        else:
            org_path = '/pedido' + path + '/' + album[5]
            imp_path = 'V:' + path + '/' + album[5] + '/'
        
        arquivos = ListaJpg(org_path)
        
        #imp_path = imp_path.replace('/','\\')

        xml  = '<?xml version="1.0" encoding="UTF-8"?>\n'
        xml += '<UltimateImposition>\n'
        xml += '    <Redirection>\n'
        xml += '		<PrintJob PrintCopies="1" JobID="'+str(album[17])+'">\n'
        xml += '			<OutputPath>C:\IMpostRIP\\rootFolder\saida</OutputPath>\n'
        xml += '			<QueueName>\n'
        xml += '			    <Name>'+str(album[12])+'</Name>\n'
        xml += '			</QueueName>\n'
        xml += '			<Documents>\n'

        for arquivo in arquivos:
            xml += '				<DocFile>\n'
            xml += '					<FullPathName>'+str(imp_path + arquivo)+'</FullPathName>\n'
            xml += '				</DocFile>\n'

        xml += '			</Documents>\n'
        xml += '			<CustomValues>\n'
        xml += '				<CustomValue key="contrato" value="'+str(album[2])+'"/>\n'
        xml += '				<CustomValue key="albumID" value="'+str(album[0])+'"/>\n'
        xml += '				<CustomValue key="albumName" value="'+str(album[5])+'"/>\n'
        xml += '			</CustomValues>\n'
        xml += '		</PrintJob>\n'
        xml += '	</Redirection>\n'
        xml += '</UltimateImposition>\n'

        arq_xml = open(str(xml_impostrip + album[17])+".pdf.xml", "w")
        arq_xml.writelines(xml)
        
        return True
        
    except:
        if os.path.exists(str(xml_impostrip + album[17])+".pdf.xml"):
            os.remove(str(xml_impostrip + album[17])+".pdf.xml")
        
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'EntradaImpostrip','Erro ao criar o arquivo "+str(xml_impostrip + album[17])+".pdf.xml')"
        myZangraf.execute(sql)
        mydbZangraf.commit()

        return False
    


def XMLImpostrip():
    destino = '/impostrip/XmlInput/'

    try:
        arquivos = os.listdir(xml_impostrip)
    except:
        arquivos = []

    for arquivo in arquivos:
        try:
            shutil.copyfile(xml_impostrip + arquivo, destino + arquivo)
            os.remove(xml_impostrip + arquivo)
        except:
            os.remove(destino + arquivo)


def EntradaImposicao():
    global mydbZangraf
    global myZangraf
    
    #                   0              1                   2                  3             4           5            6               7               8
    sql  = "SELECT gsls.id, gsls.ordem_producao, gsls.ordem_servico, gsls.tipo_pedido, gsls.path, gsls.album, p.data_entrada, i.quantidade, i.id_produto_externo, "
    #               9         10                        11                      12                      13                      14                  15
    sql += "p.id_cliente, p.cliente, it.titulo imposicao_ferramenta, ie.titulo imposicao_modelo, ih.titulo hotfolder, ib.titulo substrato, upper(prd.disposicao), "
    #                                                                                   16
    sql += "if((prd.largura + prd.sangr_dir + prd.sangr_esq) > (prd.altura + prd.sangr_sup + prd.sangr_inf), 'PAISAGEM', if((prd.largura + prd.sangr_dir + prd.sangr_esq) < (prd.altura + prd.sangr_sup + prd.sangr_inf), 'RETRATO', 'QUADRADO')) orientacao, "
    #               17
    sql += "gsls.nome_album "

    sql += "FROM gsls JOIN pedido_items i ON i.id_externo = gsls.ordem_producao JOIN pedidos p ON p.id = i.id_pedido JOIN produtos prd ON prd.id = i.id_produto  "
    sql += "JOIN imposicao_tipos it ON it.id = prd.id_imposicao_tipo LEFT JOIN imposicao_nomes ie ON ie.id = prd.id_imposicao_nome "
    sql += "LEFT JOIN impressao_hotfolders ih ON ih.id = prd.id_impressao_hotfolder LEFT JOIN impressao_substratos ib ON ib.id = prd.id_impressao_substrato "
    sql += "WHERE ((gsls.correcao = 1 AND gsls.dt_correcao_saida IS NOT NULL) OR gsls.correcao = 0) AND gsls.dt_imposicao_entrada IS NULL"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    for album in result:
        
        finalizar = False
        
        #if album[11] == 'IMPOSTRIP':
        #    finalizar = Impostrip(album)
        #elif album[11] == 'JLT':
        if album[11] == 'JLT':
            
            sql  = "SELECT po.sequencia, prd.descricao, prd.descr_reduz FROM zangraf_xkey_principal.pro_orca po "
            sql += "JOIN zangraf_xkey_publico.cad_prod prd ON po.produto = prd.codigo "
            sql += "WHERE po.orcamento = "+str(album[2])+" AND prd.codigo = "+str(album[8])
            mySax.execute(sql)
            prod_row = mySax.fetchone()
            
            if prod_row != None:
                try:
                    xml = ET.Element("ordemProducao")
                    ET.SubElement(xml, "dataCadastro").text = str(album[6])
                    ET.SubElement(xml, "numero").text = str(album[1])
                    ET.SubElement(xml, "orcamento").text = str(album[2])
                    ET.SubElement(xml, "descricao").text = str(album[1])
                    ET.SubElement(xml, "quantidade").text = str(album[7])

                    path = '/'+str(album[4])

                    if path[len(path) -1] == '/':
                        path = path[:len(path) -1]
                    
                    if album[3] == 1:
                        ET.SubElement(xml, "path").text = path
                        ET.SubElement(xml, "curso").text = "CONTRATO"
                    else:
                        ET.SubElement(xml, "path").text = path
                        ET.SubElement(xml, "curso").text = "PEDIDO"
            
            
                    cliente = ET.SubElement(xml, "cliente")
                    ET.SubElement(cliente, "codigo").text = str(album[9])
                    ET.SubElement(cliente, "nome").text = str(album[10])

                    produto = ET.SubElement(xml, "produto")
                    ET.SubElement(produto, "codigo").text = str(album[8])
                    ET.SubElement(produto, "sequencia").text = str(prod_row[0])
                    ET.SubElement(produto, "copia").text = "1"
                    ET.SubElement(produto, "nome").text = str(prod_row[1])
                    ET.SubElement(produto, "descReduz").text = str(prod_row[2])
            
                    ET.SubElement(produto, "tipo").text = "DIRETORIO"
                    arquivos = ET.SubElement(produto, "arquivos")
                    ET.SubElement(arquivos, "arquivo", copia="1").text = str(album[5])+"/"
            
            
                    processo = ET.SubElement(xml, "processo")
                    #ET.SubElement(processo, "correcao", fonte="Digital Input").text = "false"
                    ET.SubElement(processo, "imposicao").text = "true"
                    ET.SubElement(processo, "otimizacao").text = "true"
                    ET.SubElement(processo, "impressao").text = "true"

                    imposicao = ET.SubElement(xml, "imposicao")
                    ET.SubElement(imposicao, "tipo").text = str(album[11])
                    if album[12] != None:
                        ET.SubElement(imposicao, "nome").text = str(album[12])
                    else:
                        ET.SubElement(imposicao, "nome").text = " "
                    ET.SubElement(imposicao, "disposicao").text = str(album[15])
                    ET.SubElement(imposicao, "orientacao").text = str(album[16])
                    ET.SubElement(imposicao, "multPrintJob").text = "false"
                    if album[13] != None:
                        ET.SubElement(imposicao, "hotfolder").text = str(album[13])
                    else:
                        ET.SubElement(imposicao, "hotfolder").text = " "
                    ET.SubElement(imposicao, "substrato").text = str(album[14])
                    ET.SubElement(imposicao, "secagem").text = "false"
                    ET.SubElement(imposicao, "rotacao").text = " "
                    
                    propriedades = ET.SubElement(xml, "propriedades")
                    ET.SubElement(propriedades, "rotacionar").text = "false"
                    ET.SubElement(propriedades, "prioridade").text = "Medium"

                    path_xml = root_path + "print_verso/xml/"+str(album[17])+".xml"

                    tree = ET.ElementTree(xml)
                    tree.write(path_xml)
                    path_xml = None

                    finalizar = True
                except:
                    sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'EntradaPrintVerso','Erro ao criar o arquivo "+str(path_xml)+".xml')"
                    myZangraf.execute(sql)
                    mydbZangraf.commit()
            
        #else:
        #    finalizar = True

        #atualiza banco de dados
        if finalizar == True:
            sql = "UPDATE gsls SET dt_imposicao_entrada = now() WHERE id = "+str(album[0])
            myZangraf.execute(sql)
            mydbZangraf.commit()
        

def ImpFotos2Oris():
    global root_path

    origem = root_path + 'local_imp/imp_fotos/saida/'
    destino = '/oris/entrada/'
    
    try:
        arquivos = ListaPdf(origem)
    except:
        arquivos = []
    
    for arquivo in arquivos:
        try:
            shutil.copyfile(origem + arquivo, destino + arquivo)
            os.remove(origem + arquivo)
        except:
            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'ImpFotos2Oris','Erro ao mover o arquivo "+str(arquivo)+"')"
            myZangraf.execute(sql)
            mydbZangraf.commit()


def PrintVerso2Oris():
    global root_path
    
    origem = root_path + 'print_verso/saida/'
    destino = '/oris/entrada/'
    
    try:
        arquivos = ListaPdf(origem)
    except:
        arquivos = []
    
    for arquivo in arquivos:
        try:
            shutil.copyfile(origem + arquivo, destino + arquivo)
            os.remove(origem + arquivo)
        except:
            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'PrintVersoOris','Erro ao mover o arquivo "+str(arquivo)+"')"
            myZangraf.execute(sql)
            mydbZangraf.commit()


def LocalImp2Oris():
    global root_path

    origem = root_path + 'local_imp/saida/'
    destino = '/oris/entrada/'
    
    try:
        arquivos = ListaPdf(origem)
    except:
        arquivos = []
    
    for arquivo in arquivos:
        try:
            shutil.copyfile(origem + arquivo, destino + arquivo)
            os.remove(origem + arquivo)
        except:
            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'LocalImp2Oris','Erro ao mover o arquivo "+str(arquivo)+"')"
            myZangraf.execute(sql)
            mydbZangraf.commit()


# Saída Imposição
def SaidaImposicao():
    global mydbZangraf
    global myZangraf

    try:
        path = '/oris/saida/'
        arquivos = ListaPdf(path)

        #                    0            1                 2            3           4         5          6
        sql  = "SELECT gsls.id, gsls.nome_album, gsls.tipo_pedido, gsls.path, gsls.album, ihf.titulo, isu.titulo "
        sql += "FROM gsls JOIN pedido_items i ON i.id_externo = gsls.ordem_producao JOIN produtos prd ON prd.id = i.id_produto "
        sql += "JOIN impressao_hotfolders ihf ON ihf.id = prd.id_impressao_hotfolder JOIN impressao_substratos isu ON isu.id = prd.id_impressao_substrato "
        sql += "WHERE gsls.dt_imposicao_entrada IS NOT NULL AND gsls.dt_imposicao_saida IS NULL"
        myZangraf.execute(sql)
        result = myZangraf.fetchall()

        for album in result:
            
            for arquivo in arquivos:
                strct = arquivo.split('.')
                
                #if arquivo.find(album[1]) >= 0:
                if strct[0] == album[1]:
                    
                    if album[2] == 1:
                        destino = '/producao/'
                    else:
                        destino = '/pedido/'
                    
                    origem = path + arquivo
                    destino += album[3] + '/' + album[1] + '.pdf'
                    try:
                        if os.path.exists(destino):
                            os.remove(destino)
                        
                        shutil.copyfile(origem, destino)
                        os.remove(origem)

                        #atualiza banco de dados
                        sql = "UPDATE gsls SET dt_imposicao_saida = now() WHERE id = "+str(album[0])
                        myZangraf.execute(sql)
                        mydbZangraf.commit()
                    except:
                        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'SaidaImposicao','Erro ao mover o arquivo de "+origem+" para "+destino+"')"
                        myZangraf.execute(sql)
                        mydbZangraf.commit()
                
    except:
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'SaidaImposicao','Erro ao listar os arquivos da saída do Oris')"
        myZangraf.execute(sql)
        mydbZangraf.commit()


# Impressão
def Impressao():
    global mydbZangraf
    global myZangraf
    global jdf_dfe

    #                    0            1                 2            3           4         5          6         7
    sql  = "SELECT gsls.id, gsls.nome_album, gsls.tipo_pedido, gsls.path, gsls.album, ihf.titulo, isu.titulo, i.copias "
    sql += "FROM gsls JOIN pedido_items i ON i.id_externo = gsls.ordem_producao JOIN produtos prd ON prd.id = i.id_produto "
    sql += "JOIN impressao_hotfolders ihf ON ihf.id = prd.id_impressao_hotfolder JOIN impressao_substratos isu ON isu.id = prd.id_impressao_substrato "
    sql += "WHERE gsls.dt_imposicao_saida IS NOT NULL AND gsls.dt_impressao_entrada IS NULL"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    for album in result:
        data_jdf = str(datetime.now().date())+'T'+str(datetime.now().time()).split('.')[0]

        if album[2] == 1:
            path = '/producao/'
        else:
            path = '/pedido/'

        path += album[3] + '/' + album[1] + '.pdf'
        
        jdf  = '<?xml version="1.0" encoding="UTF-8"?>\n'
        jdf += '<JDF xmlns="http://www.CIP4.org/JDFSchema_1_1" Type="Combined" ID="rootNodeId" Status="Waiting" JobPartID="000.cdp.797" Version="1.3" Types="DigitalPrinting" DescriptiveName="'+str(album[1])+'">\n'
        jdf += '    <AuditPool><Created AgentName="Piovelli - HP DFE JDF Configurator" AgentVersion="8" TimeStamp="'+str(data_jdf)+'"/></AuditPool>\n'
        jdf += '    <Comment Name="JobSpec">'+str(album[5])+'</Comment>\n'
        jdf += '    <NodeInfo JobPriority="50"/><ResourcePool>\n'
        jdf += '      <Media Class="Consumable" ID="M001" Status="Available" DescriptiveName="'+str(album[6])+'"/>\n'
        jdf += '      <DigitalPrintingParams Class="Parameter" ID="DPP001" Status="Available" PageDelivery="ReverseOrderFaceUp"><MediaRef rRef="M001"/></DigitalPrintingParams>\n'
        jdf += '      <RunList ID="RunList_1" Status="Available" Class="Parameter">\n'
        jdf += '            <LayoutElement>\n'
        jdf += '               <FileSpec MimeType="application/pdf" URL="FILE://192.168.10.12' + path + '"/>\n'
        jdf += '            </LayoutElement>\n'
        jdf += '      </RunList>\n'
        jdf += '      <Component ID="Component" ComponentType="FinalProduct" Status="Unavailable" Class="Quantity" Amount="'+str(album[7])+'"/>\n'
        #jdf += '   <Device Class="Implementation" ID="D001" Status="Available"><GeneralID IDUsage="QueueDestination" IDValue="Held"/></Device><LayoutPreparationParams Class="Parameter" ID="LPP001" Status="Available" Sides="TwoSidedFlipY"/></ResourcePool>\n'
        jdf += '   <Device Class="Implementation" ID="D001" Status="Available"><GeneralID IDUsage="QueueDestination" IDValue="Held"/></Device></ResourcePool>\n'
        jdf += '   <ResourceLinkPool>\n'
        jdf += '      <MediaLink rRef="M001" Usage="Input"/>\n'
        jdf += '      <DigitalPrintingParamsLink rRef="DPP001" Usage="Input"/>\n'
        jdf += '      <RunListLink rRef="RunList_1" Usage="Input"/>\n'
        jdf += '      <ComponentLink Usage="Output" rRef="Component"/>\n'
        jdf += '   <DeviceLink rRef="D001" Usage="Input"/><LayoutPreparationParamsLink rRef="LPP001" Usage="Input"/></ResourceLinkPool>\n'
        jdf += '</JDF>\n'

        try:
            arq_xml = open(jdf_dfe + album[1] + '.jdf', "w")
            arq_xml.writelines(jdf)

            #atualiza banco de dados
            sql = "UPDATE gsls SET dt_impressao_entrada = now() WHERE id = "+str(album[0])
            myZangraf.execute(sql)
            mydbZangraf.commit()
        except:
            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'Impressao','Erro ao criar o arquivo "+str(jdf_dfe + album[1])+".jdf')"
            myZangraf.execute(sql)
            mydbZangraf.commit()


def JDF2DFE():
    try:
        arquivos = os.listdir(jdf_dfe)
    except:
        arquivos = []

    for arquivo in arquivos:
        try:
            shutil.copyfile(jdf_dfe + arquivo, '/jobs/jdf/' + arquivo)
            os.remove(jdf_dfe + arquivo)
        except:
            sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'PrintVersoOris','Erro ao mover o arquivo de "+str(jdf_dfe + arquivo)+" para "+'/jobs/jdf/' + arquivo+"')"
            myZangraf.execute(sql)
            mydbZangraf.commit()



myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'gsl'")
mydbZangraf.commit()


sql = "SELECT ativo FROM gsl_configs ORDER BY id"
myZangraf.execute(sql)
result = myZangraf.fetchall()

# Correção
'''
if result[0][0] == 1:
    EntradaCorrecao()
    TXTCorrecao()
    SaidaCorrecao()
'''

# Imposição
if result[1][0] == 1:
    EntradaImposicao()
    #XMLImpostrip()
    ImpFotos2Oris()
    PrintVerso2Oris()
    LocalImp2Oris()
    SaidaImposicao()


# Impressão
if result[2][0] == 1:
    Impressao()
    JDF2DFE()


myZangraf.close()
mydbZangraf.close()

mySax.close()
mydbSax.close()

time.sleep(10)