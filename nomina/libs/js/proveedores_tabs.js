Ext.ns('Selectra.pyme.proveedores');

Selectra.pyme.proveedores.TabPanelProveedores = {
    init: function(){
        var panelDatos = new Ext.Panel({
            contentEl:'div_tab1',
            title: 'Datos Generales'
        });
        var panelPerfil = new Ext.Panel({
            contentEl:'div_tab2',
            title:'Datos Comerciales'
        });

        this.tabs = new Ext.TabPanel({
            renderTo:'contenedorTAB',
            activeTab:0,
            plain:true,
            defaults:{
                autoHeight: true
            },
            items:[
                panelDatos, panelPerfil
            ]
        });
    }
}
Ext.onReady(Selectra.pyme.proveedores.TabPanelProveedores.init, Selectra.pyme.proveedores.TabPanelProveedores);