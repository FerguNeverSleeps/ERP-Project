Ext.define('Personal.store.Integrantes', {
    extend: 'Ext.data.Store',
    model: 'Personal.model.Integrante',
    sorters: ['ficha'],
    autoLoad: true,
    autoSync: false,    // Make sure that autosync is disabled to avoid posting invalid vendorName.
    proxy: {
        type: 'ajax',
        url: 'Integrantes.php',
        reader: {
            type: 'json',
            rootProperty: 'Integrantes'
        }
    }
});