Ext.define('Ciclos.view.Ciclos', {
    extend: 'Ext.container.Viewport',
    controller: 'Ciclo',
    requires: [
        'Ciclos.view.CicloController',
        'Ext.grid.Panel'
    ],
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'ciclo-lista',

    items: [
       {
           xtype: 'gridpanel',           
           title: 'Lista',
           store: 'Ciclos',
           id:'grid-posicion',
           columns: [
                {
                    text: 'Planilla',
                    sortable: true,  
                    dataIndex: 'codnom',
                    
                },
                 {
                    text: 'Descripción',
                    sortable: true,
                    width:400,
                    dataIndex: 'descrip',
                    
                },
                {
                    text: 'Pago',
                    sortable: true,  
                    dataIndex: 'fechapago',
                    
                },
                {
                    text: 'Inicio',
                    sortable: true,  
                    dataIndex: 'periodo_ini',
                    
                },
                 {
                    text: 'Final',
                    sortable: true,
                    width:400,
                    dataIndex: 'periodo_fin',
                    
                },
                {
                    text: 'Tipo Planilla',
                    width:160,
                    sortable: true,  
                    dataIndex: 'codtip',
                    
                },
                {
                    text: 'Estado',
                    width:160,
                    sortable: true,  
                    dataIndex: 'status',
                    
                },
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
                                document.location.href = "nominadepago_edit.php?edit&id="+rec.get('codnom');
                            }
                        }
                    ]
                }
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
                            var store = Ext.getCmp('grid-posicion').getStore();
                            var text = Ext.getCmp('texto_buscar').getValue();

                            store.load({
                                params:{
                                    accion:'Accion/Maestro/list_ciclo',
                                    start:0,
                                    limit:25,
                                    texto_buscar:text
                                }
                            });
                        }
                    },
                    '->',
                    {
                        text: 'Agregar',
                        icon: '../../includes/imagenes/icons/add.png',
                        handler: function(){
                            document.location.href = "nominapago_form.php";
                        }
                    }
            ],
            dockedItems: [
                    {
                        xtype:'pagingtoolbar',
                        store:'Posicion',
                        displayInfo:true,
                        id:'tradepage',
                        itemId:'tradepage',
                        displayMsg:'P&aacute;gina {0}-{1} de {2}',
                        emptyMsg:'No hay datos para mostrar',
                        dock:'bottom'
                    }
                ]
           
       }
    ]
});