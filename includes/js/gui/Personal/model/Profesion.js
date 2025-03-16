/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Profesion', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'codorg', type: 'int' },
        { name: 'descrip', type: 'string'},
        { name: 'ee', type: 'int' }
    ]
});
