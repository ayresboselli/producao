/*!
    * Start Bootstrap - SB Admin v6.0.2 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    (function($) {
    "use strict";

    // Add active state to sidbar nav links
    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
        $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
            if (this.href === path) {
                $(this).addClass("active");
            }
        });

    // Toggle the side navigation
    $("#sidebarToggle").on("click", function(e) {
        e.preventDefault();
        $("body").toggleClass("sb-sidenav-toggled");
    });
})(jQuery);

$(document).ready(function() {
	$('.dataTable').DataTable({
		"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese.json"},
        "lengthMenu": [[100, 200, 500], [100, 200, 500]],
	});

    $('#tblPedidos').DataTable({
        "order": [[ 0, "desc" ]],
		"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese.json"},
        "lengthMenu": [[100, 200, 500], [100, 200, 500]],
	});

    $('#tblPedidosItens').DataTable({
        "order": [[0, "desc" ], [2, "desc"]],
		"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese.json"},
        "lengthMenu": [[100, 200, 500], [100, 200, 500]],
	});
    
    $('#tblFlow').DataTable({
        "order": [[0, "desc" ], [1, "desc"], [2, "asc"]],
		"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese.json"},
        "lengthMenu": [[200, 500, 1000], [200, 500, 1000]],
	});
    
    $('#tblLog').DataTable({
        "order": [[0, "desc" ]],
		"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese.json"},
        "lengthMenu": [[200, 500, 1000], [200, 500, 1000]],
	});
    
});