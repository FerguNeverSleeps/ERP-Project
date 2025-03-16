Ext.define('Personal.view.Profesion', {
    extend: 'Ext.container.Viewport',
    controller: 'profesion',
    requires: [
        'Personal.view.ProfesionController',
        'Ext.grid.Panel'
    ],
    //requires: [/*,'Personal.store.Integrantes'*/],
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'profesion-lista',
    /*initComponent: function(){
        //storeIntegrantes = Ext.create('Personal.store.Integrantes');
        //console.log("ASD");
    },*/
    items: [
       {
           xtype: 'gridpanel',           
           title: 'Profesion',
           store: 'Profesion',
           id:'grid-profesion',
           // height: 500,
           columns: [
                {
                    text: 'Id Profesion',
                    sortable: true,  
                    dataIndex: 'codorg',
                    
                },
                 {
                    text: 'Descripcion ',
                    sortable: true,
                    width:400,
                    dataIndex: 'descrip',
                    
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
                                document.location.href = "maestro_profesion_form.php?edit&id="+rec.get('codorg');
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
                            var store = Ext.getCmp('grid-profesion').getStore();
                            var text = Ext.getCmp('texto_buscar').getValue();

                            store.load({
                                params:{
                                    accion:'Accion/Maestro/Get_list_Profesion',
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
                            document.location.href = "maestro_profesion_form.php";
                        }
                    }
            ],
            dockedItems: [//{
                //xtype: 'toolbar',
                //items: [ 
                    {
                        xtype:'pagingtoolbar',
                        store:'Profesion',
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