 var data = {
        productoSeleccionado : {},
        costounitario : {},
        cantidadunitaria : {},
};
var eventos_form = {
    vcampos: '',
    formatearNumero: function(objeto) {
        var num = objeto.numero;
        var n = num.toString();
        var nums = n.split('.');
        var newNum = "";
        if (nums.length > 1)
        {
            var dec = nums[1].substring(0, 2);
            newNum = nums[0] + "," + dec;
        }
        else
        {
            newNum = num;
        }
        return newNum;
    },
    cargarProducto: function() {
        $.ajax({
            type: 'GET',
            data: 'opt=Selectitem&v1=1',
            url: '../../libs/php/ajax/ajax.php',
            beforeSend: function() {
                $("#items").find("option").remove();
                $("#items").append("<option value=''>Cargando..</option>");
            },
            success: function(data) {
                $("#items").find("option").remove();
	$("#items").append("<option value=''>Seleccione..</option>");
                for (i = 0; i <= this.vcampos.length; i++) {
                    $("#items").append("<option value='" + this.vcampos[i].id_item + "'>"  + this.vcampos[i].referencia + " " + this.vcampos[i].descripcion1 + " " + this.vcampos[i].id_item +  "</option>");
                }
            }
        });
    },
    cargarAlmacenes: function() {
        $.ajax({
            type: 'GET',
            data: 'opt=getAlmacen',
            url: '../../libs/php/ajax/ajax.php',
            beforeSend: function() {
                $("#almacen").find("option").remove();
                $("#almacen").append("<option value=''>Cargando..</option>");
            },
            success: function(data) {
                $("#almacen").find("option").remove();
                this.vcampos = eval(data);
                for (i = 0; i <= this.vcampos.length; i++) {
                    $("#almacen").append("<option value='" + this.vcampos[i].cod_almacen + "'>" + this.vcampos[i].descripcion + "</option>");
                }
            }
        });
    },
    init: function() {
        this.cargarProducto();
        this.cargarAlmacenes();
        this.Limpiar();
    },
    Limpiar: function() {
        $("#cantidadunitaria, #items, #almacen, #descripcionitem, #codigofabricante,#cantidadunitaria,#costounitario, #totalitem_tmp, #costo","#costoreal").val("");
    },
    IncluirRegistros: function(options) {
        var html = "";
        var campos = "";


        options["costo_real"] = options.cantidad * options.costo;
        if(options.cantidad_bulto ==0)  {
            options["bulto"] = 0;
        }else{   
            options["bulto"] = options.cantidad/options.cantidad_bulto;
        }            
        $.ajax({
            type: 'GET',
            data: 'opt=DetalleSelectitem&v1=' + options.id_item,
            url: '../../libs/php/ajax/ajax.php',           
            success: function(data) {
                vcampos = eval(data);
                var id_detalle_compra = 0;
                if(typeof options.id_detalle_compra == undefined || typeof options.id_detalle_compra == "undefined" 
                || options.id_detalle_compra == undefined || options.id_detalle_compra == "undefined" 
                || options.id_detalle_compra == null   ){
                    id_detalle_compra = 0;
                }
                else{
                    id_detalle_compra = options.id_detalle_compra;
                }
                campos += $.inputHidden("_id_detalle_compra", id_detalle_compra, "[]");
                campos += $.inputHidden("_id_item", options.id_item, "[]");
                campos += $.inputHidden("_cod_item", options.cod_item, "[]");
                campos += $.inputHidden("_cod_referencia", options.referencia, "[]");
                campos += $.inputHidden("_id_almacen", options.id_almacen, "[]");
                campos += $.inputHidden("_bulto", options.bulto.toFixed(2), "[]");
                campos += $.inputHidden("_cantidad_bulto", options.cantidad_bulto, "[]");
                campos += $.inputHidden("_cantidad", options.cantidad, "[]");
                campos += $.inputHidden("_costo", options.costo, "[]");                
                campos += $.inputHidden("_mvoal_costo", options.costo_real.toFixed(2), "[]");
                
                html = "           <tr id=" + options.id_item + ">";
                // html += "		<td id=cod_item title=\"Haga click aqui para ver el detalle del Item\" class=\"info_detalle\" style=\"cursor:pointer;background-color:#507e95;color:white;\"><a class=\"codigo\" rel=\"facebox\" style=\"color:white;\" href=\"#info\">" + options.cod_item + "</a></td>";
                html += "		<td id=cod_item > " + options.cod_item + "<div id='seriales-fields-"+options.id_item+"' class='x-hide-display'></div><div id='barras-fields-"+options.id_item+"' class='x-hide-display'></div></td>";
                html += "		<td>" + options.referencia + "<div style='display:none;' id='html_escala_item_"+options.id_item+"'></div></td>";
                html += "		<td>" + options.descripcion1 + "</td>";
                html += "		<td id=\"bulto_" + options.id_item + "\">" + options.bulto.toFixed(2)+ "</td>" 
                html += "		<td id=\"cantidad_bulto_" + options.id_item + "\">" + options.cantidad_bulto + "</td>";
                html += "		<td id=\"cantidad_" + options.id_item + "\">" + options.cantidad + "</td>";
                html += "		<td id=\"costo_" + options.id_item + "\">"  + options.costo + "</td>";
                html += "		<td id=\"real_costo" + options.id_item + "\">"  + options.costo_real.toFixed(2) + "</td>";
                html += "		<td><img style=\"cursor: pointer;\" class=\"editar\" value=\"" + options.id_item + "\"  title=\"Editar Item\" height=\"16\" width=\"16\"  src=\"../../libs/imagenes/edit.gif\"></td>";
                html += "		<td><img style=\"cursor: pointer;\" class=\"eliminar\"  title=\"Eliminar Item\" src=\"../../libs/imagenes/delete.png\">" + campos + "</td>";
                //html += "		<td><img style=\"cursor: pointer;\" class=\"colortalla\" alt=\"" + options.id_item + "\" title=\"Colores y tallas del item\" height=\"16\" width=\"16\" src=\"../../libs/imagenes/12.png\"></td>";
                html += "       <td><img style=\"cursor: pointer;\" class=\"escalas\" alt=\"" + options.id_item + "\" title=\"Colores y Tallas / Escalas del item\" height=\"16\" width=\"16\" src=\"../../libs/imagenes/12.png\"></td>";
                html += "       <td><img style=\"cursor: pointer;\" class=\"seriales\" alt=\"" + options.id_item + "\" cantidad=\"" + options.cantidad + "\" title=\"Seriales\" height=\"16\" width=\"16\" src=\"../../../includes/imagenes/icons/binary-icon-16.png\"></td>";
                html += "       <td><img style=\"cursor: pointer;\" class=\"codbarras\" alt=\"" + options.id_item + "\" cantidad=\"" + options.cantidad + "\" title=\"C&oacute;digos de Barra\" height=\"16\" width=\"16\" src=\"../../../includes/imagenes/icons/bar-code-16.png\"></td>";
                html += "           </tr>";
                $(".grid table.lista tbody").append(html);
                //console.log(("#html_escala_item_"+options.id_item))
                $("#html_escala_item_"+options.id_item).html(options.ids_cantidad);
                //console.log($("#html_escala_item_"+options.id_item))
                eventos_form.CargarDisplayMontos();
                eventos_form.CargarDisplayTotales();
                win.hide();
            }
        });
    },
    IncluirColoresTallas: function(options) {
        //$.inputHidden("_cantidad", options.cantidad, "[]");
        //id=\"cantidad_" + options.id_item + "\">"

        var html = "";
        var campos = "";
        $.ajax({
            type: 'GET',
            data: 'opt=agregarColorTalla&formulario=' + options.formulario,
            url: '../../libs/php/ajax/ajax.php',
            success: function(data) {
                //vcampos = eval(data);
               // win_tmp.hide();
            }
        });
    },
    BuscarEntradaPorComprobante: function(nro_comprobante) {                          
            $.ajax({
                type: 'GET',
                data: 'opt=buscarKardexPorComprobante&comprobante=' + nro_comprobante,                
                url: '../../libs/php/ajax/ajax.php',
                success: function(data) {
                    $(".grid table.lista tbody tr").remove();
                    datos = eval(data);        
                    if(datos){
                        $(anio).attr("value", datos[0].anio);
                        $(autorizado_por).attr("value", datos[0].autorizado_por);
                        $(observaciones).attr("value", datos[0].observacion);
                        $(input_fechacompra).attr("value", datos[0].fecha);
                        $(codigo_proveedor).attr("value", datos[0].cod_proveedor);
                        STATUS_ENTRADA = parseInt(datos[0].estatus);
                        if(STATUS_ENTRADA == 0){
                            Ext.getCmp('btn-registra-entrada-ing').setDisabled(true);
                            Ext.getCmp('btn-guarda-entrada-ing').setDisabled(true);
                            Ext.getCmp('btn-agregar-producto-ing').setDisabled(true);
                        }
                        else{
                            Ext.getCmp('btn-registra-entrada-ing').setDisabled(false);
                            Ext.getCmp('btn-guarda-entrada-ing').setDisabled(false);
                            Ext.getCmp('btn-agregar-producto-ing').setDisabled(false);                            
                        }
                        if(datos[0].tipo_costo=='cif'){$(".tipo_costo_cif").attr("checked", true);}else{$(".tipo_costo_fob").attr("checked", true);}
                        eventos_form.BuscarDetKardexAlmacen(datos[0].id_transaccion);
                    }else{
                            Ext.getCmp('btn-registra-entrada-ing').setDisabled(false);
                            Ext.getCmp('btn-guarda-entrada-ing').setDisabled(false);
                            Ext.getCmp('btn-agregar-producto-ing').setDisabled(false); 
                        var fullDate = new Date();
                        var twoDigitMonth = fullDate.getMonth()+"";if(twoDigitMonth.length==1)  twoDigitMonth="0" +twoDigitMonth;
                        var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1) twoDigitDate="0" +twoDigitDate;
                        var currentDate =  fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;
                        var year = fullDate.getFullYear().toString(10).substring(2, 4);
                        $(anio).attr("value", year);
                        $(observaciones).attr("value", '');
                        $(input_fechacompra).attr("value", currentDate);
                        $(codigo_proveedor).attr("value", '');                        
                        eventos_form.CargarDisplayTotales();
                        eventos_form.CargarDisplayMontos();
                    }
                }                
            });            
    },
    
    BuscarDetKardexAlmacen: function(nro_item) {               
            $.ajax({
                type: 'GET',
                data: "opt=det_kardex_almacen&id= " + nro_item ,              
                url: '../../libs/php/ajax/ajax.php',
                success: function(data) {             
                        this.vcampos = eval(data);
                        for (i = 0; i <= this.vcampos.length; i++) {
                            var options1 = [];
                            options1['id_item'] =this.vcampos[i].id_item;
                            options1['cod_item'] = this.vcampos[i].cod_item;
                            options1['referencia'] = this.vcampos[i].referencia;
                            options1['descripcion1'] = this.vcampos[i].descripcion1;
                            options1['id_almacen'] = this.vcampos[i].id_almacen_entrada;
                            options1['cantidad'] = this.vcampos[i].cantidad;
                            options1['costo'] = this.vcampos[i].precio;                      
                            options1['bulto'] = this.vcampos[i].bulto;
                            options1['cantidad_bulto'] = this.vcampos[i].cantidad_bulto;
                            eventos_form.IncluirRegistros(options1);
                        }
                }
            });        
    },
       
    CargarDisplayTotales: function() {
        var tcostos = 0;
        var tcostos_entrada = 0;        
            $(".grid table.lista tbody tr td").each( function() {                
                str = $(this).attr('id');
                td_id  = str.substr(0,5);
                celda = "costo";
                if(td_id == celda){tcostos += parseFloat($(this).text());  }                
                celda = "real_";
                if(td_id == celda){tcostos_entrada += parseFloat($(this).text());  }                
            });
        $(".span_tcostos_items").html("<span style=\"font-size: 10px;\">Total Costos: " + parseFloat(tcostos).toFixed(2) + "</span>");
        $("input[name='input_tcostos_items']").attr("value", tcostos);
        $(".span_tcostos_entrada").html("<span style=\"font-size: 10px;\">Total Costos de Entradas: " + parseFloat(tcostos_entrada).toFixed(2) + "</span>");
        $("input[name='input_tcostos_entrada']").attr("value", tcostos_entrada);
        
        var stringDisplay = "<span style='color:green'><b>Total Costos(" + parseFloat(tcostos).toFixed(2)+ ")</b></span>";
        $("#displaytotalcostos, #displaytotalcostos2").html(stringDisplay);
        
        var stringDisplayEntrada = "<span style='color:green'><b>Total Costos de Entradas(" + parseFloat(tcostos_entrada).toFixed(2)+ ")</b></span>";
        $("#displaytotalcostosentrada, #displaytotalcostosentrada2").html(stringDisplayEntrada)
    },
    
    CargarDisplayMontos: function() {
        cantidad_ = $(".grid table.lista tbody").find("tr").length;
        $(".span_cantidad_items").html("<span style=\"font-size: 10px;\">Cantidad de Items: " + (cantidad_) + "</span>");
        $("input[name='input_cantidad_items']").attr("value", cantidad_);
        var stringDisplay = "<span style='color:#22428b'><b>Cantidad Items(" + cantidad_ + ")</b></span>";
        $("#displaytotal, #displaytotal2").html(stringDisplay);
    },
    
    GenerarCompraX: function() {
        if ($("#autorizado_por").val() === "") {
            $("#autorizado_por").css("border-color",$(this).val()===""?"red":"");
            $("#autorizado_por").css("border-width",$(this).val()===""?"1px":"");
            Ext.Msg.alert("Alerta!", "Debe Ingresar el Responsable");
        }
        else if ($("#nro_control").val() === "") {
            $("#nro_control").css("border-color",$(this).val()===""?"red":"");
            $("#nro_control").css("border-width",$(this).val()===""?"1px":"");
            Ext.Msg.alert("Alerta!", "Debe Ingresar el Nro. Control Factura");
        }
        else if ($("#nro_factura").val() === "") {
            $("#nro_factura").css("border-color",$(this).val()===""?"red":"");
            $("#nro_factura").css("border-width",$(this).val()===""?"1px":"");
            Ext.Msg.alert("Alerta!", "Debe Ingresar el Nro. de Factura");
        }
        else{
            $("input[name='entrada_temporal']").attr("value", 0);
            $("#formulario").submit();
        }
        return false;
    },    
    GuardarIngMercancia: function() {
        //AQUI GUARDA LA ENTRADA
        if ($("#autorizado_por").val() === "") {
            $("#autorizado_por").css("border-color",$(this).val()===""?"red":"");
            $("#autorizado_por").css("border-width",$(this).val()===""?"1px":"");
            Ext.Msg.alert("Alerta!", "Debe Ingresar el Responsable");
        }
        else if ($("#nro_control").val() === "") {
            $("#nro_control").css("border-color",$(this).val()===""?"red":"");
            $("#nro_control").css("border-width",$(this).val()===""?"1px":"");
            Ext.Msg.alert("Alerta!", "Debe Ingresar el Nro. Control Factura");
        }
        else if ($("#nro_factura").val() === "") {
            $("#nro_factura").css("border-color",$(this).val()===""?"red":"");
            $("#nro_factura").css("border-width",$(this).val()===""?"1px":"");
            Ext.Msg.alert("Alerta!", "Debe Ingresar el Nro. de Factura");
        }else if ($("#comprobante").val() === "") {
            $("#comprobante").css("border-color",$(this).val()===""?"red":"");
            $("#comprobante").css("border-width",$(this).val()===""?"1px":"");
            Ext.Msg.alert("Alerta!", "Debe Ingresar el Nro. de Comprobante");
        }else{    
            $("input[name='entrada_temporal']").attr("value", 1);               
            $("#formulario").submit();
        }
        return false;
    },        
    FiltrarArticulo : function(value){
//            var switchFiltro = tipoFiltro || "filtroItem"; // para el caso de pedido es: filtroItemByRCCB
//            pBuscaItem.main.storeProductos.baseParams.opt = switchFiltro;

            pBuscaItem.main.storeProductos.baseParams.limit = pBuscaItem.main.limitePaginacion;
            pBuscaItem.main.storeProductos.baseParams.start = pBuscaItem.main.iniciar;
            pBuscaItem.main.storeProductos.baseParams.cmb_tipo_item = 1;//productos
            pBuscaItem.main.storeProductos.baseParams.codigoProducto = value;
            pBuscaItem.main.storeProductos.baseParams.BuscarBy = true;
            pBuscaItem.main.storeProductos.load({                 
                callback: function(records, operation, success) { 
                    if (success) {
                        if(records.length > 1){
                            pBuscaItem.main.mostrarWin();
                        } else if(records.length == 1){
                            data.productoSeleccionado = records[0].json;
                            $("#informacionitem").val(JSON.stringify(records[0].json));

                        } else {
                            $.mensajeNotificacion({mensaje:'No se encontraron productos asociados'});
                            $.limpiarCamposFiltro();
                            data.productoSeleccionado = {};
                        } 
                    } 
                } 
            });
    },
    VerificarIngresoItem: function(fitro_item) {
            VerificarIngresoItem = true;
            $(".grid table.lista tbody").find('tr').each( function() {
                id = $(this).attr('id');
                if(parseInt(id) == parseInt(fitro_item)){
                    Ext.Msg.alert("Alerta!", "El item ya posee una entrada en el stock");     
                    VerificarIngresoItem = false;
                }                
            });
            return VerificarIngresoItem;
    },    
    Pad: function(n, length){
       n = n.toString();
       while(n.length < length) n = "0" + n;
       return n;
    },
    AnularIngMercanciaStock: function(nro_item) {               
                $.ajax({
                    type: 'GET',
                    data: "opt=anular_ing_mercancia_stock&id= " + nro_item ,              
                    url: '../../libs/php/ajax/ajax.php',
                    success: function(data) {             
                            this.vcampos = eval(data);
                            for (i = 0; i <= this.vcampos.length; i++) {
                                var options1 = [];
                                options1['id_item'] =this.vcampos[i].id_item;;
                                options1['cod_item'] = this.vcampos[i].cod_item;
                                options1['referencia'] = this.vcampos[i].referencia;
                                options1['descripcion1'] = this.vcampos[i].descripcion1;
                                options1['id_almacen'] = this.vcampos[i].id_almacen_entrada;
                                options1['cantidad'] = this.vcampos[i].cantidad;
                                options1['costo'] = this.vcampos[i].precio;       
                                options1['bulto'] = this.vcampos[i].bulto;  
                                options1['cant_bulto'] = this.vcampos[i].cantidad_bulto;                                    
                                eventos_form.IncluirRegistros(options1);
                            }
                    }
                });        
        }
        
};
