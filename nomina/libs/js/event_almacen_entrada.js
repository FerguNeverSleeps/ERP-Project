var win;

function validarSerial(serial_id,cod,cantidad){
    var cod2 = cod;
    var serial = $("#"+serial_id);
    var existe = 0;
    if(serial.val() == ""){
        return;
    }

    for(var i = 0; i < parseInt(cantidad); i++){
        //console.log(serial.val(),$("#serial-"+cod2+"-"+i).val());
        //console.log(serial.val(),$("#serial-"+cod2+"-"+i).val());

        if(serial.val() == $("#serial-"+cod2+"-"+i).val() && ("serial-"+cod2+"-"+i) != serial_id){
            alert("Serial Repetido");
            serial.val("");
            serial.focus();
            existe = -1;
            break;
        }
    }
    if(existe == 0){
        existe = Item.existeSerial(serial.val());
        if(existe == 1){
            alert("Serial Registrado");
            serial.val("");
            serial.focus();
        }
    }
}

Ext.onReady(function(){
    $("input[name='totalizar_monto_efectivo'], input[name='totalizar_monto_cheque'], input[name='totalizar_nro_cheque'], input[name='totalizar_monto_tarjeta'], input[name='totalizar_nro_tarjeta'],input[name='totalizar_monto_deposito'], input[name='totalizar_nro_deposito'],input[name='totalizar_nro_otrodocumento'],input[name='totalizar_monto_otrodocumento']").numeric();
    $("#autorizado_por, #nro_control, #nro_factura").blur(function(){
        $(this).css("border-color",$(this).val()===""?"red":"");
        $(this).css("border-width",$(this).val()===""?"1px":"");
    });
    $.setValoresInput = function(nombreObjetoDestino,nombreObjetoActual){
        $(nombreObjetoDestino).attr("value", $(nombreObjetoActual).val());
    }
    $.inputHidden = function(Input,Value,ID,classVal){
        var classVal = classVal || '';
        return '<input class="'+classVal+'" type="hidden" id="'+Input+'" name="'+Input+''+ID+'" value="'+Value+'">';
    }
    $.inputHiddenHTML = function(ID,Value){
        return '<input type="hidden" id="'+ID+'" value="'+Value+'">';
    }
    $("img.eliminar").live("click",function(){
        $(this).parents("tr").fadeOut("normal",function(){
            $(this).remove();
            eventos_form.CargarDisplayMontos();
        });
    });
    var cod;
     $('.editar').live("click", function (event) {
            event.preventDefault();
            cod = $(this).attr("value");
            var valores = $("#html_escala_item_"+cod).text();

            function validaNumerosEdit(event){
                if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8 && event.keyCode != 190 ) {
                    //event.returnValue = false;
                    return false;
                }
                return true;
            }
            /*function validaNumerosEdit(evt) {
                var tecla = String.fromCharCode(evt.which || evt.keyCode);
                if ( !/[\d.\b\r]/.test(tecla) ) return false;
                return true;
            }*/


            if(valores == ""){
                $('#cantidad_'+cod).attr("contenteditable", "true");
                $('#costo_'+cod).attr("contenteditable", "true");

                $('#costo_'+cod).keyup(function(e){ return validaNumerosEdit(e); });
                $('#costo_'+cod).keydown(function(e){ return validaNumerosEdit(e);});
                $('#cantidad_'+cod).keyup(function(e){ return validaNumerosEdit(e);  });
                $('#cantidad_'+cod).keydown(function(e){ return validaNumerosEdit(e);  });

                $(this).css({'background-image': 'url("../../libs/imagenes/ico_save.gif")'});
                $('#cantidad_'+cod).focus();
                $(this).attr("class",'guardar');
            }
            else{
                $('#costo_'+cod).attr("contenteditable", "true");
                $('#costo_'+cod).keyup(function(e){ return validaNumerosEdit(e); });
                $('#costo_'+cod).keydown(function(e){ return validaNumerosEdit(e);});
                var mask = new Ext.LoadMask(Ext.get("Contenido"), {
                    msg:'Cargando..',
                    removeMask:false
                });
                costoActualItem = $('#costo_'+cod).text(); 

                $.ajax({
                type: 'GET',
                data: 'cod='+cod+"&escala=1&editar_escala=1&costo="+costoActualItem,
                url:  'colortalla.php',
                beforeSend: function(){
                    mask.show();
                },
                success: function(data){
                    var escalas_win = new Ext.Window({
                        title:'Escalas',
                        height: 400,
                        width: 750,
                        frame:true,
                        x:70,y:70,
                        autoScroll:true,
                        modal:true,
                        html: data,
                        listeners:{
                            afterrender:function(e){
                                //console.log("render",valores);
                                var aux0 = valores.split("CA");
                                var aux01 = aux0[1].split(",");
                                $( ".cantidad-color").each(function( index ) {                                    
                                    var ID = this.id;
                                    for (var i = 0; i < aux01.length; i++) {
                                        var aux2 = aux01[i].split("=");
                                        if(aux2[0] == ID){
                                            $("#"+ID).val(aux2[1]);
                                        }
                                    }
                                });


                                var aux1 = aux0[0].split("*");
                                var escala = aux1[1];

                                var aux = aux1[0].split(",");
                                $( ".cantidad-color-talla").each(function( index ) {                                    
                                    var ID = this.id;
                                    for (var i = 0; i < aux.length; i++) {
                                        var aux2 = aux[i].split("=");
                                        if(aux2[0] == ID){
                                            $("#"+ID).val(aux2[1]);
                                        }
                                    }
                                });

                                $("#escalas_select").val(escala);
                            }
                        },
                        buttons:[{
                            text:'Guardar',
                            handler: function(){
                                
                                //@MODIFICAR CANTIDAD GENERAL
                                //$.inputHidden("_cantidad", options.cantidad, "[]");
                                //id=\"cantidad_" + options.id_item + "\">"

                                var cantTotalEscala = 0;
                                $( ".cantidad-color-talla").each(function( index ) {
                                    cantEscala = $(this).val();
                                    if(cantEscala != ""){
                                        cantTotalEscala += parseInt(cantEscala);
                                    }
                                });
                                //$("#_cantidad_"+cod).val(cantTotalEscala);
                                $("#cantidad_"+cod).html(cantTotalEscala);
                                $('#_cantidad').attr("value", cantTotalEscala);

                                var nuevo_costo = $('#costo_item_escala').val();

                                $('#costo_'+cod).html(nuevo_costo);
                                $('#_costo').attr("value", nuevo_costo);

                                valorCantidad = $('#cantidad_'+cod).text();

                                
                                $('#real_costo'+cod).html((parseFloat(nuevo_costo)*parseInt(valorCantidad)).toFixed(2));

                                $('#_cantidad').attr("value", valorCantidad);
                                
                                cantidadBulto = $('#cantidad_bulto_'+cod).text();
                                $('#bulto_'+cod).html(Math.ceil(valorCantidad/cantidadBulto));

                                eventos_form.IncluirColoresTallas({
                                        formulario: JSON.stringify($("#form-talla-color").serializeArray())
                                });

                                escalas_win.close();
                            }
                        },{
                            text:'Cancelar',
                            handler: function(){
                                escalas_win.close();
                            }
                        }]
                    });
                    escalas_win.show();
                    mask.hide();
                }
            });
            }
    });
    $('.guardar').live("click", function (event) {
            valorCantidad = $('#cantidad_'+cod).text();
            valorCosto = $('#costo_'+cod).text();
            cantidadBulto = $('#cantidad_bulto_'+cod).text();
            $('#bulto_'+cod).html(Math.ceil(valorCantidad/cantidadBulto));
            $(this).css({'background-image': 'url("../../libs/imagenes/edit.gif")'});
            $('#cantidad_'+cod).attr("contenteditable", "false");            
            $('#costo_'+cod).attr("contenteditable", "false");
            $('#_cantidad').attr("value", valorCantidad);
            $('#_costo').attr("value", valorCosto);
            var costototal = valorCantidad*valorCosto;
            $("#real_costo"+cod).html(costototal.toFixed(2));
    });

     $('#comprobante').live("focusout", function (event) {       
        if(this.value != ""){// && parseInt(this.value) != 0
            valor = eventos_form.Pad(this.value, 10);
            this.value = valor;
            eventos_form.BuscarEntradaPorComprobante(valor);            
        }
        else{
            this.value = "";
        }
    })  
    
//    $("#items").change(function(){
//        //LabelCantidadExistente
//        codAlmacen = $("select[name='almacen']").val();
//        if($(this).val()!=''){
//            $.ajax({
//                type: "GET",
//                url:  "../../libs/php/ajax/ajax.php",
//                data: "opt=verificarExistenciaItemByAlmacen&v1="+codAlmacen+"&v2="+$(this).val(),
//                beforeSend: function(){
//                // $("#descripcion_item").html(MensajeEspera("<b>Veficando Cod. item..<b>"));
//                },
//                success: function(data){
//                    resultado = eval(data)
//                    if(resultado[0].id=="-1"){
//                        alert("Verifique existencia de producto seleccionado")
//                    }else{
//                        $("#cantidad_existente").val(resultado[0].cantidad);
//                    }
//                }
//            });
//        }
//    });
    $(".info_detalle").live("click", function(){
        cod = $(this).parent('tr').find("a[rel*=facebox]").text();
        var mask = new Ext.LoadMask(Ext.get("Contenido"), {
            msg:'Cargando..',
            removeMask:false
        });
        $.ajax({
            type: 'GET',
            data: 'cod='+cod,
            url:  'info_servicio_item.php',
            beforeSend: function(){
                mask.show();
            },
            success: function(data){
                var win_tmp = new Ext.Window({
                    title:'Detalle del Producto',
                    height: 400,
                    width: 350,
                    frame:true,
                    autoScroll:true,
                    modal:true,
                    html: data,
                    buttons:[{
                        text:'Cerrar',
                        handler:function(){
                            win_tmp.hide();
                        }
                    }]
                });
                win_tmp.show(this);
                mask.hide();
            }
        });
    });

  $(".colortalla").live("click", function(){
        cod = $(this).attr("alt");
        var mask = new Ext.LoadMask(Ext.get("Contenido"), {
            msg:'Cargando..',
            removeMask:false
        });
        $.ajax({
            type: 'GET',
            data: 'cod='+cod,
            url:  'colortalla.php',
            beforeSend: function(){
                mask.show();
            },
            success: function(data){
                var win_tmp = new Ext.Window({
                    title:'Colores y Tallas del Producto',
                    height: 400,
                    width: 700,
                    frame:true,
                    autoScroll:true,
                    modal:true,
                    html: data,
                    buttons:[{
                        text:'Guardar',
                        handler: function(){
                        	eventos_form.IncluirColoresTallas({
				                    formulario: JSON.stringify($("#form-talla-color").serializeArray())
				                });
				                 win_tmp.hide();
                        }
                    }]
                });
                win_tmp.show(this);
                mask.hide();
            }
        });
    });   
    

    
    $(".escalas").live("click", function(){
        cod = $(this).attr("alt");
        
        var mask = new Ext.LoadMask(Ext.get("Contenido"), {
            msg:'Cargando..',
            removeMask:false
        });
        
        
        var valores = $("#html_escala_item_"+cod).text();
        if(valores == ""){
            alert("Este producto no posee escalas");
        }
        else{
            $.ajax({
                type: 'GET',
                data: 'cod='+cod+"&escala=1",
                url:  'colortalla.php',
                beforeSend: function(){
                    mask.show();
                },
                success: function(data){
                    var escalas_win = new Ext.Window({
                        title:'Escalas',
                        height: 400,
                        width: 750,
                        frame:true,
                        x:70,y:70,
                        autoScroll:true,
                        modal:true,
                        html: data,
                        listeners:{
                            afterrender:function(e){
                                //console.log("render",valores);
                                var aux0 = valores.split("CA");
                                var aux01 = aux0[1].split(",");
                                $( ".cantidad-color").each(function( index ) {                                    
                                    var ID = this.id;
                                    for (var i = 0; i < aux01.length; i++) {
                                        var aux2 = aux01[i].split("=");
                                        if(aux2[0] == ID){
                                            $("#"+ID).val(aux2[1]);
                                        }
                                    }
                                });


                                var aux1 = aux0[0].split("*");
                                var escala = aux1[1];

                                var aux = aux1[0].split(",");
                                $( ".cantidad-color-talla").each(function( index ) {                                    
                                    var ID = this.id;
                                    for (var i = 0; i < aux.length; i++) {
                                        var aux2 = aux[i].split("=");
                                        if(aux2[0] == ID){
                                            $("#"+ID).val(aux2[1]);
                                        }
                                    }
                                });

                                $("#escalas_select").val(escala);
                            }
                        },
                        buttons:[{
                            text:'OK',
                            handler: function(){
                                /*
                                //@MODIFICAR CANTIDAD GENERAL
                                //$.inputHidden("_cantidad", options.cantidad, "[]");
                                //id=\"cantidad_" + options.id_item + "\">"

                                var cantTotalEscala = 0;
                                $( ".cantidad-color-talla").each(function( index ) {
                                    cantEscala = $(this).val();
                                    if(cantEscala != ""){
                                        cantTotalEscala += parseInt(cantEscala);
                                    }
                                });
                                //$("#_cantidad_"+cod).val(cantTotalEscala);
                                $("#cantidad_"+cod).html(cantTotalEscala);
                                $('#_cantidad').attr("value", cantTotalEscala);
                                eventos_form.IncluirColoresTallas({
                                        formulario: JSON.stringify($("#form-talla-color").serializeArray())
                                });*/

                                escalas_win.close();
                            }
                        }]
                    });
                    escalas_win.show();
                    mask.hide();
                }
            });
        }
        

            
            
    });
    
    

    $(".seriales").live("click", function(){
        cod = $(this).attr("alt");
        var cantidad_seriales = $(this).attr("cantidad");
        var mask = new Ext.LoadMask(Ext.get("Contenido"), {
            msg:'Cargando..',
            removeMask:false
        });

        

        var tableSerialas = "<table class='lista' style='width:100%;'><tbody>";
        for(var i = 0; i < parseInt(cantidad_seriales); i++){//onfocus='this.select()'
            tableSerialas += "<tr>";
            tableSerialas += "<td>"+(i+1)+"</td>";
            tableSerialas += "<td><input onfocus='this.select()' onblur='validarSerial(\"serial-"+cod+"-"+i+"\","+cod+","+parseInt(cantidad_seriales)+")'  id='serial-"+cod+"-"+i+"' style='width=100%' type='text' /></td>";
            tableSerialas += "</tr>";
        }
        tableSerialas += "</tbody></table>";

        var win_tmp = new Ext.Window({
                    title:'Seriales',
                    height: 400,
                    width: 200,
                    frame:true,
                    autoScroll:true,
                    modal:true,
                    html: tableSerialas,
                    //listeners:{
                       // afterrender:function(e){
                            //object.addEventListener("blur", myScript);
                            // onblur='javascript:validarSerial(this,"+cod+");'
                            /*$( ".seriales" ).blur(function() {
                                var cod2 = $(this).attr("alt");
                                var serial = $(this);
                                var existe = 0;

                                for(var i = 0; i < parseInt(cantidad_seriales); i++){
                                    console.log(serial.val(),$("#serial-"+cod2+"-"+i).val())

                                    if(serial.val() == $("#serial-"+cod2+"-"+i).val()){
                                        alert("Serial Repetido");
                                        serial.val("");
                                        existe = -1;
                                        break;
                                    }
                                }
                                if(existe == 0){
                                    existe = Item.existeSerial(serial.val());
                                    if(existe == -1){
                                        alert("Serial Registrado");
                                        serial.val("");
                                    }
                                }
                                
                            });*/
                    //    }
                    //},
                    buttons:[{
                        text:'Guardar',
                        handler: function(){
                            var camposSeriales = "";
                            for(var i = 0; i < parseInt(cantidad_seriales); i++){
                                var serialNuevo = $("#serial-"+cod+"-"+i).val();
                                if(serialNuevo == ""){
                                    alert("No puede ingresar un serial vacio. Serial "+(i+1));
                                    return;
                                }
                                camposSeriales += "<input id='serial-"+cod+"-"+i+"' name='serial-"+cod+"[]' type='hidden' value='"+serialNuevo+"' />";
                            }
                            $("#seriales-fields-"+cod).html(camposSeriales);
                            win_tmp.hide();
                        }
                    }]
                });
                win_tmp.show(this);
                mask.hide();
    });  

    $(".codbarras").live("click", function(){
        cod = $(this).attr("alt");
        var cantidad_barras = $(this).attr("cantidad");
        var mask = new Ext.LoadMask(Ext.get("Contenido"), {
            msg:'Cargando..',
            removeMask:false
        });
        var tableBarras = "<table class='lista' style='width:100%;'><tbody>";
        for(var i = 0; i < parseInt(cantidad_barras); i++){
            tableBarras += "<tr>";
            tableBarras += "<td>"+(i+1)+"</td>";
            tableBarras += "<td><input onfocus='this.select()' id='barra-"+cod+"-"+i+"' style='width=100%' type='text' /></td>";
            tableBarras += "</tr>";
        }
        tableBarras += "</tbody></table>";               

        var win_tmp = new Ext.Window({
                    title:'C&oacute;digos de Barra',
                    height: 400,
                    width: 200,
                    frame:true,
                    autoScroll:true,
                    modal:true,
                    html: tableBarras,
                    buttons:[{
                        text:'Guardar',
                        handler: function(){
                            var camposBArras = "";
                            for(var i = 0; i < parseInt(cantidad_barras); i++){
                                var barraNuevo = $("#barra-"+cod+"-"+i).val();
                                if(barraNuevo == ""){
                                    alert("No puede ingresar un codigo de barra vacio. Cod Barra en linea # "+(i+1));
                                    return;
                                }
                                //console.log(BarcodeValidator.ComprobarUPC(barraNuevo), BarcodeValidator.ComprobarEAN(barraNuevo))
                                if(BarcodeValidator.ComprobarUPC(barraNuevo) == 0 && BarcodeValidator.ComprobarEAN(barraNuevo) == 0){
                                    alert("No ingreso un codigo de barra EAN valido. Cod Barra en linea # "+(i+1));
                                    return;
                                }
                                camposBArras += "<input id='barra-"+cod+"-"+i+"' name='barra-"+cod+"[]' type='hidden' value='"+barraNuevo+"'/>";
                            }
                            $("#barras-fields-"+cod).html(camposBArras);
                            win_tmp.hide();
                        }
                    }]
                });
                win_tmp.show(this);
                mask.hide();
    });   
    
    win = new Ext.Window({
        title:'Seleccionar Producto',
        height:360,
        width:459,
        autoScroll:true,
        tbar:[
        {
            text:'Actualizar lista de Productos',
            icon: '../../libs/imagenes/ico_search.gif',
            handler: function(){
                eventos_form.cargarProducto();
            }
        },
        {
            text:'Actualizar lista de Almacenes',
            icon: '../../libs/imagenes/ico_search.gif',
            handler: function(){
                eventos_form.cargarAlmacenes();
            }
        },
        {
            text:'Limpiar',
            icon: '../../libs/imagenes/back.gif',
            handler: function(){
                eventos_form.Limpiar();
            }
        }
        ],
        modal:true,
        bodyStyle:'padding-right:10px;padding-left:10px;padding-top:5px;',
        closeAction:'hide',
        contentEl:'incluirproducto',
        buttons:[
        {
            text:'Incluir',
            icon: '../../libs/imagenes/drop-add.gif',
            handler:function(){

                if($("#items").val()==""||$("#almacen").val()==""||$("#cantidadunitaria").val()==""){
                    Ext.Msg.alert("Alerta","Debe especificar todos los campos.");
                    return false;
                }
                eventos_form.IncluirRegistros({
                    //cod_item:           $("#items").val(),/////// Añadido
                    id_item:            $("#items").val(),
                    descripcion:        $("#items :selected").text(),
                    id_almacen:         $("#almacen").val(),
                    cantidad:           $("#cantidadunitaria").val(),
                    costo:           $("#costo").val()
                });
            }
        },
        {
            text:'Cerrar',
            icon: '../../libs/imagenes/cancel.gif',
            handler:function(){
                win.hide();
            }
        },
        ]
    });

    var formpanel = new Ext.Panel({
        title:'Datos del Proveedor',
        autoHeight: 300,
        width: '100%',
        collapsible: true,
        titleCollapse: true ,
        contentEl:'dp',
        frame:true
    });

    var formpanel_dcompra = new Ext.Panel({
        title:'Informaci&oacute;n del Cargo',
        autoHeight: 300,
        width: '100%',
        collapsible: true,
        titleCollapse: true ,
        contentEl:'dcompra',
        frame:true
    });

    var tab = new Ext.TabPanel({
        frame:true,
        contentEl:'PanelGeneralCompra',
        activeTab:0,
        height:300,
        items:[
        {
            title:'Productos',
            contentEl:'tabproducto',
            autoScroll:true,
            tbar: [
            {
                text:'Agregar Producto',
                id:'btn-agregar-producto-ing',
                icon: '../../libs/imagenes/add.gif',
                handler: function(){
                     pBuscaItem.main.mostrarWin();
                }
            },
            {
                xtype:'label',
                contentEl: 'displaytotal',
                fn:  eventos_form.CargarDisplayMontos()
            },
            {
                xtype:'label',
                contentEl: 'displaytotalcostos',
                fn:  eventos_form.CargarDisplayTotales()
            },       
            {
                xtype:'label',
                contentEl: 'displaytotalcostosentrada',
                fn:  eventos_form.CargarDisplayTotales()
            }               
            ]
        },
        {
            title:'Registrar Movimiento',
            contentEl:'tabpago',
            autoScroll:true,
            tbar: [
            {
                text:'<b>Registrar </b>',
                id:'btn-registra-entrada-ing',
                icon: '../../libs/imagenes/back.gif',
                iconAlign: 'left',
                height: 30,
                width:50,
                handler: function(){
                    eventos_form.GenerarCompraX();
                }
            },
            { 
                text:'<b>Guardar </b>',
                id:'btn-guarda-entrada-ing',
                icon: '../../libs/imagenes/ico_save.gif',
                iconAlign: 'left',
                height: 30,
                width:100,
                handler: function(){
                    eventos_form.GuardarIngMercancia();
                }
            }           
//            {
//                xtype:'label',
//                contentEl: 'displaytotal2',
//                fn:  eventos_form.CargarDisplayMontos()
//            },
//            {
//                xtype:'label',
//                contentEl: 'displaytotalcostos2',
//                fn:  eventos_form.CargarDisplayTotales()
//            },
//            {
//                xtype:'label',
//                contentEl: 'displaytotalcostosentrada2',
//                fn:  eventos_form.CargarDisplayTotales()
//            }            
            ]
        }
        ]
    });      
        
    formpanel.render("formulario");
    formpanel_dcompra.render("formulario");
    tab.render("formulario");
});
