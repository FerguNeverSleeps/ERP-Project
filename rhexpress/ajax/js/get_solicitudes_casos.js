$(document).ready(function() { 

    $('#busqueda_solicitudes').DataTable({
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
            //[0, 'asc'],[1, 'asc'],[2, 'asc'],[3, 'asc']
              [0, 'asc'],[1, 'asc'],[2, 'asc'],[3, 'asc'],[4, 'asc'],[5, 'asc'],[6, 'asc']
        ],
        "columns":[
                            {orderable: true},
                            {orderable: true},
                            {orderable: true},                            
                            {orderable: true},
			    {orderable: true},                         
			    {orderable: true},
			    {orderable: true}
                        ],
        "lengthMenu": 
        [
            [5,10, 15, -1],
            [5,10, 15, "Todo"] // change per page values here
        ],

        // set the initial value
        "pageLength": 20,
        "serverSide": true,
        "ajax": 
        {
            "url": "ajax/ajax_busqueda_solicitudes.php", // ajax source
            //data":  {id_empleado:id_empleado}
		data: function(d){      

      		var por_cedula    = $("#por_cedula").val();
      		//var tipo_contribuyente  = $("#tipo_contribuyente").val();

      		return $.extend(d,{       	
   		por_cedula:   por_cedula
      		//tipo_contribuyente: tipo_contribuyente
      			});
    		}  
        }
    });
$("#buscar").on('click',function(){	
      var text=$('#busqueda_solicitudes_filter input[type=search]').val();
      $('#busqueda_solicitudes').dataTable().fnFilter(text);
    });
});
