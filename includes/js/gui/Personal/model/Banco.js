/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Banco', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'cod_ban', type: 'int' },
        { name: 'des_ban', type: 'string'}
    ]
});
