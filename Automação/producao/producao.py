# https://marquesfernandes.com/desenvolvimento/criando-um-servico-linux-com-systemd/
# /etc/systemd/system/
# systemctl start servico
# systemctl enable servico

from threading import Thread
from datetime import datetime
from PIL import Image

import xml.etree.cElementTree as ET
import mysql.connector
import shutil
import time
import os

dpi = 11.811023622047244

margem_proporcao = 0.1
vCPU = 12

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


thread_crop = 0
crop_array = []
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
        global crop_array

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

        crop_array.append(self.row[10])
        '''
        sql  = "UPDATE pedido_item_arquivos SET situacao = 1, updated_at = '"+str(datetime.now())+"' "
        sql += "WHERE id = "+str(self.row[10])
        self.myZangraf.execute(sql)
        self.mydbZangraf.commit()
        '''
        thread_crop -= 1


def Crop():
    global mydbZangraf
    global myZangraf
    global thread_crop
    global vCPU

    sql  = "SELECT r.id, r.crop_pos_x, r.crop_pos_y, r.crop_largura, r.crop_altura, a.url_imagem, a.largura, a.altura, p.largura+p.sangr_esq+p.sangr_dir, p.altura+p.sangr_sup+p.sangr_inf, a.id id_arquivo "
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
    
    time.sleep(30)
    
    
    qtd_processos = len(crop_array)
    for item in crop_array:
        sql  = "UPDATE pedido_item_arquivos SET situacao = 1, updated_at = '"+str(datetime.now())+"' "
        sql += "WHERE id = "+str(item)
        myZangraf.execute(sql)
        mydbZangraf.commit()
    
    return qtd_processos
        
  

myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'producao'")
mydbZangraf.commit()


qtd_processos = Crop()

time.sleep(60)