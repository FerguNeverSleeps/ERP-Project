Ext.define('Personal.view.Viewport', {
    extend: 'Ext.container.Viewport',
    controller: 'viewport',
    requires: ['Ext.grid.Panel'],
    style: 'padding:25px',
    layout: 'fit',
    items: [
       {
           xtype: 'gridpanel',           
           title: 'Personal',
           store: 'Integrantes',
           columns: [
                {
                    text: 'Foto',
                    sortable: true,  
                    dataIndex: 'foto'
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
                    width:450,
                    dataIndex: 'apelidosynombres',
                    
                },               

                {
                    text: 'Situación',
                    sortable: true,  
                    dataIndex: 'estado',
                    
                },
                {
                    text: 'Tipo Planilla',
                    sortable: true,  
                    width:200,
                    dataIndex: 'descrip'
                    
                }
           ],
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    text: 'Add',
                    iconCls: 'icon-add',
                    handler: function(){
                        // empty record
                        store.insert(0, new Person());
                        rowEditing.startEdit(0, 0);
                    }
                }, '-', {
                    itemId: 'delete',
                    text: 'Delete',
                    iconCls: 'icon-delete',
                    disabled: true,
                    handler: function(){
                        var selection = grid.getView().getSelectionModel().getSelection()[0];
                        if (selection) {
                            store.remove(selection);
                        }
                    }
                }]
            }]
           
       }
    ]
});