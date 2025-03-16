/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Log_Transacciones', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'cod_log', type: 'int' },
        { name: 'descripcion', type: 'string'},
        { name: 'fecha_hora', type: 'date' },
        { name: 'modulo', type: 'string' },
        { name: 'url', type: 'string' },
        { name: 'accion', type: 'string' },
        { name: 'valor', type: 'string' },
        { name: 'usuario', type: 'string' }
    ]
});
