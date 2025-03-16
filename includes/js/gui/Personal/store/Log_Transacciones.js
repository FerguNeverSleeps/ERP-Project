Ext.define('Personal.store.Log_Transacciones', {
    extend: 'Ext.data.Store',
    model: 'Personal.model.Log_Transacciones',
    storeId:'store-Log_transacciones',
    sorters: ['descripcion_Log_transacciones'],
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
            accion:'Accion/Log_transacciones/Get_list_Log_transacciones'
        }
    }
});