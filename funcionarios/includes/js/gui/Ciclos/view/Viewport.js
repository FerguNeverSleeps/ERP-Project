Ext.define('Ciclos.view.Viewport', {
    extend: 'Ext.container.Viewport',

    controller: 'viewport',
    requires: [
        'Ciclos.view.ViewportController',
        'Ext.grid.Panel'
    ],
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'ciclo-lista',

    items: [
        {
            xtype: 'gridpanel',
            store: 'Ciclo',
            columns: [
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'codnom',
                    text: 'Planilla'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'codtip',
                    text: 'Tipo Planilla'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'descrip',
                    text: 'Descripción'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'status',
                    text: 'Estado'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'periodo_ini',
                    text: 'Inicio'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'periodo_fin',
                    text: 'Final'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'fechapago',
                    text: 'Pago'
                }
            ]
               ,
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
                            var store = Ext.getCmp('grid-integrantes').getStore();
                            var text = Ext.getCmp('texto_buscar').getValue();

                            store.load({
                                params:{
                                    accion:'Accion/Integrante/Get_list',
                                    start:0,
                                    limit:25,
                                    texto_buscar:text
                                }
                            });
                        }
                    },
                    {
                        xtype:'checkbox',
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
                                var store = Ext.getCmp('grid-ciclos').getStore();
                                var text = Ext.getCmp('texto_buscar').getValue();
                                store.load({
                                        params:{
                                            accion:'Accion/Ciclo/list_ciclo',
                                            start:0,
                                            limit:25,
                                            texto_buscar:text,
                                            muestra_egresados:muestra_egresados
                                        }
                                });
                            }
                        }
                    },
                    '->',
                    {
                        text: 'Agregar',
                        icon: '../../includes/imagenes/icons/add.png',
                        handler: function(){
                            document.location.href = "CiclosForm.php";
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