<?php
function Autorizacao($funcao){
    if(!is_null(session()->get('funcoes')))
        foreach(session()->get('funcoes') as $funcUser)
        {
            if(in_array($funcUser, $funcao))
                return true;
        }
    
    return false;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>@yield('title') - Produção</title>
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <!--<link href="{{ asset('css/toastr.css') }}" rel="stylesheet" />-->
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
        <!--<script src="{{ asset('js/toastr.js') }}"></script>-->

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
    </head>
	
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="/">Produção</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
			
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
			<!--
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
				-->
            </form>
			
            <!-- Navbar-->
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#">Settings</a>
                        <a class="dropdown-item" href="#">Activity Log</a>
                        <div class="dropdown-divider"></div>
						
						<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sair</a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
							@csrf
						</form>
                    </div>
                </li>
            </ul>
        </nav>
		
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="/home">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            @if(Autorizacao(['gsl_ver','gsl_edit']))
                                <a class="nav-link" href="/gsl">
                                    <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                                    Fluxo
                                </a>
                            @endif
							
                            @if(Autorizacao(['imp_ferr_ver','imp_ferr_edit','imp_mod_ver','imp_mod_edit','imp_hotf_ver','imp_hotf_edit','imp_substr_ver','imp_substr_edit','produto_ver','produto_edit','cliente_ver','cliente_edit']))
                                <div class="sb-sidenav-menu-heading">Cadastro</div>

                                @if(Autorizacao(['imp_ferr_ver','imp_ferr_edit','imp_mod_ver','imp_mod_edit']))
                                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseImposicao" aria-expanded="false" aria-controls="collapseLayouts">
                                    <div class="sb-nav-link-icon"><i class="fas fa-th-large"></i></div>
                                    Imposição
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseImposicao" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                    @if(Autorizacao(['imp_ferr_ver','imp_ferr_edit']))
                                        <a class="nav-link" href="/ferramentas_imposicao">Ferramentas de Imposição</a>
                                    @endif
                                    @if(Autorizacao(['imp_mod_ver','imp_mod_edit']))
                                        <a class="nav-link" href="/modelos_imposicao">Modelos de Imposição</a>
                                    @endif
                                    </nav>
                                </div>
                                @endif

                                @if(Autorizacao(['imp_hotf_ver','imp_hotf_edit','imp_substr_ver','imp_substr_edit']))
                                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseImpressao" aria-expanded="false" aria-controls="collapsePages">
                                    <div class="sb-nav-link-icon"><i class="fas fa-print"></i></div>
                                    Impressão
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseImpressao" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                    @if(Autorizacao(['imp_hotf_ver','imp_hotf_edit']))
                                        <a class="nav-link" href="/hotfolders_impressao">HotFolders</a>
                                    @endif
                                    @if(Autorizacao(['imp_substr_ver','imp_substr_edit']))
                                        <a class="nav-link" href="/substratos_impressao">Substratos</a>
                                    @endif
                                    </nav>
                                </div>
                                @endif

                                @if(Autorizacao(['cliente_ver','cliente_edit']))
                                <a class="nav-link" href="/clientes">
                                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                    Clientes
                                </a>
                                @endif
                                @if(Autorizacao(['produto_ver','produto_edit']))
                                <a class="nav-link" href="/produtos">
                                    <div class="sb-nav-link-icon"><i class="fas fa-images"></i></div>
                                    Produtos
                                </a>
                                @endif
                                @if(Autorizacao(['servico_ver','servico_edit']))
                                <a class="nav-link" href="/servicos">
                                    <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                    Serviços
                                </a>
                                @endif
                                @if(Autorizacao(['tab_preco_ver','tab_preco_edit']))
                                <a class="nav-link" href="/tab_precos">
                                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                    Tabela de preços
                                </a>
                                @endif
                            @endif

                            @if(Autorizacao(['pedido_ver','pedido_edit','recorte_ver','recorte_edit','arquiv_edit','reimp_ver','reimp_edit','guiasOP_ver','guiasOP_edit']))
                                <div class="sb-sidenav-menu-heading">Ordem de Serviços</div>
                                
                                @if(Autorizacao(['pedido_ver','pedido_edit']))
                                <a class="nav-link" href="/pedidos">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                    Ordens de Serviço
                                </a>
                                @endif
                                @if(Autorizacao(['pedido_ver','pedido_edit']))
                                <a class="nav-link" href="/pedidos_itens">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                    Ordens de Produção
                                </a>
                                @endif
                                @if(Autorizacao(['guiasOP_ver','guiasOP_edit']))
                                <a class="nav-link" href="/guiasOP">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                    Imprimir Guias de OPs
                                </a>
                                @endif
                                @if(Autorizacao(['recorte_ver','recorte_edit']))
                                <a class="nav-link" href="/recortes">
                                    <div class="sb-nav-link-icon"><i class="fas fa-crop-alt"></i></div>
                                    Recorte de Imagens
                                </a>
                                @endif
                                @if(Autorizacao(['reimp_ver','reimp_edit']))
                                <a class="nav-link" href="/reimpressoes_album">
                                    <div class="sb-nav-link-icon"><i class="fas fa-redo"></i></div>
                                    Reimpressão de álbuns
                                </a>
                                @endif
                                
                                @if(Autorizacao(['arquiv_edit']))
                                <a class="nav-link" href="/arquivamento">
                                    <div class="sb-nav-link-icon"><i class="fas fa-save"></i></div>
                                    Arquivamento
                                </a>
                                @endif
                            @endif

                            @if(Autorizacao(['integ_ver','integ_edit']))
                            <div class="sb-sidenav-menu-heading">Integração</div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePrintOne" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-print"></i></div>
                                Print-One
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePrintOne" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="/printone_clientes">Clientes</a>
                                    <a class="nav-link" href="/printone">Produtos</a>
                                </nav>
                            </div>
                            @endif

                            @if(Autorizacao(['perfis_ver','perfis_edit']))
                            <div class="sb-sidenav-menu-heading">Usuários</div>
                            <a class="nav-link" href="/perfis">
                                <div class="sb-nav-link-icon"><i class="fas fa-address-card"></i></div>
                                Perfis
                            </a>
                            @endif

                            @if(Autorizacao(['usuarios_ver','usuarios_edit','alt_senhas']))
                            <a class="nav-link" href="/usuarios">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Usuários
                            </a>
                            @endif
                            
                            <a class="nav-link" href="/log">
                                <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                                Logs
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">{{ Auth()->user()->name }}</div>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
						
						@yield('content')
						
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Zangraf Digital Fotolivro 2021</div>
                            <div>
                                <a href="https://www.piovelli.com.br" target="_blank">PIOVELLI - Soluções em TI</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		<!--
		<script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
		<script src="assets/demo/datatables-demo.js"></script>
		-->
    </body>
</html>
