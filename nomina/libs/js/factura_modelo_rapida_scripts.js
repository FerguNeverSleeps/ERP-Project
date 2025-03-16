/**
 * Autor: Luis E. Viera Fernandez.
 * Correo:
 *  levieraƒ@gmail.com
 */
 var data = {
        productoSeleccionado : {},
        config_general : {}
    };


function redondear_monto(monto){
return Math.round(monto*Math.pow(10,2))/Math.pow(10,2);
}

var clicked=false;
$(function(){
    ContarInputItem = 0;
    
    $("input[name='totalPedido'],input[name='montodescuentoPedido'],input[name='descuentoPedido'],input[name='cantidadPedido'],input[name='precioProductoPedido']").numeric();
    $('a[rel*=facebox]').facebox();
    $("#PanelFactura").hide();

    $(document).delegate(".grid table.lista tbody tr", "mouseover", function(){
        $(this).find("td").css("background-color", "#f1f3f3");
        $(".info_detalle").css("background-color", "#507e95");
    }).delegate(".grid table.lista tbody tr", "mouseout", function(){
        $(this).find("td").css("background-color", "");
        $(".info_detalle").css("background-color", "#507e95");
    });
    
    function fn_cantidad(){
        var_montoItemsFactura = 0;
        var_ivaTotalFactura= 0;
        var_descuentosItemFactura =0;
        var_TotalTotalFactura = 0;
        var_total_costo_actual = 0;
        var_total_porcentaje_costo_ganancia = 0;
        var_subTotal = 0;
        var_cantidad_por_bulto = 0;
        var_ganancia_total_item = 0;
        var_porcentaje_ganancia_total_items = 0;

        var_total_peso = 0;
        var_totalm3 = 0;
        var_totalft3 = 0;

        $(".grid table.lista tbody").find("tr").each(function(){
            var_subTotal = parseFloat(var_subTotal) +  parseFloat($(this).find("td.cantidad").attr("rel"))*parseFloat($(this).find("td.preciosiniva").text());
            var_montoItemsFactura = parseFloat(var_montoItemsFactura) + parseFloat($(this).find("td.totalsiniva").text());
            var_ivaTotalFactura =  parseFloat(var_ivaTotalFactura) + parseFloat($(this).find("td.piva").attr("rel"));
            var_descuentosItemFactura =  parseFloat(var_descuentosItemFactura) + parseFloat($(this).find("td.montodescuento").html());
            var_TotalTotalFactura = parseFloat(var_montoItemsFactura) + parseFloat(var_ivaTotalFactura);
            cantidad_por_bulto = parseInt($(this).find("td").find("input[name='_cantidad_bulto[]']").val());
            cantidad_items = parseInt($(this).find("td.cantidad").attr("rel"));
            tcos_actual = parseFloat($(this).find("td").find("input[name='_id_costo_actual[]']").val());
            var_ganancia_total_item += parseFloat($(this).find("td").find("input[name='_ganancia_item_individual[]']").val());

            var_total_peso += parseFloat($(this).find("td").find("input[name='_peso_total_item[]']").val());

            var_totalm3 += parseFloat($(this).find("td").find("input[name='_totalm3[]']").val());
            var_totalft3 += parseFloat($(this).find("td").find("input[name='_totalft3[]']").val());

            if(_.isNaN(tcos_actual)){
                tcos_actual = 0;
            }

            var_cantidad_por_bulto += cantidad_por_bulto;
            var_total_costo_actual += parseFloat(tcos_actual);

            var_porcentaje_ganancia_total_items = ((var_ganancia_total_item)*100)/var_montoItemsFactura;
        });
        
        $("#Totalm3").html(var_totalm3);
        $("#TotalFT3").html(var_totalft3);

        var_TotalTotalFactura = redondear_monto(var_TotalTotalFactura);
        $("#subTotal").html(var_subTotal.toFixed(2)+" "+$("#moneda").val());
        $("input[name='input_subtotal']").attr("value",var_subTotal.toFixed(2));
        $("#montoItemsFactura").html(var_montoItemsFactura.toFixed(2)+" "+$("#moneda").val());
        $("input[name='input_montoItemsFactura']").attr("value",var_montoItemsFactura.toFixed(2));
        $("#ivaTotalFactura").html(var_ivaTotalFactura.toFixed(2)+" "+$("#moneda").val());
        $("input[name='input_ivaTotalFactura']").attr("value",var_ivaTotalFactura.toFixed(2));
        $("#descuentosItemFactura").html(var_descuentosItemFactura.toFixed(2)+" "+$("#moneda").val());
        $("input[name='input_descuentosItemFactura']").attr("value",var_descuentosItemFactura.toFixed(2));
        $("#TotalTotalFactura").html(var_TotalTotalFactura.toFixed(2)+" "+$("#moneda").val());
        $("input[name='input_TotalTotalFactura']").attr("value",var_TotalTotalFactura.toFixed(2));
        cantidad = $(".grid table.lista tbody").find("tr").length;
        $(".span_cantidad_items").html("<span style=\"font-size: 15px; font-style: italic;\">Cantidad de Items: "+cantidad+"</span>");
        $("input[name='input_cantidad_items']").attr("value",cantidad.toFixed(2));
        $("input[name='input_TotalBultos']").attr("value",var_cantidad_por_bulto);
        
        $("#TotalBultos").html(var_cantidad_por_bulto);
        $("#total_ganancia_monto").html(var_ganancia_total_item.toFixed(2));
        $("#total_ganancia_porcenaje").html(var_porcentaje_ganancia_total_items.toFixed(2));
        $("#PesoTotal").html(var_total_peso.toFixed(2));

       
        $("input[name='total_bultos']").val(var_cantidad_por_bulto);
        $("input[name='peso_total_item']").val(var_total_peso);
        $("input[name='total_m3']").val(var_totalm3)
        $("input[name='total_ft3']").val(var_totalft3)

        $("input[name='total_porcentaje_ganancia']").val(var_porcentaje_ganancia_total_items.toFixed(2))
        $("input[name='total_monto_ganancia_total']").val(var_ganancia_total_item);

        $.totalizarFactura();
    }

    $(document).delegate(".info_detalle", "click", function(){
        cod = $(this).parent('tr').find("input[name='idItem_']").val();
        $.ajax({
            type: 'GET',
            data: 'cod='+cod,
            url:  'info_servicio_item.php',
            beforeSend: function(){
            //$.facebox.loading();
            },
            success: function(data){
                var win ;
                cerrarWin = new Ext.Button({
                    text:'Cerrar Ventana',
                    iconCls:'cancelar',
                    handler:function(){
                        win.close();
                    }
                });
                win = new Ext.Window({
                    title:'Informaci&oacute;n del item',
                    modal:true,
                    iconCls:'iconTitulo',
                    constrain:true,
                    autoScroll:true,
                    html:data,
                    action:'hide',
                    height:400,
                    width:450,
                    buttonAlign:'center',
                    buttons:[
                    cerrarWin
                    ]
                });
                win.show();
            }
        });
    });
    
    
    
    
    
    
      $(document).delegate("img.editar", "click",function(){
      //$('.editar').click(function (event) {
        //console.log("Sd")
            var cod;
            event.preventDefault();
             
            cod = $(this).attr("alt");
             
            //var valores = $("#html_escala_item_"+cod).text();

            function validaNumerosEdit(event){
                if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8 && event.keyCode != 190 ) {
                    //event.returnValue = false;
                    return false;
                }
                recalcular(cod) ; 
                return true;
            }

                
          
          
          
          
                $('#lista_precioe'+cod).on('change', function() {
                   recalcular1(cod,Multiprecios[this.value -2]) ; 
                    
                    });
            

            function validaNumerosEditAutorizacionPrecio(event){
                if(AUTORIZACION_CAMBIO_PRECIO == 0){

                    if (Util.verificaNivelOperacion(NIVEL_USUARIO,NIVEL_CAMBIO_LISTA)) {
                            //console.log("nivel autorizado")
                            AUTORIZACION_CAMBIO_PRECIO = 1;
                            
                    }else{
                            //console.log("nivel no autorizado solicitando")
                            Util.solicitaAutorizacion(NIVEL_CAMBIO_LISTA,function(){
                                AUTORIZACION_CAMBIO_PRECIO = 1;
                                //console.log("autorizado")
                            },function(){
                                AUTORIZACION_CAMBIO_PRECIO = 0;     
                                //console.log("no autorizado")
                            });                                 
                    }
                }

                if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8 && event.keyCode != 190 ) {
                    //event.returnValue = false;
                    return false;
                }
                if(AUTORIZACION_CAMBIO_PRECIO == 0)
                    return false;
                return true;
                recalcular(cod) ; 
            }
            /*function validaNumerosEdit(evt) {
                var tecla = String.fromCharCode(evt.which || evt.keyCode);
                if ( !/[\d.\b\r]/.test(tecla) ) return false;
                return true;
            }*/


            //if(valores == ""){
                $('#td_cantidad_'+cod).attr("contenteditable", "true");
                $('#td_precio_'+cod).attr("contenteditable", "true");
                $('#td_monto_desc_'+cod).attr("contenteditable", "true");
                $('#td_porc_desc_'+cod).attr("contenteditable", "true");
                $('#td_lista_'+cod).attr("contenteditable", "true");
                
                  $('#lista_precioe'+cod).removeAttr("disabled");
                

                //$('#td_cantidad_'+cod).keyup(function(e){ return validaNumerosEdit(e); });
                $('#td_cantidad_'+cod).keydown(function(e){ return validaNumerosEdit(e);});
                //$('#td_precio_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionPrecio(e);  });
                $('#td_precio_'+cod).keydown(function(e){ return validaNumerosEditAutorizacionPrecio(e);  });
                //$('#td_monto_desc_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                $('#td_monto_desc_'+cod).keydown(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                //$('#td_porc_desc_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                $('#td_porc_desc_'+cod).keydown(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                
                $('#td_porc_desc_'+cod).keydown(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                
                
                            //$('#td_cantidad_'+cod).keyup(function(e){ return validaNumerosEdit(e); });
                $('#td_cantidad_'+cod).keyup(function(e){ return validaNumerosEdit(e);});
                //$('#td_precio_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionPrecio(e);  });
                $('#td_precio_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionPrecio(e);  });
                //$('#td_monto_desc_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                $('#td_monto_desc_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                //$('#td_porc_desc_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                $('#td_porc_desc_'+cod).keyup(function(e){ return validaNumerosEditAutorizacionDescuento(e);  });
                
                
                
                
                
                
                
               
                

                $(this).css({'background-image': 'url("../../libs/imagenes/ico_save.gif")'});
                $('#td_cantidad_'+cod).focus();
                $(this).attr("class",'guardar');
            //}
            /*else{
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
            }*/
    });

    /*$(document).delegate('#td_monto_desc_'+cod, "click",function(){

    });

    $(document).delegate('#td_porc_desc_'+cod, "click",function(){

    });*/



    function recalcular(cod)
    {
        
                  
            valorCantidad = $('#td_cantidad_'+cod).text();            
            valorCosto = $('#td_precio_'+cod).text();
            cantidadBulto = $('#td_cantidad_bulto_'+cod).text();
            montodescuento = parseFloat($('#td_monto_desc_'+cod).text());
            descuento = parseFloat($('#td_porc_desc_'+cod).text());
            iva = $('#td_porc_iva_'+cod).text();

            $('#td_cantidad_bulto_tot_'+cod).html(Math.ceil(valorCantidad/cantidadBulto));
            $("#td_cantidad_bulto_tot_"+cod).attr("rel",valorCantidad);
            
            $(this).css({'background-image': 'url("../../libs/imagenes/edit.gif")'});
            
     

            $('#_item_cantidad-'+cod).attr("value", valorCantidad);
            $("#td_cantidad_"+cod).attr("rel",valorCantidad);
            $('#_item_preciosiniva-'+cod).attr("value", valorCosto);
            $('#_item_descuento-'+cod).attr("value", descuento);
            $('#_item_montodescuento-'+cod).attr("value", montodescuento);
            //var menos_descuento = 0;
            
            var costototal = (valorCantidad*valorCosto)- montodescuento;
            
            //if(montodescuento !=)
            $("#td_tot_siniva_"+cod).html(costototal.toFixed(2));
            var totalconiva = (costototal ) + (((costototal)*iva)/100)
            $("#td_tot_coniva_"+cod).html(totalconiva.toFixed(2));
            $("#td_tot_coniva_"+cod).attr("rel",(((costototal)*iva)/100).toFixed(2));

          
            fn_cantidad();  
        
    }



        function recalcular1(cod,preciovb)
    {
         
            valorCantidad = $('#td_cantidad_'+cod).text();            
            valorCosto = preciovb;
            cantidadBulto = $('#td_cantidad_bulto_'+cod).text();
            montodescuento = parseFloat($('#td_monto_desc_'+cod).text());
            descuento = parseFloat($('#td_porc_desc_'+cod).text());
            iva = $('#td_porc_iva_'+cod).text();
            $('#td_precio_'+cod).html(preciovb) ;
            $('#td_cantidad_bulto_tot_'+cod).html(Math.ceil(valorCantidad/cantidadBulto));
            $("#td_cantidad_bulto_tot_"+cod).attr("rel",valorCantidad);
            
            $(this).css({'background-image': 'url("../../libs/imagenes/edit.gif")'});
            
     

            $('#_item_cantidad-'+cod).attr("value", valorCantidad);
            $("#td_cantidad_"+cod).attr("rel",valorCantidad);
            $('#_item_preciosiniva-'+cod).attr("value", valorCosto);
            $('#_item_descuento-'+cod).attr("value", descuento);
            $('#_item_montodescuento-'+cod).attr("value", montodescuento);
            //var menos_descuento = 0;
            
            var costototal = (valorCantidad*valorCosto)- montodescuento;
            
            //if(montodescuento !=)
            $("#td_tot_siniva_"+cod).html(costototal.toFixed(2));
            var totalconiva = (costototal ) + (((costototal)*iva)/100)
            $("#td_tot_coniva_"+cod).html(totalconiva.toFixed(2));
            $("#td_tot_coniva_"+cod).attr("rel",(((costototal)*iva)/100).toFixed(2));

          
            fn_cantidad();  
        
    }

    $(document).delegate("img.guardar", "click",function(){
    //$('.guardar').click(function (event) {

            var cod = $(this).attr("alt");
            valorCantidad = $('#td_cantidad_'+cod).text();            
            valorCosto = $('#td_precio_'+cod).text();
            cantidadBulto = $('#td_cantidad_bulto_'+cod).text();
            montodescuento = parseFloat($('#td_monto_desc_'+cod).text());
            descuento = parseFloat($('#td_porc_desc_'+cod).text());
            iva = $('#td_porc_iva_'+cod).text();

            $('#td_cantidad_bulto_tot_'+cod).html(Math.ceil(valorCantidad/cantidadBulto));
            $("#td_cantidad_bulto_tot_"+cod).attr("rel",valorCantidad);
            
            $(this).css({'background-image': 'url("../../libs/imagenes/edit.gif")'});
            
            $('#td_cantidad_'+cod).attr("contenteditable", "false");            
            $('#td_precio_'+cod).attr("contenteditable", "false");          
            $('#td_monto_desc_'+cod).attr("contenteditable", "false");          
            $('#td_porc_desc_'+cod).attr("contenteditable", "false");

             $('#lista_precioe'+cod).attr("disabled",true);


            $('#_item_cantidad-'+cod).attr("value", valorCantidad);
            $("#td_cantidad_"+cod).attr("rel",valorCantidad);
            $('#_item_preciosiniva-'+cod).attr("value", valorCosto);
            $('#_item_descuento-'+cod).attr("value", descuento);
            $('#_item_montodescuento-'+cod).attr("value", montodescuento);
            //var menos_descuento = 0;
            
            var costototal = (valorCantidad*valorCosto)- montodescuento;
            
            //if(montodescuento !=)
            $("#td_tot_siniva_"+cod).html(costototal.toFixed(2));
            var totalconiva = (costototal ) + (((costototal)*iva)/100)
            $("#td_tot_coniva_"+cod).html(totalconiva.toFixed(2));
            $("#td_tot_coniva_"+cod).attr("rel",(((costototal)*iva)/100).toFixed(2));

            $(this).attr("class",'editar');
            fn_cantidad();
    });


    $(document).delegate("img.eliminar", "click",function(){
        //$.facebox($(this).parents("tr").find("td").eq(0).html());
        //$(this).parents("tr").fadeOut("normal");
        iditem = $(this).parents("tr").find("td").eq(9).find("input[name='_item_codigo[]']").attr("value");
        coditemprecompromiso = $(this).parents("tr").find("td").eq(9).find("input[name='_cod_item_precompromiso[]']").attr("value");
        $.ajax({
            type: "GET",
            url:  "../../libs/php/ajax/ajax.php",
            data: "opt=delete_precomprometeritem&v1="+iditem+"&codprecompromiso="+coditemprecompromiso
        });
        $(this).parents("tr").fadeOut("normal",function(){
            $(this).remove();

            var cantidad_item = $(".grid table.lista tbody tr").length;
            if(cantidad_item==0) {
                $.bloqueoFiltroHeader(false);
            }

            fn_cantidad();
        });
    });
  

    $(document).delegate("img.eliminar", "click",function(){
        //$.facebox($(this).parents("tr").find("td").eq(0).html());
        //$(this).parents("tr").fadeOut("normal");
        iditem = $(this).parents("tr").find("td").eq(9).find("input[name='_item_codigo[]']").attr("value");
        coditemprecompromiso = $(this).parents("tr").find("td").eq(9).find("input[name='_cod_item_precompromiso[]']").attr("value");
        $.ajax({
            type: "GET",
            url:  "../../libs/php/ajax/ajax.php",
            data: "opt=delete_precomprometeritem&v1="+iditem+"&codprecompromiso="+coditemprecompromiso
        });
        $(this).parents("tr").fadeOut("normal",function(){
            $(this).remove();

            var cantidad_item = $(".grid table.lista tbody tr").length;
            if(cantidad_item==0) {
                $.bloqueoFiltroHeader(false);
            }

            fn_cantidad();
        });
    });
    /*
    * Esta funcion permite crear tantos input se necesiten para la creacion de la tr
    * de la tabla de items (los que se cargan a la hora de la factura)
    **/
    $.inputHidden = function(Input,Value,ID, escapar){
        escapar = escapar || false;
        if(escapar==false){
            return '<input type="hidden" name="'+Input+''+ID+'" value="'+Value+'">';
        }else{
            return "<input type='hidden' name='"+Input+""+ID+"' value='"+Value+"'>";
        }
       
    }

    var getTipoPrecio = function() {
        var tipo_precio = JSON.parse($("select[name='lista_precio'] :selected").val())[1];
        var preciosiniva = 0;
        
        var p = {
            LIBRE: 'LIBRE',
            PRECIO_A: 'PRECIO_A',
            PRECIO_B: 'PRECIO_B',
            PRECIO_C: 'PRECIO_C',
            PRECIO_D: 'PRECIO_D',
            PRECIO_F: 'PRECIO_F',
            PRECIO_H: 'PRECIO_H',
        };
        
        switch(tipo_precio){
            case p.LIBRE: 
            case p.PRECIO_A:
                preciosiniva = parseFloat(data.productoSeleccionado.precio1);
                break;
            case p.PRECIO_B:
                preciosiniva = parseFloat(data.productoSeleccionado.precio2);
                break;
            case p.PRECIO_C:
                preciosiniva = parseFloat(data.productoSeleccionado.precio3);
                break;
            case p.PRECIO_D:
                preciosiniva = parseFloat(data.productoSeleccionado.precio4);
                break;
            case p.PRECIO_F:
                preciosiniva = parseFloat(data.productoSeleccionado.precio5);
                break; 
            case p.PRECIO_H:
                preciosiniva = parseFloat(data.productoSeleccionado.precio6);
                break;          
        }

        switch(REF_PRECIO_MIN){
            case p.LIBRE: 
            case p.PRECIO_A:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio1);
                break;
            case p.PRECIO_B:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio2);
                break;
            case p.PRECIO_C:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio3);
                break;
            case p.PRECIO_D:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio4);
                break;
            case p.PRECIO_F:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio5);
                break; 
            case p.PRECIO_H:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio6);
                break;          
        }

        switch(REF_PRECIO_MAX){
            case p.LIBRE: 
            case p.PRECIO_A:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio1);
                break;
            case p.PRECIO_B:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio2);
                break;
            case p.PRECIO_C:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio3);
                break;
            case p.PRECIO_D:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio4);
                break;
            case p.PRECIO_F:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio5);
                break; 
            case p.PRECIO_H:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio6);
                break;          
        }

        return preciosiniva;
    }

    
        var getTipoPrecio1 = function() {
        var tipo_precio =  $("select[name='lista_preciod'] :selected").val();
        var preciosiniva = 0;
         var p = {
            LIBRE: '8',
            PRECIO_A: '2',
            PRECIO_B: '3',
            PRECIO_C: '4',
            PRECIO_D: '5',
            PRECIO_F: '6',
            PRECIO_H: '7',
        };
       
        switch(tipo_precio){
            case p.LIBRE: 
            case p.PRECIO_A:
                preciosiniva = parseFloat(data.productoSeleccionado.precio1);
                break;
            case p.PRECIO_B:
                preciosiniva = parseFloat(data.productoSeleccionado.precio2);
                break;
            case p.PRECIO_C:
                preciosiniva = parseFloat(data.productoSeleccionado.precio3);
                break;
            case p.PRECIO_D:
                preciosiniva = parseFloat(data.productoSeleccionado.precio4);
                break;
            case p.PRECIO_F:
                preciosiniva = parseFloat(data.productoSeleccionado.precio5);
                break; 
            case p.PRECIO_H:
                preciosiniva = parseFloat(data.productoSeleccionado.precio6);
                break;          
        }

        switch(REF_PRECIO_MIN){
            case p.LIBRE: 
            case p.PRECIO_A:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio1);
                break;
            case p.PRECIO_B:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio2);
                break;
            case p.PRECIO_C:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio3);
                break;
            case p.PRECIO_D:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio4);
                break;
            case p.PRECIO_F:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio5);
                break; 
            case p.PRECIO_H:
                PRECIO_MINIMO = parseFloat(data.productoSeleccionado.precio6);
                break;          
        }

        switch(REF_PRECIO_MAX){
            case p.LIBRE: 
            case p.PRECIO_A:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio1);
                break;
            case p.PRECIO_B:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio2);
                break;
            case p.PRECIO_C:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio3);
                break;
            case p.PRECIO_D:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio4);
                break;
            case p.PRECIO_F:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio5);
                break; 
            case p.PRECIO_H:
                PRECIO_MAXIMO = parseFloat(data.productoSeleccionado.precio6);
                break;          
        }

        return preciosiniva;
    }





    $.agregarArticulo = function(){
        var dataitem = JSON.parse($("#informacionitem").val());
        
        if(data.productoSeleccionado.permitir_cargar=="no"){
            alert("No se puede cargar este producto o servicio.");
            return false;
        }


        if(dataitem==undefined){
            alert("Debe cargar un producto o servicio");
            return false;
        }
        
        codigo = dataitem.id_item;
        producto = dataitem.cod_item;//codigo_barras
        descripcion = dataitem.descripcion1;
        cantidad = $("input[name='filtro_cantidad']").val();

        if(parseInt(cantidad)==0){
            alert("Debe especificar la cantidad!");
            $("#filtro_cantidad").focus();
            return false
        }
        cantidad = parseInt(cantidad);

        var preciosiniva = getTipoPrecio();

        if(parseFloat(data.productoSeleccionado.precio1)==0 || (getTipoPrecio() != parseFloat($("input[name='filtro_precio']").val())) ){
           preciosiniva = parseFloat($("input[name='filtro_precio']").val());
        }

        if(preciosiniva==0){
            var tipo_precio = JSON.parse($("select[name='lista_precio'] :selected").val());
            alert("El precio del producto debe ser mayor a cero (0), verifique el precio del producto o el tipo de precio seleccionado [ "+ $("select[name='lista_precio'] :selected").text() + " ]");
            clicked=false;
            return false;
        }

        var restarDescuento = false;
        descuento = $("input[name='filtro_descuento']").val();
        if(!_.str.isBlank(descuento)){
            if(descuento.indexOf("%")>=0){
                if(_.isNaN(parseFloat(descuento))){
                    alert('El valor del descuento es invalido, verifique!');
                    return false;
                }
                descuento = parseFloat(descuento) / 100;
            } else {
                restarDescuento = true;
            }
        } else {
            descuento = 0;
        }

        if(descuento>0 || restarDescuento==true){
            if(restarDescuento){
                montodescuento  = descuento;
                descuento = 0;
            }else{
                montodescuento  = parseFloat((( preciosiniva * cantidad ) * descuento).toFixed(3));
            }
        } else {
            montodescuento = 0;
        }

        totalsiniva  = ( preciosiniva * cantidad )  - montodescuento;
        var factorImpuesto = 1;

        if(typeof(CLIENTE_EXCENTO) != "undefined" || typeof(CLIENTE_EXCENTO) == undefined || CLIENTE_EXCENTO == null){
            if(parseInt(CLIENTE_EXCENTO) == 1){
                factorImpuesto = 0;
            }
            else{
                factorImpuesto = 1;
            }
        }

        if(dataitem.monto_exento==1){// es exento
            iva = 0 * factorImpuesto;
            piva = parseFloat(0);
            totalconiva = 0;
        }else{
            
           
            iva = dataitem.iva * factorImpuesto;
            if($("select[name='excentovn']").val() == 'SI')
            iva = 0;
            piva = parseFloat((totalsiniva*iva)/100);

            totalconiva = parseFloat(piva) + parseFloat(totalsiniva);
            totalconiva = redondear_monto(totalconiva);
        }
        
        almacen = parseInt($("input[name='cod_almacen_defecto']").val());

        var fncallback = function(){
            addTabla(codigo,descripcion,cantidad,preciosiniva,descuento,montodescuento,totalsiniva,iva,piva,totalconiva,almacen,producto);
        }

        if(data.productoSeleccionado.posee_serial=="si"){
            var win_tmp = new Ext.Window({
                title:'Seriales',
                height: 300,
                width: 400,
                frame:true,
                bodyStyle:"padding:10px;background:white;",
                closable: false,
                autoScroll:true,
                modal:true,
                // html: response,
                buttons:[
                    {
                        text:'Insertar',
                        handler: function(){
                            var seriales_seleccionados = $(".input-serial:checked").length;
                            if(seriales_seleccionados>0){
                                var seriales_seleccionados = []; 
                                $(".input-serial:checked").each(function() {
                                    seriales_seleccionados.push($(this).attr('value'));
                                });
                                data.productoSeleccionado["seriales_seleccionados"]=seriales_seleccionados;
                                fncallback();
                                win_tmp.hide();
                            }else{
                                alert("Debe seleccionar un serial.");
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function(){
                            clicked=false;
                            delete data.productoSeleccionado['seriales_seleccionados'];
                            $(document).undelegate(".btn_filtro_serial","click");
                            win_tmp.destroy();
                        }
                    }
                ]
            });
            
            var cargarSeriales = function(param){
                $.ajax({
                    type: "POST",
                    url:  "../../libs/php/ajax/ajax.php",
                    data:{
                        opt: 'obtener_seriales',
                        id_item: data.productoSeleccionado.id_item,
                        filtro: param.filtro
                    },
                    success: function(xhr){
                        obj_response = JSON.parse(xhr);
                        $("#tabla_seriales").find("tbody tr").remove();
                        if(obj_response.count>0){
                            _.each(obj_response.collection, function(model_){
                                var html =  _.template($('#tr_seriales').html())({model: model_, registros:true});
                                $("#tabla_seriales").find("tbody").append(html);
                            });
                        }else if(obj_response.count==0){
                            var html =  _.template($('#tr_seriales').html())({model: {}, registros:false});
                            $("#tabla_seriales").find("tbody").append(html);
                        }
                    }
                });
            };
            
            cargarSeriales({filtro:""});
            $(document).delegate(".btn_filtro_serial", "click", function(){
                var filtro_serial = $("input[name='filtro_serial']").val();
                cargarSeriales({filtro:filtro_serial});
            });

            template_html =  _.template($('#filtro_seriales').html())();
            win_tmp.html = template_html;
            win_tmp.show(this);
        }else{
            fncallback();
        }
    };

    /**
        Regla para calculo de cantidad por bulto
    */

    function reglaCalculoPorBulto(param){
        var contar=0;
        var cantidad_pedida=param.cantidad || 0;
        var i=0;
        var bulto=0; 
        var cantidad_bulto = param.cantidad_bulto;

        while(i<cantidad_pedida){
            if(i%cantidad_bulto==0){
                bulto++;
            } 
            i+=1;
        }  

        return bulto;
    }

    /**
    * Esta funcion permite cargar los item en la tabla.
    **/

    function addTabla(codigo, descripcion, cantidad, preciosiniva, descuento, montodescuento, totalsiniva, iva, piva, totalconiva, almacen, producto, cuota,referencia_sel,unidad_empaque_sel,cantidad_bulto_sel){///*Adicionado como requisito especifico del CCP*/
        ContarInputItem += 1;
        //console.log(referencia_sel)
        var foto = (_.str.isBlank(data.productoSeleccionado.foto)) ? "sin_imagen.jpg" : data.productoSeleccionado.foto;
        var descuento_ = 0;
        var campos = "";
        descuento_ = (descuento>0) ? descuento * 100 : descuento;
        
        var cantidad_bulto = parseFloat(data.productoSeleccionado.cantidad_bulto) || cantidad_bulto_sel;
        var unidad_empaque_descripcion =data.productoSeleccionado.unidad_empaque || unidad_empaque_sel;

        if(data.productoSeleccionado.cod_item_forma=="1"){
            if(_.isNaN(cantidad_bulto)){
            alert("Debe verificar la cantidad por "+unidad_empaque_descripcion);
            return false;
            }

            if(cantidad_bulto<=0){
                alert("Debe especificar la cantidad por "+unidad_empaque_descripcion);
                return false;
            }
        }else{
            cantidad_bulto=0;
        }
        
        var cantidad_bultos_totales = reglaCalculoPorBulto({
                                        "cantidad":       cantidad,
                                        "cantidad_bulto": cantidad_bulto
                                      });

        var porcentaje_ganancia_x_item = 0;

        var costo_actual = parseFloat(data.productoSeleccionado.costo_actual);
        //var preciosiniva = getTipoPrecio();

        var ganancia_item_individual = ((preciosiniva-costo_actual)*cantidad)-montodescuento;

        var porcentaje_ganancia = ganancia_item_individual*100/ ((preciosiniva*cantidad)-montodescuento);
        var cantidad_bulto_kilos = data.productoSeleccionado.kilos_bulto;

        porcentaje_ganancia = porcentaje_ganancia.toFixed(2);

        if(_.isNaN(porcentaje_ganancia)){
            porcentaje_ganancia=0;
        }

        $("#foto-item-tmp").attr("src","../../imagenes/sin_imagen.jpg");
        var referencia = "S/I";
        try{
            referencia = data.productoSeleccionado.referencia;
        }catch(e){
            referencia = referencia_sel;
        }

        campos += $.inputHidden("_cod_item_precompromiso",ContarInputItem,"[]");
        campos += $.inputHidden("_item_codigo",codigo,"[]");
        campos += $.inputHidden("_item_codigo_producto",producto,"[]");
        campos += $.inputHidden("_item_almacen",almacen,"[]");
        campos += $.inputHidden("_item_cantidad",cantidad,"[]");
        campos += $.inputHidden("_item_preciosiniva",parseFloat(preciosiniva).toFixed(2),"[]");
        campos += $.inputHidden("_item_descuento",descuento_,"[]");
        campos += $.inputHidden("_item_montodescuento",montodescuento,"[]");
        campos += $.inputHidden("_item_totalsiniva",totalsiniva,"[]");
        campos += $.inputHidden("_item_piva",parseFloat(iva).toFixed(2),"[]");
        campos += $.inputHidden("_item_iva",piva,"[]");
        campos += $.inputHidden("_item_totalconiva",totalconiva.toFixed(2),"[]");
        campos += $.inputHidden("_item_descripcion",descripcion,"[]");
        campos += $.inputHidden("_id_cuota",cuota,"[]");/*Adicionado como requisito especifico del CCP*/
        campos += $.inputHidden("_id_cantidad_bulto",data.productoSeleccionado.cantidad_bulto,"[]");/*Adicionado como requisito especifico del CCP*/
        campos += $.inputHidden("_id_costo_actual",costo_actual,"[]");

        campos += $.inputHidden("_cantidad_bulto",cantidad_bultos_totales,"[]"); // Cantidad por bulto.
        campos += $.inputHidden("_cantidad_bulto_kilos",cantidad_bulto_kilos,"[]"); // Cantidad por bulto.
        campos += $.inputHidden("_unidad_empaque",unidad_empaque_descripcion,"[]");
        campos += $.inputHidden("_ganancia_item_individual",ganancia_item_individual,"[]");
        campos += $.inputHidden("_porcentaje_ganancia",porcentaje_ganancia,"[]");
        campos += $.inputHidden("_referencia",referencia,"[]");
        campos += $.inputHidden("_item_lista_precio",$("select[name='lista_preciod'] :selected").val(),"[]",codigo);
        if(data.productoSeleccionado.posee_talla_color=="si") {  
            campos += $.inputHidden("_cantidad_por_talla_y_color",data.productoSeleccionado.cantidad_por_talla_y_color,"[]",true);
            campos += $.inputHidden("_posee_talla_color","si","[]");
        } else {
            campos += $.inputHidden("_cantidad_por_talla_y_color","","[]");
            campos += $.inputHidden("_posee_talla_color","no","[]");
        }

        var totalm3 = data.productoSeleccionado.cubi4;
        var totalft3 = data.productoSeleccionado.cubi5;
        var peso_total_item = parseFloat(cantidad)/parseFloat(data.productoSeleccionado.cantidad_bulto) * parseFloat(data.productoSeleccionado.kilos_bulto);

        if(_.isNaN(peso_total_item)){
            peso_total_item=0;
        }

        campos += $.inputHidden("_peso_total_item",peso_total_item,"[]");
        campos += $.inputHidden("_totalm3",totalm3,"[]");
        campos += $.inputHidden("_totalft3",totalft3,"[]");

        if(!_.isUndefined(data.productoSeleccionado.seriales_seleccionados)){
            campos += $.inputHidden("_posee_serial","si","[]");
            campos += $.inputHidden("seriales_seleccionados",data.productoSeleccionado.seriales_seleccionados,"[]");
        }else{
            campos += $.inputHidden("_posee_serial","si","[]");
            campos += $.inputHidden("seriales_seleccionados","","[]");
        }

        if (data.productoSeleccionado.cod_item_forma == "1") { // Si es un Producto
            $.ajax({
                type: "GET",
                url:  "../../libs/php/ajax/ajax.php",
                data: "opt=precomprometeritem&v1="+codigo+"&cpedido="+cantidad+"&codalmacen="+almacen+"&codprecompromiso="+ContarInputItem,//+"&tipo_transaccion="+transaccion,
                beforeSend: function(){

                },
                complete: function(xhr){
                    response = xhr.responseText;
                    result = eval(response);
                    if($("input[name='validar_stock']").val()=="si" && data.productoSeleccionado.posee_talla_color == "no") {
                        if(result[0].id=="-99") {
                            alert(result[0].observacion);
                            $("#cod_almacen").trigger("change");
                            return false;
                        }
                    }
                    
                    if(response!="-1") {
                        campos += '<input type="hidden" name="_pitem_almacen" value="'+almacen+'">';
                        campos += '<input type="hidden" name="_idpitem_almacen" value="'+response+'">';
                    }else{
                        campos += '<input type="hidden" name="_pitem_almacen" value="">';
                        campos += '<input type="hidden" name="_idpitem_almacen" value="">';
                    }

                    html  = "<tr>";
                    html += "<td><img src=\"../../imagenes/"+foto+"\" width=\"50\" align=\"absmiddle\" height=\"50\"/></td>";
                    html += "<td title=\"Haga click aqu&iacute; para ver detalles\" class=\"info_detalle\" style=\"cursor:pointer;background-color:#507e95;color:white;\"><a class=\"codigo\" rel=\"facebox\" style=\"color:white; text-align: center;\" href=\"#info\">"+producto+"</a><input type='hidden' name='idItem_' value='"+codigo+"'/></td>";
                    html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+referencia+"</td>";
                    html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+descripcion+"</td>";
                    
                    html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+unidad_empaque_descripcion+"</td>";
                    html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\" id='td_cantidad_bulto_"+codigo+"'>"+cantidad_bulto+"</td>";
                    html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+porcentaje_ganancia+"</td>";

                    html += "<td style='text-align: right; padding-right: 20px;' class='cantidad' rel='"+cantidad+"' id='td_cantidad_"+codigo+"' >"+cantidad+"</td>";

                    html += "<td style='text-align: right; padding-right: 20px;' class='cantidad_bultos_totales' rel='"+cantidad_bultos_totales+"' id='td_cantidad_bulto_tot_"+codigo+"'>"+cantidad_bultos_totales+"</td>";

                    html += "<td style='text-align: right; padding-right: 20px;' class='preciosiniva' id='td_precio_"+codigo+"'>"+parseFloat(preciosiniva).toFixed(3)+"</td>";
                               if($('#activarlp').val() == 'Si')  {
                   
                    if($('#lista_preciod')) {
                        
                    html += "<td style='text-align: right; padding-right: 20px;' class='preciosiniva' id='td_lista_"+codigo+"'><select name='lista_precioe' id='lista_precioe"+codigo+"' disabled>"+Lista_Preciovn+"</select></td>";
 
                    }
                }
                    html += "<td style='text-align: right; padding-right: 20px;' class='montodescuento' id='td_monto_desc_"+codigo+"'>"+parseFloat(montodescuento).toFixed(3)+"</td>";
                    html += "<td style='text-align: right; padding-right: 20px;' id='td_porc_desc_"+codigo+"'>"+parseFloat(descuento_).toFixed(3)+"</td>";
                    html += "<td style='text-align: right; padding-right: 20px;' class='totalsiniva' id='td_tot_siniva_"+codigo+"'>"+parseFloat(totalsiniva).toFixed(3)+"</td>";
                    html += "<td style='text-align: right; padding-right: 20px;' title='"+$("#moneda").val()+" "+piva.toFixed(3)+"' id='td_porc_iva_"+codigo+"'>"+parseFloat(iva).toFixed(3)+"</td>";
                    html += "<td style='text-align: right; padding-right: 20px;' class='piva' rel='"+parseFloat(piva).toFixed(3)+"'  id='td_tot_coniva_"+codigo+"'>"+totalconiva.toFixed(3)+"</td>";
                    html += "<td style='text-align: center;'><img style=\"cursor: pointer;\" class=\"editar\" alt=\""+codigo+"\"  title=\"Editar Item\" src=\"../../libs/imagenes/edit.gif\">&nbsp;<img style=\"cursor: pointer;\" alt=\"\" class=\"eliminar\"  title=\"Eliminar Item\" src=\"../../libs/imagenes/delete.png\">"+campos+"</td>";
                    html += "</tr>";

                    permitirInsertar = true;
                    $("input[name='_item_codigo[]']").each(function(){ 
                        if($(this).val()==data.productoSeleccionado.id_item){
                            permitirInsertar=false;
                        }
                    });

                    if(!permitirInsertar){
                        alert("Este producto ya fue cargado");
                        $.limpiarCamposFiltro();
                        return false;
                    }

                    $(".grid table.lista tbody").append(html);
                     if($('#activarlp').val() == 'Si')  {
                    document.getElementById("lista_precioe"+codigo).value = $('#lista_preciod').val()  ; 
                }
                    var cantidad_item = $(".grid table.lista tbody tr").length;
                    if(cantidad_item>=1) {
                        $.bloqueoFiltroHeader(true);
                    }
                    fn_cantidad();
                    $.limpiarCamposFiltro();
                    clicked=false;
                }
            });
        }else{
            //total=parseFloat(totalsiniva)+parseFloat(piva);

            campos += '<input type="hidden" name="_pitem_almacen" value="">';
            campos += '<input type="hidden" name="_idpitem_almacen" value="">';

            html  = "<tr>";
            html += "<td><img src=\"../../imagenes/"+foto+"\" width=\"50\" align=\"absmiddle\" height=\"50\"/></td>";
            html += "<td title=\"Haga click aqu&iacute; para ver detalles\" class=\"info_detalle\" style=\"cursor:pointer;background-color:#507e95;color:white;\"><a class=\"codigo\" rel=\"facebox\" style=\"color:white; text-align: center;\" href=\"#info\">"+producto+"</a><input type='hidden' name='idItem_' value='"+codigo+"'/></td>";
            html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+referencia+"</td>";
            html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+descripcion+"</td>";
            
            html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+unidad_empaque_descripcion+"</td>";
            html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+cantidad_bulto+"</td>";
            html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+porcentaje_ganancia+"</td>";

            html += "<td style='text-align: right; padding-right: 20px;' class='cantidad' rel='"+cantidad+"'>"+cantidad+"</td>";

            html += "<td style='text-align: right; padding-right: 20px;' class='cantidad_bultos_totales' rel='"+cantidad_bultos_totales+"'>"+cantidad_bultos_totales+"</td>";

            html += "<td style='text-align: right; padding-right: 20px;' class='preciosiniva'>"+parseFloat(preciosiniva).toFixed(3)+"</td>";
            html += "<td style='text-align: right; padding-right: 20px;' class='montodescuento'>"+parseFloat(montodescuento).toFixed(3)+"</td>";
            html += "<td style='text-align: right; padding-right: 20px;'>"+parseFloat(descuento_).toFixed(3)+"</td>";
            html += "<td style='text-align: right; padding-right: 20px;' class='totalsiniva'>"+parseFloat(totalsiniva).toFixed(3)+"</td>";
            html += "<td style='text-align: right; padding-right: 20px;' title='"+$("#moneda").val()+" "+piva.toFixed(3)+"'>"+parseFloat(iva).toFixed(3)+"</td>";
            html += "<td style='text-align: right; padding-right: 20px;' class='piva' rel='"+parseFloat(piva).toFixed(3)+"'>"+totalconiva.toFixed(3)+"</td>";
            html += "<td style='text-align: center;'><img style=\"cursor: pointer;\" class=\"eliminar\"  title=\"Eliminar Item\" src=\"../../libs/imagenes/delete.png\">"+campos+"</td>";
            html += "</tr>";
            $(".grid table.lista tbody").append(html);
            var cantidad_item = $(".grid table.lista tbody tr").length;
            if(cantidad_item>=1) {
                $.bloqueoFiltroHeader(true);
            }
            fn_cantidad();
            $.limpiarCamposFiltro();
        }
    }
    /**
     * Este es el evento click del boton incluir (item)
     **/

    $(document).delegate("#seleccionarPedido", "click",function(){
        if(confirm('Incluir Pedido') == false){
            return false;
        }
        codigo = $(this).children().val();
        //codigo_producto = $("#cod_item").val();
        $("#pedido_seleccionado").val(codigo);
        $.ajax({
            type: "GET",
            url:  "../../libs/php/ajax/ajax.php",
            data: "opt=seleccionarPedidoPendiente&id_pedido="+codigo,
            success: function(response){
                var reg = JSON.parse(response);
                var gastos = reg.gastos;
                $("#pedido_cargado").val("si");
                _.each(reg.productos, function(res, index){
                    iva = parseFloat(res._item_piva);
                    data.productoSeleccionado = res;
                    addTabla(res.id_item,res._item_descripcion,res._item_cantidad,res._item_preciosiniva,res._item_descuento,res._item_montodescuento,res._item_totalsiniva,iva,res._item_preciosiniva*res._item_cantidad*iva/100,parseFloat(res._item_totalconiva),res._item_almacen,res.cod_item,res.referencia,res.unidad_empaque,res.cantidad_bulto);
                });
                // gastos
                $("input[name='transpaso_salida']").attr("value",gastos.transporte_salida);
                $("input[name='transporte']").attr("value",gastos.transporte);
                $("input[name='empaques']").attr("value",gastos.empaques);
                $("input[name='seguro']").attr("value",gastos.seguro);
                $("input[name='flete']").attr("value",gastos.flete);
                $("input[name='comisiones']").attr("value",gastos.comisiones);
                $("input[name='otros']").attr("value",gastos.otros);
                $("input[name='manejo']").attr("value",gastos.manejo);
                $("input[name='total_fob_gatos']").attr("value",gastos.total_fob_gasto);
                //fn_cantidad();
            }
        });
        desplegarOcultarPanelItem();
        $("#lista_pedidos").hide();
        return 0;
    });
    

    $(document).delegate("#seleccionarNotaEntrega", "click",function(){
        if(confirm('Incluir Nota Entrega') == false){
            return false;
        }
        codigo = $(this).children().val();
        $("#nota_entrega_seleccionada").val(codigo);
        $.ajax({
            type: "GET",
            url:  "../../libs/php/ajax/ajax.php",
            data: "opt=seleccionarNotaEntregaPendiente&id_nota="+codigo,
            success: function(data){
                resultado=eval(data);
                i=0;
                while(resultado.length)
                {
                    iva=(resultado[i]._item_piva!=0)&&(resultado[i]._item_piva!='')?parseFloat(resultado[i]._item_piva):0;
                    addTabla(resultado[i].id_item,resultado[i]._item_descripcion,resultado[i]._item_cantidad,resultado[i]._item_preciosiniva,resultado[i]._item_descuento,resultado[i]._item_montodescuento,resultado[i]._item_totalsiniva,iva,resultado[i]._item_preciosiniva*resultado[i]._item_cantidad*iva/100,parseFloat(resultado[i]._item_totalconiva),resultado[i]._item_almacen,resultado[i].cod_item);
                    i=i+1;
                }
            //fn_cantidad();
            }
        });
        desplegarOcultarPanelItem();
        $("#lista_notas_entrega").hide();
        return 0;
    });
    $(document).delegate("#seleccionarCotizacion", "click",function(){
        if(confirm('Incluir Cotizacion') == false){
            return false;
        }
        codigo = $(this).children().val();
        $("#cotizacion_seleccionada").val(codigo);
        var cod_almacen = "";
        if(!_.isUndefined($("#cod_almacen_filtro :selected").val())){
            cod_almacen = $("#cod_almacen_filtro :selected").val();
        }
        $.ajax({
            type: "GET",
            url:  "../../libs/php/ajax/ajax.php",
            data: "opt=seleccionarCotizacionPendiente&id_cotizacion="+codigo+"&almacen=1",
            success: function(data){
                resultado=eval(data);
                i=0;
                var resultaVerificacion = "";
                var agregarItemCotizado = 1;
                while(resultado.length)
                {
                    //console.log(resultado[i].referencia_item);
                    iva=(resultado[i]._item_piva!=0)&&(resultado[i]._item_piva!='')?parseFloat(resultado[i]._item_piva):0;
                    agregarItemCotizado = 1;

                    if(parseInt(resultado[i]._item_cantidad) > parseInt(resultado[i]._item_cantidad_existencia)){
                        resultaVerificacion = resultado[i].referencia_item;
                        agregarItemCotizado = 0;
                        alert("No se agregara el producto "+resultaVerificacion+ " por  falta de existencia");                        
                    }
                    if(agregarItemCotizado == 1){
                        addTabla(resultado[i].id_item,
                            resultado[i]._item_descripcion,
                            resultado[i]._item_cantidad,
                            resultado[i]._item_preciosiniva,
                            resultado[i]._item_descuento,
                            resultado[i]._item_montodescuento,
                            resultado[i]._item_totalsiniva,
                            iva,resultado[i]._item_preciosiniva*resultado[i]._item_cantidad*iva/100,
                            parseFloat(resultado[i]._item_totalconiva),
                            resultado[i]._item_almacen,
                            resultado[i].cod_item,
                            resultado[i].referencia_item,
                            resultado[i].unidad_empaque,
                            resultado[i].cantidad_bulto);                        
                    }
                    
                    i=i+1;
                }
            }
        });
        desplegarOcultarPanelItem();
        $("#lista_cotizaciones").hide();
        return 0;
    });
    $(document).delegate("#incluir_cuotas", "click", function(){
        if(confirm('Incluir Cuota') == false){
            return false;
        }
        var cuotas_seleccionadas = [];
        $("input[type=checkbox]:checked#cuotas_seleccionadas").each(function (){
            cuotas_seleccionadas.push($(this).val());
            $(this).removeAttr("checked");
        });
        //alert(cuotas_seleccionadas);//Aqui tengo el array
        /*Stringify the javascript object (json) with*/
        var c = JSON.stringify(cuotas_seleccionadas);

        $.post("../../libs/php/ajax/ajax.php", {
            "opt": "ponerCuotaPagada",
            "cliente": $("#id_cliente").val(),
            cuotas:c
        },
        function(data){
            var resultado = eval(data);
            var i = 0;
            while(resultado.length){
                var iva = 0;//(resultado[i]._item_piva!=0 && resultado[i]._item_piva!='') ? parseFloat(resultado[i]._item_piva) : 0;
                var descripcion = resultado[i].descripcion + " " + resultado[i].anio + "-" + (resultado[i].mes < 10 ? "0" + resultado[i].mes : resultado[i].mes);
                var cantidad = 1;//Siempre 1
                var descuento = 0;//Siempre 0
                var montodescuento = 0;//Siempre 0
                var preciosiniva = resultado[i].precio;
                var totalsiniva = resultado[i].precio;//Siempre es cantidad = 1
                var totalconniva = 0;
                var almacen = 0;
                addTabla(resultado[i].id_item, descripcion, cantidad, resultado[i].precio, descuento, montodescuento, totalsiniva, iva, preciosiniva*cantidad*iva/100, parseFloat(totalconniva), almacen, resultado[i].cod_item, resultado[i].id);
                i=i+1;
            }
        }, "json");
        desplegarOcultarPanelItem();
        $("#lista_cuotas").hide();
        return 0;
    });
    /*
    * Este ajax carga el tipo de precio que aplica para el cliente. Esto se especifica en los datos del cliente.
    */
    $.ajax({
        type: "GET",
        url:  "../../libs/php/ajax/ajax.php",
        data: "opt=DetalleCliente&v1="+$("input[name='id_cliente']").val(),
        beforeSend: function(){
        // $("#descripcion_item").html(MensajeEspera("<b>Veficando Cod. item..<b>"));
        },
        success: function(response){
            datoscliente = eval(response);
            $("input[name='DatosCliente']").val(response);
            preciolibre = $("#idpreciolibre").val();
            if (datoscliente[0].cod_tipo_precio != preciolibre ){
                $("#cod_tipo_precio").attr("disabled","disabled");
            }else{
                $("#precioProductoPedido").removeAttr("readonly");
            }
        }
    });

    function desplegarOcultarPanelItem(){
        if($("#LabelMensaje").html()=="Agregar Nuevo Item"){
            $("#LabelMensaje").html("Ocultar Panel");
            $("#ImgMensaje").attr("src","../../libs/imagenes/drop-add2.gif");
            $("#PanelFactura").show();
        }else{
            $("#LabelMensaje").html("Agregar Nuevo Item");
            $("#ImgMensaje").attr("src","../../libs/imagenes/drop-add.gif");
            $("#cancelaradd").trigger("click");
            $("#PanelFactura").hide();
        }
    }
    /**
    * Este evento permite desplegar u ocultar el panel para agregar el item.
    **/
    $("#MostrarTabla").click(function(){
        /*valor = $("#LabelMensaje").html();
        if(valor=='Agregar Nuevo Item'){
            $("#LabelMensaje").html("Ocultar Panel");
            $("#ImgMensaje").attr("src","../../libs/imagenes/drop-add2.gif");
            $("#PanelFactura").show();
        }else{
            $("#LabelMensaje").html("Agregar Nuevo Item");
            $("#ImgMensaje").attr("src","../../libs/imagenes/drop-add.gif");
            $("#cancelaradd").trigger("click");
            $("#PanelFactura").hide();
        }*/
        desplegarOcultarPanelItem();
    });
    /**
    * Este evento permite cancelar o limpiar el panel del item a incluir.
    **/
    $("#cancelaradd").click(function(){
        $("input[name='totalPedido'],input[name='montodescuentoPedido'],input[name='descuentoPedido'],input[name='precioProductoPedido'],input[name='cantidadPedido']").val("0");
        //      $("input[name='cod_item_forma'], input[name='id_item']").removeAttr("disabled");
        //      $("input[name='id_item']").find("option").remove();
        $("input[name='cod_item_forma']").val("");
        /*$("#descripcion_tipo_forma").html("Item");
        $("#descripcion_tipo_forma").attr("style", "font-family:'Verdana'; font-weight: bold;");*/
        $("#descripcion_tipo_forma").attr("style", "font-family:'Verdana';");
        $("#descripcion_item").html("");
        $("input[name='cod_item_forma'], input[name='id_item']").val("");
        /*$("#descripcion_tipo_forma").html("Item");
        $("#descripcion_tipo_forma").attr("style", "font-family:'Verdana'; font-weight: bold;");*/
        $("#cod_almacen").val("");
        $("#descripcion_item").val("");
        $("#cod_almacen").removeAttr("disabled");
        $("#LabelDetalleItem").html("");
        $("#informacionitem").val("");
        $("#fila_precio1").val("");
        $("#fila_precio2").val("");
        $("#fila_precio3").val("");
        //$("#LabelCantidadExistente").html("");
        $("#fila_precio1_iva").val("");
        $("#fila_precio2_iva").val("");
        $("#fila_precio3_iva").val("");
        $("#cod_almacen").find("option").remove();
        $("#cantidadItem, #cantidadTotalItem,#cantidadItemComprometidoByAlmacen").val("");
        // AÃ±adido 10-06-2013 luego de modificar la funcion $("#LabelMensaje").click(function(){} para acceder rapidamente a la ventana de busqueda de Items.
        $("#LabelMensaje").html("Agregar Nuevo Item");
        $("#ImgMensaje").attr("src","../../libs/imagenes/drop-add.gif");
        $("#PanelFactura").hide();
    });
    $("#cantidadPedido").blur(function(){
        //descuentopedido = parseFloat($("#descuentoPedido").val());
        //$("#montodescuentoPedido").val("0");
        if($(this).val()==''){
            $(this).val("0")
            $("#totalPedido").val("0");
            $("#descuentoPedido").val("0");
            $("#montodescuentoPedido").val("0");
            return false;
        }
        $("#descuentoPedido").trigger("blur");
        if($("#cantidadPedido").val()==0){
            $("#totalPedido").val(parseFloat(0));
            $("#descuentoPedido").val("0");
            $("#montodescuentoPedido").val("0");
            return false;
        }
        $(this).val(parseFloat($(this).val()));
        cantidad = parseFloat($(this).val());
        if ($("input[name='cod_item_forma']").val() == 1) { // Si es igual al Producto
            cantidadActual = $("#cantidadItem").val();
            if (cantidad > cantidadActual) {
                Ext.MessageBox.show({
                    title: 'Notificaci&oacute;n',
                    msg: "Disculpe, la cantidad pedida es mayor que la existente, verifique existencia.",
                    buttons: Ext.MessageBox.OK,
                    animEl: 'cantidadPedido',
                    icon: 'ext-mb-warning'
                });
                $(this).val("0").focus();
                return false;
            }
        }
        if (cantidad == 0) {
            $("#totalPedido").val(parseFloat(0));
            $("#descuentoPedido").val("0");
            $("#montodescuentoPedido").val("0");
            return false;
        }
        else {
            porcentaje  = $("#descuentoPedido").val();
            precioitem = $("#precioProductoPedido").val();
            descuento = parseFloat(porcentaje/100) * parseFloat(precioitem);
            total = parseFloat(cantidad) * parseFloat($("#precioProductoPedido").val()-parseFloat(descuento));
            $("#totalPedido").val(parseFloat(total.toFixed(3)));
        }
    }).click(function(){
        if($(this).val()==''){
            $(this).val("0");
            $("#totalPedido").val("0");
            $("#descuentoPedido").val("0");
            $("#montodescuentoPedido").val("0");
            return false;
        }
        porcentaje  = $("#descuentoPedido").val();
        precioitem = $("#precioProductoPedido").val();
        descuento = parseFloat(porcentaje/100) * parseFloat(precioitem);
        total = parseFloat($(this).val()) * parseFloat($("#precioProductoPedido").val()-parseFloat(descuento));
        $("#totalPedido").val(parseFloat(total.toFixed(3)));
    });
    $("#descuentoPedido").blur(function(){
        datoscliente = eval($("input[name='DatosCliente']").val());
        if($(this).val()==''){
            $(this).val("0");
        }
        porcentaje = $(this).val();
        if (parseFloat(porcentaje) > parseFloat(datoscliente[0].porc_parcial)) {
            Ext.MessageBox.show({
                title: 'NotificaciÃƒÂ³n',
                msg: "El porcentaje no puede ser mayor al limite de cliente",
                buttons: Ext.MessageBox.OK,
                animEl: 'descuentoPedido',
                icon: 'ext-mb-warning'
            });
            $(this).val("0");
            porcentaje = 0;
        }
        cantidad = $("#cantidadPedido").val();
        precioitem = $("#precioProductoPedido").val();
        descuento = parseFloat(porcentaje) * (parseFloat(precioitem) * parseFloat(cantidad)) / 100 ;
        total = parseFloat(precioitem) * parseFloat(cantidad) - descuento;
        $("#montodescuentoPedido").val(descuento.toFixed(3));//antes solo descuento
        $("#totalPedido").val(total.toFixed(3));
    });

    $("#cod_tipo_precio").change(function(){
        valor = $(this).val();
        switch(valor){
            case $("#idpreciolibre").val():
                $("#precioProductoPedido").removeAttr("readonly");
                $("#precioProductoPedido").trigger("blur");
                break;
            case $("#idprecio1").val():
                precio = $("#fila_precio1").val();
                $("#precioProductoPedido").val(precio);
                $("#precioProductoPedido").attr("readonly","readonly");
                break;
            case $("#idprecio2").val():
                precio = $("#fila_precio2").val();
                $("#precioProductoPedido").val(precio);
                $("#precioProductoPedido").attr("readonly","readonly");
                break;
            case $("#idprecio3").val():
                precio = $("#fila_precio3").val();
                $("#precioProductoPedido").val(precio);
                $("#precioProductoPedido").attr("readonly","readonly");
                break;
        }
        $("#cantidadPedido").trigger("blur");
    });

    $("#precioProductoPedido").blur(function(){
        if($(this).val()==''){
            $(this).val("0");
        }
        $(this).val(parseFloat($(this).val()));

        $("#cantidadPedido").trigger("blur");
    });
    /*
     * Funcion que Instancia a la ventana de busqueda de items (Productos y servicios).
     * Paquete pBuscarItem en buscar_productos_Servicios.js
     */
    $("#LabelMensaje").click(function(){
        /*Creado para sustituir $("#seleccionItem").click(function(){}); abajo*/
        if($(this).html() == "Agregar Nuevo Item"){
            pBuscaItem.main.mostrarWin();
        }
    });

    $.limpiarCamposFiltro = function () {
        $("input[name='filtro_descripcion']").val("");
        $("input[name='filtro_referencia']").val("");
        $("input[name='filtro_descripcion']").val("");
        $("input[name='filtro_unidad']").val("");
        $("input[name='filtro_referencia']").val("");
        $("input[name='filtro_bulto']").val("0.00");
        $("input[name='filtro_descuento']").val("0.00");
        $("input[name='filtro_precio']").val("0.00");
        $("input[name='filtro_importe']").val("0.00");
        $("input[name='filtro_kilos']").val("0.00");
        $("input[name='filtro_bultos_total']").val("0");
        
        $("input[name='filtro_cantidad']").val("0");
        data.productoSeleccionado = {};
        $("#informacionitem").val("");
        $("input[name='filtro_codigo']").val("").focus();
    }

    $.bloqueoFiltroHeader = function(bloquear) {
        if(bloquear) {
            $("#cod_almacen_filtro").attr("disabled","disabled");
            $("select[name='validar_stock']").attr("disabled","disabled");
            //$("select[name='lista_precio']").attr("disabled","disabled");
        } else {
            $("#cod_almacen_filtro").removeAttr("disabled")
            $("select[name='validar_stock']").removeAttr("disabled");
            //$("select[name='lista_precio']").removeAttr("disabled");
        }
    }

    $.filtrarArticulo = function(value, tipoFiltro){
            var switchFiltro = tipoFiltro || "filtroItem"; // para el caso de pedido es: filtroItemByRCCB
            pBuscaItem.main.storeProductos.baseParams.opt = switchFiltro;

            if(!_.isUndefined($("select[name='cod_almacen_filtro'] :selected").val())){
                pBuscaItem.main.storeProductos.baseParams.cod_almacen = $("select[name='cod_almacen_filtro'] :selected").val();
            }

            pBuscaItem.main.storeProductos.baseParams.limit = pBuscaItem.main.limitePaginacion;
            pBuscaItem.main.storeProductos.baseParams.start = pBuscaItem.main.iniciar;
            // pBuscaItem.main.storeProductos.baseParams.cmb_tipo_item = 1;//productos
            pBuscaItem.main.storeProductos.baseParams.codigoProducto = value;
            pBuscaItem.main.storeProductos.baseParams.BuscarBy = true;
            pBuscaItem.main.storeProductos.baseParams.seleccionado = true;
            
            pBuscaItem.main.storeProductos.load({ 
                callback: function(records, operation, success) { 
                    if (success) {
                        /*if(records.length > 1){
                            pBuscaItem.main.mostrarWin();
                        } else if(records.length == 1){*/
                        //console.log(records)
                        if(records.length > 0){
                            data.productoSeleccionado = records[0].json;
                            $("#informacionitem").val(JSON.stringify(records[0].json));

                            if( !_.str.isBlank(data.productoSeleccionado.foto)) {
                                $("#foto-item-tmp").attr("src","../../imagenes/"+data.productoSeleccionado.foto);
                            }
                            
                            // data.productoSeleccionado.foto1
                            $("input[name='filtro_descripcion']").val(data.productoSeleccionado.descripcion1);
                            $("input[name='filtro_referencia']").val(data.productoSeleccionado.referencia);
                            $("input[name='filtro_unidad']").val(data.productoSeleccionado.unidad_empaque);

                            $("input[name='filtro_bulto']").val(data.productoSeleccionado.cantidad_bulto);
                            $("input[name='filtro_kilos']").val(data.productoSeleccionado.kilos_bulto);
                            
                            $.verificarBloqueoCampoPrecio();

                            var preciosiniva = getTipoPrecio();
                            $("input[name='filtro_precio']").val(preciosiniva.toFixed(2));
                            $("input[name='filtro_importe']").val("0.00");

                            $("input[name='filtro_cantidad']").focus();
                            $("input[name='filtro_cantidad']").select();

                            if(data.productoSeleccionado.posee_talla_color=="si") {
                                $("input[name='filtro_cantidad']").attr("readonly","readonly");
                            } else {
                                $("input[name='filtro_cantidad']").removeAttr("readonly");
                            }

                            /*if(parseFloat(data.productoSeleccionado.precio1)==0){
                                $("#lista_precio").val('["8","LIBRE"]');
                                $.verificarBloqueoCampoPrecio();
                            }*/

                        } else {
                            $.mensajeNotificacion({mensaje:'No se encontraron productos asociados'});
                            $.limpiarCamposFiltro();
                            data.productoSeleccionado = {};
                        } 
                    } 
                } 
            });
    };

    $("input[name='filtro_codigo']").keypress(function(ev){
        var teclaTabMasP  = 13;
        var codeCurrent = ev.keyCode;
        var value = $(this).val();
        if(teclaTabMasP == codeCurrent){
            if(_.str.isBlank(value)) { 
                pBuscaItem.main.mostrarWin();
                return false;
            }

            var tipo_filtro = $("input[name='tipo_filtro']:checked").val()
            
            if(tipo_filtro=="filtro_codigo_barra"){
                $.filtrarArticulo(value, "filtroItemByCodigoBarra");
            }else{
                $.filtrarArticulo(value, "filtroItemByRCCB");
            }
            return false;
        }
    });
    
    $.validarCantidad = function(val){
        var value = parseInt(val);
        if(_.isNaN(value)){
            $.mensajeNotificacion({mensaje: "Debe especificar una cantidad valida."});
            value = 0;
        } else if(value==0) {
            $.mensajeNotificacion({mensaje: "La cantidad debe ser mayor a cero (0)"});
            value = 0;
        }
        return value;
    }

    $.calculoImporte = function() {

        if(_.isUndefined(data.productoSeleccionado.id_item)){
                $("input[name='filtro_cantidad']").val(0);
                $("input[name='filtro_codigo']").focus();
                return false;
        }

        var precioProducto = parseFloat($("input[name='filtro_precio']").val());
        var cantidad = parseFloat($("input[name='filtro_cantidad']").val());


        var cantidad_bulto = parseFloat(data.productoSeleccionado.cantidad_bulto);
        var unidad_empaque_descripcion = data.productoSeleccionado.unidad_empaque || "";

        if(data.productoSeleccionado.cod_item_forma=="1"){
            if(_.isNaN(cantidad_bulto)){
                alert("Debe verificar la cantidad por "+unidad_empaque_descripcion);
                return false;
            }

            if(cantidad_bulto<=0){
                alert("Debe especificar la cantidad por "+unidad_empaque_descripcion);
                return false;
            }
        } else {
            // Cuando es un servicio
            cantidad_bulto=0;
        }
        var cantidad_bultos_totales = reglaCalculoPorBulto({
                                        "cantidad":       cantidad,
                                        "cantidad_bulto": cantidad_bulto
                                      });

        var total_ = precioProducto * cantidad;

        if(_.isNaN(total_)){
            total_ = "0.00";
            $("input[name='filtro_cantidad']").val(0);
        }
        $("input[name='filtro_importe']").val(total_.toFixed(2));
        $("input[name='filtro_bultos_total']").val(cantidad_bultos_totales);
        $("input[name='filtro_precio']").val(precioProducto);

    }

    $.verificarBloqueoCampoPrecio = function(){
        if(JSON.parse($("select[name='lista_precio'] :selected").val())[1]=='LIBRE') {
            $("input[name='filtro_precio']").removeAttr("readonly").attr("style","width: 100%;padding:5px;");
        } else {
            $("input[name='filtro_precio']").attr("readonly","readonly").attr("style","width: 100%;background: #DDD;padding:5px;");
        }
    }
    
    
        $.verificarBloqueoCampoPrecio1 = function(){
        if($("select[name='lista_preciod'] :selected").val()=='8') {
            $("input[name='filtro_precio']").removeAttr("readonly").attr("style","width: 100%;");
        } else {
            $("input[name='filtro_precio']").attr("readonly","readonly").attr("style","width: 100%;background: #DDD;");
        }
    } 

    /*$("select[name='lista_precio']").change(function(){
        $.verificarBloqueoCampoPrecio();
        if(!_.isUndefined(data.productoSeleccionado.id_item)){
            var preciosiniva = getTipoPrecio();
            $.verificarBloqueoCampoPrecio();
            $("input[name='filtro_precio']").val(preciosiniva.toFixed(2));
            $.calculoImporte();
        }else{

        }
    });*/

    var lista_precio_prev = $("#lista_precio");
    lista_precio_prev.data("lista_precio_prev",lista_precio_prev.val());

    $("#lista_precio").change(function(){
        var jqThis = $(this);
        var aux = "";
        var aux2 = "";
        var aux3 = "";
           
        if (Util.verificaNivelOperacion(NIVEL_USUARIO,NIVEL_CAMBIO_LISTA)) {
            
            $.verificarBloqueoCampoPrecio();
            if(!_.isUndefined(data.productoSeleccionado.id_item)){
                var preciosiniva = getTipoPrecio();
                $.verificarBloqueoCampoPrecio();
                $("input[name='filtro_precio']").val(preciosiniva.toFixed(2));
                $.calculoImporte();
            }else{

            }
            jqThis.data("lista_precio_prev",jqThis.val());
                               //Envio formulario... o lo que vayas hacer
        }else{     
            Util.solicitaAutorizacion(NIVEL_CAMBIO_LISTA,function(){
                $.verificarBloqueoCampoPrecio();
                if(!_.isUndefined(data.productoSeleccionado.id_item)){
                    var preciosiniva = getTipoPrecio();
                    $.verificarBloqueoCampoPrecio();
                    $("input[name='filtro_precio']").val(preciosiniva.toFixed(2));
                    $.calculoImporte();
                }
                jqThis.data("lista_precio_prev",jqThis.val());
            },function(){
                //alert("no autorizado")
                $("#lista_precio").val(jqThis.data("lista_precio_prev"));      
            });                                 
        }
             
    });



  var lista_precio_prev = $("#lista_precio1");
    lista_precio_prev.data("lista_precio_prev",lista_precio_prev.val());
    $("#lista_preciod").change(function(){
        var jqThis = $(this);
        var aux = "";
        var aux2 = "";
        var aux3 = "";

        if (Util.verificaNivelOperacion(NIVEL_USUARIO,NIVEL_CAMBIO_LISTA)) {
            //alert("autonizado")
            
            $.verificarBloqueoCampoPrecio1();
            if(!_.isUndefined(data.productoSeleccionado.id_item)){
                var preciosiniva = getTipoPrecio1();
                $.verificarBloqueoCampoPrecio1();
                $("input[name='filtro_precio']").val(preciosiniva.toFixed(2));
                $.calculoImporte();
            }else{

            }
            jqThis.data("lista_precio_prev",jqThis.val());
        }else{
            Util.solicitaAutorizacion(NIVEL_CAMBIO_LISTA,function(){
                $.verificarBloqueoCampoPrecio1();
                if(!_.isUndefined(data.productoSeleccionado.id_item)){
                    var preciosiniva = getTipoPrecio1();
                    $.verificarBloqueoCampoPrecio1();
                    $("input[name='filtro_precio']").val(preciosiniva.toFixed(2));
                    $.calculoImporte();
                }
                jqThis.data("lista_precio_prev",jqThis.val());
            },function(){
                $("#lista_precio1").val(jqThis.data("lista_precio_prev"));      
            });                                 
        }
             
    });


    $.verificarExistenciaEnAlmacen = function(value){
        data.productoSeleccionado.permitir_cargar = "no";
        var cantidad_solicitada = parseInt($("input[name='filtro_cantidad']").val());
            if(_.isUndefined(data.productoSeleccionado.id_item)){
                $("input[name='filtro_cantidad']").val(0);
                $("input[name='filtro_codigo']").focus();
                return false;
            }

            if (data.productoSeleccionado.posee_talla_color=="si") {
                var cod = data.productoSeleccionado.id_item;
                var validador = true;
                $.ajax({
                    type: 'GET',
                    data: 'cod='+cod,
                    url:  'colortalla_pedido.php',
                    beforeSend: function(){},
                    success: function(response) {

                        var win_tmp = new Ext.Window({
                            title:'Colores y Tallas del Producto',
                            height: 400,
                            width: 1024,
                            frame:true,
                            autoScroll:true,
                            modal:true,
                            html: response,
                            buttons:[{
                                text:'Guardar',
                                handler: function(){
                                   var cantidad_por_items = []; 
                                   $("#form-talla-color").find("input[type='text']").each(function() {
                                        if(!_.str.isBlank($(this).val())){
                                            cantidad_por_items.push({
                                                "cantidad": $(this).val(),
                                                "name": $(this).attr("name"),
                                                "data-cantidad-actual": $(this).attr("data-cantidad-actual"),
                                                "talla": $(this).attr("data-id-talla"),
                                                "color": $(this).attr("data-id-color"),
                                                "item": $(this).attr("data-id-item")
                                            });
                                        }

                                    });

                                    data.productoSeleccionado.cantidad_por_talla_y_color = JSON.stringify(cantidad_por_items);

                                    var pregunta_valida_contra_stock = $("input[name='validar_stock']").val();
                                    var validador = true;
                                    if(pregunta_valida_contra_stock=="si"){
                                        $("#form-talla-color").find(".cantidad-color-talla").each(function() {
                                            var cantidad_existente = parseFloat($(this).attr("data-cantidad-actual"));
                                            var cantidad_solicitada = parseFloat($(this).val());
                                            if (cantidad_solicitada>cantidad_existente) {
                                                alert("El producto actual no posee la cantidad solicitada para la talla y color solicitada, disponibilidad actual: "+cantidad_existente+", verifique.");
                                                $(this).focus();
                                                validador = false;
                                                return false;
                                            }
                                        });
                                    }

                                    if(validador) {
                                        var sum_cantidad_total = 0;
                                        $("#form-talla-color").find(".cantidad-color-talla").each(function() {
                                            if(!_.str.isBlank($(this).val())) {
                                                var cantidad_solicitada = parseFloat($(this).val());
                                                sum_cantidad_total += cantidad_solicitada;
                                            }
                                        });

                                        $("input[name='filtro_cantidad']").val(sum_cantidad_total);
                                        $.calculoImporte();
                                        data.productoSeleccionado.permitir_cargar = "si";
                                        $("input[name='filtro_descuento']").focus();
                                        win_tmp.hide();
                                    } 
                                }
                            }]
                        });

                        win_tmp.show(this);
                    }
                });

            } else {

                if (_.isNaN(cantidad_solicitada)) {
                    alert("value",  "Debe especificar una cantidad valida.");
                    return false;
                } else if(value==0) {
                     alert("La cantidad debe ser mayor a cero (0)");
                    return false;
                }

                $.existenciaPorArticulo({
                    id_item: data.productoSeleccionado.id_item,
                    fcallback: function(resultado){
                        var cantidad_existente = parseFloat(resultado[0].cantidad);
                        var cod_almacen = parseFloat(resultado[0].cod_almacen);

                        var pregunta_valida_contra_stock = $("input[name='validar_stock']").val();
                        if(pregunta_valida_contra_stock=="si"){
                            if(cantidad_solicitada>cantidad_existente){
                                alert("El producto actual no posee la cantidad solicitada, disponibilidad actual: "+cantidad_existente);
                                return false;
                            }
                        }
                        
                        $.calculoImporte();
                        data.productoSeleccionado.permitir_cargar = "si";
                        $("input[name='filtro_descuento']").focus();
                    }
                });
            }
            
    };

    $("input[name='filtro_cantidad']").keypress(function(ev){
        var teclaTabMasP  = 13;
        var codeCurrent = ev.keyCode;
        var value = $(this).val();
        if(teclaTabMasP == codeCurrent) {
            var self = this;
            $.verificarExistenciaEnAlmacen(value);
        }
    });

    $("input[name='filtro_cantidad']").blur(function(ev){
        var value = $(this).val();
        if(value>0){
            $.verificarExistenciaEnAlmacen(value);
        }
    });

    $("input[name='filtro_descuento']").keypress(function(ev){
        var teclaTabMasP  = 13;
        var codeCurrent = ev.keyCode;
        var value = $(this).val();

        if(clicked==true){
            return false;
        }
        if(teclaTabMasP == codeCurrent){
            if(_.str.isBlank(value)){
                $("input[name='filtro_descuento']").val(0);
            }

            if(_.isUndefined(data.productoSeleccionado.id_item)){
                $("input[name='filtro_codigo']").focus();
                return false;
            }
            clicked=true;
            var preciosiniva = getTipoPrecio();
            
                    preciosiniva = parseFloat($("input[name='filtro_precio']").val());
                    if(preciosiniva > PRECIO_MAXIMO || preciosiniva < PRECIO_MINIMO){
                        Util.solicitaAutorizacion(NIVEL_CAMBIO_LISTA,function(){
                            $.agregarArticulo();                        
                        },function(){
                                
                        },"Precio fuera de rango");   
                        
                    }
                    else{
                        $.agregarArticulo(); 
                    }
        }
    });
    $("input[name='filtro_precio']").blur(function(ev){
        var isReadOnly = $("input[name='filtro_precio']").attr("readonly");
        var value = $(this).val();
        if(!isReadOnly){
            if(!_.isUndefined(data.productoSeleccionado.id_item)){
               if(_.str.isBlank(value) || _.isNaN(parseFloat(value))){
                    $(this).val(0);
               }
               $.calculoImporte();
            }
        }
    });

    $("input[name='filtro_precio']").keypress(function(ev){
        var teclaTabMasP  = 13;
        var codeCurrent = ev.keyCode;
        var value = $(this).val();
        if(clicked==true){
            return false;
        }
        if(teclaTabMasP == codeCurrent){
            if(_.str.isBlank(value)){
                $(this).val(0);
            }

            if(_.isUndefined(data.productoSeleccionado.id_item)){
                $("input[name='filtro_codigo']").focus();
                return false;
            }
            clicked=true;
            var preciosiniva = getTipoPrecio();
                    preciosiniva = parseFloat($("input[name='filtro_precio']").val());
                    if(preciosiniva > PRECIO_MAXIMO || preciosiniva < PRECIO_MINIMO){
                        Util.solicitaAutorizacion(NIVEL_CAMBIO_LISTA,function(){
                            $.agregarArticulo();                        
                        },function(){
                                
                        },"Precio fuera de rango");   
                        
                    }
                    else{
                        $.agregarArticulo(); 
                    }

        }

    });

    $.mensajeNotificacion = function(param){
        Ext.MessageBox.show({
            title: 'Notificaci&oacute;n',
            msg: param.mensaje,
            buttons: Ext.MessageBox.OK
        });
    
    }

    $.existenciaPorArticulo = function(parametros) {
        if(_.isUndefined($("#cod_almacen_filtro :selected").val())){
            var cod_almacen = $("input[name='cod_almacen_defecto']").val();
        }else{
            var cod_almacen = $("#cod_almacen_filtro :selected").val();
        }

        $.ajax({
            type: "GET",
            url:  "../../libs/php/ajax/ajax.php",
            data: "opt=ExistenciaProductoAlmacenDefaultByIdItem&v1="+parametros.id_item+"&cod_almacen="+cod_almacen,
            beforeSend: function(){
            //$("#descripcion_item").html(MensajeEspera("<b>Veficando Cod. item..<b>"));
            },
            success: function(data){
                var resultado = eval(data);
                if(resultado[0].id=="-1"){
                    alert("Verifique existencia.")
                    return false;
                }else{
                    parametros.fcallback(resultado);
                }//Fin de if(resultado[0].id=="-1")
            }
        });
    }

    $("#btnBuscar").click(function(ev){
        ev.preventDefault();
        pBuscaItem.main.mostrarWin();
    });

    $("input[name='transpaso_salida'],input[name='transporte'],input[name='empaques'],input[name='seguro'],input[name='flete'],input[name='comisiones'],input[name='otros'],input[name='manejo']").numeric();
    $("input[name='copias']").numeric();

    $("input[name='filtro_descuento'],input[name='filtro_cantidad'],input[name='filtro_bultos_total'],input[name='filtro_precio']").keypress(function(ev){
        var teclaTabMasP  = 402;
        var teclaTabMasL  = 32;
        var codeCurrent = (ev.keyCode == 0) ? ev.charCode : ev.keyCode;

        if(ev.keyCode == 45)
            return false;
        return true;
    });

    $("input[name='filtro_descuento'],input[name='filtro_cantidad'],input[name='filtro_bultos_total'],input[name='filtro_precio']").numeric();
    
    $("input.input-gastos").numeric();
    $("input[name='copias']").numeric();
    //$("#lista_precio").val('["8","LIBRE"]');
});