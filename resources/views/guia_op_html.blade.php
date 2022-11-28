<html>
    <header>
        <style>
            #produto td{
                padding:5px;
            }
        </style>
    </header>
    <body>
        <table width="100%">
            <tr>
                <td>OS: {{$pedido->os}}</td>
                <td>
                    <b>Cliente:</b> {{$pedido->cliente}}<br>
                    <b>Tipo de Pedido:</b> @if($pedido->tipo_contrato == 1) Contrato @else Pedido @endif<br>
                    <b>Contrato:</b> {{$pedido->contrato}}<br>
                    <b>Data de Entrada:</b> {{$pedido->data_entrada}}<br>
                    <b>Previsão de Entrega:</b> {{$pedido->previsao_entrega}}<br>
                    <b>Usuário:</b> {{$pedido->usuario}}<br>
                </td>
                <td>OP: {{$pedido->op}}</td>
            </tr>
        </table>

        <table width="100%" border=1>
            <thead>
                <tr>
                    <th>OP</th>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Álbuns</th>
                    <th>Arquivos</th>
                </tr>
                <tr id="produto">
                    <td><b>{{$pedido->op}}</b></td>
                    <td><b>{{$pedido->id_produto}}</b></td>
                    <td><b>{{$pedido->titulo_produto}}</b></td>
                    <td><b>{{$pedido->albuns}}</b></td>
                    <td><b>{{$pedido->arquivos}}</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach($itens as $item)
                <tr>
                    <td>{{$item->op}}</td>
                    <td>{{$item->id_produto}}</td>
                    <td>{{$item->titulo_produto}}</td>
                    <td>{{$item->albuns}}</td>
                    <td>{{$item->arquivos}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Observações</h4>
        <pre>{{$observacoes}}</pre>

        <h4>Códigos de Barras</h4>


        <h4>Planilha de Álbuns</h4>
        
        <?php
            $cnt = 0;
            $total = 0;
        ?>
        <table border=1>
            <thead>
                <tr>
                    <th>Álbum</th>
                    <th>Fotos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($planilhas as $planilha)
                <tr>
                    <td>{{$planilha->album}}</td>
                    <td>{{$planilha->fotos}}</td>
                </tr>
                <?php
                    $cnt++;
                    $total += $planilha->fotos;
                ?>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td><b><?=$cnt?></b></td>
                    <td><b><?=$total?></b></td>
                </tr>
            </tfoot>
        </table>

        <table border=1>
            <thead>
                <tr>
                    <th></th>
                    <th>Álbum</th>
                    <th>Fotos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>TOTAIS: </b></td>
                    <td><b><?=$cnt?></b></td>
                    <td><b><?=$total?></b></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>