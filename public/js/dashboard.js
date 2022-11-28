var _token = document.getElementsByName('_token')[0].value;

function Decimal(val){
    return parseFloat(parseFloat(val).toFixed(2))
}

function Data(mes = null){
    var d = new Date();
    if(mes != null){
        d.setMonth(d.getMonth() + mes);
    }

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' +
        (month<10 ? '0' : '') + month + '-' +
        (day<10 ? '0' : '') + day;
    
    return output;
}

function StatusServicos(url){
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var html = "";
            result.map((r)=>{
                html += "<tr>";
                html += "<td>"+r.descricao+"</td>";
                html += "<td>"+r.ultimo_processo+"</td>";
                
                if(r.reiniciar == 1){
                    html += "<td class='text-warning'>Reiniciando</td>";
                }else if(r.ativo == 1){
                    html += "<td class='text-success'>Ativo</td>";
                }else{
                    html += "<td><button class='btn btn-danger btn-sm' onclick='StatusServicosRestart("+r.id+")'><i class='fa fa-redo'></i></button></td>";
                }
                
                html += "</tr>";
            });

            $('#tb_status_servicos').html(html);
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function StatusServicosRestart(id){
    $.ajax({
        url: 'statusservicosresart',
        method: 'post',
        dataType: 'json',
        data: { 
            id,
            _token 
        },
        success: function (result) {
            StatusServicos("http://producao.zangraf.local/statusservicos");
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function ProducaoMensal(url){
    var container_width = $('#chart_producao_mensal').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Mês", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.mes, parseFloat(r.quantidade)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation" },
                   ]);

                var options = {
                    title: "Produções Mensais",
                    width: container_width,
                    height: container_width/2,
                    bar: {groupWidth: "50%"},
                    legend: { position: "none" },
                    vAxis: {
                        title: 'OPs'
                    }
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_producao_mensal"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function ImpressaoMensal(url){
    var container_width = $('#chart_impressao_mensal').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Mês", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.mes, parseFloat(r.quantidade)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation" },
                   ]);

                var options = {
                    title: "Impressões Mensais",
                    width: container_width,
                    height: container_width/2,
                    bar: {groupWidth: "50%"},
                    legend: { position: "none" },
                    vAxis: {
                        title: 'Impressões',
                    }
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_impressao_mensal"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function PerdaMensal(url){
    var container_width = $('#chart_perda_pensal').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Mês", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.mes, parseFloat(r.quantidade)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation" },
                   ]);

                var options = {
                    title: "Perdas Mensais",
                    width: container_width,
                    height: container_width/2,
                    bar: {groupWidth: "50%"},
                    legend: { position: "none" },
                    vAxis: {
                        title: 'Fotos'
                    }
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_perda_pensal"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function OpPorCelula(url){
    var container_width = $('#chart_op_celula').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Célula", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.celula, parseFloat(r.quantidade)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation" },
                   ]);

                var options = {
                    title: "OPs por células",
                    width: container_width,
                    height: container_width,
                    chartArea: {width: '50%'},
                    legend: { position: "none" },
                    hAxis: {
                      title: 'Ordens de Produção',
                      minValue: 0
                    },
                    vAxis: {
                      title: 'Células'
                    }
                };
                var chart = new google.visualization.BarChart(document.getElementById("chart_op_celula"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function AlbunsMensais(url){
    var container_width = $('#chart_albuns_mensais').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Mês", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.mes, parseFloat(r.quantidade)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation" },
                   ]);

                var options = {
                    title: "Álbuns Mensais",
                    width: container_width,
                    height: container_width/2,
                    bar: {groupWidth: "50%"},
                    legend: { position: "none" },
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_albuns_mensais"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function TempoPorCelula(url){
    var container_width = $('#chart_tempo_medio_celula').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Célula", "Horas"]
            ];
            result.map((r) => {
                var tempo = r.tempo / 60 / 60;
                if(tempo > 0.001){
                    dados[dados.length] = [r.celula, Decimal(tempo)];
                }
            });
            
            google.charts.load("current", {packages:['corechart', 'bar']});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation" },
                   ]);

                var options = {
                    title: "Tempo Médio por Célula",
                    width: container_width,
                    height: container_width,
                    chartArea: {width: '50%'},
                    legend: { position: "none" },
                    hAxis: {
                      title: 'Tempo médio (Horas)',
                      minValue: 0
                    },
                    vAxis: {
                      title: 'Células'
                    }
                  };
                var chart = new google.visualization.BarChart(document.getElementById("chart_tempo_medio_celula"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function SituacaoOPsMensais(url){
    var container_width = $('#chart_situacao_op').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Mês", 'Finalizados', 'Cancelados', 'bm', 'Orçamentos', 'Pedidos']
            ];
            result.map((r) => {
                dados[dados.length] = [
                    r.mes, 
                    parseFloat(r.fi), 
                    parseFloat(r.ca), 
                    parseFloat(r.bm), 
                    parseFloat(r.ea), 
                    parseFloat(r.ai), 
                ];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                    2, { calc: "stringify",
                        sourceColumn: 2,
                        type: "string",
                        role: "annotation" },
                    3, { calc: "stringify",
                        sourceColumn: 3,
                        type: "string",
                        role: "annotation" },
                    4, { calc: "stringify",
                        sourceColumn: 4,
                        type: "string",
                        role: "annotation" },
                    5, { calc: "stringify",
                        sourceColumn: 5,
                        type: "string",
                        role: "annotation" },
                    ]);
                
                var options = {
                    title: "Situação de OSs",
                    width: container_width,
                    height: container_width/2,
                    bar: { groupWidth: '75%' },
					isStacked: true,
                    legend: { position: "top" },
                    vAxis: {
                        title: '%'
                    }
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);
                formatter.format(data, 2);
                formatter.format(data, 3);
                formatter.format(data, 4);
                formatter.format(data, 5);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_situacao_op"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function ProdutosMaisVendidos(url){
    var container_width = $('#chart_ProdutosMaisVendidos').width();

    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Produtos", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.produto, Decimal(r.quantidade)];
            });

            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var options = {
                    title: "Produtos mais vendidos",
                    width: container_width,
                    height: container_width/4,
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.PieChart(document.getElementById("chart_ProdutosMaisVendidos"));
                chart.draw(data, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function ProdutosMaisProduzidos(url){
    var container_width = $('#chart_ProdutosMaisProduzidos').width();

    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Produtos", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.produto, Decimal(r.quantidade)];
            });

            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var options = {
                    title: "Produtos mais produzidos",
                    width: container_width,
                    height: container_width/4,
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.PieChart(document.getElementById("chart_ProdutosMaisProduzidos"));
                chart.draw(data, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}


// FINANCEIRO

function IndiceLiquidez(url){
    var container_width = $('#chart_indice_liquidez').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ['Data', 'Índice de vencimento', 'Índice de recebimento']
            ];
            result.map((r) => {
                dados[dados.length] = [
                    r.data, 
                    parseFloat(r.indice_vencimento), 
                    parseFloat(r.indice_recebimento)
                ];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                    2, { calc: "stringify",
                        sourceColumn: 2,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    title: "Índice de liquidêz",
                    width: container_width,
                    height: container_width/4,
                    hAxis: {title: "Semanas"},
                    vAxis: {title: "%"}
                };
                
                var chart = new google.visualization.ColumnChart(document.getElementById("chart_indice_liquidez"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function MediaPrazosRecebimento(url){
    var container_width = $('#chart_mediaPrazosRecebimento').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ['Mês', 'Média']
            ];
            result.map((r) => {
                dados[dados.length] = [r.data, parseFloat(r.media)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    title: "Media de Prazos de Recebimento",
                    width: container_width,
                    height: container_width/4,
                    legend: { position: "none" },
                    vAxis: {title: "Dias"},
                };
                
                var chart = new google.visualization.ColumnChart(document.getElementById("chart_mediaPrazosRecebimento"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function TotalPagarReceber(url){
    var container_width = $('#chart_totalPagarReceber').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Element", "Density" ],
                ['A pagar', parseFloat(result[0].a_pagar)],
                ['A receber', parseFloat(result[0].a_receber)]
            ];
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    title: "Total a pagar e receber",
                    width: container_width,
                    height: container_width/2,
                    legend: { position: "none" },
                    vAxis: {title: "R$"},
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_totalPagarReceber"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function Top5Pagar(url){
    var container_width = $('#chart_top5pagar').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ['Fornecedor', 'Valor']
            ];
            result.map((r) => {
                dados[dados.length] = [r.fornecedor, parseFloat(r.valor)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    title: "5 mais a pagar",
                    width: container_width,
                    height: container_width/2,
                    legend: { position: "none" },
                    vAxis: {title: "R$"},
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_top5pagar"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function Top5Receber(url){
    var container_width = $('#chart_top5receber').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ['Cliente', 'Valor']
            ];
            result.map((r) => {
                dados[dados.length] = [r.cliente, parseFloat(r.valor)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart1);
            function drawChart1() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    title: "5 mais a pagar",
                    width: container_width,
                    height: container_width/2,
                    legend: { position: "none" },
                    vAxis: {title: "R$"},
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_top5receber"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function ValoresPorTipos(url){
    var container_width = $('#chart_valoresPorTipos').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ['Tipo', 'A pagar', 'A receber'],
                ['Contas', Decimal(result[0].ct_pagar), Decimal(result[0].ct_receb)],
                ['Cheques', Decimal(result[0].ch_pagar), Decimal(result[0].ch_receb)],
                ['Cartões', Decimal(result[0].cartao_pagar), Decimal(result[0].cartao_receb)]
            ];

            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([
                    0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                    2, { calc: "stringify",
                        sourceColumn: 2,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    title: "Valores por tipos",
                    width: container_width,
                    height: container_width/2,
                    legend: { position: 'bottom' },
                    vAxis: {title: "Reais"},
                };

                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                      groupingSymbol: '.',
                    fractionDigits: 2
                });
                formatter.format(data, 1);
                formatter.format(data, 2);

                var chart = new google.visualization.ColumnChart(document.getElementById("chart_valoresPorTipos"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function PerdasPorFaturamento(url){
    var container_width = $('#chart_perdasPorFaturamento').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ['Mês', 'Faturado', 'Perdido']
            ];
            
            result.map((r) => {
                var titulo = r.mes + ' ('+Decimal(r.perdas_percentual)+'%)';
                var perdido = r.perdido * r.perdas_percentual;
                var produzido = r.valor;
                dados[dados.length] = [titulo, Decimal(produzido), Decimal(perdido)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([
                    0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                    2, { calc: "stringify",
                        sourceColumn: 2,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    title: "Perdas por faturamento",
                    width: container_width,
                    height: container_width/2,
                    legend: { position: 'bottom' },
                    bar: { groupWidth: '75%' },
                    isStacked: true,
                    vAxis: {title: "Reais"},
                };

                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                      groupingSymbol: '.',
                    fractionDigits: 2
                });
                formatter.format(data, 1);
                formatter.format(data, 2);
                
                var chart = new google.visualization.ColumnChart(document.getElementById("chart_perdasPorFaturamento"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function ComprasPorFaturamento(url){
    var container_width = $('#chart_comprasPorFaturamento').width();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ['Mês', 'Compras', 'Faturamento']
            ];
            
            result.map((r) => {
                dados[dados.length] = [r.mes, Decimal(r.compras), Decimal(r.comp_fat)];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);
          
                var options = {
                    title: 'Compras por faturamento',
                    width: container_width,
                    height: container_width/4,
                    curveType: 'function',
                    legend: { position: 'bottom' },
                    vAxis: {title: "%"},
                };
          
          
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 2,
                });
                formatter.format(data, 1);
                formatter.format(data, 2);
                
                var chart = new google.visualization.LineChart(document.getElementById("chart_comprasPorFaturamento"));
                chart.draw(data, options);
                
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function BoletosPorMes(url){
    var container_width = $('#chart_boletosPorMes').width();

    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            var dados = [
                ["Boletos", "Valor"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.tipo, Decimal(r.valor)];
            });

            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var options = {
                    title: "Boletos mensais",
                    width: container_width,
                    height: container_width/4,
                    pieHole: 0.6,
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 2
                });
                formatter.format(data, 1);
                
                var chart = new google.visualization.PieChart(document.getElementById("chart_boletosPorMes"));
                chart.draw(data, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}
    
function BoletosAVencerPorSemana(url){
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { _token },
        success: function (result) {
            $('#boletoAVencerPorSemana').text('R$ '+result[0].valor.replace('.',','));
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function RankingIndicadores(url){
    var container_width = $('#chart_RankingIndicadores').width();
    var dt_inicio = $('#dt_inicio_RankingIndicadores').val();
    var dt_fim = $('#dt_fim_RankingIndicadores').val();

    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { 
            _token,
            dt_inicio,
            dt_fim
        },
        success: function (result) {
            var dados = [
                ["Indicadores", "Quantidade"]
            ];
            result.map((r) => {
                dados[dados.length] = [r.indicador, Decimal(r.quantidade)];
            });

            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var options = {
                    width: container_width,
                    height: container_width/4,
                };
                
                var formatter = new google.visualization.NumberFormat({
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                    fractionDigits: 0
                });
                formatter.format(data, 1);

                var chart = new google.visualization.PieChart(document.getElementById("chart_RankingIndicadores"));
                chart.draw(data, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function IndicadoresPorOP(url){
    var container_width = $('#chart_IndicadoresPorOP').width();
    var op = $('#op').val();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { 
            _token,
            op
        },
        success: function (result) {
            var dados = [
                ['Indicador', 'Quantidade']
            ];
            
            result.map((r) => {
                dados[dados.length] = [r.indicador, r.quantidade];
            });
            
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    width: container_width,
                    height: container_width/2,
                    legend: { position: "none" },
                    vAxis: {title: "Quantidade"},
                };
                
                var chart = new google.visualization.ColumnChart(document.getElementById("chart_IndicadoresPorOP"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}

function IndicadoresPorProduto(url){
    var container_width = $('#chart_IndicadoresPorProduto').width();
    var dt_inicio = $('#dt_inicio_IndicadoresPorProduto').val();
    var dt_fim = $('#dt_fim_IndicadoresPorProduto').val();
    var produto = $('#produto').val();
    
    $.ajax({
        url,
        method: 'post',
        dataType: 'json',
        data: { 
            _token,
            dt_inicio,
            dt_fim,
            produto
        },
        success: function (result) {
            var dados = [
                ['Indicador', 'Quantidade']
            ];
            
            result.map((r) => {
                dados[dados.length] = [r.indicador, r.quantidade];
            });
            console.log(dados)
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(dados);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 
                    1, { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                ]);
                    
                var options = {
                    width: container_width,
                    height: container_width/2,
                    legend: { position: "none" },
                    vAxis: {title: "Quantidade"},
                };
                
                var chart = new google.visualization.ColumnChart(document.getElementById("chart_IndicadoresPorProduto"));
                chart.draw(view, options);
            }
          
        },
        error: function (result) {
            console.log('Erro',result);
        }
    });
}
