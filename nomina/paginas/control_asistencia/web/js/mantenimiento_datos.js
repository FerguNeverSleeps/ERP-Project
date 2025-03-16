jQuery(document).ready(function() { 

    $('#table_datatable').DataTable({
        "iDisplayLength": 25,
        "aaSorting":[[1,'asc'], [2, 'asc'], [3, 'asc']], 
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "ajax/server_side/mantenimiento_datos.php?archivo="+archivo_reloj,
        "sPaginationType": "bootstrap_extended", 
        "oLanguage": {
            "sProcessing": "Procesando...",
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
            [5, 10, 25, 50, 100,  -1],
            [5, 10, 25, 50, 100, "Todos"]
        ],                
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': [0, 5, 6, 7] },
            { "bSearchable": false, "aTargets": [0, 5, 6, 7] },
            { "sWidth": "5%", "aTargets": [0, 5, 6, 7] },
           // { "sWidth": "17%", "aTargets": [1] },
           // { "sWidth": "20%", "aTargets": [2, 3, 4] },
            { "sClass": "text-center", "aTargets": [ 0, 1, 2, 3, 4, 5, 6, 7 ] },
           // { "bVisible": false, "aTargets": [ 4, 5 ] }
        ],
        "fnDrawCallback": function() {
              $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
              $('[data-toggle="tooltip"]').tooltip(); 
              $("#table_datatable tbody input:checkbox").uniform();  

              $(".group-checkable").change();
        },
        "fnInitComplete": function(oSettings, json) {
                //window.setTimeout(function () {
                   // App.unblockUI('#blockui_portlet_body');
                   // $('#table_datatable').children('tbody').show();                                         
                //}, 2000);       
                //$('[data-toggle="tooltip"]').tooltip(); 
                //$("#table_datatable tbody input:checkbox").uniform();        
        }
    });
    
    /*
    $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
        $(this).parents('tr').toggleClass("active");
    });
    */

    $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
    $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 

    $(".group-checkable").change(function(){
        var checked = $(this).is(":checked");

        $('#table_datatable > tbody > tr > td:nth-child(1) input[type="checkbox"]').each(function () {
            $(this).attr("checked", checked);
        });

        $.uniform.update('#table_datatable > thead > tr > th:nth-child(1) input[type="checkbox"]');
        $.uniform.update('#table_datatable > tbody > tr > td:nth-child(1) input[type="checkbox"]');
    });
});

function corregir_error(tipo_error, archivo, codigo, total_entr, total_sali){

    // Primero tengo que ver que checkbox se marcaron
    var chk = $("#table_datatable tbody input:checked" ).length;

    if(chk>0 || (typeof codigo != 'undefined') )
    {
        if(chk>0)
            data = $("#frmPrincipal").serialize() + "&tipo_error="+tipo_error+"&archivo="+archivo;
        if(typeof codigo != 'undefined')
            data = "codigo="+codigo+"&tipo_error="+tipo_error+"&archivo="+archivo+"&total_entr="+total_entr+"&total_sali="+total_sali;

        console.log("Data: " +data);

        App.blockUI({
            target: '#blockui_portlet_body',
            boxed: true,
            message: 'Procesando...'
        });

        $.ajax({
            method:   "POST",
            url:      "ajax/corregir_tipo_error.php",
            data: data,
            success: function(data){
                console.log(data);

                $('#table_datatable').DataTable().fnDraw(); 

                 if(chk>0)
                 {
                    console.log("Desmarcar Todos");
                    $(".group-checkable").prop("checked", false);
                    $(".group-checkable").change();
                 }

                window.setTimeout(function () {
                    App.unblockUI('#blockui_portlet_body');
                    $("#div-result").show();
                }, 1000);  

            }
        });
    }
    else
    {
        alert("Debe seleccionar al menos un registro");
    }
}

function enviar(op,id, archivo){
  if (op==2)
  {   // Opcion de Modificar
    document.frmPrincipal.registro_id.value=id;   
    document.frmPrincipal.action="agregar_movimiento_reloj.php?archivo="+archivo;
    document.frmPrincipal.submit();   
  }
  if (op==3)
  {   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro de querer eliminar el registro?"))
    {         
      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
      document.frmPrincipal.submit();
    }   
  }
}