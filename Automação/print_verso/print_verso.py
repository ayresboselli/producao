from shutil import copyfile
from PIL import Image, ImageDraw, ImageFont

import mysql.connector
import shutil
import time
import xml.etree.ElementTree as ET
import os
import img2pdf

mydbZangraf = mysql.connector.connect(
    host="192.168.10.50",
    user="root",
    password="1234",
    database="producao"
)
myZangraf = mydbZangraf.cursor()


root_path = '/root/automacao/print_verso/'

def MapeiaArquivos(path,subpath,tmp_path):
    try:
        arq_tmp = os.listdir(path)
        
        for arq in arq_tmp:
            ext = arq.split('.')
            if len(ext) >= 2:
                if ext[1] == 'jpg' or ext[1] == 'JPG':
                    pre = ''
                    if len(subpath) > 0:
                        pre = subpath+'_'
                    arquivos.append(pre+arq)
                    
                    copyfile(path+'/'+arq, tmp_path+'/'+pre+arq)
            else:
                MapeiaArquivos(path+'/'+arq,arq,tmp_path)
    except:
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'PrintVerso','Pasta não encontrada "+str(path)+"')"
        myZangraf.execute(sql)
        mydbZangraf.commit()


def Add0(val):
    val = str(val)
    if len(val) == 1:
        return '00'+str(val)
    elif len(val) == 2:
        return '0'+str(val)
    else:
        return str(val)


def FichaIdentificacao(path, arquivos):
    global root_path

    for arq in arquivos:
        img_new = Image.open(root_path + 'FichaBase.jpg')
        
        nome_str = arq.split(' ')
        cod = nome_str[0]
        del(nome_str[0])
        nome = ' '.join(nome_str).split('.')[0]
        
        draw = ImageDraw.Draw(img_new)
        font = ImageFont.truetype(root_path + "sans-serif.ttf", 48)
        draw.text((50, 50), nome, (0,0,0), font=font)
        draw.text((600, 410), contrato+' / '+cod, (0,0,0), font=font)
        
        img = Image.open(path+'/'+arq).resize((446,591))
        img_new.paste(img, (1916,0))
        img_new.save(path+'/'+arq, 'JPEG', dpi=(300, 300))
        
        


entrada = root_path + 'xml/'
jlt_entrada = os.listdir(entrada)

arq_xml = []
for arq in jlt_entrada:
    ext = arq.split('.')
    if len(ext) >= 2 and ext[1].lower() == 'xml':
        arq_xml.append(arq)
        
arquivos = []

qtd_processos = len(arq_xml)
for xml in arq_xml:
    try:
        filename = xml.split('.')[0]
        
        contrato = ''
        curso = ''
        path = ''
        album = ''
        codigo = ''
        jlt = ''
        
        # mapeia xml
        tree =  ET.parse(entrada+xml)
        root = tree.getroot()
        
        for child in root.findall("produto"):
            for prd in child.findall("codigo"):
                codigo = prd.text
        
        for child in root.findall("descricao"):
            contrato = child.text

        for child in root.findall("curso"):
            curso = child.text

        for child in root.findall("path"):
            path = child.text

        for prd in root.findall("produto"):
            for arqs in prd.findall("arquivos"):
                for arqv in arqs.findall("arquivo"):
                    album = arqv.text
        
        p = ''
        strct = path.split('/')
        for s in strct:
            p += '/'+s.strip()

        if curso == 'PEDIDO':
            path = '/pedido'+p+'/'+album
        else:
            path = '/producao'+p+'/'+album
        
        # cria pasta temporária
        tmp_path = root_path + 'tmp/'+str(contrato)
        
        #if os.path.exists(tmp_path):
        #    shutil.rmtree(tmp_path, ignore_errors=True)

        if not os.path.exists(tmp_path):
            os.mkdir(tmp_path)
        
        
        # mapeia arquivos originais
        arquivos = []
        MapeiaArquivos(path,'',tmp_path)
        
        # cria arquivo com print no verso
        arq_tmp = os.listdir(tmp_path)
        arq_tmp.sort()
        
        if codigo == '1004':
            FichaIdentificacao(tmp_path, arq_tmp)
            #destino = 'PDF_1up_sim_PV/'
        else:
            cnt = 0
            for arq in arq_tmp:
                ext = arq.split('.')
                
                img_f = Image.open(tmp_path+'/'+arq)
                largura, altura = img_f.size
            
                os.remove(tmp_path+'/'+arq)
            
                img_v = Image.new('RGB', (largura, altura), (255, 255, 255))
                draw = ImageDraw.Draw(img_v)
                font = ImageFont.truetype(root_path + "sans-serif.ttf", 32)
                
                texto = ext[0]
                txt_width, txt_height = draw.textsize(texto, font)
                draw.text(((largura/2) - (txt_width/2), (altura/2) - (txt_height/2)), texto, (0,0,0), font=font)
            
                img_f.save(tmp_path+'/'+Add0(cnt)+'_f.'+ext[1], 'JPEG', dpi=(300, 300))
                img_v.save(tmp_path+'/'+Add0(cnt)+'_v.'+ext[1], 'JPEG', dpi=(300, 300))
            
                cnt += 1
            '''
            if codigo == '1123':
                destino = 'PDF_1up_PV_10x15/'
            else:
                destino = 'PDF_1up_PV/'
            '''
        
        # gera o PDF
        arq_tmp = os.listdir(tmp_path)
        arq_tmp.sort()
        
        image_list = []
        
        for arq in arq_tmp:
            ext = arq.split('.')
            if len(ext) == 2:
                image_list.append(tmp_path+'/'+arq)
        
        
        with open(tmp_path+'/'+filename+".pdf","wb") as f:
            f.write(img2pdf.convert(image_list))
        
        
        # move PDF para o DFE
        #copyfile(tmp_path+'/'+filename+'.pdf', '/jobs/'+destino+filename+".pdf")
        shutil.copyfile(tmp_path+'/'+filename+'.pdf', root_path+'saida/'+filename+'.pdf')
        
        
        # deleta pasta temporária
        if os.path.exists(tmp_path):
            shutil.rmtree(tmp_path, ignore_errors=True)
            
        os.remove(entrada + xml)

    except:
        None


myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'print_verso'")
mydbZangraf.commit()


time.sleep(60)
