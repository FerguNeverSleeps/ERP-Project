
Ext.define('Ciclos.model.Ciclo', {
    extend: 'Ext.data.Model',

   requires: [
        'Ext.data.field.Integer',
        'Ext.data.field.String',
        'Ext.data.field.Date'
    ],

    fields: [
        {
            type: 'int',
            name: 'codnom'
        },
        {
            type: 'string',
            name: 'descrip'
        },
        {
            type: 'date',
            name: 'fechapago'
        },
        {
            type: 'date',
            name: 'periodo_ini'
        },
        {
            type: 'date',
            name: 'periodo_fin'
        },
        {
            type: 'int',
            name: 'codtip'
        },
        {
            type: 'string',
            name: 'status'
        }
    ]
});
