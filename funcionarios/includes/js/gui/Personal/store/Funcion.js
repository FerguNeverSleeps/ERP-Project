Ext.define('Personal.store.Funcion', {
    extend: 'Ext.data.Store',
    model: 'Personal.model.Funcion',
    storeId:'store-funcion',
    sorters: ['descripcion_funcion'],
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
            accion:'Accion/Maestro/Get_list_Funcion'
        }
    }
});