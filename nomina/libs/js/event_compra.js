/*var win;
var win2;

Ext.onReady(function(){

    $("input[name='totalizar_monto_efectivo'], input[name='totalizar_monto_cheque'], input[name='totalizar_nro_cheque'], input[name='totalizar_monto_tarjeta'], input[name='totalizar_nro_tarjeta'],input[name='totalizar_monto_deposito'], input[name='totalizar_nro_deposito'],input[name='totalizar_nro_otrodocumento'],input[name='totalizar_monto_otrodocumento']").numeric();

    setValoresInput = function(nombreObjetoDestino,nombreObjetoActual){
        $(nombreObjetoDestino).attr("value", $(nombreObjetoActual).val());
    };

    inputHidden  = function(Input,Value,ID){
        return '<input type="hidden" name="'+Input+''+ID+'" value="'+Value+'">';
    };

    $("#cantidadunitaria, #costounitario").numeric();

    $("#cantidadunitaria2, #costounitario2").numeric();

    $("#responsable, #num_factura, #num_control_factura").blur(function(){
        $(this).css("border-color",$(this).val()===""?"red":"");
        $(this).css("border-width",$(this).val()===""?"1px":"");
    });

    $("#cantidadunitaria, #costounitario").blur(function(){
        canuni = $("#cantidadunitaria").val();
        costo = $("#costounitario").val();
        resultado =  parseFloat(canuni)*parseFloat(costo);
        resulta = !isNaN(resultado) ? resultado.toFixed(2) : 0.00;
        $("#totalitem_tmp").val(resulta);
    });

    $("#cantidadunitaria2, #costounitario2").blur(function(){
        canuni = $("#cantidadunitaria2").val();
        costo = $("#costounitario2").val();
        resultado =  parseFloat(canuni)*parseFloat(costo);
        resulta = !isNaN(resultado) ? resultado.toFixed(2) : 0.00;
        $("#totalitem_tmp2").val(resulta);
    });

    $("img.eliminar").live("click",function(){
        $(this).parents("tr").fadeOut("normal",function(){
            $(this).remove();
            eventos_form.CargarDisplayMontos2();
            eventos_form.CargarDisplayMontos();
            $("#totalizar_monto_cancelar").trigger("click");
        });
    });

    $("#opt_cheque").change(function(){
        valor = $(this).val();
        if(valor==0){
            $("#totalizar_nombre_banco,input[name='totalizar_nro_cheque'], input[name='totalizar_monto_cheque'], input[name='totalizar_nro_cheque']").val("0");
            $("#totalizar_nombre_banco,input[name='totalizar_nro_cheque'], input[name='totalizar_monto_cheque'], input[name='totalizar_nro_cheque']").attr("readonly", "readonly");
        }
        else if(valor==1){
            $("#totalizar_nombre_banco,input[name='totalizar_nro_cheque'], input[name='totalizar_monto_cheque'], input[name='totalizar_nro_cheque']").val("0");
            $("#totalizar_nombre_banco,input[name='totalizar_nro_cheque'], input[name='totalizar_monto_cheque'], input[name='totalizar_nro_cheque']").removeAttr("readonly");
        }
    });

    $("#opt_tarjeta").change(function(){
        valor = $(this).val();
        if(valor==0){
            $("#totalizar_tipo_tarjeta,input[name='totalizar_monto_tarjeta'], input[name='totalizar_nro_tarjeta']").val("0");
            $("#totalizar_tipo_tarjeta,input[name='totalizar_monto_tarjeta'], input[name='totalizar_nro_tarjeta']").attr("readonly", "readonly");
        }
        else if(valor==1){
            $("#totalizar_tipo_tarjeta,input[name='totalizar_monto_tarjeta'], input[name='totalizar_nro_tarjeta']").val("0");
            $("#totalizar_tipo_tarjeta,input[name='totalizar_monto_tarjeta'], input[name='totalizar_nro_tarjeta']").removeAttr("readonly");
        }
    });

    $("#opt_deposito").change(function(){
        valor = $(this).val();
        if(valor==0){
            $("#totalizar_banco_deposito,input[name='totalizar_monto_deposito'], input[name='totalizar_nro_deposito']").val("0");
            $("#totalizar_banco_deposito,input[name='totalizar_monto_deposito'], input[name='totalizar_nro_deposito']").attr("readonly", "readonly");
        }
        else if(valor==1){
            $("#totalizar_banco_deposito,input[name='totalizar_monto_deposito'], input[name='totalizar_nro_deposito']").val("0");
            $("#totalizar_banco_deposito,input[name='totalizar_monto_deposito'], input[name='totalizar_nro_deposito']").removeAttr("readonly");
        }
    });

    $("#opt_otrodocumento").change(function(){
        valor = $(this).val();
        if(valor==0){
            $("#totalizar_banco_otrodocumento,#totalizar_tipo_otrodocumento,input[name='totalizar_monto_otrodocumento'], input[name='totalizar_nro_otrodocumento']").val("0");
            $("#totalizar_banco_otrodocumento,#totalizar_tipo_otrodocumento,input[name='totalizar_monto_otrodocumento'], input[name='totalizar_nro_otrodocumento']").attr("readonly", "readonly");
        }
        else if(valor==1){
            $("#totalizar_banco_otrodocumento,#totalizar_tipo_otrodocumento,input[name='totalizar_monto_otrodocumento'], input[name='totalizar_nro_otrodocumento']").val("0");
            $("#totalizar_banco_otrodocumento,#totalizar_tipo_otrodocumento,input[name='totalizar_monto_otrodocumento'], input[name='totalizar_nro_otrodocumento']").removeAttr("readonly");
        }
    });

    $("#opt_cheque, #opt_tarjeta, #opt_deposito,  #opt_otrodocumento").val(0).trigger("change");

    $("#totalizar_monto_cancelar").bind("click", function(){
        //$(this).numeric();
        monto = eval($("#input_tciniva").val());
        $(this).val(monto.toFixed(2)).select();
        $("input[name='totalizar_saldo_pendiente']").val(0);
        $("input[name='totalizar_monto_efectivo']").val(monto.toFixed(2));
        $("input[name='totalizar_cambio']").val(0);
    }).blur(function(){
        montoOLD = eval($(this).val());
        montoOLD = parseFloat(montoOLD);        
        totalgeneral = eval($("#input_tcostos_entrada").val());
        
        if(totalgeneral<=0){
            Ext.Msg.alert("Alerta!","El monto  es cero, probablemente usted deba cargar un item.");
            return false;
        }
        if(montoOLD<0){
            Ext.Msg.alert("Alerta!","El monto debe ser positivo.");

            $(this).trigger("click");
            $(this).focus();
            return false;
        }
        if(montoOLD==0){
            $("input[name='totalizar_saldo_pendiente']").val(totalgeneral);
            $("input[name='totalizar_monto_efectivo']").val(0);
            $("input[name='totalizar_cambio']").val(0);
            return false;
        }
        if(montoOLD==""){
            resultado = totalgeneral;
            $(this).val(resultado);
            $(this).select();
            $("input[name='totalizar_saldo_pendiente'], input[name='totalizar_cambio'], input[name='totalizar_monto_efectivo']").val("0");
            return false;
        }
        //total_pendiente = parseFloat(totalgeneral) - parseFloat(montoOLD);
        if( montoOLD  < totalgeneral){
            restante = parseFloat(totalgeneral) - parseFloat(montoOLD);
            $("input[name='totalizar_saldo_pendiente']").val(restante.toFixed(2));
            $("input[name='totalizar_monto_efectivo']").val(montoOLD.toFixed(2));
            return false;
        }
        if(montoOLD>totalgeneral){
            restante = parseFloat(montoOLD) - parseFloat(resultado);
            $("input[name='totalizar_cambio']").val(restante.toFixed(2));
            $("input[name='totalizar_monto_efectivo']").val(totalgeneral);
            return false;
        }
    });

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
                    id_item:            $("#items").val(),
                    descripcion:     $("#items :selected").text(),
                    id_almacen:     $("#almacen").val(),
                    cantidad:         $("#cantidadunitaria").val(),
                    costo:              $("#costo").val()
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

    win2 = new Ext.Window({
        title:'Cargar Servicio',
        height:360,
        width:459,
        autoScroll:true,
        tbar:[
        {
            text:'Actualizar lista de Servicios',
            icon: '../../libs/imagenes/ico_search.gif',
            handler: function(){
                eventos_form.cargarServicio();
            }
        },
        {
            text:'Limpiar',
            icon: '../../libs/imagenes/Clear.gif',
            handler: function(){
                eventos_form.Limpiar();
            }
        }
        ],
        modal:true,
        bodyStyle:'padding-right:10px;padding-left:10px;padding-top:5px;',
        closeAction:'hide',
        contentEl:'incluirservicio',
        buttons:[
        {
            text:'Incluir',
            icon: '../../libs/imagenes/drop-add.gif',
            handler:function(){
                if($("#items2").val()==""||$("#cantidadunitaria2").val()==""||$("#costounitario2").val()==""){
                    Ext.Msg.alert("Alerta","Debe especificar todos los campos.");
                    return false;
                }
                if(isNaN($("#totalitem_tmp2").val())){
                    Ext.Msg.alert("Alerta","Error en el Calculo, verifique e intente de nuevo.");
                    $("#cantidadunitaria2, #costounitario2, #totalitem_tmp2").val("");
                    $("#cantidadunitaria2").focus();
                    return false;
                }
                if($("#totalitem_tmp2").val()<=0){
                    Ext.Msg.alert("Alerta","El monto debe ser Mayor a cero (0)");
                    return false;
                }
                eventos_form.IncluirRegistros2({
                    id_item:            $("#items2").val(),
                    descripcion:        $("#items2 :selected").text(),
                    cantidad:           $("#cantidadunitaria2").val(),
                    precio:             $("#costounitario2").val(),
                    totalsiniva:        (($("#cantidadunitaria2").val())*($("#costounitario2").val()))
                });
            }
        },
        {
            text:'Cerrar',
            icon: '../../libs/imagenes/cancel.gif',
            handler:function(){
                win2.hide();
            }
        }
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

    var panelInfoCompra = new Ext.Panel({
        frame:true,
        title:'Informaci&oacute;n de la Compra',
        contentEl:'dcompra',
        autoHeight: 300,
        width: '100%',
        collapsible: true,
        titleCollapse: true
    });

    var tab = new Ext.TabPanel({
        //frame:true,
        plain:true,
        contentEl:'PanelGeneralCompra',
        activeTab:0,
        //height:300,
        defaults:{
            autoHeight: true
        },
        items:[
        {
            title:'Productos',
            contentEl:'tabproducto',
            autoScroll:true,
            tbar: [
            {
                text:'Agregar Producto',
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
//        {
//            title:'Servicios',
//            contentEl:'tabservicio',
//            autoScroll:true,
//            tbar: [{
//                text:'Agregar Servicio',
//                icon: '../../libs/imagenes/add.gif',
//                handler: function(){
//                    eventos_form.init2();
//                    win2.show();
//                }
//            },{
//                xtype:'label',
//                contentEl: 'displaytotal',
//                fn:eventos_form.CargarDisplayMontos2()
//            }]
//        },
        {
            title:'Registrar Compra',
            contentEl:'tabpago',
            autoScroll:true,
            tbar:[
            {
                text:'<b>Generar Compra</b>',
                icon: '../../libs/imagenes/back.gif',
                iconAlign: 'left',
                height: 20,
                handler: function(){
                    if($("#num_factura").val()){                       
                       // if($("#estado_entrega").val()==="Pendiente" || $("#estado_entrega").val()==="Entregado" && $("#num_control_factura").val()!=="" && $("#num_factura").val()!==""){
                        eventos_form.GenerarCompras();
                    }
                    else{
                        $("#num_factura").css("border-color",$("#num_factura").val()===""?"red":"");
                        $("#num_factura").css("border-width",$("#num_factura").val()===""?"1px":"");
                        Ext.Msg.alert("Alerta!", "Debe Proporcionar Datos en los Campos Resaltados");
                    }
                    return false;
                }
            },
            {
                xtype:'label',
                contentEl: 'displaytotal2',
                fn:eventos_form.CargarDisplayMontos()
            }
            ]
        }
        ]
    });

    var panelTabs = new Ext.Panel({
        title:'Informaci&oacute;n del Cargo',
        autoHeight: true,
        width: '100%',
        collapsible:true,
        titleCollapse:true,
        frame:true,
        items:[tab]
    });

    var name = "datosGral";//form-item
    formpanel.render(name);
    tab.render(name);
    panelInfoCompra.render(name);
    panelTabs.render(name);//tab.render(name);
});*/

var win;

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

            if(valores == ""){
                $('#cantidad_'+cod).attr("contenteditable", "true");
                $('#costo_'+cod).attr("contenteditable", "true");
                $(this).css({'background-image': 'url("../../libs/imagenes/ico_save.gif")'});
                $('#cantidad_'+cod).focus();
                $(this).attr("class",'guardar');
            }
            else{
                $('#costo_'+cod).attr("contenteditable", "true");
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
    });
     $('#comprobante').live("focusout", function (event) {       
        if(this.value != "" && parseInt(this.value) != 0){
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
        for(var i = 0; i < parseInt(cantidad_seriales); i++){
            tableSerialas += "<tr>";
            tableSerialas += "<td>"+(i+1)+"</td>";
            tableSerialas += "<td><input onfocus='this.select()' id='serial-"+cod+"-"+i+"' style='width=100%' type='text' /></td>";
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
                text:'<b>Registrar Compra</b>',
                id:'btn-registra-entrada-ing',
                icon: '../../libs/imagenes/back.gif',
                iconAlign: 'left',
                height: 30,
                width:50,
                handler: function(){
                    eventos_form.GenerarCompraX();
                }
            }/*,
            {
                text:'<b>Guardar Compra</b>',
                id:'btn-guarda-entrada-ing',
                icon: '../../libs/imagenes/ico_save.gif',
                iconAlign: 'left',
                height: 30,
                width:100,
                handler: function(){
                    eventos_form.GuardarIngMercancia();
                }
            }    */       
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
