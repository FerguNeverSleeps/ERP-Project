Ext.ns("pBuscarCliente");
/**
 * @class pBuscarCliente
 */


pBuscarCliente.main = {
    limitePaginacion:10,
    iniciar:0,
    init:function(){

        this.storeClientes = this.getLista();
        this.cancelar = new Ext.Button({
            text:'Cerrar ventana',
            iconCls:'cancelar',
            handler:function(){
                $("input[name='forma_salida_cliente']").focus();
                pBuscarCliente.main.ocultarWin();
            }
        });
        this.seleccionar = new Ext.Button({
            text:'Selecionar Cliente',
            iconCls:'seleccionar',
            handler:function(){
                //En construcción...
            }
        });
        this.ciruc = new Ext.form.TextField({
            fieldLabel:'CI / RUC',
            name:'ciruc'
        });

        ///////////////////////////////////////////////////////////////////////////
        this.cod_cliente = new Ext.form.TextField({
            fieldLabel:'Código',
            name:'cod_cliente'
        });
        this.ciruc.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscarCliente.main.aplicarFiltroByFormularioCliente();
            }
        }, this);

        ///////////////////////////////////////////////////////////////////////////

        this.cod_cliente.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscarCliente.main.aplicarFiltroByFormularioCliente();
            }
        }, this);

        this.cliente = new Ext.form.TextField({
            fieldLabel:'Cliente',
            name:'cliente',
            width:250
        });

        this.cliente.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscarCliente.main.aplicarFiltroByFormularioCliente();
            }
        }, this);

        this.buscarPro = new Ext.Button({
            text:'Buscar',
            iconCls:'iconBuscar',
            handler:function(){
                pBuscarCliente.main.aplicarFiltroByFormularioCliente();
            }
        });

        this.limpiarPro = new Ext.Button({
            text:'Limpiar Filtro',
            iconCls:'iconLimpiar',
            handler:function(){
                pBuscarCliente.main.limpiarFiltroClientes();
            }
        });

        this.fieldsetClientes = new Ext.form.FieldSet({
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
                    columnWidth:.22,
                    items:[
                    this.ciruc 
                    ]
                },
                {
                    columnWidth:.22,
                    items:[
                    this.cod_cliente
                    ]
                },
                {
                    columnWidth:.4,
                    items:[
                    this.cliente
                    ]
                }
                ]
            }
            ]
        });

        this.renderImagenCliente = function(val, meta, record, rowIndex, colIndex, store) {
            var html = "";
            var nombreImagenFoto = store.data.itemAt(rowIndex).json.foto;
            if ( !_.str.isBlank(nombreImagenFoto) ) {
                html = "<img width='70' src='../../imagenes/"+nombreImagenFoto+"'>";
            }else{
                html = "<img width='70' src='../../imagenes/sin_imagen.jpg'>";
            }
            return html;
        }
    
        this.gridClientes = new Ext.grid.GridPanel({
            store:this.storeClientes,
            style:'padding:8px',
            height:320,
            width:773,
            loadMask:true,
            frame:true,
            stripeRows: true,
            autoScroll:true,
            stateful: true,
            columns:[
                new Ext.grid.RowNumberer(),
                {header:'Img', width:120, height:300,  renderer: this.renderImagenCliente, menuDisabled:true, dataIndex:'foto'},
                {header:'Id', hidden:true, width:120, menuDisabled:true, dataIndex:'id_cliente'},
                {header:'CI / RUC', width:120, menuDisabled:true, dataIndex:'rif'},
                {header:'Código', width:120, sortable: true, menuDisabled:true, dataIndex:'cod_cliente'},
                {header:'Cliente', width:400, sortable: true, menuDisabled:true, dataIndex:'nombre'}
            ],
            bbar: new Ext.PagingToolbar({
                pageSize: pBuscarCliente.main.limitePaginacion,
                store: this.storeClientes,
                displayInfo: true,
                html:'<div style="padding-left:5px;color:black;font-size:10px;"><b>Nota: Para seleccionar debe hacer doble clic sobre el registro.</b></div>',
                displayMsg: '<span style="color:black">Registros: {0} - {1} de {2}</span>',
                emptyMsg: "<span style=\"color:black\">No se encontraron registros</span>"
            })
        });
        
        var self =  this;
        //Evento Doble Click
        this.gridClientes.on('rowdblclick', function( grid, row, evt){
            this.record = pBuscarCliente.main.storeClientes.getAt(row);
            pBuscarCliente.main.ocultarWin();

            var registro = this.record;
            self.fncallback(registro);

        });

        this.formClientes = new Ext.form.FormPanel({
            border:false,
            items:[
            this.fieldsetClientes
            ]
        });
        this.tabClientes = new Ext.Panel({
            title:'Filtro',
            style:'padding:5px;',
            items:[
            this.formClientes,
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
            this.gridClientes
            ],
            autoHeight:true
        });
        this.tabPanel = new Ext.TabPanel({
            title: '',
            activeTab:0,
            height:470,
            items:[
            this.tabClientes
            ]
        });
        this.win = new Ext.Window({
            title:'Busqueda de Clientes',
            modal:true,
            iconCls:'iconTitulo',
            constrain:true,
            action:'hide',
            frame:true,
            closable:false,
            width:800,
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
    },
    fncallback: function(){

    },
    mostrarWin:function(fncallback){
        this.fncallback = fncallback;

        this.win.show();
        this.limpiarFiltroClientes();
        setTimeout(function(){
            //pBuscarCliente.main.codigoPro.focus();
            pBuscarCliente.main.cod_cliente.focus();
        },200);
    },
    ocultarWin:function(){
        this.win.setVisible(false);
    },
    limpiarFiltroClientes:function(){
        pBuscarCliente.main.formClientes.getForm().reset();
        pBuscarCliente.main.storeClientes.baseParams={};
        pBuscarCliente.main.storeClientes.baseParams.opt = 'filtroClientes';
        pBuscarCliente.main.storeClientes.baseParams.limit = pBuscarCliente.main.limitePaginacion;
        pBuscarCliente.main.storeClientes.baseParams.start = pBuscarCliente.main.iniciar;
        pBuscarCliente.main.storeClientes.load();
    },
    aplicarFiltroByFormularioCliente: function(){
        //Capturamos los campos con su value para posteriormente verificar cual
        //esta lleno y trabajar en base a ese.
        var campo = pBuscarCliente.main.formClientes.getForm().getValues();
        pBuscarCliente.main.storeClientes.baseParams={};
        var swfiltrar = false;
        for(campName in campo){
            if(campo[campName]!=''){
                swfiltrar = true;
                eval("pBuscarCliente.main.storeClientes.baseParams."+campName+" = '"+campo[campName]+"';");
            }
        }
        pBuscarCliente.main.storeClientes.baseParams.opt = 'filtroClientes';
        pBuscarCliente.main.storeClientes.baseParams.limit = pBuscarCliente.main.limitePaginacion;
        pBuscarCliente.main.storeClientes.baseParams.start = pBuscarCliente.main.iniciar;
        pBuscarCliente.main.storeClientes.baseParams.BuscarBy = true;
        pBuscarCliente.main.storeClientes.load();
    },
    getLista: function(){
        this.store = new Ext.data.JsonStore({
            url:'../../libs/php/ajax/ajax.php',
            params:{
                opt:'filtroClientes'
            },
            root:'data',
            fields:[
            {
                name: 'id_cliente'
            },
            {
                name: 'rif'
            },
            {
                name: 'cod_cliente'
            },
            {
                name: 'nombre'
            }
            ]
        });
        return this.store;
    }
};
Ext.onReady(pBuscarCliente.main.init,pBuscarCliente.main);
