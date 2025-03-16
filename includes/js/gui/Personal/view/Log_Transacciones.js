Ext.define('Personal.view.Log_Transacciones', {
    extend: 'Ext.container.Viewport',
    controller: 'log_transacciones',
    requires: [
        'Personal.view.Log_TransaccionesController',
        'Ext.grid.Panel'
    ],
    //requires: [/*,'Personal.store.Integrantes'*/],
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'log_transacciones-lista',
    /*initComponent: function(){
        //storeIntegrantes = Ext.create('Personal.store.Integrantes');
        //console.log("ASD");
    },*/
    items: [
       {
           xtype: 'gridpanel',           
           title: 'Log Transacciones',
           store: 'Log_Transacciones',
           id:'grid-log_transacciones',
           // height: 500,
           columns: [
                {
                    text: 'Id',
                    sortable: true,  
                    dataIndex: 'cod_log',
                    
                },
                 {
                    text: 'Descripcion ',
                    sortable: true,
                    width:400,
                    dataIndex: 'descripcion',
                    
                },
                {
                    text: 'fecha_hora',
                    width:160,
                    sortable: true,  
                    dataIndex: 'fecha_hora',
                    
                },
                {
                    text: 'modulo',
                    width:160,
                    sortable: true,  
                    dataIndex: 'modulo',
                    
                },

                {
                    text: 'url',
                    width:160,
                    sortable: true,  
                    dataIndex: 'url',
                    
                },
                {
                    text: 'accion',
                    width:100,
                    sortable: true,  
                    dataIndex: 'accion',
                    
                },
                {
                    text: 'valor',
                    width:100,
                    sortable: true,  
                    dataIndex: 'valor',
                    
                },
                 {
                    text: 'usuario',
                    width:100,
                    sortable: true,  
                    dataIndex: 'usuario',
                    
                },/*
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
                                document.location.href = "maestro_log_form.php?edit&id="+rec.get('cod_log');
                            }
                        }
                    ]
                }*/
            ],
            tbar: [
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
                            var store = Ext.getCmp('grid-log_transacciones').getStore();
                            var text = Ext.getCmp('texto_buscar').getValue();

                            store.load({
                                params:{
                                    accion:'Accion/Log_transacciones/Get_list_Log_transacciones',
                                    start:0,
                                    limit:25,
                                    texto_buscar:text
                                }
                            });
                        }
                    }/*,
                    '->',
                    {
                        text: 'Agregar',
                        icon: '../../includes/imagenes/icons/add.png',
                        handler: function(){
                            document.location.href = "maestro_log_form.php";
                        }
                    }*/
            ],
            dockedItems: [//{
                //xtype: 'toolbar',
                //items: [ 
                    {
                        xtype:'pagingtoolbar',
                        store:'Log_Transacciones',
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