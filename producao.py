from threading import Thread
from datetime import datetime
from shutil import copyfile
from PIL import Image

import xml.etree.cElementTree as ET
import mysql.connector
import time
import os

dpi = 11.81102362204724
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
	password="Person!#@$",
	database="zangraf_xkey_principal"
)
mySax = mydbSax.cursor()

mydbZangraf = mysql.connector.connect(
    host="192.168.10.160",
    user="root",
    password="Person!#@$",
    database="dev_producao"
)
myZangraf = mydbZangraf.cursor()


thread_crop = 0
class RecortaImagem(Thread):

    def __init__ (self, row, mydbZangraf, myZangraf):
        Thread.__init__(self)
        self.row = row
        self.mydbZangraf = mydbZangraf
        self.myZangraf = myZangraf

    def run(self):
        global root_fotos
        global root_thumbs
        global dpi
        global thread_crop

        filename = root_fotos+'/'+self.row[5]
        img = Image.open(filename)
        
        ##### RECORTA #####
        pos_x = self.row[1]
        pos_y = self.row[2]
        larg = self.row[3]
        alt = self.row[4]
        
        if pos_x < 0 or pos_y < 0:
            (largura_img, altura_img) = img.size
            (largura_new, altura_new) = (largura_img, altura_img)
            pos_x_new = 0
            pos_y_new = 0
            
            if pos_x < 0:
                largura_new = largura_img + (pos_x * (-1))
                pos_x_new = pos_x * (-1)
                pos_x = 0
            if pos_y < 0:
                altura_new = altura_img + (pos_y * (-1))
                pos_y_new = pos_y * (-1)
                pos_y = 0
            
            if largura_img < larg:
                largura_new += larg - largura_new
            if altura_img < alt:
                altura_new += alt - altura_new
            
            im = Image.new('RGB', (largura_new, altura_new), color = 'white')
            im.paste(img, (pos_x_new, pos_y_new))
            img = im
            
        img = img.crop((pos_x, pos_y, pos_x+larg, pos_y+alt))
        
        (largura, altura) = img.size
        prod_largura = self.row[8] * dpi
        prod_altura = self.row[9] * dpi


        ##### ROTACIONA #####
        if (prod_largura > prod_altura and largura < altura) or (prod_largura < prod_altura and largura > altura):
            img.transpose(Image.ROTATE_90)
            (largura, altura) = img.size
        

        ##### REDIMENCIONA #####
        img = img.resize((int(prod_largura), int(prod_altura)), Image.ANTIALIAS)
        img.save(filename, 'JPEG', dpi=(300, 300))

        img_thumb = img.resize((int(prod_largura * 0.1), int(prod_altura * 0.1)))
        img_thumb.save(root_thumbs+'/'+self.row[5])

        sql  = "UPDATE pedido_item_arquivos SET situacao = 1, updated_at = '"+str(datetime.now())+"' "
        sql += "WHERE id = "+str(self.row[10])
        self.myZangraf.execute(sql)
        self.mydbZangraf.commit()
        
        thread_crop -= 1


thread_import = 0
class ImportaImagem(Thread):

    def __init__ (self, img, row, id_album, filename, url, id_arquivo, mydbZangraf, myZangraf):
        Thread.__init__(self)
        self.img = img
        self.row = row
        self.filename = filename
        self.url = url
        self.id_arquivo = id_arquivo
        self.mydbZangraf = mydbZangraf
        self.myZangraf = myZangraf

    def run(self):
        global root_fotos
        global root_thumbs
        global dpi
        global margem_proporcao
        global thread_import


        (largura, altura) = self.img.size
        prod_largura = self.row[3] * dpi
        prod_altura = self.row[4] * dpi


        ##### ROTACIONA #####
        if (prod_largura > prod_altura and largura < altura) or (prod_largura < prod_altura and largura > altura):
            self.img = self.img.transpose(Image.ROTATE_90)
            (largura, altura) = self.img.size
            
        
        ##### REDIMENCIONA #####
        if largura > altura:
            prop_produto = prod_altura / prod_largura
            prop_imagem = altura / largura
        else:
            prop_produto = prod_largura / prod_altura
            prop_imagem = largura / altura
        
        prop_imagem_variacao = prop_imagem * margem_proporcao

        if prop_produto > prop_imagem - prop_imagem_variacao and prop_produto < prop_imagem + prop_imagem_variacao:
            self.img = self.img.resize((int(prod_largura), int(prod_altura)), Image.ANTIALIAS)
        else:
            # cadastrar recorte
            sql  = "INSERT INTO recortes(id_arquivo, created_at, updated_at) "
            sql += "VALUES("+str(self.id_arquivo)+", now(), now())"
            self.myZangraf.execute(sql)

            sql = "UPDATE pedido_item_arquivos SET situacao = 0 WHERE id = "+str(self.id_arquivo)
            self.myZangraf.execute(sql)

            self.mydbZangraf.commit()
        

        ##### SALVA #####
        self.img.save(root_fotos + '/' + self.url, 'JPEG', dpi=(300, 300))
        
        if largura > altura:
            i = 300 / altura
        else:
            i = 300 / largura
        
        img_thumb = self.img.resize((int(largura * i), int(altura * i)), Image.ANTIALIAS)
        img_thumb.save(root_thumbs + '/' + self.url)


        thread_import -= 1


count_files = 0
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
            count_files += 1
        except:
            imagem = None
            
        # deleta self.origem
        thread_import -= 1


thread_export = 0
class ExportarImagem(Thread):

    def __init__ (self, origem, destino, tipo):
        Thread.__init__(self)
        self.origem = origem
        self.destino = destino
        self.tipo = tipo

    def run(self):
        global root
        global thread_export

        try:
            #imagem = Image.open(self.origem)

            if self.tipo == 1:
                #imagem.save(root+'/arquivo/'+self.destino, 'JPEG', dpi=(300, 300))
                #imagem.save(root+'/producao/'+self.destino, 'JPEG', dpi=(300, 300))

                copyfile(self.origem, root+'/arquivo/'+self.destino)
                copyfile(self.origem, root+'/producao/'+self.destino)
            else:
                #imagem.save(root+'/pedido/'+self.destino, 'JPEG', dpi=(300, 300))
                copyfile(self.origem, root+'/pedido/'+self.destino)
            
            # deleta self.origem
        except:
            imagem = None
        
        thread_export -= 1


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


def Crop():
    global mydbZangraf
    global myZangraf
    global thread_crop
    global vCPU

    sql  = "SELECT r.id, r.crop_pos_x, r.crop_pos_y, r.crop_largura, r.crop_altura, a.url_imagem, a.largura, a.altura, p.largura, p.altura, a.id id_arquivo "
    sql += "FROM recortes r JOIN pedido_item_arquivos a ON a.id = r.id_arquivo JOIN pedido_items i ON i.id = a.id_item JOIN produtos p ON p.id = i.id_produto "
    sql += "WHERE a.situacao = 0 AND r.crop_pos_x IS NOT NULL AND r.crop_pos_y IS NOT NULL AND r.crop_largura IS NOT NULL AND r.crop_altura IS NOT NULL"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    cnt_result = 0
    while cnt_result < len(result):
        if thread_crop < vCPU:
            thread = RecortaImagem(result[cnt_result], mydbZangraf, myZangraf)
            thread.start()

            cnt_result += 1
            thread_crop += 1
        else:
            time.sleep(0.5)
    
    return cnt_result


def ImportarOP():
    global mydbZangraf
    global myZangraf
    global mydbSax
    global mySax

    sql  = "SELECT i.id, p.id_externo os, i.id_produto_externo id_produto "
    sql += "FROM pedidos p JOIN pedido_items i On i.id_pedido = p.id "
    sql += "WHERE i.id_externo IS NULL"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    cnt_processos = 0
    for row in result:
        sql  = "SELECT codigo FROM zangraf_xkey_producao.producoes "
        sql += "WHERE cod_producao = "+str(row[1])+" AND produto = "+str(row[2])
        mySax.execute(sql)
        op = mySax.fetchone()

        if op != None:
            sql = "UPDATE pedido_items SET id_externo = "+str(op[0])+" WHERE id = "+str(row[0])
            myZangraf.execute(sql)
            mydbZangraf.commit()
            cnt_processos += 1
        
    return cnt_processos


def ImportarProdutos():
    global arquivo_log
    global mydbZangraf
    global myZangraf
    global thread_import
    global vCPU

    sql  = "SELECT * FROM ( "
    sql += "SELECT i.id, i.id_pedido, i.url_origem, p.largura, p.altura, count(a.id) arquivos "
    sql += "FROM pedido_items i JOIN produtos p  ON p.id = i.id_produto LEFT JOIN pedido_item_arquivos a ON i.id = a.id_item "
    sql += "WHERE i.url_origem IS NOT NULL GROUP BY i.id "
    sql += ") t WHERE arquivos = 0 LIMIT 1"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    cnt_processos = 0
    for row in result:
        arquivo = open(arquivo_log, "a")
        arquivo.write(str(datetime.now())+' IMPORTAR PRODUTOS - pedido: '+str(row[1])+', ítem: '+str(row[0])+', origem: '+str(row[2])+'\n')
        arquivo.close()

        path = row[2]
        pastas = os.listdir(path)
        pastas.sort()

        album = None
        id_album = None
        for pasta in pastas:
            if os.path.isdir(path+'/'+pasta):
                arquivos = os.listdir(path+'/'+pasta)

                # cadastrar album
                sql  = "INSERT INTO pedido_albums(id_item, id_pedido, codigo, created_at, updated_at) "
                sql += "VALUES ("+str(row[0])+", "+str(row[1])+", '"+str(pasta)+"', now(), now())"
                myZangraf.execute(sql)
                
                sql = "SELECT max(id) FROM pedido_albums WHERE id_pedido = "+str(row[1])+""
                myZangraf.execute(sql)
                id_album = myZangraf.fetchall()[0][0]
                mydbZangraf.commit()
                
                cnt_arqivos = 0
                while cnt_arqivos < len(arquivos):
                    if thread_import < vCPU:
                        img = None
                        try:
                            img = Image.open(path+'/'+pasta+'/'+arquivos[cnt_arqivos])
                        except:
                            img = None
                        
                        cnt_arqivos += 1
                        
                        if img != None:
                            
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
                            
                            filename = pasta + '/' + arquivos[cnt_arqivos].split('.')[0] + '.jpg'
                            url = str(row[1])+"/"+str(row[0])+"/"+str(filename)
                            

                            # cadastrar arquivo
                            (largura, altura) = img.size

                            sql  = "INSERT INTO pedido_item_arquivos(id_item, id_album, url_imagem, nome_arquivo, largura, altura, situacao, created_at, updated_at) "
                            sql += "VALUES ("+str(row[0])+", "+str(id_album)+", '"+url+"', '"+filename+"', "+str(largura)+", "+str(altura)+", 1, now(), now())"
                            myZangraf.execute(sql)

                            sql = "SELECT max(id) FROM pedido_item_arquivos WHERE url_imagem = '"+url+"'"
                            myZangraf.execute(sql)
                            id_arquivo = myZangraf.fetchall()[0][0]
                            mydbZangraf.commit()

                            thread = ImportaImagem(img, row, id_album, filename, url, id_arquivo, mydbZangraf, myZangraf)
                            thread.start()

                            thread_import += 1
                    else:
                        time.sleep(1)
            
            else:
                # cadastrar album
                if album == None:
                    album = 1
                    sql  = "INSERT INTO pedido_albums(id_item, id_pedido, codigo, created_at, updated_at) "
                    sql += "VALUES ("+str(row[0])+", "+str(row[1])+", '"+Add0(album)+"', now(), now())"
                    myZangraf.execute(sql)
                    
                    sql = "SELECT max(id) FROM pedido_albums WHERE id_pedido = "+str(row[1])+""
                    myZangraf.execute(sql)
                    id_album = myZangraf.fetchall()[0][0]
                    mydbZangraf.commit()
                
                loop = True
                while loop:
                    if thread_import < vCPU:
                        loop = False

                        try:
                            img = Image.open(path+'/'+pasta)
                        except:
                            img = None
                        
                        if img != None:
                            
                            ##### CRIA ESTRUTURA DE PASTAS #####
                            if not os.path.exists(root_fotos+'/'+str(row[1])):
                                os.mkdir(root_fotos+'/'+str(row[1]))
                            if not os.path.exists(root_fotos+'/'+str(row[1])+"/"+str(row[0])):
                                os.mkdir(root_fotos+'/'+str(row[1])+"/"+str(row[0]))
                            if not os.path.exists(root_fotos+'/'+str(row[1])+"/"+str(row[0])+"/"+Add0(album)):
                                os.mkdir(root_fotos+'/'+str(row[1])+"/"+str(row[0])+"/"+Add0(album))
                                
                            
                            if not os.path.exists(root_thumbs+'/'+str(row[1])):
                                os.mkdir(root_thumbs+'/'+str(row[1]))
                            if not os.path.exists(root_thumbs+'/'+str(row[1])+"/"+str(row[0])):
                                os.mkdir(root_thumbs+'/'+str(row[1])+"/"+str(row[0]))
                            if not os.path.exists(root_thumbs+'/'+str(row[1])+"/"+str(row[0])+"/"+Add0(album)):
                                os.mkdir(root_thumbs+'/'+str(row[1])+"/"+str(row[0])+"/"+Add0(album))
                            

                            filename = Add0(album) + "/" + pasta.split('.')[0] + '.jpg'
                            url = str(row[1])+"/"+str(row[0])+"/"+str(filename)
                            

                            # cadastrar arquivo
                            (largura, altura) = img.size

                            sql  = "INSERT INTO pedido_item_arquivos(id_item, id_album, url_imagem, nome_arquivo, largura, altura, situacao, created_at, updated_at) "
                            sql += "VALUES ("+str(row[0])+", "+str(id_album)+", '"+url+"', '"+filename+"', "+str(largura)+", "+str(altura)+", 1, now(), now())"
                            myZangraf.execute(sql)

                            sql = "SELECT max(id) FROM pedido_item_arquivos WHERE url_imagem = '"+url+"'"
                            myZangraf.execute(sql)
                            id_arquivo = myZangraf.fetchall()[0][0]
                            mydbZangraf.commit()

                            thread = ImportaImagem(img, row, id_album, filename, url, id_arquivo, mydbZangraf, myZangraf)
                            thread.start()

                            thread_import += 1

                    else:
                        time.sleep(1)
        cnt_processos += 1
    
    return cnt_processos


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

    cnt_processos = 0
    for row in result:
        log = open(arquivo_log, "a")
        log.write(str(datetime.now())+' IMPORTAR SERVIÇOS - pedido: '+str(row[1])+', ítem: '+str(row[0])+', origem: '+str(row[2])+'\n')
        log.close()

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
            else:
                time.sleep(0.3)
        
        sql = "UPDATE pedido_item_servicos SET arquivos = "+str(count_files)+" WHERE id = "+str(row[0])
        myZangraf.execute(sql)
        mydbZangraf.commit()

        cnt_processos += 1
    
    return cnt_processos


def ExportarProdutos():
    global arquivo_log
    global mydbZangraf
    global myZangraf
    global mydbSax
    global mySax
    global thread_export
    global vCPU

    #               0           1               2               3           4               5               6          
    sql  = "SELECT i.id, p.id_externo, i.id_produto_externo, l.codigo, a.url_imagem, a.nome_arquivo, p.tipo_contrato, "
    #               7               8              9            10          11          12
    sql += "p.data_entrada, i.id_externo op, i.quantidade, p.id_cliente, p.cliente, i.corrigir, "
    #                   13                  14                  15                  16                  17
    sql += "it.titulo imp_tipo, ino.titulo imp_nome, upper(r.disposicao), h.titulo hotfolder, s.titulo substrato, "
    #                                                                           18
    sql += "if((r.largura + r.sangr_dir + r.sangr_esq) > (r.altura + r.sangr_sup + r.sangr_inf), 'PAISAGEM', if((r.largura + r.sangr_dir + r.sangr_esq) < (r.altura + r.sangr_sup + r.sangr_inf), 'RETRATO', 'QUADRADO')) orientacao "
    #       19
    sql += "p.id "

    sql += "FROM pedido_item_arquivos a JOIN pedido_albums l ON l.id = a.id_album JOIN pedido_items i ON i.id = l.id_item JOIN pedidos p ON p.id = i.id_pedido "
    sql += "JOIN produtos r ON r.id = i.id_produto JOIN imposicao_tipos it ON it.id = r.id_imposicao_tipo "
    sql += "LEFT JOIN imposicao_nomes ino ON ino.id = r.id_imposicao_nome LEFT JOIN impressao_hotfolders h ON h.id = r.id_impressao_hotfolder LEFT JOIN impressao_substratos s ON s.id = r.id_impressao_substrato "
    sql += "WHERE i.imprimir AND i.data_envio_impressao IS NULL ORDER BY l.codigo ASC, a.nome_arquivo ASC"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()

    orcamento = None
    id_produto = None
    old_album_name = None
    old_path = None
    strct = None
    album = None
    xml = None
    path_xml = None
    cnt = 1
    
    cnt_row = 0
    while cnt_row < len(result):
        row = result[cnt_row]

        if thread_export < vCPU:
            if orcamento != row[1] or id_produto != row[2]:
                sql = "UPDATE pedido_items SET data_envio_impressao = now() WHERE id = "+str(row[0])
                myZangraf.execute(sql)
                mydbZangraf.commit()

                log = open(arquivo_log, "a")
                log.write(str(datetime.now())+' EXPORTAR PRODUTOS - pedido: '+str(row[19])+', ítem: '+str(row[0])+'\n')
                log.close()
                
                #                   0           1           2           3               4               5           6              7                8
                sql  = "SELECT cli.codigo, cli.apelido, orc.codigo, orc.contato, orc.tipo_pedido, po.sequencia, prd.codigo, prd.descricao, prd.descr_reduz "
                sql += "FROM zangraf_xkey_principal.cad_orca orc "
                sql += "JOIN zangraf_xkey_publico.cad_clie cli ON cli.codigo = orc.cliente "
                sql += "JOIN zangraf_xkey_principal.pro_orca po ON po.orcamento = orc.codigo "
                sql += "JOIN zangraf_xkey_publico.cad_prod prd ON prd.codigo = po.produto "
                sql += "WHERE po.orcamento = "+str(row[1])+" AND po.produto = "+str(row[2])
                mySax.execute(sql)
                strct = mySax.fetchone()
                
                if strct != None:
                    orcamento = strct[2]
                    id_produto = strct[6]
                    cnt = 1

                    if path_xml != None:
                        tree = ET.ElementTree(xml)
                        tree.write(path_xml)
                    
                    xml = ET.Element("ordemProducao")
                    ET.SubElement(xml, "dataCadastro").text = str(row[7])
                    ET.SubElement(xml, "numero").text = str(row[8])
                    ET.SubElement(xml, "orcamento").text = str(row[1])
                    ET.SubElement(xml, "descricao").text = str(row[8])
                    ET.SubElement(xml, "quantidade").text = str(row[9])

                    if strct[4] == 1:
                        path = "/["+str(strct[0])+"]"+str(strct[1])+"/["+str(strct[0])+"]["+str(strct[2])+"]"+str(strct[3])+"/["+str(strct[5])+"]"+str(strct[7])
                        ET.SubElement(xml, "path").text = path
                        ET.SubElement(xml, "curso").text = "CONTRATO"
                    else:
                        path = "/["+str(strct[0])+"]"+str(strct[1])+"/"+str(strct[2])+"/["+str(strct[5])+"]"+str(strct[7])
                        ET.SubElement(xml, "path").text = path
                        ET.SubElement(xml, "curso").text = "PEDIDO"

                    cliente = ET.SubElement(xml, "cliente")
                    ET.SubElement(cliente, "codigo").text = str(row[10])
                    ET.SubElement(cliente, "nome").text = str(row[11])

                    produto = ET.SubElement(xml, "produto")
                    ET.SubElement(produto, "codigo").text = str(strct[6])
                    ET.SubElement(produto, "sequencia").text = str(strct[5])
                    ET.SubElement(produto, "copia").text = "1"
                    ET.SubElement(produto, "nome").text = str(strct[7])
                    ET.SubElement(produto, "descReduz").text = str(strct[8])
                    ET.SubElement(produto, "tipo").text = "DIRETORIO"

                    arquivos = ET.SubElement(produto, "arquivos")

                    
                    processo = ET.SubElement(xml, "processo")
                    if row[12] == 1:
                        ET.SubElement(processo, "correcao").text = "true"
                    else:
                        ET.SubElement(processo, "correcao").text = "false"

                    ET.SubElement(processo, "imposicao").text = "true"
                    ET.SubElement(processo, "otimizacao").text = "true"
                    ET.SubElement(processo, "impressao").text = "true"

                    imposicao = ET.SubElement(xml, "imposicao")
                    ET.SubElement(imposicao, "tipo").text = str(row[13])
                    if row[14] != None:
                        ET.SubElement(imposicao, "nome").text = str(row[14])
                    else:
                        ET.SubElement(imposicao, "nome").text = ""
                    ET.SubElement(imposicao, "disposicao").text = str(row[15])
                    ET.SubElement(imposicao, "orientacao").text = str(row[18])
                    ET.SubElement(imposicao, "multPrintJob").text = "false"
                    if row[16] != None:
                        ET.SubElement(imposicao, "hotfolder").text = str(row[16])
                    else:
                        ET.SubElement(imposicao, "hotfolder").text = ""
                    ET.SubElement(imposicao, "substrato").text = str(row[17])

                    propriedades = ET.SubElement(xml, "propriedades")
                    ET.SubElement(propriedades, "rotacionar").text = "true"
                    ET.SubElement(propriedades, "prioridade").text = "Medium"
                    
            if album != row[3]:
                album = row[3]
                cnt = 1
            
            filename = row[3]+' '+Add0(cnt)+'.jpg'
            cnt += 1
            cnt_row += 1

            if strct != None:
                if strct[4] == 1:
                    #contrato
                    path = "["+str(strct[0])+"]"+str(strct[1])+"/["+str(strct[0])+"]["+str(strct[2])+"]"+str(strct[3])+"/["+str(strct[5])+"]"+str(strct[7])+"/"+str(row[3])+"/"
                    #path_xml = root+"/producao/["+str(strct[0])+"]"+str(strct[1])+"/["+str(strct[0])+"]["+str(strct[2])+"]"+str(strct[3])+"/"+str(strct[0])+"_"+str(row[1])+"_"+str(strct[5])+"_"+str(strct[3])+".xml"
                    path_xml = "/robo/FLUXO_ENTRADA/"+str(strct[0])+"_"+str(row[1])+"_"+str(strct[5])+"_"+str(strct[3])+".xml"

                    if old_path != path:
                        old_path = path
                        MakeDirectory('/arquivo/'+path)
                        MakeDirectory('/producao/'+path)
                        ET.SubElement(arquivos, "arquivo", copia="1").text = str(row[3])+"/"
                    
                else:
                    #pedido
                    path = "["+str(strct[0])+"]"+str(strct[1])+"/"+str(strct[2])+"/["+str(strct[5])+"]"+str(strct[7])+"/"+str(row[3])+"/"
                    #path_xml = root+"/pedido/["+str(strct[0])+"]"+str(strct[1])+"/"+str(strct[2])+"/"+str(strct[0])+"_PEDIDO_"+str(strct[5])+"_"+str(row[8])+".xml"
                    path_xml = "/robo/FLUXO_ENTRADA/"+str(strct[0])+"_PEDIDO_"+str(strct[5])+"_"+str(row[8])+".xml"

                    if old_path != path:
                        old_path = path
                        MakeDirectory('/pedido/'+path)
                        ET.SubElement(arquivos, "arquivo", copia="1").text = str(row[3])+"/"
                    
                origem = '/brutos/Sistema/fotos/'+row[4]
                destino = path+filename
                thread = ExportarImagem(origem, destino, strct[4])
                thread.start()

                thread_export += 1

        # cria nome do album do Fluxo antigo
        if strct != None:
            if strct[4] == 1:
                album_name = str(strct[0])+'_'+str(strct[2])+'_'+str(strct[5])+'_'+str(strct[3])+'_'+str(row[3])
            else:
                album_name = str(strct[0])+'_PEDIDO_'+str(strct[5])+'_'+str(row[3])
            
            if old_album_name != album_name:
                old_album_name = album_name

                sql  = "INSERT INTO zangraf_flow.albuns(nome, paginas, ordemProducao) "
                sql += "VALUES('"+album_name+"', "+str(cnt-1)+", "+str(strct[2])+")"
                mySax.execute(sql)
                mydbSax.commit()
                
    if path_xml != None:
        tree = ET.ElementTree(xml)
        tree.write(path_xml)
    
    return cnt_row
        

def ExportarBrutos():
    global arquivo_log
    global mydbZangraf
    global myZangraf
    global mydbSax
    global mySax
    global thread_export
    global vCPU

    sql  = "SELECT i.id, i.id_pedido, p.id_externo FROM pedido_item_servicos i JOIN pedidos p ON p.id = i.id_pedido "
    sql += "WHERE i.imprimir = 1 AND i.data_envio_impressao IS NULL AND i.id_servico = 1"
    myZangraf.execute(sql)
    result = myZangraf.fetchall()
    
    cnt_processos = 0
    for row in result:
        path = root_servicos+'/'+str(row[1])+'/'+str(row[0])
        arquivos = ListarPasta(path, '')

        log = open(arquivo_log, "a")
        log.write(str(datetime.now())+' EXPORTAR SERVIÇOS - pedido: '+str(row[1])+', ítem: '+str(row[0])+'\n')
        log.close()

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
                        destino = '/arquivo/['+str(strct[0])+']'+str(strct[1])+'/['+str(strct[0])+']['+str(strct[2])+']'+str(strct[3])+'/BRUTO'+arquivos[cnt_arq]
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

        cnt_processos += 1
    
    return cnt_processos


#while 1:
retorno = 0
retorno += Crop()
#retorno += ImportarOP()
retorno += ImportarProdutos()
retorno += ImportarServicos()
retorno += ExportarProdutos()
retorno += ExportarBrutos()

if retorno == 0:
    time.sleep(60)