var ListadoRebaja = function () {

    var grid = null;
    var gridVerEscala = null;
    var gridEditEscala = null;
    var idEscala = "";

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
                            {"name": "accion", "value": "Accion/Rebaja/Get_List" },
                            {"name": "db_connect", "value": DB_CONNECT },
                            {"name": "usuario", "value": $("#usuario_b").val() },
                            {"name": "escala_id", "value": $("#escala_id_b").val() },
                            {"name": "creacion_desde", "value": $("#fecha_desde_b").val() },
                            {"name": "creacion_hasta", "value": $("#fecha_hasta_b").val() }
                        );
                    },
                    "aaSorting": [[ 1, "asc" ]] // set first column as a default sort by asc
                }
            });

            // handle group actionsubmit button click
            grid.getTableWrapper().on('click', '.table-group-action-submit', function(e){
                e.preventDefault();
                //var action = $(".table-group-action-input", grid.getTableWrapper());
                /*grid.addAjaxParam("usuario", $("#usuario_b").val());
                grid.addAjaxParam("escala_id", $("#escala_id_b").val());
                grid.addAjaxParam("creacion_desde", $("#fecha_desde_b").val());
                grid.addAjaxParam("creacion_hasta", $("#fecha_hasta_b").val());*/
                grid.getDataTable().fnDraw();
                /*if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                    grid.addAjaxParam("sAction", "group_action");
                    grid.addAjaxParam("sGroupActionName", action.val());
                    var records = grid.getSelectedRows();
                    for (var i in records) {
                        grid.addAjaxParam(records[i]["name"], records[i]["value"]);    
                    }
                    grid.getDataTable().fnDraw();
                    grid.clearAjaxParams();
                } else if (action.val() == "") {
                    App.alert({type: 'danger', icon: 'warning', message: 'Please select an action', container: grid.getTableWrapper(), place: 'prepend'});
                } else if (grid.getSelectedRowsCount() === 0) {
                    App.alert({type: 'danger', icon: 'warning', message: 'No record selected', container: grid.getTableWrapper(), place: 'prepend'});
                }*/
            });

    }

    return {

        //main function to initiate the module
        init: function () {

            initPickers();
            handleRecords();
        },

        cargarVerEscala:function(id){
            idEscala = id;
            $("#header-ver-escala").html("Escala "+rellenarConCeros(id, 4))
            gridVerEscala.getDataTable().fnDraw();
        },

        initVerEscala:function(){
            gridVerEscala = new Datatable();
            gridVerEscala.init({
                src: $("#datatable_ajax_2"),
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
                        [5, 10],
                        [5, 10] // change per page values here
                    ],
                    "iDisplayLength": 20, // default record count per page
                    "bServerSide": true, // server side processing
                    "sAjaxSource": "../../../includes/clases/controladores/ControladorApp.php", // ajax source &db_connect="+DB_CONNECT
                    "fnServerParams": function ( aoData ) {
                        aoData.push(
                            {"name": "accion", "value": "Accion/Escala/Get_One" },
                            {"name": "db_connect", "value": DB_CONNECT },
                            {"name": "escala_id", "value": idEscala }
                        );
                    },
                    "aaSorting": [[ 1, "asc" ]] // set first column as a default sort by asc
                }
            });
        },

        cargarEditEscala:function(id){
            gridEditEscala.addAjaxParam("escala_id", id);
            gridEditEscala.getDataTable().fnDraw();
        },

        initEditEscala:function(){

        },

        recargar:function(){
            grid.getDataTable().fnDraw();
        }

    };

}();