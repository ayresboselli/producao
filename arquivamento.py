from threading import Thread
#from shutil import copyfile
from PIL import Image

import mysql.connector
import shutil
import os

vCPU = 24

root = ""
root_fotos = "/brutos/Sistema/fotos"
root_thumbs = "/brutos/Sistema/thumbs"
root_servicos = "/brutos/Sistema/servicos"

connZangraf = mysql.connector.connect(
    host="192.168.10.160",
    user="root",
    password="Person!#@$",
    database="dev_producao"
)
cursorZangraf = connZangraf.cursor()


thread_import = 0
class ThreadMoverFTP(Thread):
    def __init__ (self, origem, destino):
        Thread.__init__(self)
        self.origem = origem
        self.destino = destino

    def run(self):
        global thread_import

        try:
            imagem = Image.open(self.origem)
            imagem.save(self.destino, 'JPEG', dpi=(300, 300))
            imagem = None
        except:
            try:
                shutil.copyfile(self.origem, self.destino)
            except:
                None
            
        thread_import -= 1


def MakeDirectory(folder):
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


def MoverFTP(origem, destino):
    global vCPU
    global thread_import

    arqs = ListarPasta(origem,'')
    old_path = ''
    cnt = 0
    while cnt < len(arqs):
        if thread_import < vCPU:
            tmp = arqs[cnt].split('/')
            tmp.pop()
            path = '/'.join(tmp)
            
            if old_path != path:
                old_path = path
                MakeDirectory(destino + path)
            
            thread = ThreadMoverFTP(origem + arqs[cnt], destino + arqs[cnt])
            thread.start()
            
            cnt += 1
            thread_import += 1


#               0           1           2               3           4             5                                                 6           7   
sql  = "SELECT p.id, p.id_externo, p.arquivar, p.tipo_contrato, p.contrato, p.id_cliente, concat('[',p.id_cliente,']',p.cliente) cliente, c.ftp_usuario "
sql += "FROM pedidos p LEFT JOIN clientes c ON c.id_externo = p.id_cliente "
sql += "WHERE p.data_fechamento IS NOT NULL AND p.arquivar IS NOT NULL AND p.processado = 0"
cursorZangraf.execute(sql)
result = cursorZangraf.fetchall()

for row in result:
    
    if row[3] == 1: # contrato
        path_producao = root + '/producao/' + row[6] + '/[' + str(row[5]) + '][' + str(row[1]) + ']' + str(row[4]).strip()
        path_arquivo = root + '/arquivo/' + row[6] + '/[' + str(row[5]) + '][' + str(row[1]) + ']' + str(row[4]).strip()

        if row[2] == 1:
            try:
                shutil.rmtree(path_arquivo)
            except:
                None
        elif row[2] == 2:
            # FTP
            path_ftp = '/ftp/' + str(row[7]) + '/' + str(row[1])
            if len(row[4]) > 0:
                path_ftp +=  '_' + str(row[4])
            
            MoverFTP(path_arquivo, path_ftp)
        
        try:
            shutil.rmtree(path_producao)
        except:
            None
            
    else: # pedido
        path_pedido = root + '/pedido/' + row[6] + '/' + str(row[1])

        if row[2] == 1:
            try:
                shutil.rmtree(path_pedido)
            except:
                None
        elif row[2] == 2:
            # FTP
            path_ftp = '/ftp/' + str(row[7]) + '/' + str(row[1])
            MoverFTP(path_pedido, path_ftp)
    
    
    # lista itens
    sql = "SELECT id, url_origem FROM pedido_items WHERE id_pedido = "+str(row[0])
    cursorZangraf.execute(sql)
    itens = cursorZangraf.fetchall()

    for item in itens:
        
        # exclui arquivos do sistema
        try:
            shutil.rmtree(root_fotos + '/' + str(row[0]) + '/' + str(item[0]))
        except:
            None

        try:
            shutil.rmtree(root_thumbs + '/' + str(row[0]) + '/' + str(item[0]))
        except:
            None
            
        try:
            shutil.rmtree(root_servicos + '/' + str(row[0]) + '/' + str(item[0]))
        except:
            None

        # exclui arquivos das origens dos produtos
        if item[1] != None:
            try:
                shutil.rmtree(item[1])
            except:
                None
    
    
    if row[2] == 1:
        sql = "UPDATE pedidos SET processado = 1, excluido = 1 WHERE id = "+str(row[0])
    else:
        sql = "UPDATE pedidos SET processado = 1 WHERE id = "+str(row[0])
    
    cursorZangraf.execute(sql)
    connZangraf.commit()


cursorZangraf.close()
connZangraf.close()