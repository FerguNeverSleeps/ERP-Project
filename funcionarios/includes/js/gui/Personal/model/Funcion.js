/**
 * This entity represents an organization which is a container for projects, users and
 * groups.
 */
Ext.define('Personal.model.Funcion', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'nomfuncion_id', type: 'int' },
        { name: 'descripcion_funcion', type: 'string'}
        //{ name: 'ee', type: 'int' }
    ]
});
