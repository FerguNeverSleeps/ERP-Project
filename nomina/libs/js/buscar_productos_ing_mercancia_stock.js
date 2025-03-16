/**
 * Creación de ventana para la busqueda de items.
 * Fecha de Creación: dom 4 de marzo de 2012, 10:49:51 PM
 *
 * Autor: Luis E. Viera Fernandez.
 * Correo:
 *  lviera@armadillotec.com
 *  lviera86@gmail.com
 *
 * Adaptaciones y Mejoras: Charli J. Vívenes Rengel.
 * Correo:
 *  cvivenes@asys.com.ve
 *  cjvrinf@gmail.com
 *
 */

Ext.ns("pBuscaItem");
/**
 * @class pBuscaItem
 */

pBuscaItem.main = {
    limitePaginacion:10,
    iniciar:0,
    init:function(){
        this.storeProductos = this.getLista();
        this.storeAlmacen = this.getStoreAlmacen();        
   
        this.cancelar = new Ext.Button({
            text:'Cerrar ventana',
            iconCls:'cancelar',
            handler:function(){
                $("input[name='filtro_codigo']").focus();
                pBuscaItem.main.ocultarWin();
            }
        });
        this.seleccionar = new Ext.Button({
            text:'Selecionar Producto',
            iconCls:'seleccionar',
            handler:function(){
                //En construcción...
            }
        });
        var storeBuscarCodigoEnItem = new Ext.data.JsonStore({
            proxy:new Ext.data.HttpProxy({url:'../../../includes/clases/controladores/ControladorApp.php'}),
            remoteSort : true,
            restful : true,
            autoLoad:false,
            baseParams: {
                accion:'Accion/Item/Get_Lista_Valores',
                valor:'cod_item',
                db_connect:DB_CONNECT
            },
            fields : [
                {name:'campo', type:'string'}
            ]
        });
        var storeBuscarReferenciaEnItem = new Ext.data.JsonStore({
            proxy:new Ext.data.HttpProxy({url:'../../../includes/clases/controladores/ControladorApp.php'}),
            remoteSort : true,
            restful : true,
            autoLoad:false,
            baseParams: {
                accion:'Accion/Item/Get_Lista_Valores',
                valor:'referencia',
                db_connect:DB_CONNECT
            },
            fields : [
                {name:'campo', type:'string'}
            ]
        });
        var storeBuscarDescripcionEnItem = new Ext.data.JsonStore({
            proxy:new Ext.data.HttpProxy({url:'../../../includes/clases/controladores/ControladorApp.php'}),
            remoteSort : true,
            restful : true,
            autoLoad:false,
            baseParams: {
                accion:'Accion/Item/Get_Lista_Valores',
                valor:'descripcion1',
                db_connect:DB_CONNECT
            },
            fields : [
                {name:'campo', type:'string'}
            ]
        });
        this.codigoPro = new Ext.form.ComboBox({
                xtype:          'combo',
                mode:           'query',
                width:90,
                minChars :2,
                forceSelection : false,
                typeAhead : false,
                autoSelect : false,
                hideTrigger : true,
                triggerAction:  'all',
                fieldLabel:     'Código',
                name:           'codigoProducto',
                id:           'codigoProducto-cmb',
                hiddenName:     'codigoProducto',
                displayField:   'campo',
                valueField : 'campo',
                allowBlank:true,
                editable:true,
                store: storeBuscarCodigoEnItem,
                            listeners:{
                                beforeselect:function(combo,record,index){
                                },
                                select:function(cmb,rec,idx){
                                    //pBuscaItem.main.aplicarFiltroByFormularioProducto();
                                },
                                beforequery : function(  queryEvent )   {
                                    this.setValue(queryEvent.query );
                                }                                 
                            }
        });
        this.codigoPro.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
                //Ext.getCmp("codigoProducto-cmb").setValue("");
            }
        }, this);
        /*this.codigoPro = new Ext.form.TextField({
            fieldLabel:'Código',
            name:'codigoProducto',
            width:125
        });
        this.codigoPro.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
            }
        }, this);*/
        ///////////////////////////////////////////////////////////////////////////
//        this.codigoBarrasPro = new Ext.form.TextField({
//            fieldLabel:'Código de Barra',
//            name:'codigoBarrasProducto',
//            width:140
//        });
//        this.codigoBarrasPro.on('specialkey', function(f, event){
//            if(event.getKey() == event.ENTER) {
//                pBuscaItem.main.aplicarFiltroByFormularioProducto();
//            }
//        }, this);
        //this.codigoBarrasProd.focus();
        ///////////////////////////////////////////////////////////////////////////
        /*
        
        */
        this.referencia = new Ext.form.ComboBox({
                xtype:          'combo',
                width:90,
                mode:           'query',
                hideTrigger : true,
                minChars :2,
                forceSelection : false,
                typeAhead : false,
                autoSelect : false,
                triggerAction:  'all',
                fieldLabel:     'Referencia',
                name:           'referencia',
                id:           'referencia-cmb',
                hiddenName:     'referencia',
                displayField:   'campo',
                valueField : 'campo',
                allowBlank:true,
                editable:true,
                store: storeBuscarReferenciaEnItem,
                            listeners:{
                                beforeselect:function(combo,record,index){
                                },
                                select:function(cmb,rec,idx){
                                    //pBuscaItem.main.aplicarFiltroByFormularioProducto();
                                },
                                beforequery : function(  queryEvent )   {
                                    this.setValue(queryEvent.query );
                                }                        
                            }
        });
        this.referencia.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
                //Ext.getCmp("referencia-cmb").setValue("");
            }
        }, this);
        /*this.descripcionPro = new Ext.form.TextField({
            fieldLabel:'C&oacute;digo / Referencia / Nombre',
            name:'buscarEn',//'descripcionProducto',
            width:200
        });
        this.descripcionPro.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
            }
        }, this);*/
        this.descripcionPro = new Ext.form.ComboBox({
                xtype:          'combo',
                width:200,
                mode:           'query',
                hideTrigger : true,
                minChars :2,
                forceSelection : false,
                typeAhead : false,
                autoSelect : false,
                triggerAction:  'all',
                fieldLabel:     'Nombre',
                name:           'descripcionProducto',
                id:           'descripcionProducto-cmb',
                hiddenName:     'descripcionProducto',
                displayField:   'campo',
                valueField : 'campo',
                allowBlank:true,
                editable:true,
                store: storeBuscarDescripcionEnItem,
                            listeners:{
                                beforeselect:function(combo,record,index){
                                },
                                select:function(cmb,rec,idx){
                                    //pBuscaItem.main.aplicarFiltroByFormularioProducto();
                                }  ,
                                beforequery : function(  queryEvent )   {
                                    this.setValue(queryEvent.query );
                                }                               
                            }
        });
        this.descripcionPro.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
                //Ext.getCmp("descripcionProducto-cmb").setValue("");
            }
        }, this);
        this.cmbTipoItem = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                id:0,
                fields:[
                'value',
                'display'
                ],
                data:[
                [1,'Producto'],
                [2,'Servicio']
                ]
            }),
            displayField:'display',
            fieldLabel:'Típo',
            valueField:'value',
            hiddenName:'cmb_tipo_item',
            typeAhead: true,
            mode: 'local',
            value:'1',
            width:70,
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus:true
        });
      this.cmbAlmacen = new Ext.form.ComboBox({
            store: this.storeAlmacen,            
            displayField:'descripcion',
            fieldLabel:'Bodega',
            valueField:'cod_almacen',
            hiddenName:'cmb_almacen',
            typeAhead: true,
            mode: 'remote',
            width:130,
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus:true,
            value: 1,
           listeners: {
                'render':function(obj){ 
                    var store = obj.getStore();
                    store.load({ 
                    callback:function(){ 
                        var fila= obj.getStore(0).getAt(0);
                        obj.setValue(fila.get('cod_almacen'));
                    }   
                });        
            }
            }
        });

        this.cantidadxBulto = new Ext.form.NumberField({
            fieldLabel:'Cant x Bulto',
            name:'cantidadxBulto',
            id:'cantidadxBulto',
            width:80,
            readOnly: true,
            allowDecimals:true,
            decimalPrecision:2
        });

        this.cantidadUnitaria = new Ext.form.NumberField({
            fieldLabel:'Cantidad Unitaria',
            name:'cantidadUnitaria',
            id:'cantidadUnitaria',
            width:80,
            listeners:{
                change:function(nf,newval,oldval){
                    if(Ext.getCmp("cantidadxBulto").getValue() == 0){
                        Ext.getCmp("cantidadBultos").setValue(0);
                    }
                    else{
                        Ext.getCmp("cantidadBultos").setValue(this.getValue()/Ext.getCmp("cantidadxBulto").getValue());    
                    }
                    
                }
            }
        }); 
        this.cantidadBultos = new Ext.form.NumberField({
            fieldLabel:'Bultos',
            name:'cantidadBultos',
            id:'cantidadBultos',
            width:80,
            allowDecimals:true,
            decimalPrecision:2,
            listeners:{
                change:function(nf,newval,oldval){
                    Ext.getCmp("cantidadUnitaria").setValue(this.getValue()*Ext.getCmp("cantidadxBulto").getValue());
                }
            }
        });       
        this.costoUnitario = new Ext.form.NumberField({
            fieldLabel:'Costo',
            name:'costoUnitario',
            id:'costoUnitario',
            width:80
        });                
        this.buscarPro = new Ext.Button({
            text:'Buscar',
            iconCls:'iconBuscar',
            handler:function(){
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
            }
        });
        this.limpiarPro = new Ext.Button({
            text:'Limpiar Filtro',
            iconCls:'iconLimpiar',
            handler:function(){
                pBuscaItem.main.limpiarFiltroProductos();
            }
        });

        this.fieldsetDatos = new Ext.form.FieldSet({
            title:'Introduzca los siguientes datos..',
            items:[
            {
               layout:'column',
                border:false,
                defaults:{
                    layout:'form',
                    labelAlign:'top',
                    border:false
                },
                 items:[
                {
                    columnWidth:.20,
                    items:[
                    this.cmbAlmacen
                    ]
                },
                {
                    columnWidth:.20,
                    items:[
                    this.cantidadxBulto
                    ]
                },
                {
                    columnWidth:.20,
                    items:[
                    this.cantidadUnitaria
                    ]
                },
                {
                    columnWidth:.20,
                    items:[
                    this.cantidadBultos
                    ]
                },
                {
                    columnWidth:.20,
                    items:[
                    this.costoUnitario
                    ]
                }
                ]
            }
            ]
        });
        
        this.fieldsetProductos = new Ext.form.FieldSet({
            title:'Parámetros de Busqueda',
            //defaultButtom:codigoBarrasPro,
            //tabIndex:1,
            items:[
            {
                layout:'column',
                border:false,
                defaults:{
                    layout:'form',
                    labelAlign:'top',
                    border:false
                },
                items:[
                {
                    columnWidth:.10,
                    items:[
                    this.cmbTipoItem
                    ]
                },
                {
                    columnWidth:.19,
                    items:[
                    this.codigoPro
                    ]
                },
//                {
//                    columnWidth:.21,
//                    items:[
//                    this.codigoBarrasPro
//                    ]
//                },
                {
                    columnWidth:.15,
                    items:[
                   this.referencia
                    ]
                },
                {
                    columnWidth:.3,
                    items:[
                    this.descripcionPro
                    ]
                }
                ]
            }
            ]
        });
        
        this.renderImagenProducto = function(val, meta, record, rowIndex, colIndex, store) {
            var html = "";
            var nombreImagenFoto = store.data.itemAt(rowIndex).json.foto;
            if ( !_.str.isBlank(nombreImagenFoto) ) {
                html = "<img width='70' src='../../imagenes/"+nombreImagenFoto+"'>";
            }else{
                html = "<img width='70' src='../../imagenes/sin_imagen.jpg'>";
            }
            return html;
        }
    
        this.gridProductos = new Ext.grid.GridPanel({
            store:this.storeProductos,
            style:'padding:8px',
            height:300,
            width:773,
            loadMask:true,
            frame:true,
            stripeRows: true,
            autoScroll:true,
            stateful: true,
            columns:[
                new Ext.grid.RowNumberer(),
                {header:'Img', width:120, height:300,  renderer: this.renderImagenProducto, menuDisabled:true, dataIndex:'foto'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'posee_talla_color'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'id_item'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'cod_item_forma'},
                {header:'Código', width:120, sortable: true, menuDisabled:true, dataIndex:'cod_item'},
                {header:'Referencia', width:120, sortable: true, menuDisabled:true, dataIndex:'referencia'},
                {header:'Código de Barras', width:120, sortable: true, menuDisabled:true, dataIndex:'codigo_barras'},
                {header:'Nombre', width:410, sortable: true, menuDisabled:true, dataIndex:'descripcion1'}
            ],
            bbar: new Ext.PagingToolbar({
                pageSize: pBuscaItem.main.limitePaginacion,
                store: this.storeProductos,
                displayInfo: true,
                html:'<div style="padding-left:5px;color:black;font-size:10px;"><b>Nota: Doble Click sobre el producto a cargar</b></div>',
                displayMsg: '<span style="color:black">Registros: {0} - {1} de {2}</span>',
                emptyMsg: "<span style=\"color:black\">No se encontraron registros</span>"
            })
        });

        this.gridProductos.on('rowcontextmenu', function(grid, rowIndex, event){
            event.stopEvent();
            var record = pBuscaItem.main.storeProductos.getAt(rowIndex);
            var menu_importar = new Ext.menu.Menu({
                tbar:[
                {
                    xtype:'displayfield',
                    value:'x'
                }
                ],
                items:[
                {
                    text:'Ver '+record.data["descripcion1"]+" (<b>"+record.data["cod_item"]+"</b>)",
                    cls:'iconInformacion',
                    handler:function(){

                        Ext.Ajax.request({
//                            method:'GET',
//                            url:  'info_servicio_item.php',
                            type: 'GET',
                            data: 'opt=DetalleSelectitem&v1=' + options.id_item,
                            url: '../../libs/php/ajax/ajax.php',                            
                            params:{
                                cod:record.data["id_item"]
                            },
                            failure: function(form, action) {
                                Ext.MessageBox.alert('Error en transacción', action.result.msg);
                            },
                            success:function(result, request){
                                var win;
                                cerrarWin = new Ext.Button({
                                    text:'Cerrar Ventana',
                                    iconCls:'cancelar',
                                    handler:function(){
                                        win.close();
                                    }
                                });
                                seleccionar = new Ext.Button({
                                    text:'Seleccionar producto',
                                    iconCls:'seleccionar',
                                    handler:function(){
                                        win.close();
                                  //      pBuscaItem.main.ocultarWin();
                                        var filtro_referencia = record.data["cod_item"];
                                        eventos_form.FiltrarArticulo(filtro_referencia); //filtrarArticulo(filtro_referencia);
                                        eventos_form.IncluirRegistros(data.productoSeleccionado);
                                    }
                                });
                                win =  new Ext.Window({
                                    title:'Información: '+record.data["descripcion1"],
                                    modal:true,
                                    iconCls:'iconTitulo',
                                    constrain:true,
                                    autoScroll:true,
                                    html:result.responseText,
                                    action:'hide',
                                    height:400,
                                    width:450,
                                    buttonAlign:'center',
                                    buttons:[
                                    seleccionar,
                                    cerrarWin
                                    ]
                                });
                                win.show();
                            }
                        });
                    }
                }
                ]
            });
            menu_importar.showAt(event.getXY());
        });
        //Evento Doble Click
        this.gridProductos.on('rowdblclick', function( grid, row, evt){
            this.record = pBuscaItem.main.storeProductos.getAt(row);
            
            if($("#costoUnitario").val() == ""){

                $("#costoUnitario").val(this.record.data.costo_actual);
            }
                

            if($("#items").val()==""){
                Ext.Msg.alert("Alerta","Debe especificar algún ítem."); 
                return false;
           }
            if($("#cmb_almacen").val()==""){
                Ext.Msg.alert("Alerta","Debe especificar la bodega.");  
                return false;      
            }
            if($("#costoUnitario").val()=="" || $("#costoUnitario").val()==0){
                Ext.Msg.alert("Alerta","Debe especificar el costo.");  
               return false;
            }
            //console.log(this.record)

            if(($("#cantidadUnitaria").val()=="" || $("#cantidadUnitaria").val()==0) && this.record.data.posee_talla_color == "no"){
                Ext.Msg.alert("Alerta","Debe especificar la cantidad.");  
                 return false; 
            }  
            
            
//           pBuscaItem.main.ocultarWin();
            //console.log(MONTO_TASA,$("#costoUnitario").val())
            if(MONTO_TASA != 1){
                this.record.data["costo"] = $("#costoUnitario").val()/MONTO_TASA;
            }
            else{
                this.record.data["costo"] = $("#costoUnitario").val();     
            }
           
           this.record.data["cantidad"] = $("#cantidadUnitaria").val();                     
           this.record.data["id_almacen"] = $("#cmb_almacen").val();

           var filtro_referencia = this.record.data["id_item"];

           var existe_item = eventos_form.VerificarIngresoItem(filtro_referencia);
           if(existe_item){
                eventos_form.FiltrarArticulo(filtro_referencia); 
                //eventos_form.IncluirRegistros(this.record.data);                
                var recordAdd = this.record;

                if(this.record.data.posee_talla_color == "si"){

                    //pBuscaItem.main.ocultarWin();
                    
                    cod = this.record.data.id_item;
                    var mask = new Ext.LoadMask(Ext.get("Contenido"), {
                        msg:'Cargando..',
                        removeMask:false
                    });
                    $.ajax({
                        type: 'GET',
                        data: 'cod='+cod+"&escala=1",
                        url:  'colortalla.php',
                        beforeSend: function(){
                            mask.show();
                        },
                        success: function(data){
                            var win_tmp = new Ext.Window({
                                id:'window-escalas-item-'+cod,
                                title:'Escalas',
                                height: 400,
                                x:70,y:70,
                                width: 750,
                                frame:true,
                                autoScroll:true,
                                modal:false,
                                html: data,
                                estado:"nuevo",
                                buttons:[{
                                    text:'Guardar',
                                    handler: function(){

                                        //console.log(win_tmp.estado);
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
                                               
                         //   data.productoSeleccionado.cantidad_por_talla_y_color = JSON.stringify(cantidad_por_items);
           
                                               
                                               
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
                                            recordAdd.data["cantidad"] = cantTotalEscala;

                                            //console.log($("#form-talla-color")[0]);

                                            eventos_form.IncluirColoresTallas({
                                                    formulario: JSON.stringify($("#form-talla-color").serializeArray())
                                            });

                                            //console.log($("#form-talla-color")[0].innerHTML);
                                            
                                            recordAdd.data["ids_cantidad"] = ids_cantidad+"*"+escala_id_selec+"CA"+ids_cantidad_color;
                                            recordAdd.data["escala_item_estado_win"] = "1";//$("#form-talla-color")[0]; 
                                            win_tmp.close();

                                            eventos_form.IncluirRegistros(recordAdd.data);
                                        }
                                        else{

                                        }
                                        
                                        //pBuscaItem.main.mostrarWin();
                                    }
                                }]
                            });
                            win_tmp.show();
                            mask.hide();
                        }
                    });
                }
                else{
                    eventos_form.IncluirRegistros(this.record.data);
                }
            }
        });

        //click
        this.gridProductos.on('rowclick', function( grid, row, evt){
            this.record = pBuscaItem.main.storeProductos.getAt(row);

            //console.log(this.record)
            $("#cantidadxBulto").val(this.record.data.cantidad_bulto);

            if($("#costoUnitario").val() == "")
                $("#costoUnitario").val(this.record.data.costo_actual);
           /*
            if($("#costoUnitario").val()==""){
                Ext.Msg.alert("Alerta","Debe especificar el costo.");  
               return false;
            }
            this.record.data.id_item;
            
            this.record.data["costo"] = $("#costoUnitario").val();*/
        });


        this.formProductos = new Ext.form.FormPanel({
            border:false,
            items:[
            this.fieldsetProductos,
            this.fieldsetDatos
            ]
        });
        this.tabProductos = new Ext.Panel({
            title:'Productos/Servicios',
            style:'padding:5px;',
            items:[
            this.formProductos,
            {
                layout:'column',
                border:false,
                defaults:{
                    border:false
                },
                items:[
                {
                    columnWidth:.1,
                    items:[
                    this.buscarPro
                    ]
                },
                {
                    items:[
                    this.limpiarPro
                    ]
                }
                ]
            },
            this.gridProductos
            ],
            autoHeight:true
        });
        this.tabPanel = new Ext.TabPanel({
            title: '',
            activeTab:0,
            height:600,
            items:[
            this.tabProductos
            ]
        });
        this.win = new Ext.Window({
            title:'Busqueda de Item',
            modal:true,
            x:30,y:30,
            iconCls:'iconTitulo',
            constrain:true,
            action:'hide',
            frame:true,
            closable:false,
            width:800,
            items:[
            this.tabPanel
            ],
            //autoHeight:true,
            height:600,
            buttonAlign:'center',
            buttons:[
            //this.seleccionar,
            this.cancelar
            ]
        });
    },
    mostrarWin:function(){
        this.win.show();
        this.limpiarFiltroProductos();
        setTimeout(function(){
            pBuscaItem.main.codigoPro.focus();
//            pBuscaItem.main.codigoBarrasPro.focus();
        },200);
    },
    ocultarWin:function(){
        this.win.setVisible(false);
    },
    limpiarFiltroProductos:function(){
        pBuscaItem.main.formProductos.getForm().reset();
        pBuscaItem.main.storeProductos.baseParams={};
        pBuscaItem.main.storeProductos.baseParams.opt = 'filtroItemGeneral';
        pBuscaItem.main.storeProductos.baseParams.limit = pBuscaItem.main.limitePaginacion;
        pBuscaItem.main.storeProductos.baseParams.start = pBuscaItem.main.iniciar;
        pBuscaItem.main.storeProductos.baseParams.tipo_item = 1;
        pBuscaItem.main.storeProductos.load();
    },

    aplicarFiltroByFormularioProducto: function(){
        //Capturamos los campos con su value para posteriormente verificar cual
        //esta lleno y trabajar en base a ese.
        var campo = pBuscaItem.main.formProductos.getForm().getValues();
        pBuscaItem.main.storeProductos.baseParams={};
        var swfiltrar = false;
        for(campName in campo){
            if(campo[campName]!=''){
                swfiltrar = true;
                eval("pBuscaItem.main.storeProductos.baseParams."+campName+" = '"+campo[campName]+"';");
            }
        }        
        pBuscaItem.main.storeProductos.baseParams.opt = 'filtroItemGeneral';
        pBuscaItem.main.storeProductos.baseParams.limit = pBuscaItem.main.limitePaginacion;
        pBuscaItem.main.storeProductos.baseParams.start = pBuscaItem.main.iniciar;
        pBuscaItem.main.storeProductos.baseParams.tipo_item = 1;//productos
        pBuscaItem.main.storeProductos.baseParams.BuscarBy = true;
        pBuscaItem.main.storeProductos.load();
    },
    
      getStoreAlmacen:function(){
        return new Ext.data.JsonStore({
             url:'../../libs/php/ajax/ajax.php',
             baseParams:{opt:'getAlmacenStock'},
             root: 'data',
             fields: [
                 {name: 'cod_almacen'},
                 {name: 'descripcion'}
             ],
             autoLoad: true
         });
       }, 
        getLista: function(){
        this.store = new Ext.data.JsonStore({
            url:'../../libs/php/ajax/ajax.php',
            params:{
                opt:'filtroItemGeneral'
            },
            root:'data',
            fields:[
            {
                name: 'id_item'
            },
            {
                name: 'posee_talla_color'
            },
            {
                name: 'cod_item_forma'
            },
            {
                name: 'cod_item'
            },
            {
                name: 'codigo_barras'
            },
            {
                name: 'descripcion1'
            },
            {
                name: 'cantidad_bulto'
            },            
            {
                name: 'referencia'
            },            
            {
                name: 'costo_actual'
            } 
            ]
        });
        return this.store;
    }
};
Ext.onReady(pBuscaItem.main.init,pBuscaItem.main);
/************* FIN DE PAQUETE *********************/
