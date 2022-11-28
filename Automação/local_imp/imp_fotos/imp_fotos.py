from barcode.writer import ImageWriter
from PIL import Image, ImageDraw, ImageFont
#from multiprocessing import Process
from datetime import datetime
from threading import Thread
import mysql.connector
import img2pdf
import barcode
import shutil
import time
import os

vCPU = 48
dpi = 11.811023622047244
cb_album = None
path_album = None

mydbZangraf = mysql.connector.connect(
    host="192.168.10.50",
    user="root",
    password="1234",
    database="producao"
)
myZangraf = mydbZangraf.cursor()

root_path = '/root/automacao/local_imp/imp_fotos/'

def Add0(txt, tam=3):
    txt = str(txt)
    if len(txt) < tam:
        tmp = ''
        cnt = 0
        while cnt < tam - len(txt):
            tmp += '0'
            cnt += 1
        
        txt = tmp + txt
    
    return txt


def CodeBar(numero):
    global root_path

    try:
        filename = root_path+'tmp/'+str(numero)
        
        options = {
            'module_width':0.4,
            'module_height':8,
            #'text_distance':1,
            'text_distance':2,
            'font_path':root_path+'sans-serif.ttf',
            #'font_size':10
            'font_size':4
        }

        codeClass = barcode.get_barcode_class('code128')

        codigo_barra = codeClass(numero, writer=ImageWriter())
        filename = codigo_barra.save(filename, options = options)
        
        return Image.open(filename)
    except:
        return None


def Dorso(largura, altura, texto, id_album, id_foto):
    global root_path
    global cb_album

    try:
        # cb_album = CodeBar(str(id_album))
        cb_foto = CodeBar(str(id_foto))

        img = Image.new('RGB', (largura, altura), (255, 255, 255))
        draw = ImageDraw.Draw(img)

        # aplica o texto
        font = ImageFont.truetype(root_path + "sans-serif.ttf", 32)
        txt_width, txt_height = draw.textsize(texto, font)
        draw.text(((largura/2) - (txt_width/2), (altura/2) - (txt_height/2)), texto, (0,0,0), font=font)

        # aplica as margens de corte
        shape = [(1, int(altura-5*dpi)), (1, int(altura-2*dpi))]
        draw.line(shape, fill ="black", width = 0)

        shape = [(int(2*dpi), altura -1), (int(5*dpi), altura -1)]
        draw.line(shape, fill ="black", width = 0)

        shape = [(largura-1, int(altura-5*dpi)), (largura-1, int(altura-2*dpi))]
        draw.line(shape, fill ="black", width = 0)

        shape = [(int(largura-2*dpi), altura -1), (int(largura-5*dpi), altura -1)]
        draw.line(shape, fill ="black", width = 0)
        
        # adiciona códigos de barras na imagem
        if cb_album != None and cb_foto != None:
            img.paste(cb_album, (int(largura/3) - int(cb_album.size[0]), int(altura/2) - int(cb_album.size[1]/2)))
            img.paste(cb_foto, (int(largura/3*2), int(altura/2) - int(cb_foto.size[1]/2)))
            
            return img
        else:
            return None
    except:
        return None


thread_count = 0
thread_erro = 0
class ProcessaFoto(Thread):

    def __init__ (self, path, row, cnt):

        Thread.__init__(self)
        
        self.path = path
        self.row = row
        self.cnt = cnt


    def run(self):
        global root_path
        global path_album
        global thread_count
        global thread_erro

        try:
            img = Image.open(self.path)

            dorso_larg = 25
            rotaciona_verso = True
            imagem_substrato = True

            if self.row[5] == 'FOTO 21x30':
                dorso_larg = 24
                imagem_substrato = False

            if self.row[5] == 'FOTO 30x22':
                rotaciona_verso = False
                imagem_substrato = False
            elif self.row[6] == "CF 230 NEVIA 33x48" or self.row[6] == "CF 300 AMOSTRAS 33X48":
                rotaciona_verso = False


            dorso = Dorso(img.size[1], int(dorso_larg * dpi), self.row[1], self.row[0], Add0(str(self.cnt+1), 6))
            if dorso != None:
                imagem = Image.new('RGB', (dorso.size[1] + img.size[0], img.size[1]), (255, 255, 255))

                if (self.cnt % 2) == 0:
                    dorso = dorso.transpose(Image.ROTATE_90)
                    imagem.paste(dorso, (0, 0))
                    imagem.paste(img, (dorso.size[0], 0))
                else:
                    dorso = dorso.transpose(Image.ROTATE_270)
                    imagem.paste(img, (0, 0))
                    imagem.paste(dorso, (img.size[0], 0))

                    if rotaciona_verso:
                        imagem = imagem.transpose(Image.ROTATE_180)

                if imagem_substrato:
                    if self.row[6] == 'CF 230 NEVIA 32x48' or self.row[6] == 'CF 300 AMOSTRAS 32X48':
                        sub_largura = 479 #mm
                        sub_altura = 320 #mm
                    else:
                        sub_largura = 329 #mm
                        sub_altura = 479 #mm
                    
                    if rotaciona_verso:
                        s_larg = 0
                    else:
                        if (self.cnt % 2) != 0:
                            s_larg = int(sub_largura * dpi) - imagem.size[0]
                        else:
                            s_larg = 0

                    s_alt = int(((sub_altura * dpi) - imagem.size[1]) / 2)

                    substrato = Image.new('RGB', (int(sub_largura * dpi), int(sub_altura * dpi)), (255, 255, 255))
                    substrato.paste(imagem, (s_larg, s_alt))

                    imagem = substrato

                imagem.save(path_album + Add0(str(self.cnt+1)) + '.jpg', 'JPEG', dpi=(300, 300))
        
        except:
            thread_erro += 1
            print('Erro:',self.path)
        finally:
            thread_count -= 1


def Fotos(row, image_list, quantidade, path):
    global cb_album
    global thread_count
    global thread_erro

    # se a quantidade de imagens for impar, cria uma em branco
    if (quantidade % 2) != 0:
        img_base = Image.open(image_list[0])
        img = Image.new('RGB', (img_base.size[0], img_base.size[1]), (255, 255, 255))
        img.save(path + '/final.jpg', 'JPEG', dpi=(300, 300))

        image_list.append(path+'/final.jpg')

        img_base = None
        img = None

        quantidade += 1


    cb_album = CodeBar(str(row[0]))
    if cb_album != None:
        cnt = 0
        while cnt < len(image_list):
            if thread_erro > 0:
                break

            if thread_count < vCPU:
                thread = ProcessaFoto(image_list[cnt],row,cnt)
                thread.start()

                thread_count += 1
                cnt += 1
        
        if thread_erro == 0:
            while thread_count > 0:
                continue
            

    arquivos = os.listdir(path_album)
    arquivos.sort()

    image_list = []
    
    for arq in arquivos:
        ext = arq.split('.')
        if len(ext) == 2 and ext[1] == 'jpg':
            image_list.append(path_album + arq) 


    if len(image_list) >= quantidade:
        return image_list
    else:
        return []


class ProcessaRevista(Thread):
    def __init__ (self, lamina, font):

        Thread.__init__(self)
        
        self.lamina = lamina
        self.font = font


    def run(self):
        global thread_count
        global thread_erro
        
        try:
            # Verso
            img1 = Image.open(self.lamina[0])

            if len(self.lamina[1]) > 0:
                draw = ImageDraw.Draw(img1)
                
                txt_width, txt_height = draw.textsize(self.lamina[1], self.font)
                #posicao = ((img1.size[0]/2) - (txt_width/2), img1.size[1] - txt_height - 2*dpi)
                posicao = (img1.size[0] - txt_width, img1.size[1] - txt_height - 2*dpi)
                w, h = self.font.getsize(self.lamina[1])
                draw.rectangle((posicao, posicao[0] + w, posicao[1] + h), fill='white')
                draw.text(posicao, self.lamina[1], (0,0,0), font=self.font)
                draw = None
                posicao = None

            # Frente
            img2 = Image.open(self.lamina[2])

            if len(self.lamina[3]) > 0:
                draw = ImageDraw.Draw(img2)

                txt_width, txt_height = draw.textsize(self.lamina[3], self.font)
                #posicao = ((img2.size[0]/2) - (txt_width/2), img2.size[1] - txt_height - 2*dpi)
                posicao = (img2.size[0] - txt_width, img2.size[1] - txt_height)
                w, h = self.font.getsize(self.lamina[3])
                draw.rectangle((posicao, posicao[0] + w, posicao[1] + h), fill='white')
                draw.text(posicao, self.lamina[3], (0,0,0), font=self.font)
                draw = None
                posicao = None

            # Monta a página
            img_largura = img1.size[0] + img2.size[0]
            img_altura = img1.size[1]

            img = Image.new('RGB', (img_largura, img_altura), (255, 255, 255))
            img.paste(img1, (0, 0))
            img.paste(img2, (int(img.size[0]/2), 0))
            img1 = None
            Img2 = None

            if self.lamina[5]:
                img = img.transpose(Image.ROTATE_180)

            # Salvar
            img.save(self.lamina[4], 'JPEG', dpi=(300, 300))
            img = None

        except:
            thread_erro += 1
        finally:
            thread_count -= 1


def Revista(album, arquivos, quantidade, path_album):
    global dpi
    global thread_count
    global thread_erro
    
    if len(arquivos) % 4 == 0:
        laminas = []
        '''
        lamina = [
            path_foto_verso
            cnt_foto_verso
            path_foto_frente
            cnt_foto_frente
            path_foto_saida
            rotaciona_verso
        ]
        '''

        cnt = 0
        while cnt < len(arquivos) / 2:
            cnt_v = len(arquivos) - cnt
            if cnt % 2 == 0:
                laminas.append([
                    arquivos[cnt_v-1],
                    #str(album[8])+'_'+Add0(cnt_v),
                    str(album[9])+'_'+str(album[4]),
                    arquivos[cnt],
                    #str(album[8])+'_'+Add0(cnt+1),
                    '',#str(album[9])+'_'+str(album[4]),
                    path_album + Add0(cnt+1) + '.jpg',
                    False
                ])
            else:
                laminas.append([
                    arquivos[cnt],
                    #str(album[8])+'_'+Add0(cnt+1),
                    '',#str(album[9])+'_'+str(album[4]),
                    arquivos[cnt_v-1],
                    #str(album[8])+'_'+Add0(cnt_v),
                    '',#str(album[9])+'_'+str(album[4]),
                    path_album + Add0(cnt+1) + '.jpg',
                    True
                ])

            cnt += 1
        
        
        font = ImageFont.truetype(root_path + "sans-serif.ttf", 32)
        for lamina in laminas:
            thread = ProcessaRevista(lamina, font)
            thread.start()

            thread_count += 1
        
        
        if thread_erro == 0:
            while thread_count > 0:
                continue
        

        arquivos = os.listdir(path_album)
        arquivos.sort()

        image_list = []
        
        for arq in arquivos:
            ext = arq.split('.')
            if len(ext) == 2 and ext[1] == 'jpg':
                image_list.append(path_album + arq) 
        
        if len(image_list) >= quantidade/2:
            return image_list
        else:
            return []




myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'imp-fotos'")
mydbZangraf.commit()


#                0         1             2           3        4               5                 6             7                 8               9
sql  = "SELECT g.id, g.nome_album, g.tipo_pedido, g.path, g.album, n.titulo modelo, s.titulo substrato, g.quantidade, g.ordem_producao, g.ordem_servico FROM gsls g "
sql += "JOIN pedido_items i ON i.id = g.id_pedido_item JOIN produtos p ON p.id = i.id_produto "
sql += "JOIN imposicao_tipos t ON t.id = id_imposicao_tipo JOIN imposicao_nomes n ON n.id = id_imposicao_nome "
sql += "JOIN produtos r ON r.id = i.id_produto JOIN impressao_substratos s ON s.id = r.id_impressao_substrato "
sql += "WHERE ((g.correcao AND g.dt_correcao_saida IS NOT NULL) OR (NOT g.correcao)) AND g.dt_imposicao_entrada IS NULL "
sql += "AND t.titulo = 'LOCAL' AND n.titulo NOT IN('CAPAS', 'CAPA_FRENTE', 'CAPA_VERSO') "
#sql += "AND g.ordem_producao = 151332 "
#sql += "LIMIT 10"
myZangraf.execute(sql)
result = myZangraf.fetchall()

process = False
for row in result:
    process = True
    quantidade = 0
    thread_erro = 0

    try:
        if os.path.exists(root_path + 'tmp/'):
            shutil.rmtree(root_path + 'tmp/', ignore_errors=True)
        
        if not os.path.exists(root_path + 'tmp/'):
            os.mkdir(root_path + 'tmp/')
        
        path_album = root_path + 'album/' + str(row[0]) + '/'

        if os.path.exists(path_album):
            trash = os.listdir(path_album)
            for arq in trash:
                os.unlink(path_album + arq)
            shutil.rmtree(path_album, ignore_errors=True)
        
        if not os.path.exists(path_album):
            os.mkdir(path_album)
        
        if row[2] == 1:
            path = '/producao/'
        else:
            path = '/pedido/'

        p = ''
        strct = row[3].split('/')
        for s in strct:
            p += '/'+s.strip()

        path += p + str(row[4])
        
        arquivos = os.listdir(path)
        
        if len(arquivos) > 0:

            arquivos.sort()
            
            image_list = []
            
            for arq in arquivos:
                ext = arq.split('.')
                if len(ext) >= 2 and (ext[len(ext)-1].lower() == 'jpg' or ext[len(ext)-1].lower() == 'jpeg'):
                    image_list.append(path+'/'+arq)
            
            quantidade = len(image_list)

            
            if row[5][:4].lower() == 'foto':
                image_list = Fotos(row, image_list, quantidade, path)
                        
                        

            elif row[5].lower() == 'revista':
                image_list = Revista(row, image_list, quantidade, path_album)
            


            if len(image_list) > 0:
                
                with open(root_path + 'saida/' + str(row[1]) + ".pdf", "wb") as f:
                    f.write(img2pdf.convert(image_list))
                
                sql = "UPDATE gsls SET dt_imposicao_entrada = now() WHERE id = "+str(row[0])
                myZangraf.execute(sql)
                mydbZangraf.commit()


        trash = os.listdir(path_album)
        for arq in trash:
            os.unlink(path_album + arq)
            
        shutil.rmtree(path_album, ignore_errors=True)

        
        myZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'imp-fotos'")
        mydbZangraf.commit()

    except:
        sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'Imposição de Fotos','Erro ao imposicionar o álbum "+str(row[1])+"')"
        myZangraf.execute(sql)
        mydbZangraf.commit()

#if not process:
time.sleep(60)
