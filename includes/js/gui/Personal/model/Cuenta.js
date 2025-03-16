Ext.define('Personal.model.Cuenta', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'Cuenta', type: 'string' },
		{name:'id', type: 'int'},
		{name:'Descrip', type: 'string'}
	]
});