var ListadoAuditoria = function () {

    var grid = null;
    
    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            autoclose: true
        });
    }

    var handleRecords = function() {

        grid = new Datatable();
            grid.init({
                src: $("#datatable_ajax"),
                onSuccess: function(grid) {
                    // execute some code after table records loaded
                },
                onError: function(grid) {
                    // execute some code on network or other general error  
                },
                dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
                    /* 
                        By default the ajax datatable's layout is horizontally scrollable and this can cause an issue of dropdown menu is used in the table rows which.
                        Use below "sDom" value for the datatable layout if you want to have a dropdown menu for each row in the datatable. But this disables the horizontal scroll. 
                    */
                    //"sDom" : "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>r>>", 
                   
                    "aLengthMenu": [
                        [20, 25],
                        [20, 25] // change per page values here
                    ],
                    "iDisplayLength": 20, // default record count per page
                    "bServerSide": true, // server side processing
                    "sAjaxSource": "../../../includes/clases/controladores/ControladorApp.php", // ajax source &db_connect="+DB_CONNECT
                    "fnServerParams": function ( aoData ) {
                        aoData.push(
                            {"name": "accion", "value": "Accion/Auditoria/Get_List" },
                            {"name": "usuario", "value": $("#usuario_b").val() },
                            {"name": "fecha_desde", "value": $("#fecha_desde_b").val() },
                            {"name": "fecha_hasta", "value": $("#fecha_hasta_b").val() }
                        );
                    },
                    "aaSorting": [[ 1, "asc" ]] // set first column as a default sort by asc
                }
            });

            // handle group actionsubmit button click
            grid.getTableWrapper().on('click', '.table-group-action-submit', function(e){
                e.preventDefault();
                grid.getDataTable().fnDraw();
            });

    }

    return {

        //main function to initiate the module
        init: function () {

            initPickers();
            handleRecords();
        }
    };

}();