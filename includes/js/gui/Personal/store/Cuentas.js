Ext.define('Personal.store.Cuentas', {
    extend: 'Ext.data.Store',
    model: 'Personal.model.Cuenta',
    storeId:'store-cuentas',
    sorters: ['Descrip'],
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
            accion:'Accion/Cwconcue/Get_list'
        }
    }
});