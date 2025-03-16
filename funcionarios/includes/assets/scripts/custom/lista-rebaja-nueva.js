var TableEditableRebaja = function () {
    var oTable = null;
    var oTableEdit = null;
    var fila = 1;
    var nItems = 0;
    return {
        recargar:function(){
            ListadoRebaja.recargar();
        },
        //main function to initiate the module
        guardarNuevaRebaja:function(){
            var itemIds = "";
            var precioN1 = "";
            var precioN2 = "";
            var precioV1 = "";
            var precioV2 = "";
            var total = 0;
            var idItemField = 0;
            var idPrecionV1 = 0;
            var idPrecionV2 = 0;
            var idPrecionN1 = 0;
            var idPrecionN2 = 0;
            nItems = oTable[0].childNodes[4].childNodes.length;
            for (var i = 0; i < nItems; i++) {
                //console.log(oTable[0].childNodes[4].childNodes[i]);
                idItemField = oTable[0].childNodes[4].childNodes[i].childNodes[0].childNodes[1].id;
                idPrecionV1 = oTable[0].childNodes[4].childNodes[i].childNodes[3].childNodes[0].id;
                idPrecionV2 = oTable[0].childNodes[4].childNodes[i].childNodes[5].childNodes[0].id;
                idPrecionN1 = oTable[0].childNodes[4].childNodes[i].childNodes[4].childNodes[0].id;
                idPrecionN2 = oTable[0].childNodes[4].childNodes[i].childNodes[6].childNodes[0].id;
                //console.log(idTallaSelect,idTallaCant)
                itemIds = itemIds + $("#"+idItemField).val() + ","
                precioV1 = precioV1 + $("#"+idPrecionV1).val() + ",";
                precioV2 = precioV2 + $("#"+idPrecionV2).val() + ",";
                precioN1 = precioN1 + $("#"+idPrecionN1).val() + ",";
                precioN2 = precioN2 + $("#"+idPrecionN2).val() + ",";
            };
            itemIds = itemIds.substring(0,itemIds.length-1);
            precioV1 = precioV1.substring(0,precioV1.length-1);
            precioV2 = precioV2.substring(0,precioV2.length-1);
            precioN1 = precioN1.substring(0,precioN1.length-1);
            precioN2 = precioN2.substring(0,precioN2.length-1);

            //console.log(itemIds,precioN1,precioN2);
            //return;
            
            $.ajax({
              dataType: "json",
              async:false,
              url: "../../../includes/clases/controladores/ControladorApp.php",
              data: {
                    accion:'Accion/Rebaja/Save',
                    operacion:'add',                    
                    output:'json',
                    lista1:$("#lista_1").val(),
                    lista2:$("#lista_2").val(),
                    idItems:itemIds,
                    precioV1:precioV1,
                    precioV2:precioV2,
                    precioN1:precioN1,
                    precioN2:precioN2,
                    nItems:nItems,
                    db_connect:DB_CONNECT
              },
              success:  function(data){
                    if(data.mensaje = "OK"){
                        //TableEditableRebaja.recargar();
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
                var combo = "";//'<select onchange="javascript:unlockPrecio('+fila+')" class="bs-select form-control dataTables_filter" data-show-subtext="true" id="item-' + fila + '">';
                //combo = combo + "<option value='' selected></option>" + opcionesComboTallaHtml+"</select>";
                ///jqTds[0].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[0] + '">';
                //combo = '<div class="form-group  col-md-4" style="maring-top=0"><label style="display:block;"></label><input zindex="-1" id="item-' + fila + '" type="text"  onchange="javascript:unlockPrecio('+fila+')" value="" data-source="../../../includes/clases/controladores/ControladorApp.php?accion=Accion/Item/Get_Referencias_Rebajas&db_connect='+DB_CONNECT+'&query=" /></div>'
                /*combo = '<div class="form-group" style="width:100%">'+
                            //'<label class="control-label col-md-4">Loading Remote Data</label>'+
                            '<div class="col-md-14 autocomplete">'+
                            '<input zindex="-1" id="item-' + fila + '" type="text"  onchange="javascript:unlockPrecio('+fila+')" value="" data-source="../../../includes/clases/controladores/ControladorApp.php?accion=Accion/Item/Get_Referencias_Rebajas&db_connect='+DB_CONNECT+'&query=" />'+//'<input type="hidden"  id="item-' + fila + '" class="form-control select2 item-rebaja">'+
                            '</div>'+
                            '</div>';*/
                combo = '<div class="form-group">'+
                    '<div class="col-sm-12">'+
                        '<div class="input-group" id="row-'+fila+'">'+
                            //'<input type="text" id="typeahead_example_3"  class="form-control"/>'+
                        '</div>'+
                    '</div>'+
                '</div>';
                jqTds[0].innerHTML = combo+'<input type="hidden" id="id_item-' + fila + '">';
                jqTds[1].innerHTML = '<input disabled type="text" min="0" class="form-control input-small" id="referencia-' + fila + '" value="">';
                jqTds[2].innerHTML = '<input disabled type="text" min="0" class="form-control input-small" id="descripcion-' + fila + '" value="">';
                jqTds[3].innerHTML = '<input disabled type="number" min="0" class="form-control input-small" id="precio-1-' + fila + '" value="0.0">';
                jqTds[4].innerHTML = '<input disabled type="number" min="0" class="form-control input-small" id="precio-nuevo-1-' + fila + '" value="0.0">';
                jqTds[5].innerHTML = '<input disabled type="number" min="0" class="form-control input-small" id="precio-2-' + fila + '" value="0.0">';
                jqTds[6].innerHTML = '<input disabled type="number" min="0" class="form-control input-small" id="precio-nuevo-2-' + fila + '" value="0.0">';
                jqTds[7].innerHTML = '<a class="delete" href="">Cancelar</a>';

                var referencia = new Ext.form.ComboBox({
                        xtype:          'combo',
                        width:90,
                        mode:           'query',
                        hideTrigger : true,
                        forceSelection : false,
                        typeAhead : false,
                        autoSelect : false,
                        cls:"form-control",
                        triggerAction:  'all',
                        fieldLabel:     'Referencia',
                        name:           'referencia',
                        id:           'referencia-cmb-'+fila,
                        hiddenName:     'referencia',
                        displayField:   'referencia',
                        valueField : 'referencia',
                        renderTo:'row-'+fila,
                        allowBlank:true,
                        editable:true,
                        store: storeBuscarReferenciaEnItem,
                                    listeners:{
                                        beforeselect:function(combo,record,index){
                                        },
                                        select:function(cmb,rec,idx){
                                            //pBuscaItem.main.aplicarFiltroByFormularioProducto();
                                            //unlockPrecio(fila)
                                            //console.log(rec)
                                            var idcmb = this.id;
                                            var filaAux = idcmb.split("-");
                                            var filaSel = filaAux[2];

                                            $("#precio-nuevo-1-"+filaSel).prop("disabled",false);  
                                            $("#precio-nuevo-2-"+filaSel).prop("disabled",false);
                                            var item = rec.data.codigo;
                                            //console.log(fila)
                                            //console.log(item)
                                            //var itemSplitCode = item.split("*");
                                            //console.log(itemSplitCode)

                                            /*$("#referencia-"+fila).val(itemSplitCode[1]);
                                            $("#descripcion-"+fila).val(itemSplitCode[2]);
                                            $("#id_item-"+fila).val(itemSplitCode[0]);*/
                                            //console.log(rec.data);
                                            //console.log($("#precio-nuevo-1-"+fila));
                                            $("#referencia-"+filaSel).val(rec.data.referencia);
                                            $("#descripcion-"+filaSel).val(rec.data.descripcion1);
                                            $("#id_item-"+filaSel).val(rec.data.id_item);

                                            switch($("#lista_1").val()) {
                                                case "A":
                                                    //$("#precio-1-"+fila).val(itemSplitCode[3]);     
                                                    $("#precio-1-"+filaSel).val(rec.data.precio1);
                                                    break;
                                                case "B":
                                                   // $("#precio-1-"+fila).val(itemSplitCode[4]);     
                                                    $("#precio-1-"+filaSel).val(rec.data.precio2);
                                                    break;
                                                case "C":
                                                   // $("#precio-1-"+fila).val(itemSplitCode[5]);     
                                                    $("#precio-1-"+filaSel).val(rec.data.precio3);
                                                    break;
                                                case "D":
                                                   // $("#precio-1-"+fila).val(itemSplitCode[6]);     
                                                    $("#precio-1-"+filaSel).val(rec.data.precio4);
                                                    break;
                                                case "E":
                                                   // $("#precio-1-"+fila).val(itemSplitCode[7]);     
                                                    $("#precio-1-"+filaSel).val(rec.data.precio5);
                                                    break;
                                                default:
                                                   // $("#precio-1-"+fila).val(itemSplitCode[8]);      
                                                    $("#precio-1-"+filaSel).val(rec.data.precio6);
                                            }

                                            if($("#lista_2").val()==$("#lista_1").val())
                                                return;

                                            switch($("#lista_2").val()) {
                                                case "A":
                                                    //$("#precio-2-"+fila).val(itemSplitCode[3]);     
                                                    $("#precio-2-"+filaSel).val(rec.data.precio1);  
                                                    break;
                                                case "B":
                                                    //$("#precio-2-"+fila).val(itemSplitCode[4]);    
                                                    $("#precio-2-"+filaSel).val(rec.data.precio2); 
                                                    break;
                                                case "C":
                                                    //$("#precio-2-"+fila).val(itemSplitCode[5]);    
                                                    $("#precio-2-"+filaSel).val(rec.data.precio3); 
                                                    break;
                                                case "D":
                                                    //$("#precio-2-"+fila).val(itemSplitCode[6]);    
                                                    $("#precio-2-"+filaSel).val(rec.data.precio4); 
                                                    break;
                                                case "E":
                                                    //$("#precio-2-"+fila).val(itemSplitCode[7]);    
                                                    $("#precio-2-"+filaSel).val(rec.data.precio5); 
                                                    break;
                                                default:
                                                    //$("#precio-2-"+fila).val(itemSplitCode[8]);     
                                                    $("#precio-2-"+filaSel).val(rec.data.precio6); 
                                            }   
                                        },
                                        beforequery : function(  queryEvent )   {
                                            //this.setValue(queryEvent.query );
                                        }                        
                                    }
                });
                //$('.autocomplete').autocomplete();
                //ComponentsDropdowns.init();
                //omponentsFormTools.init();
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

            jQuery('#sample_editable_1_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); // modify table search input
            jQuery('#sample_editable_1_wrapper .dataTables_length select').addClass("form-control input-small"); // modify table per page dropdown
            jQuery('#sample_editable_1_wrapper .dataTables_length select').select2({
                showSearchInput : false //hide search box with special css class
            }); // initialize select2 dropdown
            //$("#sample_editable_1 .dataTables_filter input").hide();
            //$("#sample_editable_1_wrapper .dataTables_filter select").hide();

            var nEditing = null;

            $('#sample_editable_1_new').click(function (e) {
                e.preventDefault();
                var aiNew = oTable.fnAddData(['', '','','','','','',
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
            });*/
        }

    };

}();