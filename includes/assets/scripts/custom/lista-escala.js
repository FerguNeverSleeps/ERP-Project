var ListadoEscala = function () {

    var grid = null;
    var gridVerEscala = null;
    var gridEditEscala = null;
    var idEscala = "";
    var fila = 0;

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
                            {"name": "accion", "value": "Accion/Escala/Get_List" },
                            {"name": "db_connect", "value": DB_CONNECT },
                            {"name": "usuario", "value": $("#usuario_b").val() },
                            {"name": "escala_id", "value": $("#escala_id_b").val() },
                            {"name": "descripcion", "value": $("#escala_descripcion_b").val() },
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
            $("#header-ver-escala").html("Escala "+rellenarConCeros(id, 4));
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

        guardarModificarEscala:function(){
            var tallaIds = "";
            var cantidades = "";
            var total = 0;
            var idTallaSelect = 0;
            var idDet = 0;
            var idTallaCant = 0;
            var detalleIds = "";
            
            var tabla = $("#body-escala-modificar");
            nItems = tabla[0].childNodes.length;
            for (var i = 0; i < nItems; i++) {
                //console.log(tabla[0].childNodes[i].childNodes[0]);
                idDet = tabla[0].childNodes[i].childNodes[0].childNodes[0].id;
                idTallaSelect = tabla[0].childNodes[i].childNodes[0].childNodes[1].id;
                idTallaCant = tabla[0].childNodes[i].childNodes[1].childNodes[0].id;
                //console.log(idTallaSelect,idTallaCant)
                tallaIds = tallaIds + $("#"+idTallaSelect).val() + ","
                cantidades = cantidades + $("#"+idTallaCant).val() + ",";
                detalleIds = detalleIds + $("#"+idDet).val() + ","
                total = total + parseInt($("#"+idTallaCant).val());
            };
            tallaIds = tallaIds.substring(0,tallaIds.length-1);
            cantidades = cantidades.substring(0,cantidades.length-1);
            detalleIds = detalleIds.substring(0,detalleIds.length-1);
            //console.log(tallaIds,cantidades);
            if(total != 6 && total != 12 && total != 24){//if(total > 12){
                alert("La escala debe contener 1/2, 1 o 2 docenas");
                return;
            }

            $.ajax({
              dataType: "json",
              async:false,
              url: "../../../includes/clases/controladores/ControladorApp.php",
              data: {
                    accion:'Accion/Escala/Save',
                    operacion:'update',                    
                    output:'json',
                    descripcion:$("#descripcion_escala_modificada").val(),
                    escala_id:idEscala,
                    talla:tallaIds,
                    cantidad:cantidades,
                    det_ids:detalleIds,
                    nItems:nItems,
                    db_connect:DB_CONNECT
              },
              success:  function(data){
                    if(data.mensaje = "OK"){
                        //TableEditable.recargar();
                        document.location.reload();
                    }
                    else
                        alert("Ha ocurrido un error");
              }
            });
        },  

        cargarEditEscala:function(id){
            idEscala = id;
            //gridEditEscala.addAjaxParam("escala_id", id);
            $("#header-modificar-escala").html("Escala "+rellenarConCeros(id, 4));
            //gridEditEscala.getDataTable().fnDraw();
            ListadoEscala.cargaEscalaEditar(id);
        },



        eliminarTalla:function(fila){
            $("#cantidad-"+fila).val(0);
            $("#col-acc-"+fila).empty();
            $("#cantidad-"+fila).attr("readOnly",true);
            $("#talla-"+fila).attr("readOnly",true);
            $("#col-acc-"+fila).html("<img src='../../../includes/imagenes/icons/exclamation.png' /> Por Eliminar...");
        },

        cargaEscalaEditar:function(escala){
            $("#body-escala-modificar").empty();
            var records = null;
            fila = 0;
            $.ajax({
                dataType: "json",
                async:true,
                url: "../../../includes/clases/controladores/ControladorApp.php",
                data: {
                    accion:'Accion/Escala/Get_One',
                    output:'json',
                    escala_id:escala,
                    db_connect:DB_CONNECT
                },
                success:  function(data){
                    if(data.mensaje = "OK"){
                        
                        //console.log(data.matches)
                        $("#descripcion_escala_modificada").val(data.matches[0].descripcion);
                        var html = "";
                        fila = data.matches.length;
                        for (var i = 0; i < data.matches.length; i++) {
                            html = "<tr id='fila-escala-"+i+"'><td>";
                            html += '<input type="hidden" id="detalle-' + i + '" value="' + data.matches[i].escala_det_id + '">';
                            var combo = '<select class="bs-select form-control dataTables_filter" data-show-subtext="true" id="talla-' + i + '">';
                            combo = combo + opcionesComboTallaHtml+"</select>";
                            
                            html += combo+"</td>";
                            html += '<td id="col-can-'+i+'"><input type="number" min="1" max="12" class="form-control input-small" id="cantidad-' + i + '" value="' + data.matches[i].cantidad + '"></td>';
                            html += '<td id="col-acc-'+i+'"><a onclick="javascript:ListadoEscala.eliminarTalla('+i+')" href="#">Eliminar</a></td></tr>';

                            $('#body-escala-modificar').append(html);
                            $("#talla-"+i).val(data.matches[i].talla_id);   
                        }
                    }
                    else{
                        alert("Ha ocurrido un error cargando la escala");
                    }
                }
            });
        },   

        initEditEscala:function(){
            /*function editRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                aData[1] = 1;
                var combo = '<select class="bs-select form-control dataTables_filter" data-show-subtext="true" id="talla-' + fila + '">';
                combo = combo + opcionesComboTallaHtml+"</select>";
                ///jqTds[0].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[0] + '">';
                jqTds[0].innerHTML = combo;
                jqTds[1].innerHTML = '<input type="number" min="1" max="12" class="form-control input-small" id="cantidad-' + fila + '" value="' + aData[1] + '">';
                jqTds[2].innerHTML = '<a class="delete" href="">Cancelar</a>';
                fila++;
            }*/

            /*gridEditEscala = new Datatable();
            gridEditEscala.init({
                src: $("#sample_editable_4"),
                onSuccess: function(grid) {
                    // execute some code after table records loaded
                },
                onError: function(grid) {
                    // execute some code on network or other general error  
                },
                dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
                    
                    "aLengthMenu": [
                        [5, 10],
                        [5, 10] // change per page values here
                    ],
                    "iDisplayLength": 20, // default record count per page
                    "bServerSide": true, // server side processing
                    "sAjaxSource": "../../../includes/clases/controladores/ControladorApp.php", // ajax source &db_connect="+DB_CONNECT
                    "fnServerParams": function ( aoData ) {
                        aoData.push(
                            {"name": "accion", "value": "Accion/Escala/Get_One_Edit" },
                            {"name": "db_connect", "value": DB_CONNECT },
                            {"name": "escala_id", "value": idEscala },
                            {"name": "editar", "value": '1' }
                        );
                    },
                    "aaSorting": [[ 1, "asc" ]] // set first column as a default sort by asc
                }
            });*/
            /*oTableEdit = $('#sample_editable_4').dataTable({
                "aLengthMenu": [
                    [5],
                    [5] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 5,
                
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ records",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': false,
                        'aTargets': [0]
                    }
                ]
            });*/
            /*
            jQuery('#sample_editable_4_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); // modify table search input
            jQuery('#sample_editable_4_wrapper .dataTables_length select').addClass("form-control input-small"); // modify table per page dropdown
            jQuery('#sample_editable_4_wrapper .dataTables_length select').select2({
                showSearchInput : false //hide search box with special css class
            }); // initialize select2 dropdown
            */

            
            

            //$('#sample_editable_4_new').click(function (e) {
                //console.log(fila)
                /*var html = "<tr id='fila-escala-"+fila+"'><td>";
                html += '<input type="hidden" id="detalle-' + fila + '" value="0">';
                var combo = '<select class="bs-select form-control dataTables_filter" data-show-subtext="true" id="talla-' + fila + '">';
                combo = combo + opcionesComboTallaHtml+"</select>";
                            
                html += combo+"</td>";
                html += '<td id="col-can-'+fila+'"><input type="number" min="1" max="12" class="form-control input-small" id="cantidad-' + fila + '" value="1"></td>';
                html += '<td id="col-acc-'+fila+'"><a onclick="javascript:ListadoEscala.eliminarTalla('+fila+')" href="#">Eliminar</a></td></tr>';

                $('#body-escala-modificar').append(html); 
                alert()*/
           //});
        },

        agregatItem:function () {
            var html = "<tr id='fila-escala-"+fila+"'><td>";
                html += '<input type="hidden" id="detalle-' + fila + '" value="0">';
                var combo = '<select class="bs-select form-control dataTables_filter" data-show-subtext="true" id="talla-' + fila + '">';
                combo = combo + opcionesComboTallaHtml+"</select>";
                            
                html += combo+"</td>";
                html += '<td id="col-can-'+fila+'"><input type="number" min="1" max="12" class="form-control input-small" id="cantidad-' + fila + '" value="1"></td>';
                html += '<td id="col-acc-'+fila+'"><a onclick="javascript:ListadoEscala.eliminarTalla('+fila+')" href="#">Eliminar</a></td></tr>';

                $('#body-escala-modificar').append(html); 
        },

        recargar:function(){
            grid.getDataTable().fnDraw();
        }

    };

}();