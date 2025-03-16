var TableEditable = function () {
    var oTable = null;
    var oTableEdit = null;
    var fila = 1;
    var nItems = 0;
  
    return {
        recargar:function(){
            ListadoEscala.recargar();
        },
        //main function to initiate the module
        guardarNuevaEscala:function(){
            var tallaIds = "";
            var cantidades = "";
            var total = 0;
            var idTallaSelect = 0;
            var idTallaCant = 0;
            nItems = oTable[0].childNodes[4].childNodes.length;
            
            
            for (var i = 0; i < nItems; i++) {
                //console.log(oTable[0].childNodes[4].childNodes[i].childNodes[0].childNodes[0].id);
                idTallaSelect = oTable[0].childNodes[4].childNodes[i].childNodes[0].childNodes[0].id;
                idTallaCant = oTable[0].childNodes[4].childNodes[i].childNodes[1].childNodes[0].id;
                //console.log(idTallaSelect,idTallaCant)
                tallaIds = tallaIds + $("#"+idTallaSelect).val() + ","
                cantidades = cantidades + $("#"+idTallaCant).val() + ",";
                total = total + parseInt($("#"+idTallaCant).val());
                
            };
            tallaIds = tallaIds.substring(0,tallaIds.length-1);
            cantidades = cantidades.substring(0,cantidades.length-1);
            //console.log(tallaIds,cantidades);
           
            if(total != 6 && total != 12 && total != 24){//if(total > 12){
                alert("La escala debes contener 1/2, 1 o 2 docenas");
                return;
            }

            $.ajax({
              dataType: "json",
              async:false,
              url: "../../../includes/clases/controladores/ControladorApp.php",
              data: {
                    accion:'Accion/Escala/Save',
                    operacion:'add',                    
                    output:'json',
                    descripcion:$("#descripcion_escala").val(),
                    talla:tallaIds,
                    cantidad:cantidades,
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

        guardarEditarEscala:function(){
            /*var tallaIds = "";
            var cantidades = "";
            var total = 0;
            var idTallaSelect = 0;
            var idTallaCant = 0;
            nItems = oTable[0].childNodes[4].childNodes.length;
            for (var i = 0; i < nItems; i++) {
                //console.log(oTable[0].childNodes[4].childNodes[i].childNodes[0].childNodes[0].id);
                idTallaSelect = oTable[0].childNodes[4].childNodes[i].childNodes[0].childNodes[0].id;
                idTallaCant = oTable[0].childNodes[4].childNodes[i].childNodes[1].childNodes[0].id;
                //console.log(idTallaSelect,idTallaCant)
                tallaIds = tallaIds + $("#"+idTallaSelect).val() + ","
                cantidades = cantidades + $("#"+idTallaCant).val() + ",";
                total = total + parseInt($("#"+idTallaCant).val());
            };
            tallaIds = tallaIds.substring(0,tallaIds.length-1);
            cantidades = cantidades.substring(0,cantidades.length-1);
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
                    operacion:'add',                    
                    output:'json',
                    escalaDet:escalaDetIds,
                    talla:tallaIds,
                    cantidad:cantidades,
                    nItems:nItems,
                    db_connect:DB_CONNECT
              },
              success:  function(data){
                    if(data.mensaje = "OK"){
                        TableEditable.recargar();
                    }
                    else
                        alert("Ha ocurrido un error");
              }
            });*/
        },

        init: function () {
            function restoreRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);

                for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                    oTable.fnUpdate(aData[i], nRow, i, false);
                }

                oTable.fnDraw();
            }

            function editRow(oTable, nRow) {
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
            }

            function saveRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 2, false);
                oTable.fnDraw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
                oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
                oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 4, false);
                oTable.fnDraw();
            }

            oTable = $('#sample_editable_1').dataTable({
                "aLengthMenu": [
                    [16],
                    [16] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 16,
                
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
            });

            jQuery('#sample_editable_1_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); // modify table search input
            jQuery('#sample_editable_1_wrapper .dataTables_length select').addClass("form-control input-small"); // modify table per page dropdown
            jQuery('#sample_editable_1_wrapper .dataTables_length select').select2({
                showSearchInput : false //hide search box with special css class
            }); // initialize select2 dropdown


            

            var nEditing = null;

            $('#sample_editable_1_new').click(function (e) {
                e.preventDefault();
                var aiNew = oTable.fnAddData(['', '',
                        '<a class="cancel" data-mode="new" href="">Cancel</a>'
                ]);
                nItems++;
                var nRow = oTable.fnGetNodes(aiNew[0]);
                editRow(oTable, nRow);
                nEditing = nRow;
            });

            $('#sample_editable_1 a.delete').live('click', function (e) {
                e.preventDefault();

                //if (confirm("Are you sure to delete this row ?") == false) {
                //    return;
                //}
                nItems--;
                var nRow = $(this).parents('tr')[0];
                oTable.fnDeleteRow(nRow);
                //alert("Deleted! Do not forget to do some ajax to sync with backend :)");
            });

            
            $('#sample_editable_1 a.cancel').live('click', function (e) {
                e.preventDefault();
                if ($(this).attr("data-mode") == "new") {
                    var nRow = $(this).parents('tr')[0];
                    oTable.fnDeleteRow(nRow);
                } else {
                    restoreRow(oTable, nEditing);
                    nEditing = null;
                }
            });

            $('#sample_editable_1 a.edit').live('click', function (e) {
                e.preventDefault();

                /* Get the row as a parent of the link that was clicked on */
                var nRow = $(this).parents('tr')[0];

                if (nEditing !== null && nEditing != nRow) {
                    /* Currently editing - but not this row - restore the old before continuing to edit mode */
                    restoreRow(oTable, nEditing);
                    editRow(oTable, nRow);
                    nEditing = nRow;
                } else if (nEditing == nRow && this.innerHTML == "Save") {
                    /* Editing this row and want to save it */
                    saveRow(oTable, nEditing);
                    nEditing = null;
                    alert("Updated! Do not forget to do some ajax to sync with backend :)");
                } else {
                    /* No edit in progress - let's start one */
                    editRow(oTable, nRow);
                    nEditing = nRow;
                }
            });
        },

        initEditTable: function () {
            /*function restoreRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);

                for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                    oTable.fnUpdate(aData[i], nRow, i, false);
                }

                oTable.fnDraw();
            }

            function editRow(oTable, nRow) {
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
            }

            function saveRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 2, false);
                oTable.fnDraw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
                oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
                oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 4, false);
                oTable.fnDraw();
            }

            oTableEdit = $('#sample_editable_4').dataTable({
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
            });

            jQuery('#sample_editable_4_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); // modify table search input
            jQuery('#sample_editable_4_wrapper .dataTables_length select').addClass("form-control input-small"); // modify table per page dropdown
            jQuery('#sample_editable_4_wrapper .dataTables_length select').select2({
                showSearchInput : false //hide search box with special css class
            }); // initialize select2 dropdown


            

            var nEditing = null;

            $('#sample_editable_4_new').click(function (e) {
                alert("asda");
                return;
                e.preventDefault();
                var aiNew = oTableEdit.fnAddData(['', '',
                        '<a class="cancel" data-mode="new" href="">Cancel</a>'
                ]);
                nItems++;
                var nRow = oTableEdit.fnGetNodes(aiNew[0]);
                editRow(oTableEdit, nRow);
                nEditing = nRow;
            });
            /*
            $('#sample_editable_4 a.delete').live('click', function (e) {
                e.preventDefault();

                //if (confirm("Are you sure to delete this row ?") == false) {
                //    return;
                //}
                nItems--;
                var nRow = $(this).parents('tr')[0];
                oTableEdit.fnDeleteRow(nRow);
                //alert("Deleted! Do not forget to do some ajax to sync with backend :)");
            });*/
        }

    };

}();