Ext.define('Personal.store.Posicion', {
    extend: 'Ext.data.Store',
    model: 'Personal.model.Posicion',
    storeId:'store-posicion',
    sorters: ['descripcion_posicion'],
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
            accion:'Accion/Maestro/Get_list_Posicion'
        }
    }
});