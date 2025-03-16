App.models.otros_egresos = Backbone.Model.extend({
	defaults: {
		causa:'',
		importe:0,
		imputacion: '',
	}
});

App.collections.otros_egresos = Backbone.Collection.extend({
	model: App.models.otros_egresos
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
		cheques_:{},
		movimientos_bancarios:{}
	},
	initialize: function(){
		this.cheques = new App.collections.cheques;
		this.movimientos_bancarios = {};
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
		seleccionado: false,
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

App.models.movimientos_bancarios = Backbone.Model.extend({
	defaults: {
		cod_banco: 0,
		cod_tesor_bandodet: 0,
		tipo_movimiento:'',
		movimientos: {},
		monto_total_pago:'',
		monto_total_acumulado:'0.00',
		monto_total_saldo_restante:'0.00',
		descripcion_causa:''
	}, 
	initialize: function(){
		this.movimientos = {};
	}
});

App.models.banco = Backbone.Model.extend({
	defaults: {
		numero: 0,
		cod_cheque:0,
		fecha_movimiento: new Date().format("Y-m-d"),
		a_orden_de:'',
		importe:'0.00'
	}
});

App.collections.bancos = Backbone.Collection.extend({
	model: App.models.banco,
});