/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Cargo', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'cod_car', type: 'int' },
        { name: 'des_car', type: 'string'},
        { name: 'ee', type: 'int'},
        { name: 'markar', type: 'int'}
    ]
});
