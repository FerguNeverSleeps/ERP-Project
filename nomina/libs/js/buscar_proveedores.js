Ext.ns("pBuscarProveedores");
/**
 * @class pBuscarProveedores
 */


pBuscarProveedores.main = {
    limitePaginacion:10,
    iniciar:0,
    init:function(){

        this.storeProveedoress = this.getLista();
        this.cancelar = new Ext.Button({
            text:'Cerrar ventana',
            iconCls:'cancelar',
            handler:function(){
                $("input[name='forma_salida_Proveedores']").focus();
                pBuscarProveedores.main.ocultarWin();
            }
        });
        this.seleccionar = new Ext.Button({
            text:'Selecionar Proveedor',
            iconCls:'seleccionar',
            handler:function(){
                //En construcción...
            }
        });

        ///////////////////////////////////////////////////////////////////////////
        this.cod_Proveedores = new Ext.form.TextField({
            fieldLabel:'Código',
            name:'cod_proveedor'
        });


        ///////////////////////////////////////////////////////////////////////////

        this.cod_Proveedores.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscarProveedores.main.aplicarFiltroByFormularioProveedores();
            }
        }, this);

        this.Proveedores = new Ext.form.TextField({
            fieldLabel:'Proveedor',
            name:'proveedor',
            width:250
        });

        this.Proveedores.on('specialkey', function(f, event){
            if(event.getKey() == event.ENTER) {
                pBuscarProveedores.main.aplicarFiltroByFormularioProveedores();
            }
        }, this);

        this.buscarPro = new Ext.Button({
            text:'Buscar',
            iconCls:'iconBuscar',
            handler:function(){
                pBuscarProveedores.main.aplicarFiltroByFormularioProveedores();
            }
        });

        this.limpiarPro = new Ext.Button({
            text:'Limpiar Filtro',
            iconCls:'iconLimpiar',
            handler:function(){
                pBuscarProveedores.main.limpiarFiltroProveedores();
            }
        });

        this.fieldsetProveedoress = new Ext.form.FieldSet({
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
                    this.cod_Proveedores
                    ]
                },
                {
                    columnWidth:.4,
                    items:[
                    this.Proveedores
                    ]
                }
                ]
            }
            ]
        });

        this.renderImagenProveedores = function(val, meta, record, rowIndex, colIndex, store) {
            var html = "";
            var nombreImagenFoto = store.data.itemAt(rowIndex).json.foto;
            if ( !_.str.isBlank(nombreImagenFoto) ) {
                html = "<img width='70' src='../../imagenes/"+nombreImagenFoto+"'>";
            }else{
                html = "<img width='70' src='../../imagenes/sin_imagen.jpg'>";
            }
            return html;
        }
    
        this.gridProveedoress = new Ext.grid.GridPanel({
            store:this.storeProveedoress,
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
                {header:'Cod. Proveedor', width:150, sortable: true, menuDisabled:true, dataIndex:'cod_proveedor'},
                {header:'Proveedor', width:400, sortable: true, menuDisabled:true, dataIndex:'descripcion'}
            ],
            bbar: new Ext.PagingToolbar({
                pageSize: pBuscarProveedores.main.limitePaginacion,
                store: this.storeProveedoress,
                displayInfo: true,
                html:'<div style="padding-left:5px;color:black;font-size:10px;"><b>Nota: Para seleccionar debe hacer doble clic sobre el registro.</b></div>',
                displayMsg: '<span style="color:black">Registros: {0} - {1} de {2}</span>',
                emptyMsg: "<span style=\"color:black\">No se encontraron registros</span>"
            })
        });
        
        var self =  this;
        //Evento Doble Click
        this.gridProveedoress.on('rowdblclick', function( grid, row, evt){
            this.record = pBuscarProveedores.main.storeProveedoress.getAt(row);
            pBuscarProveedores.main.ocultarWin();

            var opt = "";

            if($("#ingreso_comprobante").val() == "si"){
                opt = "getComprasPendientesPorPagar";
            }
            else{
                opt = "getComprasPendientes";
            }
            
            var registro = this.record;
            $.ajax({
                    type: 'GET',
                    data: 'opt='+opt+'&v1='+registro.data.id_proveedor,
                    url:  '../../libs/php/ajax/ajax.php',
                    beforeSend: function(){
                    },
                    success: function(data){
                        $('#facturasPendientes').empty();
                        $('#facturasPendientes').append(data);
                    }
            });
            $("#id_proveedor").val(registro.data.id_proveedor);
            self.fncallback(registro);

        });

        this.formProveedoress = new Ext.form.FormPanel({
            border:false,
            items:[
            this.fieldsetProveedoress
            ]
        });
        this.tabProveedoress = new Ext.Panel({
            title:'Filtro',
            style:'padding:5px;',
            items:[
            this.formProveedoress,
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
            this.gridProveedoress
            ],
            autoHeight:true
        });
        this.tabPanel = new Ext.TabPanel({
            title: '',
            activeTab:0,
            height:470,
            items:[
            this.tabProveedoress
            ]
        });
        this.win = new Ext.Window({
            title:'Busqueda de Proveedoress',
            modal:true,
            iconCls:'iconTitulo',
            constrain:true,
            x:70,y:70,
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
        this.limpiarFiltroProveedores();
        setTimeout(function(){
            //pBuscarProveedores.main.codigoPro.focus();
            pBuscarProveedores.main.cod_Proveedores.focus();
        },200);
    },
    ocultarWin:function(){
        this.win.setVisible(false);
    },
    limpiarFiltroProveedores:function(){
        pBuscarProveedores.main.formProveedoress.getForm().reset();
        pBuscarProveedores.main.storeProveedoress.baseParams={};
        pBuscarProveedores.main.storeProveedoress.baseParams.opt = 'filtroProveedores';
        pBuscarProveedores.main.storeProveedoress.baseParams.limit = pBuscarProveedores.main.limitePaginacion;
        pBuscarProveedores.main.storeProveedoress.baseParams.start = pBuscarProveedores.main.iniciar;
        pBuscarProveedores.main.storeProveedoress.load();
    },
    aplicarFiltroByFormularioProveedores: function(){
        //Capturamos los campos con su value para posteriormente verificar cual
        //esta lleno y trabajar en base a ese.
        var campo = pBuscarProveedores.main.formProveedoress.getForm().getValues();
        pBuscarProveedores.main.storeProveedoress.baseParams={};
        var swfiltrar = false;
        for(campName in campo){
            if(campo[campName]!=''){
                swfiltrar = true;
                eval("pBuscarProveedores.main.storeProveedoress.baseParams."+campName+" = '"+campo[campName]+"';");
            }
        }
        pBuscarProveedores.main.storeProveedoress.baseParams.opt = 'filtroProveedores';
        pBuscarProveedores.main.storeProveedoress.baseParams.limit = pBuscarProveedores.main.limitePaginacion;
        pBuscarProveedores.main.storeProveedoress.baseParams.start = pBuscarProveedores.main.iniciar;
        pBuscarProveedores.main.storeProveedoress.baseParams.BuscarBy = true;
        pBuscarProveedores.main.storeProveedoress.load();
    },
    getLista: function(){
        this.store = new Ext.data.JsonStore({
            url:'../../libs/php/ajax/ajax.php',
            params:{
                opt:'filtroProveedores'
            },
            root:'data',
            fields:[
            {
                name: 'id_proveedor'
            },
            {
                name: 'cod_proveedor'
            },
            {
                name: 'descripcion'
            }
            ]
        });
        return this.store;
    }
};
Ext.onReady(pBuscarProveedores.main.init,pBuscarProveedores.main);
