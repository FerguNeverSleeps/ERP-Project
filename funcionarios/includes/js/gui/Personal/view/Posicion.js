Ext.define('Personal.view.Posicion', {
    extend: 'Ext.container.Viewport',
    controller: 'posicion',
    requires: [
        'Personal.view.PosicionController',
        'Ext.grid.Panel'
    ],
    //requires: [/*,'Personal.store.Integrantes'*/],
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'posicion-lista',
    /*initComponent: function(){
        //storeIntegrantes = Ext.create('Personal.store.Integrantes');
        //console.log("ASD");
    },*/
    items: [
       {
           xtype: 'gridpanel',           
           title: 'Posiciones',
           store: 'Posicion',
           id:'grid-posicion',
           // height: 500,
           columns: [
                {
                    text: 'Id',
                    sortable: true,  
                    width:50,
                    dataIndex: 'nomposicion_id',
                    
                },
                 {
                    text: 'Descripcion ',
                    sortable: true,
                    width:400,
                    dataIndex: 'descripcion_posicion',
                    
                },
                {
                    text: 'Sueldo Propuesto',
                    width:120,
                    sortable: true,  
                    dataIndex: 'sueldo_propuesto',
                    
                },
                {
                    text: 'Sueldo Anual',
                    width:120,
                    sortable: true,  
                    dataIndex: 'sueldo_anual',
                    
                },
                {
                    text: 'Partida',
                    width:130,
                    sortable: true,  
                    dataIndex: 'partida',
                    
                },
                {
                    text: 'Gastos Rep.',
                    width:100,
                    sortable: true,  
                    dataIndex: 'gastos_representacion',
                    
                },
                {
                    text: 'Paga GR.',
                    width:80,
                    sortable: true,  
                    dataIndex: 'paga_gr',
                    
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
                                //Ext.Msg.alert('Sell', 'Sell ' + rec.get('name'));
                                document.location.href = "maestro_posicion_form.php?edit&id="+rec.get('nomposicion_id');
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
                            //console.log(Ext.getCmp('grid-integrantes').getStore())
                            var store = Ext.getCmp('grid-posicion').getStore();
                            var text = Ext.getCmp('texto_buscar').getValue();

                            store.load({
                                params:{
                                    accion:'Accion/Maestro/Get_list_Posicion',
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
                            document.location.href = "maestro_posicion_form.php";
                        }
                    }
            ],
            dockedItems: [//{
                //xtype: 'toolbar',
                //items: [ 
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
                //]
            /*}*/]
           
       }
    ]
});