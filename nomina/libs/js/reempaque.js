var win;

Ext.onReady(function(){
    
    var cod;
	var cod2;
    var item;
    var doc;
    var tipo;
    var num;
    
  $(".producto").live("click", function(){
        cod = $(this).attr("alt");
        cod2 = cod.toString();
        var arre = cod2.split("ila");
        item=arre[0];
        doc=arre[1];
        tipo=arre[2];
        num=arre[3];
        var mask = new Ext.LoadMask(Ext.get("Contenido"), {
            msg:'Cargando..',
            removeMask:false
        });
        $.ajax({
            type: 'GET',
            data: 'items='+item+'&doc='+doc+'&tipo='+tipo+'&num='+num,
            url: 'reempaquedat.php',
            beforeSend: function(){
                mask.show();
            },
            success: function(data){
                var win_tmp = new Ext.Window({
                    title:'Reempaque de producto',
                    height: 300,
                    width: 400,
                    frame:true,
                    autoScroll:true,
                    modal:true,
                    html: data,
                    buttons:[{
                        text:'Guardar',
                        handler: function(){
                        	var numero=$("#numero").val();
                        	var valor=$("#bultoss").val();
                        	$("#bultos"+numero).val(valor)
                        	alert(numero)
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
                icon: '../../libs/imagenes/add.gif',
                handler: function(){
                     pBuscaItem.main.mostrarWin();
//                     
//                     eventos_form.IncluirRegistros();
//                     eventos_form.init()
            //        win.show();
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
                text:'<b>Registrar Entrada</b>',
                icon: '../../libs/imagenes/back.gif',
                iconAlign: 'left',
                height: 30,
                width:50,
                handler: function(){
                    eventos_form.GenerarCompraX();
                }
            },
            {
                text:'<b>Guardar Entrada</b>',
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
