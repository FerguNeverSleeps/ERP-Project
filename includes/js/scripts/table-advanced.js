var TableAdvanced = function () {

    var initTable2 = function () {
        var table = $('#profesion');

        /* Table tools samples: https://www.datatables.net/release-datatables/extras/TableTools/ */

        /* Set tabletools buttons and button container */

        $.extend(true, $.fn.DataTable.TableTools.classes, {
            "container": "btn-group tabletools-btn-group pull-right",
            "buttons": {
                "normal": "btn btn-sm default",
                "disabled": "btn btn-sm default disabled"
            }
        });

        var oTable = table.dataTable({
        "processing": true,

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
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
                [0, 'asc'],[1, 'asc']
            ],
            "lengthMenu": [
                [5,10, 15, 20, -1],
                [5,10, 15, 20, "Todo"] // change per page values here
            ],

            // set the initial value
            "pageLength": 10,
            "serverSide": true,
            "ajax": {
                    "url": "../../includes/clases/controladores/table_profesion.php", // ajax source
                }
        });
    $('#profesion').on( 'click', '#eliminar', function () {
        var data = oTable.row( $(this).parents('tr') ).data();
        alert( data[0] +"'ses: "+ data[ 1 ] );
    } );

    }

    return {

        //main function to initiate the module
        init: function () {

            if (!jQuery().dataTable) {
                return;
            }

            //ListarTabla();
            initTable2();

        }

    };

}();
function ListarTabla()
{
    Accion = "Listar";
    $.get("../../includes/clases/controladores/ControlProfesion.php",{Accion:Accion},function(data){
        $("#Listar").append(data);


    });
}