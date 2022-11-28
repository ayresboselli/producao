import mysql.connector
import shutil
import time
import os

root = ""
root_fotos = "/sistema/fotos"
root_thumbs = "/sistema/thumbs"
root_servicos = "/sistema/servicos"

connZangraf = mysql.connector.connect(
    host="192.168.10.50",
    user="root",
    password="1234",
    database="producao"
)
cursorZangraf = connZangraf.cursor()



cursorZangraf.execute("UPDATE servicos_status SET ultimo_processo = now() WHERE servico = 'producao-arquivamento'")
connZangraf.commit()


 #               0           1           2               3           4             5                                                 6           7   
sql  = "SELECT p.id, p.id_externo, p.arquivar, p.tipo_contrato, p.contrato, p.id_cliente, concat('[',p.id_cliente,']',p.cliente) cliente, c.ftp_usuario "
sql += "FROM pedidos p LEFT JOIN clientes c ON c.id_externo = p.id_cliente "
sql += "WHERE p.data_fechamento IS NOT NULL AND p.arquivar IS NOT NULL AND p.processado = 0"
cursorZangraf.execute(sql)
result = cursorZangraf.fetchall()

qtd_processos = len(result)
for row in result:
    #try:
    if row[3] == 1: # contrato
        path_arquivo = root + '/producao/' + row[6] + '/[' + str(row[5]) + '][' + str(row[1]) + ']' + str(row[4]).strip()
        
        if not os.path.exists(path_arquivo):
            path_arquivo = root + '/producao/' + row[6] + '/' + str(row[1])
        

        if row[2] == 1:
            if os.path.exists(path_arquivo):
                shutil.rmtree(path_arquivo)
            
        elif row[2] == 2:
            # FTP
            path_ftp = '/ftp/' + str(row[7]) + '/' + str(row[1])
            if len(row[4]) > 0:
                path_ftp +=  '_' + str(row[4])
            
            if os.path.exists(path_ftp):
                shutil.rmtree(path_ftp)
            
            path_arquivo = path_arquivo.strip()
            if os.path.exists(path_arquivo):
                shutil.copytree(path_arquivo, path_ftp)
        
    else: # pedido
        path_pedido = root + '/pedido/' + row[6] + '/' + str(row[1])

        if row[2] == 1:
            if os.path.exists(path_pedido):
                print(path_pedido)
                shutil.rmtree(path_pedido)
        
        elif row[2] == 2:
            # FTP
            path_ftp = '/ftp/' + str(row[7]) + '/' + str(row[1])

            path_pedido = path_pedido.strip()
            shutil.copytree(path_pedido, path_ftp)
            
    

    # lista itens
    sql = "SELECT id, url_origem FROM pedido_items WHERE id_pedido = "+str(row[0])
    cursorZangraf.execute(sql)
    itens = cursorZangraf.fetchall()

    for item in itens:
        path_fotos = root_fotos + '/' + str(row[0]) + '/' + str(item[0])
        path_thumb = root_thumbs + '/' + str(row[0]) + '/' + str(item[0])
        path_serv = root_servicos + '/' + str(row[0]) + '/' + str(item[0])

        # exclui arquivos do sistema

        if os.path.exists(path_fotos):
            try:
                shutil.rmtree(path_fotos)
            except:
                None

        if os.path.exists(path_thumb):
            try:
                shutil.rmtree(path_thumb)
            except:
                None
        
        if os.path.exists(path_serv):
            try:
                shutil.rmtree(path_serv)
            except:
                None

        # exclui arquivos das origens dos produtos
        '''
        if item[1] != None:
            if os.path.exists(item[1]):
                try:
                    shutil.rmtree(item[1])
                except:
                    None
        '''
    
    
    if row[2] == 1:
        sql = "UPDATE pedidos SET processado = 1, excluido = 1 WHERE id = "+str(row[0])
    else:
        sql = "UPDATE pedidos SET processado = 1 WHERE id = "+str(row[0])
    
    cursorZangraf.execute(sql)
    connZangraf.commit()
#     except:
#         sql = "INSERT INTO log(tipo,modulo,mensagem) VALUES(1,'Arquivamento','Erro ao arquivar a OS "+str(row[1])+")"
#         cursorZangraf.execute(sql)
#         connZangraf.commit()

time.sleep(60)
