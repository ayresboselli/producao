# -*- coding: utf-8 -*-

from pdf2image import pdfinfo_from_path, convert_from_path

import mysql.connector
import urllib.request
import requests
import shutil
import json
import os


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
myZangraf = mydbZangraf.cursor(buffered=True)


##### VARIÁVEIS DE INICIALIZAÇÃO #####
apiKey = "23C874AF58294AE0BB96617B183C6E97"
pagAtual = 1
totalPag = 1

printOnePath = '/brutos/PrintOne/'



##### FUNÇÕES #####
def Add0(val):
	val = str(val)
	if len(val) == 1:
		return '00'+str(val)
	elif len(val) == 2:
		return '0'+str(val)
	else:
		return str(val)


def ListaJpg(path):
    lista = []
    arquivos = os.listdir(path)
    for arquivo in arquivos:
        strct = arquivo.split('.')
        if len(strct) >= 2 and strct[1].lower() == 'jpg':
            lista.append(arquivo)
    
    return lista


def CalculaQuantidades(path, tipo):
    quantidade = 0
    
    lista = os.listdir(path)
    if tipo == 0:
        for item in lista:
            if os.path.isdir(path + item):
                quantidade += 1
    else:
        for item in lista:
            if os.path.isdir(path + item):
                quantidade += CalculaQuantidades(path + item + '/', 1)
            else:
                strct = item.split('.')
                if len(strct) >= 2 and strct[1].lower() == 'jpg':
                    quantidade += 1

    return quantidade


def CadCliente(detalhe):
    global mydbZangraf
    global myZangraf
    global mydbSax
    global mySax

    if detalhe['txtClienteCPF'] != '':
        tipo_pessoa = 'F'
        cpf_cnpj = detalhe['txtClienteCPF']
    else:
        tipo_pessoa = 'J'
        cpf_cnpj = detalhe['txtClienteCNPJ']

    
    try:
        sql = "SELECT * FROM clientes WHERE cpf_cnpj = '"+str(cpf_cnpj)+"'"
        myZangraf.execute(sql)
        cliente = myZangraf.fetchone()

        if cliente == None:
        
            fatura = detalhe['Fatura'][0]
            
            sql = "SELECT * FROM zangraf_xkey_publico.cad_clie WHERE cpf = '"+str(cpf_cnpj)+"'"
            mySax.execute(sql)
            clieSax = mySax.fetchone()

            if clieSax == None:
                sql  = "INSERT INTO zangraf_xkey_publico.cad_clie(nome, email, fis_jur, cpf, telefone_res, celular, cep, endereco, numero, complemento, bairro, cidade, estado) "
                sql += "VALUES ('"+str(detalhe['txtClienteNome'])+"', '"+str(detalhe['txtClienteEmail'])+"', '"+tipo_pessoa+"', '"+cpf_cnpj+"', '"+str(detalhe['txtClienteTel'])+"', '"+str(detalhe['txtClienteCel'])+"', '"+str(fatura['txtCEP'])+"', '"+str(fatura['Logradouro'])+"', '"+str(fatura['Numero'])+"', '"+str(fatura['Complemento'])+"', '"+str(fatura['Bairro'])+"', '"+str(fatura['Cidade'])+"', '"+str(fatura['ESTADO'])+"')"
                mySax.execute(sql)
                mydbSax.commit()

                sql = "SELECT * FROM zangraf_xkey_publico.cad_clie WHERE cpf = '"+str(cpf_cnpj)+"'"
                mySax.execute(sql)
                clieSax = mySax.fetchone()

            sql  = "INSERT INTO clientes(id_externo, nome, email, tipo_pessoa, cpf_cnpj, telefone, celular, cep, logradouro, numero, complemento, bairro, cidade, estado, created_at) "
            sql += "VALUES ("+str(clieSax[0])+", '"+str(detalhe['txtClienteNome'])+"', '"+str(detalhe['txtClienteEmail'])+"', '"+tipo_pessoa+"', '"+cpf_cnpj+"', '"+str(detalhe['txtClienteTel'])+"', '"+str(detalhe['txtClienteCel'])+"', '"+str(fatura['txtCEP'])+"', '"+str(fatura['Logradouro'])+"', '"+str(fatura['Numero'])+"', '"+str(fatura['Complemento'])+"', '"+str(fatura['Bairro'])+"', '"+str(fatura['Cidade'])+"', '"+str(fatura['ESTADO'])+"', now())"
            myZangraf.execute(sql)
            mydbZangraf.commit()

            sql = "SELECT id FROM clientes WHERE cpf_cnpj = '"+str(cpf_cnpj)+"'"
            myZangraf.execute(sql)
            cliente = myZangraf.fetchone()
            
        
        return cliente[1]
    except:
        return None
    
    
    

##### MAIN #####
while pagAtual <= totalPag:
    # lista de pedidos
    url = "http://colorup.com.br/webservices/v1/pedidoLista.asp?txtAPIKey="+apiKey+"&txtFormatoRetorno=JSON&numPagina="+str(pagAtual)
    r = requests.get(url)
    lista_pedidos = json.loads(r.text)
    
    for l_pedido in lista_pedidos['objPedidos']:
        
        '''
        2 - Pedido com pendencias
        3 - Aguardando Pagamento
        4 - Pagamento Confirmado
        5 - Pedido Aprovado
        7 - Em Produção
        8 - Concluído
        9 - Entregue
        10 - Cancelado
        11 - Abandonado
        '''
        
        id_pedido = None
        
        if l_pedido['idStatus'] == '4' or l_pedido['idStatus'] == '5':
            
            # detalhes de pedidos
            url = "http://colorup.com.br/webservices/v1/pedidoDetalhe.asp?txtAPIKey="+apiKey+"&txtFormatoRetorno=JSON&numeroPedido="+l_pedido['txtNumeroPedido']
            r = requests.get(url)
            detalhe = json.loads(r.text)
            
            # diretório do pedido
            path = printOnePath + str(detalhe['txtNumeroPedido']) + '/'
            if not os.path.exists(path):
                os.mkdir(path)
            

            id_cliente = CadCliente(detalhe)

            try:
                # cria pedido
                
                sql = "SELECT id_sax FROM print_one_clientes WHERE id_loja = "+str(detalhe['idLoja'])
                myZangraf.execute(sql)
                id_vendedor = myZangraf.fetchone()[0]
                
                sql = "SELECT count(*) FROM pedidos WHERE id_printone = '"+str(detalhe['txtNumeroPedido'])+"' AND id_cliente = '"+str(id_cliente)+"'"
                myZangraf.execute(sql)
                cnt_pedido = myZangraf.fetchone()[0]
                
                if cnt_pedido == 0:
                    sql = "SELECT nome FROM clientes WHERE id_externo = "+str(id_cliente)
                    myZangraf.execute(sql)
                    cliente = myZangraf.fetchone()
                    if cliente == None:
                        cliente = ''
                    else:
                        cliente = cliente[0]
                    
                    entrega = detalhe['Entrega'][0]
                    
                    observacao = "ENDEREÇO PARA A ENTREGA: \r\n"
                    observacao += "    Nome: "+detalhe['txtClienteNome']+"\r\n"
                    observacao += "    E-mail: "+detalhe['txtClienteEmail']+"\r\n"
                    observacao += "    CPF/CNPJ: "+detalhe['txtClienteCPF']+detalhe['txtClienteCNPJ']+"\r\n"
                    observacao += "    Telefone: "+detalhe['txtClienteTel']+" "+detalhe['txtClienteCel']+"\r\n"
                    observacao += "    Frete: "+detalhe['txtFrete']+"\r\n"
                    
                    observacao += "    Logradouro: "+entrega['Logradouro']+"\r\n"
                    observacao += "    Número: "+entrega['Numero']+"\r\n"
                    observacao += "    Complemento: "+entrega['Complemento']+"\r\n"
                    observacao += "    Bairro: "+entrega['Bairro']+"\r\n"
                    observacao += "    Cidade: "+entrega['Cidade']+" - "+entrega['ESTADO']+"\r\n"
                    observacao += "    CEP: "+entrega['txtCEP']+"\r\n"
                    
                    observacao = observacao.replace("'", "\'")

                    sql  = "INSERT INTO pedidos(id_printone, tipo_contrato, id_cliente, id_vendedor, cliente, contrato, observacoes, data_entrada, previsao_entrega, deletar_origem, formaPagamento, valor, frete, valorFrete, cep, logradouro, numero, complemento, bairro, cidade, estado, created_at) "
                    sql += "VALUES('"+str(detalhe['txtNumeroPedido'])+"', 2, '"+str(id_cliente)+"', '"+str(id_vendedor)+"', '"+str(cliente)+"', 'ColorUP', '"+observacao+"',now(), ADDDATE(now(), INTERVAL 5 DAY), 1, '"+str(detalhe['txtFormaPagamento'])+"', "+str(detalhe['numValorProdutos'])+", '"+str(detalhe['txtFrete'])+"', "+str(detalhe['numValorFrete'])+",     '"+str(entrega['txtCEP'])+"', '"+str(entrega['Logradouro'])+"', '"+str(entrega['Numero'])+"', '"+str(entrega['Complemento'])+"', '"+str(entrega['Bairro'])+"', '"+str(entrega['Cidade'])+"', '"+str(entrega['ESTADO'])+"', now())"
                    myZangraf.execute(sql)
                    mydbZangraf.commit()
                
                
                if id_pedido == None:
                    sql  = "SELECT id FROM pedidos WHERE id_printone = '"+str(detalhe['txtNumeroPedido'])+"' AND id_cliente = '"+str(id_cliente)+"'"
                    myZangraf.execute(sql)
                    id_pedido = myZangraf.fetchone()[0]
                
                '''
                # cadastro de serviços de frete
                sql  = "INSERT INTO pedido_item_servicos(id_pedido, id_servico, id_servico_externo, valor, created_at) "
                sql += "VALUES("+str(id_pedido)+", 2, 30, "+str(detalhe['numValorFrete'])+", now())"
                myZangraf.execute(sql)
                mydbZangraf.commit()
                '''

                # processa produtos
                produtos = []
                for produto in detalhe['Produtos']:
                    tipo_produto = 0 # simples
                    
                    # diretório do produto
                    path_prod = path + str(produto['txtCodProduto']) + '/'
                    if not os.path.exists(path_prod):
                        os.mkdir(path_prod)
                    
                    # diretório do album
                    if len(produto['Arquivos']) > 0:
                        #album = Add0(produto['idProdutoCarrinho'])
                        album = produto['idProduto']
                        path_album = path_prod + album + '/'
                        if not os.path.exists(path_album):
                            os.mkdir(path_album)
                        
                        
                        for arquivo in produto['Arquivos']:
                            filename = path_album + arquivo['txtNomeArquivo']
                            #if not os.path.exists(filename):
                            urllib.request.urlretrieve(arquivo['txtURL'], filename)
                            
                            # extrai JPG do PDF
                            info = pdfinfo_from_path(filename, userpw=None, poppler_path=None)
                            images = convert_from_path(filename, dpi=300, first_page=1, last_page = info["Pages"])

                            cnt = 1
                            for image in images:
                                image.save(path_album+' '+Add0(cnt)+'.jpg', 'JPEG')
                                cnt += 1
                            
                            # exclui PDF
                            os.remove(filename)
                            
                        
                        # Mapear produtos compostos
                        sql  = "SELECT i.id_sax, i.intervalo, i.unid_medida, i.preco FROM print_one_produtos p "
                        sql += "JOIN print_one_produto_items i ON i.id_produto = p.id "
                        sql += "WHERE p.id_printone = " + str(produto['txtCodProduto'])
                        myZangraf.execute(sql)
                        result = myZangraf.fetchall()
                        
                        if len(result) > 0: # se o produto for composto, retornará os resultados
                            tipo_produto = 1 # composto

                            arquivos = ListaJpg(path_album)
                            for row in result:
                                quantidade = 1
                                preco = row[3]
                                
                                path_prod_novo = path + str(row[0]) + '/'
                                if not os.path.exists(path_prod_novo):
                                    os.mkdir(path_prod_novo)

                                path_prod_novo += album + '/'
                                if not os.path.exists(path_prod_novo):
                                    os.mkdir(path_prod_novo)
                                
                                if row[1] != None:
                                    paginas = row[1].split(',')
                                    cnt = 0
                                    for pagina in paginas:
                                        if pagina.find('-') >= 0:
                                            intervalo = pagina.split('-')
                                            ini = int(intervalo[0])
                                            fim = int(intervalo[1])

                                            while ini <= fim:
                                                os.rename(path_album + arquivos[ini-1], path_prod_novo + arquivos[ini-1])
                                                ini += 1
                                                cnt += 1
                                        else:
                                            os.rename(path_album + arquivos[int(pagina)-1], path_prod_novo + arquivos[int(pagina)-1])
                                            cnt += 1
                                    
                                    if int(row[2]) == 2:
                                        quantidade = float(produto['numQuantidade']) * cnt
                                        preco = float(row[3]) / quantidade
                                    else:
                                        quantidade = int(produto['numQuantidade'])
                                        preco = row[3]
                                
                                produtos.append([row[0], quantidade, str(preco)])

                            
                            try:
                                # deleta pasta original
                                shutil.rmtree(path_prod)
                                if os.path.exists(path_prod):
                                    os.rmdir(path_prod)
                            except:
                                None

                        else:
                            produtos.append([produto['txtCodProduto'],produto['numQuantidade'], produto['numValorUnitario']])
                        
                
                try:
                    for prod in produtos:
                        # diretório do produto
                        path_prod = path + str(prod[0]) + '/'
                        
                        sql = "SELECT count(*) FROM pedido_items WHERE id_pedido = "+str(id_pedido)+" AND id_produto_externo = "+str(prod[0])
                        myZangraf.execute(sql)
                        cnt_item = myZangraf.fetchone()[0]
                        
                        if cnt_item == 0:
                            # busca id do produto
                            sql = "SELECT id FROM produtos WHERE id_externo = " + str(prod[0])
                            myZangraf.execute(sql)
                            id_produto = myZangraf.fetchone()
                            if id_produto != None:
                                id_produto = id_produto[0]
                            else:
                                id_produto = 'NULL'

                            # criar ítens de pedidos
                            sql  = "INSERT INTO pedido_items(id_pedido, id_produto, id_produto_externo, quantidade, preco_unitario, url_origem, corrigir, created_at) "
                            sql += "VALUES("+str(id_pedido)+", "+str(id_produto)+", '"+str(prod[0])+"', '"+str(prod[1])+"', '"+str(prod[2])+"', '"+str(path_prod)+"', 0, now())"
                            myZangraf.execute(sql)
                    
                    sql = "UPDATE pedidos SET updated_at = now() WHERE id = "+str(id_pedido)
                    myZangraf.execute(sql)

                    mydbZangraf.commit()
                except mysql.connector.Error as err:
                    mydbZangraf.rollback()
                    print('Erro')

                

                ##### atualiza status de pedido #####
                url = "http://colorup.com.br/webservices/v1/pedidoTrocaStatus.asp?txtAPIKey="+apiKey+"&txtFormatoRetorno=JSON&numeroPedido="+str(detalhe['txtNumeroPedido'])+"&idStatus=7"
                r = requests.get(url)
                

            except:
                None
            

    pagAtual += 1

