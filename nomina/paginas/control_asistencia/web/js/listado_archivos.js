$(document).ready(function() { 

    $('#table_datatable').DataTable({
      "iDisplayLength": 25,
      "sPaginationType": "bootstrap_extended",
      "aaSorting":[[0,'desc']], 
        "oLanguage": {
          "sSearch": "<img src='web/images/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
            "sInfo":"Total _TOTAL_ registros",
            "sInfoFiltered": "",
            "sEmptyTable":  "No hay archivos disponibles",
            "sZeroRecords": "No se encontraron registros",
            "oPaginate": {
                "sPrevious": "P&aacute;gina Anterior",
                "sNext": "P&aacute;gina Siguiente",
                "sPage": "P&aacute;gina",
                "sPageOf": "de",
            }
        },
        "aLengthMenu": [ 
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],                
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': [5, 6] },
            { "bSearchable": false, "aTargets": [5, 6] },
            { "sWidth": "5%", "aTargets": [5, 6] },
            { "sClass": "text-center", "aTargets": [ 0, 1, 2, 3, 4, 5, 6 ] }

        ],
        "fnDrawCallback": function() {
              $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
        }
    });

    $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
    $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
});

function enviar(op,id){

  if (op==3)
  {   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro de querer eliminar el archivo?"))
    {    
      App.blockUI({
          target: '#blockui_portlet_body',
          boxed: true,
          message: 'Procesando...',
      });

      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
      document.frmPrincipal.submit();
    }   
  }
}