$(document).ready(function() { 
    var cedula = $("#cedula").val();
    var tipo  = $("#tipo").val();
    //console.log(cedula+" "+tipo );
    //alert(cedula);
    table = $('#table_datatable').DataTable({
        "processing": true,
        "paging"    : true,
        "ordering"  : true,
        "info"      : false,
        "searching" : true,
        "language": {
            "aria": {
                "sortAscending" : ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
        "emptyTable"  : "No hay datos disponibles en la tabla",
        "info"        : "Mostrando _START_ de _END_ de  _TOTAL_ registros",
        "infoEmpty"   : "No se encontraron registros",
        "infoFiltered": "(filtrados de _MAX_ total registros)",
        "lengthMenu"  : "Mostrando _MENU_ entradas",
        "search"      : "Buscar:",
        "zeroRecords" : "No se encontraron registros"
        },
//        "order": [
//            [0, 'desc']
//        ],
        "lengthMenu": [
            [5,10, 15, 20, 25, -1],
            [5,10, 15, 20, 25, "Todo"] // change per page values here
        ],
        "autoWidth": true,
        // set the initial value
        "pageLength": 10,
        "serverSide": true,
        "ajax": {
                    "url" : "ajax/listadoTiempoDetalles.php", // ajax source
                    "data":  {cedula:cedula,tipo:tipo}
                },
        "columns": [
            { "searchable": false,"width": "10%" },
            { "searchable": false,"width": "10%" },
            { "searchable": true,"width": "60%" },
            { "searchable": false,"width": "10%" },
            { "searchable": false,"width": "10%" },
            { "searchable": false,"width": "10%" },
            { "searchable": false,"width": "10%" }
          ],
         "dom": 'lfrtip',
        
    });
});