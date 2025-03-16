/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Situacion', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'codigo', type: 'int' },
        { name: 'situacion', type: 'string'}
    ]
});
