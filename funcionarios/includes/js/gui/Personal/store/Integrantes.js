Ext.define('Personal.store.Integrantes', {
    extend: 'Ext.data.Store',
    model: 'Personal.model.Integrante',
    storeId:'store-integrantes',
    sorters: ['ficha'],
    autoLoad: true,
    autoSync: false,    // Make sure that autosync is disabled to avoid posting invalid vendorName.
    proxy: {
        type: 'ajax',
        //url: 'Integrantes.php',
        url: '../../includes/clases/controladores/ControladorApp.php',
        reader: {
            type: 'json',
            rootProperty: 'matches',
            totalProperty: "totalCount",
            //root: "Integrantes"
        },
        extraParams:{
            accion:'Accion/Integrante/Get_list'
        }
    }
});