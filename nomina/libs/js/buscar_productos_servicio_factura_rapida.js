/**
 * Creación de ventana para la busqueda de items.
 * Fecha de Creación: dom 4 de marzo de 2012, 10:49:51 PM
 *
 * Autor: Luis E. Viera Fernandez.
 * Correo:
 *  levieraf@gmail.com
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
     
        this.lineas = this.getLineas();

        this.departamentos = this.getDepartamentos();
        this.grupos = this.getGrupos();

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
        /*
        ///////////////////////////////////////////////////////////////////////////
        this.codigoBarrasPro = new Ext.form.TextField({
            fieldLabel:'Código de Barra',
            name:'codigoBarrasProducto',
            width:140
        });
        this.codigoBarrasPro.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
            }
        }, this);
        //this.codigoBarrasProd.focus();
        ///////////////////////////////////////////////////////////////////////////
        this.codigoPro = new Ext.form.TextField({
            fieldLabel:'Código',
            name:'codigoProducto'
        });
        this.codigoPro.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
            }
        }, this);*/
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
                forceSelection : false,
                typeAhead : false,
                autoSelect : false,
                hideTrigger : true,
                minChars :2,
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
        
        
        
        /*this.descripcionPro = new Ext.form.TextField({
            fieldLabel:'C&oacute;digo / Referencia / Nombre',//fieldLabel:'Nombre',
            name:'buscarEn',//'descripcionProducto',
            width:200
        });*/
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
        /*this.referencia = new Ext.form.TextField({
            fieldLabel:'Referencia',
            name:'referencia',
            width:90
        });
        this.referencia.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
            }
        }, this);
        */
        this.referencia = new Ext.form.ComboBox({
                xtype:          'combo',
                width:150,
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

        this.cmbDepartamentos = new Ext.form.ComboBox({
            resizable:true,
            fieldLabel: $("input[name='string_clasificador_inventario1']").val(),
            store: this.departamentos,
            typeAhead: true,
            valueField: 'cod_departamento',
            displayField:'descripcion',
            hiddenName:'cod_departamento',
            forceSelection:true,
            triggerAction: 'all',
            emptyText:'',
            selectOnFocus:true,
            mode: 'local',
            width:150
        });

        this.cmbGrupos = new Ext.form.ComboBox({
            resizable:true,
            fieldLabel: $("input[name='string_clasificador_inventario2']").val(),
            store: this.grupos,
            typeAhead: true,
            valueField: 'cod_grupo',
            displayField:'descripcion',
            hiddenName:'cod_grupo',
            forceSelection:true,
            triggerAction: 'all',
            emptyText:'',
            selectOnFocus:true,
            mode: 'local',
            width:150,
            resizable:true
        });

        this.cmbLineas = new Ext.form.ComboBox({
            resizable:true,
            fieldLabel: $("input[name='string_clasificador_inventario3']").val(),
            store: this.lineas,
            typeAhead: true,
            valueField: 'cod_linea',
            displayField:'descripcion',
            hiddenName:'cod_linea',
            forceSelection:true,
            triggerAction: 'all',
            emptyText:'',
            selectOnFocus:true,
            mode: 'local',
            width:150,
            resizable:true
        });

        
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
        this.buscarPro = new Ext.Button({
            text:'Buscar',
            iconCls:'iconBuscar',
            handler:function(){
                pBuscaItem.main.aplicarFiltroByFormularioProducto();
            }
        });
        
        
     this.prueba = new Ext.Button({
            text:'Buspruecar',
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
                /*{
                    columnWidth:.21,
                    items:[
                    this.codigoBarrasPro
                    ]
                },*/
                {
                    columnWidth:.25,
                    items:[
                    this.referencia
                    ]
                },
                {
                    columnWidth:.3,
                    items:[
                    this.descripcionPro
                    ]
                },
                {
                    columnWidth:.3,
                    items:[
                    this.cmbDepartamentos
                    ]
                },
                {
                    columnWidth:.3,
                    items:[
                    this.cmbGrupos
                    ]
                },
                {
                    columnWidth:.3,
                    items:[
                    this.cmbLineas
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
    

   
    if($('#activarlp').val() == 'No')  {
    
        this.gridProductos = new Ext.grid.GridPanel({
            store:this.storeProductos,
            style:'padding:8px',
            height:300,
            id:'grid-seleccion-producto',
            width:800,
            /*sm:new Ext.grid.RowSelectionModel({
                singleSelect:true,
                listeners: {
                    rowselect: function(SelectionModel,rownumber,row){
                        //console.log(row.data);
                    }
                }
            },*/
            sm:new Ext.grid.RowSelectionModel({singleSelect:true}),
            loadMask:true,
            frame:true,
            stripeRows: true,
            autoScroll:true,
            stateful: true,
            columns:[
                //new Ext.grid.RowNumberer(),
              

                {header:'Img', width:80, height:300,  renderer: this.renderImagenProducto, menuDisabled:true, dataIndex:'foto'},
                {header:'posee_talla_color', hidden:true, width:120, menuDisabled:true, dataIndex:'posee_talla_color'},
                {header:'posee_serial', hidden:true, width:120, menuDisabled:true, dataIndex:'posee_serial'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'id_item'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'cod_item_forma'},
                {header:'Código', width:120, sortable: true, menuDisabled:true, dataIndex:'cod_item'},
                {header:'Referencia', width:160, sortable: true, menuDisabled:true, dataIndex:'referencia'},
                {header:'Código de Barras',hidden:true, width:120, sortable: true, menuDisabled:true, dataIndex:'codigo_barras'},
                {header:'Descripción', width:250, sortable: true, menuDisabled:true, dataIndex:'descripcion1'},
                {header:'Stock', width:70, sortable: false, menuDisabled:true, dataIndex:'stock'},
                {header:'Precio', width:70, sortable: false, menuDisabled:true, dataIndex:'precio1'},
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
    } else {
        
                this.gridProductos = new Ext.grid.GridPanel({
            store:this.storeProductos,
            style:'padding:8px',
            height:300,
            id:'grid-seleccion-producto',
            width:880,
            /*sm:new Ext.grid.RowSelectionModel({
                singleSelect:true,
                listeners: {
                    rowselect: function(SelectionModel,rownumber,row){
                        //console.log(row.data);
                    }
                }
            },*/
            sm:new Ext.grid.RowSelectionModel({singleSelect:true}),
            loadMask:true,
            frame:true,
            stripeRows: true,
            autoScroll:true,
            stateful: true,
            columns:[
                //new Ext.grid.RowNumberer(),
              

                {header:'Img', width:80, height:300,  renderer: this.renderImagenProducto, menuDisabled:true, dataIndex:'foto'},
                {header:'posee_talla_color', hidden:true, width:120, menuDisabled:true, dataIndex:'posee_talla_color'},
                {header:'posee_serial', hidden:true, width:120, menuDisabled:true, dataIndex:'posee_serial'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'id_item'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'cod_item_forma'},
                {header:'Código', width:120, sortable: true, menuDisabled:true, dataIndex:'cod_item'},
                {header:'Referencia', width:160, sortable: true, menuDisabled:true, dataIndex:'referencia'},
                {header:'Código de Barras',hidden:true, width:120, sortable: true, menuDisabled:true, dataIndex:'codigo_barras'},
                {header:'Descripción', width:250, sortable: true, menuDisabled:true, dataIndex:'descripcion1'},
                {header:'Stock', width:70, sortable: false, menuDisabled:true, dataIndex:'stock'},
                {header:'Lista Precio', width:80, height:300,sortable: false,  menuDisabled:true, dataIndex:'tprecio'},
                {header:'Precio', width:70, sortable: false, menuDisabled:true, dataIndex:'precio1'},
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
        
        
        
        
    }
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
                            method:'GET',
                            url:  'info_servicio_item.php',
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
                                    text:'Seleccionar '+((record.data["cod_item_forma"]==1)?'producto':'servicio'),
                                    iconCls:'seleccionar',
                                    handler:function(){
                                        win.close();
                                        pBuscaItem.main.ocultarWin();

                                        var filtro_referencia = record.data["cod_item"];
                                        $("input[name='filtro_codigo']").val(filtro_referencia);
                                        debugger;
                                        $.filtrarArticulo(filtro_referencia);
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
            pBuscaItem.main.ocultarWin();
               ;
            var filtro_referencia = this.record.data["id_item"];
           
           
           
                   // ,{name: 'dppa'},{name: 'dppb'},{name: 'dppc'},{name: 'dppd'},{name: 'dppe'},{name: 'dppf'},{name: 'dpph'}
           

      /* $("#lista_preciod").each(function () { 
                $(this).remove();
            }); */
            
           if($('#lista_preciod')) {
            $('#lista_preciod option[value!="0"]').remove() ;
           // ,{name: 'pa'},{name: 'pb'},{name: 'pc'},{name: 'pd'},{name: 'pe'},{name: 'pf'},{name: 'ph'}
         
     $('#lista_preciod').append('<option value="'+this.record.data["pa"]+'"> '+this.record.data["dppa"]+' '+this.record.data["precio1"]+'</option>')  ;
     $('#lista_preciod').append('<option value="'+this.record.data["pb"]+'"> '+this.record.data["dppb"]+' '+this.record.data["precio2"]+'</option>')  ;
     $('#lista_preciod').append('<option value="'+this.record.data["pc"]+'"> '+this.record.data["dppc"]+' '+this.record.data["precio3"]+'</option>')  ;
     $('#lista_preciod').append('<option value="'+this.record.data["pd"]+'"> '+this.record.data["dppd"]+' '+this.record.data["precio4"]+'</option>')  ;
     $('#lista_preciod').append('<option value="'+this.record.data["pe"]+'"> '+this.record.data["dppe"]+' '+this.record.data["precio5"]+'</option>')  ;
     $('#lista_preciod').append('<option value="'+this.record.data["pf"]+'"> '+this.record.data["dppf"]+' '+this.record.data["precio6"]+'</option>')  ;
     $('#lista_preciod').append('<option value="'+this.record.data["ph"]+'"> '+this.record.data["dpph"]+'</option>')  ;
        
        Multiprecios[0]= this.record.data["precio1"] ;
        Multiprecios[1]= this.record.data["precio2"] ;
        Multiprecios[2]= this.record.data["precio3"] ;
        Multiprecios[3]= this.record.data["precio4"] ; 
        Multiprecios[4]= this.record.data["precio5"] ; 
        Multiprecios[5]= this.record.data["precio6"] ; 
        Multiprecios[6]= '' ; 
        
        Lista_Preciovn = ['<option value="'+this.record.data["pa"]+'"> '+this.record.data["dppa"]+' '+this.record.data["precio1"]+'</option>'] ;
        Lista_Preciovn =Lista_Preciovn+['<option value="'+this.record.data["pb"]+'"> '+this.record.data["dppb"]+' '+this.record.data["precio2"]+'</option>'] ;
        Lista_Preciovn = Lista_Preciovn+['<option value="'+this.record.data["pc"]+'"> '+this.record.data["dppc"]+' '+this.record.data["precio3"]+'</option>'] ;

        Lista_Preciovn =Lista_Preciovn+ ['<option value="'+this.record.data["pd"]+'"> '+this.record.data["dppd"]+' '+this.record.data["precio4"]+'</option>'] ;

        Lista_Preciovn =Lista_Preciovn+ ['<option value="'+this.record.data["pe"]+'"> '+this.record.data["dppe"]+' '+this.record.data["precio5"]+'</option>'] ;

        Lista_Preciovn =Lista_Preciovn+ ['<option value="'+this.record.data["pf"]+'"> '+this.record.data["dppf"]+' '+this.record.data["precio6"]+'</option>'] ;
          Lista_Preciovn =Lista_Preciovn+ ['<option value="'+this.record.data["ph"]+'"> '+this.record.data["dpph"]+'</option>'] ;

            
            
            
            }
           
           
           
           
            $("input[name='filtro_codigo']").val(this.record.data["cod_item"]);
             
            if($("#moneda_cotizacion_select") != null){
                $("#moneda_cotizacion_select").attr('disabled',true);
            }
            $.filtrarArticulo(filtro_referencia);
            //$.totalizarFactura();
        });

        this.formProductos = new Ext.form.FormPanel({
            border:false,
            items:[
            this.fieldsetProductos
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
            height:500,
            items:[
            this.tabProductos
            ]
        });
        this.win = new Ext.Window({
            title:'Busqueda de Item',
            modal:true,
            iconCls:'iconTitulo',
            constrain:true,
            action:'hide',
            frame:true,
            closable:false,
            width:880,
            items:[
            this.tabPanel
            ],
            autoHeight:true,
            buttonAlign:'center',
            buttons:[
            //this.seleccionar,
            this.cancelar
            ]
        });


        this.lineas.load({
            params:{
                opt:'getLineas'
            }
        });

        this.departamentos.load({
            params:{
                opt:'getDepartamentos'
            }
        });


        this.grupos.load({
            params:{
                opt:'getGrupos'
            }
        });

    },
    mostrarWin:function(){
        this.win.show();
        this.limpiarFiltroProductos();
        setTimeout(function(){
            //pBuscaItem.main.codigoPro.focus();
            //pBuscaItem.main.codigoBarrasPro.focus();
            pBuscaItem.main.descripcionPro.focus();
        },200);
    },
    ocultarWin:function(){
        this.win.setVisible(false);
    },
    limpiarFiltroProductos:function(){
        pBuscaItem.main.formProductos.getForm().reset();
        pBuscaItem.main.storeProductos.baseParams={};
        pBuscaItem.main.storeProductos.baseParams.opt = 'filtroItem';

        if(!_.isUndefined($("select[name='cod_almacen_filtro'] :selected").val())){
            pBuscaItem.main.storeProductos.baseParams.cod_almacen = $("select[name='cod_almacen_filtro'] :selected").val();
        }

        pBuscaItem.main.storeProductos.baseParams.limit = pBuscaItem.main.limitePaginacion;
        pBuscaItem.main.storeProductos.baseParams.start = pBuscaItem.main.iniciar;
        pBuscaItem.main.storeProductos.baseParams.tipo_item = 1;//productos
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
        pBuscaItem.main.storeProductos.baseParams.opt = 'filtroItem';
        pBuscaItem.main.storeProductos.baseParams.limit = pBuscaItem.main.limitePaginacion;
        pBuscaItem.main.storeProductos.baseParams.start = pBuscaItem.main.iniciar;
        pBuscaItem.main.storeProductos.baseParams.tipo_item = 1;//productos
        pBuscaItem.main.storeProductos.baseParams.BuscarBy = true;
        pBuscaItem.main.storeProductos.load();
                /*Ext.getCmp("codigoProducto-cmb").setValue("");
                Ext.getCmp("descripcionProducto-cmb").setValue("");
                Ext.getCmp("referencia-cmb").setValue("");*/
    },
    getLineas: function(){
        this.store = new Ext.data.JsonStore({
                    url:'../../libs/php/ajax/ajax.php',
                    params:{
                        opt:'getLineas'
                    },
                    root:'data',
                    fields: [
                        {name:'cod_linea'},
                        {name:'descripcion'}
                    ]
        });
        return this.store;
    },

    getDepartamentos: function(){ // Rubro
        this.store = new Ext.data.JsonStore({
                    url:'../../libs/php/ajax/ajax.php',
                    params:{
                        opt:'getDepartamentos'
                    },
                    root:'data',
                    fields: [
                        {name:'cod_departamento'},
                        {name:'descripcion'}
                    ]
        });
        return this.store;
    },
    getGrupos: function(){ // SUbRubro
        this.store = new Ext.data.JsonStore({
                    url:'../../libs/php/ajax/ajax.php',
                    params:{
                        opt:'getGrupos'
                    },
                    root:'data',
                    fields: [
                        {name:'cod_grupo'},
                        {name:'descripcion'}
                    ]
        });
        return this.store;
    },
    getLista: function(){
        this.store = new Ext.data.JsonStore({
            url:'../../libs/php/ajax/ajax.php',
            params:{
                opt:'filtroItem'
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
                name: 'posee_serial'
            },
            {
                name: 'cod_item_forma'
            },
            {
                name: 'cod_item'
            },
            {
                name: 'referencia'
            },
            {
                name: 'codigo_barras'
            },
            {
                name: 'descripcion1'
            },
            {
                name: 'stock'
            },
            {
                name: 'precio1'
            }
            
            ,
            {
                name: 'precio2'
            },
            {
                name: 'precio3'
            },
            {
                name: 'precio4'
            },
            {
                name: 'precio5'
            },
            {
                name: 'precio6'
            }
            ,
            {
                name: 'tprecio'
            }
             ,{name: 'pa'},{name: 'pb'},{name: 'pc'},{name: 'pd'},{name: 'pe'},{name: 'pf'},{name: 'ph'}
             ,{name: 'ppa'},{name: 'ppb'},{name: 'ppc'},{name: 'ppd'},{name: 'ppe'},{name: 'ppf'},{name: 'pph'}
              ,{name: 'dppa'},{name: 'dppb'},{name: 'dppc'},{name: 'dppd'},{name: 'dppe'},{name: 'dppf'},{name: 'dpph'}
            ]
        });
         
        return this.store;
    }
};
Ext.onReady(pBuscaItem.main.init,pBuscaItem.main);
/************* FIN DE PAQUETE *********************/
