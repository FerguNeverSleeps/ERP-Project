Ext.define('Ciclos.store.Ciclos', {
    extend: 'Ext.data.Store',
    model: 'Ciclos.model.Ciclo',
    storeId:'store-ciclo',
    sorters: ['codnom'],
    autoLoad: true,
    autoSync: false,   
    proxy: {
        type: 'ajax',
        url: '../../includes/clases/controladores/ControladorApp.php',
        reader: {
            type: 'json',
            rootProperty: 'matches',
            totalProperty: "totalCount",
        },
        extraParams:{
            accion:'Accion/Ciclo/list_ciclo'
        }
    }
});