/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Turno', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'turno_id', type: 'int' },
        { name: 'descripcion', type: 'string'},
        { name: 'entrada', type: 'string'},
        { name: 'salida', type: 'string'}
    ]
});
