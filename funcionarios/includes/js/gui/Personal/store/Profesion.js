Ext.define('Personal.store.Profesion', {
    extend: 'Ext.data.Store',
    model: 'Personal.model.Profesion',
    storeId:'store-profesion',
    sorters: ['descripcion_profesion'],
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
            accion:'Accion/Maestro/Get_list_Profesion'
        }
    }
});