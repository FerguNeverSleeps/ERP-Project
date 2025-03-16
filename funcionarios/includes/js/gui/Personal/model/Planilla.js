/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Planilla', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'codtip', type: 'int' },
        { name: 'descrip', type: 'string'},
        { name: 'codnom', type: 'int' },
        { name: 'tipo_ingreso', type: 'string'}
    ]
});
