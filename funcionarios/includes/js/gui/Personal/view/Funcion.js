Ext.define('Personal.view.Funcion', {
    extend: 'Ext.container.Viewport',
    controller: 'funcion',
    requires: [
        'Personal.view.FuncionController',
        'Ext.grid.Panel'
    ],
    //requires: [/*,'Personal.store.Integrantes'*/],
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'funcion-lista',
    /*initComponent: function(){
        //storeIntegrantes = Ext.create('Personal.store.Integrantes');
        //console.log("ASD");
    },*/
    items: [
       {
           xtype: 'gridpanel',           
           title: 'Funciones',
           store: 'Funcion',
           id:'grid-funcion',
           // height: 500,
           columns: [
                {
                    text: 'Id Funcion',
                    sortable: true,  
                    dataIndex: 'nomfuncion_id',
                    
                },
                 {
                    text: 'Descripcion ',
                    sortable: true,
                    width:400,
                    dataIndex: 'descripcion_funcion',
                    
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
                                document.location.href = "maestro_funcion_form.php?edit&id="+rec.get('nomfuncion_id');
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
                            var store = Ext.getCmp('grid-funcion').getStore();
                            var text = Ext.getCmp('texto_buscar').getValue();

                            store.load({
                                params:{
                                    accion:'Accion/Maestro/Get_list_Funcion',
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
                            document.location.href = "maestro_funcion_form.php";
                        }
                    }
            ],
            dockedItems: [//{
                //xtype: 'toolbar',
                //items: [ 
                    {
                        xtype:'pagingtoolbar',
                        store:'Funcion',
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