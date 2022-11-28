from PIL import Image, ImageDraw, ImageFont

import mysql.connector
import img2pdf
import time
import shutil
import os

mydbZangraf = mysql.connector.connect(
    host="192.168.10.50",
    user="root",
    password="1234",
    database="producao"
)
myZangraf = mydbZangraf.cursor()

root = '/root/automacao/local_imp/'

def MakeDirectory(folder):
    directory = ''
    pastas = folder.split('/')
    for pasta in pastas:
        directory += '/'+pasta
        if not os.path.exists(directory):
            os.mkdir(directory)


#               0           1              2        3        4              5                   6
sql  = "SELECT g.id, g.nome_album, g.tipo_pedido, g.path, g.album, g.ordem_producao, n.titulo modelo FROM gsls g "
sql += "JOIN pedido_items i ON i.id = g.id_pedido_item JOIN produtos p ON p.id = i.id_produto "
sql += "JOIN imposicao_tipos t ON t.id = id_imposicao_tipo JOIN imposicao_nomes n ON n.id = id_imposicao_nome "
sql += "WHERE ((g.correcao AND g.dt_correcao_saida IS NOT NULL) OR (NOT g.correcao)) AND g.dt_imposicao_entrada IS NULL "
sql += "AND t.titulo = 'LOCAL' AND n.titulo IN('CAPAS', 'CAPA_FRENTE', 'CAPA_VERSO')"
myZangraf.execute(sql)
result = myZangraf.fetchall()

for row in result:
    try:
        if row[2] == 1:
            path = '/producao/'
        else:
            path = '/pedido/'

        path += row[3] + str(row[4])
        
        arquivos = os.listdir(path)
        if len(arquivos) > 0:
            arquivos.sort()
            
            image_list = []
            
            for arq in arquivos:
                ext = arq.split('.')
                if len(ext) == 2 and ext[1] == 'jpg':
                    image_list.append(path+'/'+arq) 
            
            if row[6] == 'CAPAS':
                
                with open(root + 'saida/' + str(row[1]) + ".pdf", "wb") as f:
                    f.write(img2pdf.convert(image_list))

            elif row[6] == 'CAPA_FRENTE':
                # cria pasta temporária
                path_tmp = root + 'local_imp/tmp/'+str(row[5])
                
                MakeDirectory(path_tmp)

                lista_saida = []
                # add info na foto
                for item in image_list:
                    font = ImageFont.truetype(root+'sans-serif.ttf', 32)

                    # carrega imagem original
                    img = Image.open(item)
                    draw = ImageDraw.Draw(img)

                    #cria imagem de texto
                    txt_width, txt_height = draw.textsize(item, font)
                    img_txt = Image.new('RGB', (txt_width, txt_height), (255, 255, 255))
                    draw = ImageDraw.Draw(img_txt)
                    draw.text((0, 0), item, (0,0,0), font=font)

                    # rotaciona imagem de texto
                    img_txt = img_txt.transpose(Image.ROTATE_90)

                    # salva texto na imagem
                    img.paste(img_txt, (300, int((img.size[1]/2) - (img_txt.size[1]/2))))
                    strct = item.split('/')
                    filename = strct[-1]
                    img.save(path_tmp + '/' + filename, 'JPEG', dpi=(300, 300))
                    
                    lista_saida.append(path_tmp + '/' + filename)
                    

                # cria PDF
                with open(root + 'local_imp/saida/' + str(row[1]) + ".pdf", "wb") as f:
                    f.write(img2pdf.convert(lista_saida))
                
                shutil.rmtree(path_tmp)

            elif row[6] == 'CAPA_VERSO':
                # cria pasta temporária
                path_tmp = root + 'local_imp/tmp/'+str(row[5])
                
                MakeDirectory(path_tmp)

                lista_saida = []
                # add info na foto
                for item in image_list:
                    font = ImageFont.truetype(root+'sans-serif.ttf', 32)

                    # carrega imagem original
                    img = Image.open(item)
                    draw = ImageDraw.Draw(img)

                    #cria imagem de texto
                    txt_width, txt_height = draw.textsize(item, font)
                    img_txt = Image.new('RGB', (txt_width, txt_height), (255, 255, 255))
                    draw = ImageDraw.Draw(img_txt)
                    draw.text((0, 0), item, (0,0,0), font=font)

                    # rotaciona imagem de texto
                    img_txt = img_txt.transpose(Image.ROTATE_270)

                    # salva texto na imagem
                    pos_x = img.size[0] - 300 - img_txt.size[0]
                    pos_y = int((img.size[1]/2) - (img_txt.size[1]/2))

                    img.paste(img_txt, (pos_x, pos_y))
                    strct = item.split('/')
                    filename = strct[-1]
                    img.save(path_tmp + '/' + filename, 'JPEG', dpi=(300, 300))
                    
                    lista_saida.append(path_tmp + '/' + filename)
                    

                # cria PDF
                with open(root + 'local_imp/saida/' + str(row[1]) + ".pdf", "wb") as f:
                    f.write(img2pdf.convert(lista_saida))
                shutil.rmtree(path_tmp)
            

        myZangraf.execute("UPDATE gsls SET dt_imposicao_entrada = now() WHERE id = " + str(row[0]))
        mydbZangraf.commit()
    
    except:
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'LocalImp2Oris','Erro ao criar o arquivo "+str(row[1])+".pdf')"
        myZangraf.execute(sql)
        mydbZangraf.commit()
    

time.sleep(60)