 var productoSeleccionado = {};
 var data = {
        productoSeleccionado : {},
        config_general : {}
    };

$(function(){
    ContarInputItem = 0;
    
    /*
    * Esta funcion permite crear tantos input se necesiten para la creacion de la tr
    * de la tabla de items (los que se cargan a la hora de la factura)
    **/
    $.inputHidden = function(Input,Value,ID){
        return '<input type="hidden" name="'+Input+''+ID+'" value="'+Value+'">';
    }
    
    $.filtrarArticulo = function(value){
            pBuscaItem.main.storeProductos.baseParams.opt = 'filtroItem';
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

                            $("input[name='filtro_descripcion']").val(data.productoSeleccionado.descripcion1);
                            $("input[name='filtro_referencia']").val(data.productoSeleccionado.referencia);
                            $("input[name='filtro_unidad']").val(data.productoSeleccionado.unidad_empaque);

                            $("input[name='filtro_bulto']").val(data.productoSeleccionado.cantidad_bulto);
                            $("input[name='filtro_kilos']").val(data.productoSeleccionado.kilos_bulto);
                            
                            $("input[name='filtro_precio']").val(data.productoSeleccionado.precio1);
                            $("input[name='filtro_importe']").val("0.00");

                            $("input[name='filtro_cantidad']").focus();
                            $("input[name='filtro_cantidad']").select();
                        }
                    } 
                } 
            });
    };
    
    $.agregarArticulo = function(){
        var dataitem = JSON.parse($("#informacionitem").val());


        if(dataitem==undefined){
            Ext.MessageBox.show({
                title: 'Notificaci&oacute;n',
                msg: "Debe seleccionar un art&iacute;culo",
                buttons: Ext.MessageBox.OK,
                animEl: 'addTabla',
                icon: 'ext-mb-warning'
            });
            return false;
        }
        
        codigo = dataitem.id_item;
        producto = dataitem.cod_item;//codigo_barras
        descripcion = dataitem.descripcion1;
        cantidad = 1;

        
        addTabla(codigo,descripcion,cantidad,producto);
    };



    
    /**
    * Esta funcion permite cargar los item en la tabla.
    **/

    function addTabla(codigo, descripcion, cantidad,producto){
        ContarInputItem += 1;
        
        var campos = "";
        var html = "";
        
        campos += $.inputHidden("_item_codigo",codigo,"[]");
        campos += $.inputHidden("_item_codigo_producto",producto,"[]");
        campos += $.inputHidden("_item_descripcion",descripcion,"[]");
        campos += $.inputHidden("_item_cantidad",cantidad,"[]");
        
        html  = "<tr>";
            html += "<td title=\"Haga click aqu&iacute; para ver detalles\" class=\"info_detalle\" style=\"cursor:pointer;background-color:#507e95;color:white;\"><a class=\"codigo\" rel=\"facebox\" style=\"color:white; text-align: center;\" href=\"#info\">"+producto+"</a><input type='hidden' name='idItem_' value='"+codigo+"'/></td>";
            html += "<td style='text-align: left;' class=\"filter-column\" style=\"width:auto;\">"+descripcion+"</td>";
            html += "<td style='text-align: right; padding-right: 20px;' rel='"+cantidad+"'>"+cantidad+"</td>";
            html += "<td style='text-align: right; padding-right: 20px;' rel='"+cantidad+"'>"+cantidad+"</td>";
            html += "</tr>";
            $("#addTabla tbody").append(html);
    }
    

    $("#filtro_codigo").focus(function(ev){
            alert("SDASD");
        var teclaTabMasP  = 13;
        var codeCurrent = ev.keyCode;
        var value = $(this).val();
        if(teclaTabMasP == codeCurrent){
            if(_.str.isBlank(value)) { 
                pBuscaItem.main.mostrarWin();
            }

            pBuscaItem.main.storeProductos.baseParams.opt = 'filtroItem';
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
                        }
                    }
                }
            });
        }
    });

    $("input[name='filtro_descuento']").keypress(function(ev){
        var teclaTabMasP  = 13;
        var codeCurrent = ev.keyCode;
        var value = $(this).val();
        if(teclaTabMasP == codeCurrent){
            if(_.str.isBlank(value)){
                $("input[name='filtro_descuento']").val(0);
            }

            if(_.isUndefined(data.productoSeleccionado.id_item)){
                $("input[name='filtro_codigo']").focus();
                return false;
            }
            $.agregarArticulo();
        }
    });
});