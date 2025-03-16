Ext.define('Personal.view.Viewport', {
    extend: 'Ext.container.Viewport',
    controller: 'viewport',
    requires: [
        'Personal.view.ViewportController',
        'Ext.grid.Panel'
    ],
    //requires: [/*,'Personal.store.Integrantes'*/],
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'personal-lista',
    /*initComponent: function(){
        //storeIntegrantes = Ext.create('Personal.store.Integrantes');
        //console.log("ASD");
    },*/
    items: [
       {
           xtype: 'gridpanel',           
           title: 'Personal',
           store: 'Integrantes',
           id:'grid-integrantes',
           // height: 500,
           columns: [
                {
                    text: 'Foto',
                    sortable: true,  
                    dataIndex: 'foto',
                    renderer: function (value, metaData, record, row, col, store, gridView) {
                        var foto = "";
                        if(value == ""){
                            //foto = "../../includes/imagenes/user.png";
                            foto = "fotos/silueta.gif";
                        }
                        else{
                            foto = value;
                        }
                        return '<img width="48" height="48" src="'+foto+'" />';
                    }
                },
                 {
                    text: 'Ficha',
                    sortable: true,  
                    dataIndex: 'ficha',
                    
                },
                 {
                    text: 'Cedula',
                    sortable: true,  
                    dataIndex: 'cedula',
                    
                },
                {
                    text: 'Apellidos y nombres ',
                    sortable: true,
                    width:400,
                    dataIndex: 'apenom',
                    
                },               

                {
                    text: 'Situación',
                    sortable: true,  
                    dataIndex: 'estado',
                    
                },/*
                {
                    text: 'Tipo Planilla',
                    sortable: true,  
                    width:200,
                    dataIndex: 'descrip'
                    
                }*/
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 20,
                    items: [
                        {
                            iconCls: 'editar',
                            tooltip: 'Editar',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                                document.location.href = "IntegrantesForm.php?edit&ficha="+rec.get('ficha');
                            }
                        }
                    ]
                }, 
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 20,
                    items: [
                        {
                            iconCls: 'calendario',
                            tooltip: 'Ver Calendario',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                                document.location.href = "calendarios_personal.php?anio="+rec.get('anio')+"&ficha="+rec.get('ficha');
                            }
                        }
                    ]
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 20,
                    items: [
                        {
                            iconCls: 'cargas_familiares',
                            tooltip: 'Cargas Familiares',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                document.location.href = "familiares.php?cedula="+rec.get('cedula')+"&txtficha="+rec.get('ficha');
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                            }
                        }
                    ]
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 20,
                    items: [
                        {
                            iconCls: 'campos_adicionales',
                            tooltip: 'Campos Adicionales',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                document.location.href = "otrosdatos_integrantes.php?txtficha="+rec.get('ficha');
                                
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                            }
                        }
                    ]
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 20,
                    items: [
                        {
                            iconCls: 'imprimir',
                            tooltip: 'Imprimir',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                document.location.href = "../fpdf/datos_personal.php?cedula=cedula&ficha=ficha";
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                            }
                        }
                    ]
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 20,
                    items: [
                        {
                            iconCls: 'expediente',
                            tooltip: 'Ver Expediente',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                
                                document.location.href = "../expediente/expediente_list_ajax.php?cedula="+rec.get('cedula');
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                            }
                        }
                    ]
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 20,
                    items: [
                        {
                            iconCls: 'eliminar',
                            tooltip: 'Eliminar',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                            }
                        }
                    ]
                }
            ],
            tbar: [
                    {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    id: 'buscar-por',
                                    displayField: 'descripcion',
                                    valueField: 'cod',
                                    queryMode: 'local',
                                    emptyText: 'Buscar Por',
                                    value:-1,
                                    editable:false,
                                    margin: '0 6 0 0',
                                    store: new Ext.data.Store({
                                        fields: ['cod', 'descripcion'],
                                        data: (function() {
                                            var data = [];
                                            data[0] = {cod: "-1", descripcion: "Todo"};
                                            data[1] = {cod: "0", descripcion: "Nombre"};
                                            data[2] = {cod: "1", descripcion: "Cedula"};
                                            data[3] = {cod: "2", descripcion: "Posicion"};
                                            return data;
                                        })()
                                    }),
                                    //width: 100,
                                    forceSelection: true
                    },
                    {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    //name: 'cbosituacion',
                                    editable:false,
                                    id: 'buscar-por-estado',
                                    displayField: 'situacion',
                                    valueField: 'situacion',
                                    value:'Activo',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Situacion',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Situacion',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Situacion'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    forceSelection: true
                    },
                    {
                        xtype:'textfield',
                        emptyText:'Escriba frase para buscar',
                        width:250 ,
                        id:'texto_buscar'
                    },
                    {
                        text: 'Buscar',
                        icon: '../../includes/imagenes/icons/magnifier.png',
                        handler: function(){
                            //console.log(Ext.getCmp('grid-integrantes').getStore())
                            var store = Ext.getCmp('grid-integrantes').getStore();
                            var text = Ext.getCmp('texto_buscar').getValue();
                            var buscar_por = Ext.getCmp('buscar-por').getValue();
                            var buscar_por_estado = Ext.getCmp('buscar-por-estado').getValue();

                            store.load({
                                params:{
                                    accion:'Accion/Integrante/Get_list',
                                    start:0,
                                    limit:25,
                                    texto_buscar:text,
                                    buscar_por:buscar_por,
                                    buscar_por_estado:buscar_por_estado
                                }
                            });
                        }
                    },
                    /*{
                        xtype:'checkbox',
                        //fieldLabel: 'Favorite Animals',
                        boxLabel: 'Mostrar Solo Egresados',
                        id:'muestra-egresados',
                        listeners:{
                            change:function(thiscm,newv,old){
                                var muestra_egresados = 0;
                                if(newv){
                                    muestra_egresados = 1;
                                }
                                else{
                                    muestra_egresados = 0;
                                }
                                var store = Ext.getCmp('grid-integrantes').getStore();
                                var text = Ext.getCmp('texto_buscar').getValue();
                                store.load({
                                        params:{
                                            accion:'Accion/Integrante/Get_list',
                                            start:0,
                                            limit:25,
                                            texto_buscar:text,
                                            muestra_egresados:muestra_egresados
                                        }
                                });
                            }
                        }
                    },*/
                    '->',
                    {
                        text: 'Agregar',
                        icon: '../../includes/imagenes/icons/add.png',
                        handler: function(){
                            document.location.href = "IntegrantesForm.php";
                        }
                    }
            ],
            dockedItems: [//{
                //xtype: 'toolbar',
                //items: [ 
                    {
                        xtype:'pagingtoolbar',
                        store:'Integrantes',
                        displayInfo:true,
                        id:'tradepage',
                        itemId:'tradepage',
                        displayMsg:'P&aacute;gina {0}-{1} de {2}',
                        emptyMsg:'No hay datos para mostrar',
                        dock:'bottom'
                    }
                //]
            /*}*/]
           
       }
    ]
});