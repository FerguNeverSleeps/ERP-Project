Ext.define('Personal.model.Integrante', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'foto', type: 'string' },
        { name: 'cedula', type: 'string' },
        { name: 'apelidosynombres', type: 'string' },
        { name: 'estado', type: 'string' },
        { name: 'ficha', type: 'string' }
    ]
});