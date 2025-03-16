App.models.IngresoCambioCheques = Backbone.Model.extend({
	defaults: {
		causa:'',
		importe:0,
		imputacion: '',
	}
});

App.collections.IngresoCambioCheques = Backbone.Collection.extend({
	model: App.models.IngresoCambioCheques,
});

App.models.forma_pago = Backbone.Model.extend({
	defaults: {
		codigo: 0,
		cuenta_contable: '',
		descripcion: '',
		id_caja_tp_concepto: 0,
		id_caja_tp_registro: 0,
		id_forma_pago: 0,
		siglas:'',
		monto:'0.00',
		actualizado:false,
		cheques_: {},
		bancos_: {}
	},	
	initialize: function(){
		this.cheques = new App.collections.cheques;
		this.bancos = {};
	}
});

App.collections.forma_pago = Backbone.Collection.extend({
	model: App.models.forma_pago,
	url: function(){
		return '../../libs/php/ajax/ajax.php';
	}
});

App.models.cheque = Backbone.Model.extend({
	defaults: {
		numero: 0,
		fecha_cheque: new Date().format("Y-m-d"),
		secuencia:0,
		banco:'',
		localidad:'',
		importe:'0.00',
		local:'Si'
	}
});

App.collections.cheques = Backbone.Collection.extend({
	model: App.models.cheque,
	url: function(){
		return '../../libs/php/ajax/ajax.php';
	}
});

App.models.banco = Backbone.Model.extend({
	defaults: {
		cod_banco: 0,
		comprobante: 0,
		fecha_deposito_bancario: new Date().format("Y-m-d"),
		concepto:'',
		importe:'0.00',
	}
});

App.collections.bancos = Backbone.Collection.extend({
	model: App.models.banco,
});