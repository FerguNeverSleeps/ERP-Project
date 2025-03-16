$(document).ready(function() { 

    $('#marcaciones_relojes').DataTable({
        "processing": true,

        // Internationalisation. For more info refer to http://datatables.net/manual/i18n
        "paging":   true,
        "ordering": true,
        "info":     true,
        "searching":     true,
        "language": 
        {
            "aria": 
            {
                "sortAscending": true,
                "sortDescending": true,
            },
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ de _END_ de  _TOTAL_ registros",
            "infoEmpty": "No se encontraron registros",
            "infoFiltered": "(filtrados de _MAX_ total registros)",
            "lengthMenu": "Mostrando _MENU_ entradas",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros"
        },

        "order": [
            [1, 'asc'],[2, 'asc'],[3, 'asc'],[4, 'asc']
        ],
        "columns":[
                            //{orderable: false},
                            {orderable: true},
                            {orderable: true},                            
                            {orderable: true},
                            {orderable: true},
                            {orderable: true},
                            // {orderable: false},
                            // {orderable: false},
                            // {orderable: false},
                            // {orderable: false}
                        ],
        "lengthMenu": 
        [
            [15,30, 40, 50, -1],
            [15,30, 40, 50, "Todo"] // change per page values here
        ],

        // set the initial value
        "pageLength": 15,
        "serverSide": true,
        "ajax": 
        {
            "url": "ajax/ajax_rhexpress_marcaciones_relojes.php", // ajax source
            //data":  {id_empleado:id_empleado}
        }
    });
});
