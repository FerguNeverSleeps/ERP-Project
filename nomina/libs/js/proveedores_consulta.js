var mostrarVentana = function(param){
	var winClave = new Ext.Window({
	    title: param.title,
	    height: param.height,
	    width: param.width,
	    autoScroll:true,
	    modal:true,
	    bodyStyle:'padding-right:10px;padding-left:10px;padding-top:5px;background:white;',
	    closeAction:'hide',
	    contentEl: param.contentEl,
	    buttonAlign:'center',
	    buttons:[
	    {
	        text: param.labelOk,
	        icon: '../../libs/imagenes/save.gif',
	        handler:function()
	        {
	        	param.accionOk();
	        }
	    },
	    {
	        text:'Cerrar',
	        icon: '../../libs/imagenes/cancel.gif',
	        handler:function()
	        {
	            winClave.hide();
	        }
	    }
	    ]
	}).show();

}

function cargarDetalleCompra(id_compra){
	$("#id_compra").val(id_compra);
	$(".grid table.lista tbody").empty();

    var opt = "";

    if($("#ingreso_comprobante").val() == "si"){
        opt = "get_datos_compra_comprobannte";
    }
    else{
        opt = "get_detalle_compra_ingreso";
    }

	$.ajax({
                type: 'GET',
                data: "opt="+opt+"&id="+ id_compra ,              
                url: '../../libs/php/ajax/ajax.php',
                success: function(data) {
                        this.vcampos = eval(data);

                        if($("#ingreso_comprobante").val() == "si"){
                            $("#fecha").val(this.vcampos.fechacompra);
                            $("#comprobante").val("Fact");
                            $("#numero").val(this.vcampos[0].numero);
                            $("#neto_gravado").val(parseFloat(this.vcampos[0].total/this.vcampos[0].tasa).toFixed(2));
                            $("#itbm").val(parseFloat(this.vcampos[0].impuesto/this.vcampos[0].tasa).toFixed(2));
                            $("#total").val((parseFloat(this.vcampos[0].impuesto/this.vcampos[0].tasa)+parseFloat(this.vcampos[0].total/this.vcampos[0].tasa)).toFixed(2));
                        }
                        else{
                            var options1 = null;
                            for (i = 0; i <= this.vcampos.length; i++) {
                                options1 = [];
                                options1['id_item'] =this.vcampos[i].id_item;
                                options1['id_detalle_compra'] =this.vcampos[i].id_detalle_compra;
                                options1['cod_item'] = this.vcampos[i].cod_item;
                                options1['referencia'] = this.vcampos[i].referencia;
                                options1['descripcion1'] = this.vcampos[i].descripcion1;
                                options1['id_almacen'] = this.vcampos[i].id_almacen_entrada;
                                options1['cantidad'] = this.vcampos[i].cantidad;
                                var costo_item = parseFloat(this.vcampos[i].precio);
                                //console.log(costo_item)
                                options1['costo'] = costo_item.toFixed(2);                      
                                options1['bulto'] = this.vcampos[i].bulto;
                                options1['cantidad_bulto'] = this.vcampos[i].cantidad_bulto;
                                
                                //eventos_form.IncluirRegistros(options1);

                                if(parseInt(this.vcampos[i].tiene_tallas) != 0){
                                        cod = this.vcampos[i].id_item;
                                        var det_compra = this.vcampos[i].id_detalle_compra;
                                        $.ajax({
                                            type: 'GET',
                                            async:false,
                                            data: 'cod='+cod+"&escala=1&det_compra="+det_compra,
                                            url:  'colortalla_compra.php',
                                            success: function(data2){
                                                var win_tmp = new Ext.Window({
                                                    id:'window-escalas-item-'+cod,
                                                    title:'Escalas',
                                                    height: 400,
                                                    x:70,y:70,
                                                    width: 750,
                                                    frame:true,
                                                    autoScroll:true,
                                                    modal:false,
                                                    html: data2,
                                                    estado:"nuevo",
                                                    opciones:options1,
                                                    listeners:{
                                                        beforeclose:function(p){
                                                            //console.log(options1,this.opciones)
                                                            var options2 = this.opciones;
                                                            if(win_tmp.estado == "nuevo"){
                                                                //@MODIFICAR CANTIDAD GENERAL
                                                                var cantTotalEscala = 0;
                                                                var ids_cantidad = "";
                                                                var ids_cantidad_color = "";
                                                                var escala_id_selec = $("#escalas_select").val();
                                                                var cantColor = "";

                                                                $( ".cantidad-color-talla").each(function( index ) {
                                                                    cantEscala = $(this).val();
                                                                    ids_cantidad += this.id + "=" + $(this).val() + ",";
                                                                    if(cantEscala != ""){
                                                                        cantTotalEscala += parseInt(cantEscala);
                                                                    }
                                                                });
                                                                $( ".cantidad-color").each(function( index ) {
                                                                    cantColor = $(this).val();
                                                                    ids_cantidad_color += this.id + "=" + $(this).val() + ",";
                                                                });
                                                                ids_cantidad_color = ids_cantidad_color.substring(0, ids_cantidad_color.length-1);
                                                                ids_cantidad = ids_cantidad.substring(0, ids_cantidad.length-1);
                                                                //$("#_cantidad_"+cod).val(cantTotalEscala);
                                                                $("#cantidad_"+cod).html(cantTotalEscala);
                                                                options2["cantidad"] = cantTotalEscala;

                                                                //console.log($("#form-talla-color")[0]);

                                                                eventos_form.IncluirColoresTallas({
                                                                        formulario: JSON.stringify($("#form-talla-color").serializeArray())
                                                                });

                                                                //console.log($("#form-talla-color")[0].innerHTML);
                                                                
                                                                options2["ids_cantidad"] = ids_cantidad+"*"+escala_id_selec+"CA"+ids_cantidad_color;
                                                                options2["escala_item_estado_win"] = "1";//$("#form-talla-color")[0]; 
                                                                

                                                                //eventos_form.IncluirRegistros(recordAdd.data);
                                                                eventos_form.IncluirRegistros(options1);
                                                            }
                                                        }
                                                    },
                                                    buttons:[{
                                                        text:'Guardar',
                                                        handler: function(){

                                                            //console.log(options1,this.opciones)
                                                        }
                                                    }]
                                                });
                                                win_tmp.opciones = options1;
                                                win_tmp.show();
                                                win_tmp.close();
                                            }
                                        });
                                }
                                else{
                                    eventos_form.IncluirRegistros(options1);
                                }
                            }
                        }                       
                }
            });
}

$(function(){

    $("#pBuscarProveedores").click(function(e){
            e.preventDefault();
            var fncallBack = function(registro){
                $("input[name='codigo_proveedor']").val(registro.json.cod_proveedor);
                $("#id_proveedor'").val(registro.json.id_proveedor);
                $("#descripcion_proveedor").text(registro.json.descripcion);
                $("input[name='codigo_proveedor']").click();
            };

        pBuscarProveedores.main.mostrarWin(fncallBack);
    });
    
    /*$("#id_proveedor").change(function(e){//codigo_proveedor
            e.preventDefault();
            $.ajax({
                    type: 'GET',
                    data: 'opt=getComprasPendientes&v1='+$(this).val(),
                    url:  '../../libs/php/ajax/ajax.php',
                    beforeSend: function(){
                    },
                    success: function(data){
                        $('#facturasPendientes').empty();
                        $('#facturasPendientes').append(data);
                    }
            });            
    });*/
});