App.views.OtrosIngresos = Backbone.View.extend({
	tagName: "tr",

	template: _.template($('#template-otros_egresos').html()),

	events: {
		"click a.agregar": "agregarRenglon",
		"click a.eliminar": "eliminarRenglon",
		"blur input[name='importe']" : "changeInputMonto",
		"keypress input[name='importe']" : "keypressInputMonto",
		"change select[name='imputacion']" : "actualizarImputacionContable",
		"change input[name='causa']" : "actualizarCausa"
	},

	initialize: function() {
		// this.listenTo(this.model, "change", this.render);
	},

	agregarRenglon: function(ev) {
		ev.preventDefault();
		App.collections.instancias.otros_ingresos.add(new App.models.otros_ingresos());
	},

	eliminarRenglon: function(ev) {
		ev.preventDefault();
		this.collection.remove(this.model); 
		this.remove();
	},	

	actualizarCausa: function(ev) {
		var value = $(ev.currentTarget).val();
		this.model.set("causa", value);
	},

	actualizarImputacionContable: function(ev){
		var cuenta_contable_seleccionada = $(ev.currentTarget).find(":selected").val();
		this.model.set("imputacion", cuenta_contable_seleccionada);
	},

	changeInputMonto: function(ev) {
		this.actualizarMonto_(ev);
	},

	actualizarMonto_: function(ev) {
		value = parseFloat($(ev.currentTarget).val());

		if(_.str.isBlank(value)) {
			value=0;
		}

		if (!_.isNaN(value) && _.isNumber(value)) {
			value = value.toFixed(2);
			this.model.set("importe", value);
			$(ev.currentTarget).val(value);
			$("input[name='total_cobro']").val( this.totalizarMontosxPagar_() );
		}else{
			this.model.set("importe", 0);
			$(ev.currentTarget).val('0.00').focus();
			$("input[name='total_cobro']").val( this.totalizarMontosxPagar_() );
		}
	},

	keypressInputMonto: function(ev) {
		var codeEnter = ev.charCode || ev.keyCode;
		var value = 0;
		if (codeEnter == 13) {
			this.actualizarMonto_(ev)
		}

	},

	totalizarMontosxPagar_: function() {
		var suma_montos = 0;
		this.collection.each(function(model) { 
			suma_montos += parseFloat(model.get("importe"));  
		});
		return suma_montos.toFixed(2);

	},

	render: function() {
		var cuentas_contables = JSON.parse($("input[name='campos_cuentas_cant']").val());

		var self = this;
		this.$el.html(this.template(this.model.toJSON()));
		_.each(cuentas_contables, function(registro){
			self.$el.find("select").append("<option value='"+registro.cuenta+"'>"+registro.cuenta+" "+registro.Descrip+"</option>");
		});

		if(cuentas_contables.length>0){
			this.model.set("imputacion",cuentas_contables[0].cuenta);
		}

		return this;

	}
});

App.views.TipoFormaPago = Backbone.View.extend({

	tagName: "tr",

	template: _.template($('#template-tipo-forma-pago-tr').html()),

	events: {
		"blur input.monto" : "blurInputMonto",
		"keypress input.monto" : "keypressInputMonto"
	},

	initialize: function() {},

	blurInputMonto: function(ev) {
		var value = 0;
		var currentTarget = $(ev.currentTarget);
		value = parseFloat($(currentTarget).val());
		if (parseFloat(this.model.get("monto"))!=value) {
			this.updateField_(value, currentTarget);
		}
	},

	keypressInputMonto: function(ev) {
		var codeEnter = ev.charCode || ev.keyCode;
		var currentTarget = $(ev.currentTarget);
		var value = 0;
		if (codeEnter == 13) {
			value = parseFloat($(currentTarget).val());
			if (parseFloat(this.model.get("monto"))!=value) {
				this.updateField_(value, currentTarget);
			}
		}
	},

	updateField_: function(value, input) {
		if(_.str.isBlank(value)) {
			value=0;
		}

		if(this.validadorMontoPago_(input)) 
		{
			if ( !_.isNaN(value) && _.isNumber(value) ) {
				value = value.toFixed(2);

				this.model.set("monto", value);
				$(input).val(value);
			} else {
				this.model.set("monto", 0);
				$(input).val('0.00').focus();
			}
		} else {
			this.model.set("monto", 0);
			$(input).val('0.00');
			alert("Monto Superior, verifique");
		}
		this.calculoTotalaPagar_();
	},

	validadorMontoPago_: function(input) {
		var total_verificacion = 0;
		var total_efectivo = 0;
		var monto_total_pagar = parseFloat($("input[name='monto_total_pagar']").val());
		var total_efectivo_verificador = parseFloat($("input[name='monto_total_efectivo']").val());
		var monto_total_importe_acumulado = parseFloat($("input[name='monto_total_importe_acumulado']").val());
		var monto_total_cambio = parseFloat($("input[name='monto_total_cambio']").val());
		var currentValue = parseFloat($(input).val());

		if(currentValue==0) {
			return true;
		}

		if( $(input).data("concepto-referencia")==App.constante.EFECTIVO) {
			return true;
		}
		
		var sumTotal = 0;
		var self = this;
		this.collection.each(function(model, i) { 
			value = parseFloat(model.get("monto"));
			if ( model.get("id_caja_tp_concepto")!=App.constante.EFECTIVO && (self.model != model)) {
				sumTotal += value;
			} 
		});

		var caseValidationA = ( monto_total_cambio>0 );
		var caseValidationB = ( sumTotal+currentValue > monto_total_pagar );

		if(caseValidationA || caseValidationB) {
			return false;
		}

		return true;

	},

	calculoTotalaPagar_: function() {
		var total_importe_acumulado=0;
		var total_efectivo = 0;
		var total_restante = 0;
		var monto_total_cambio = 0;
		var total_verificacion = 0;
		var total_efectivo_verificador = parseFloat($("input[name='monto_total_efectivo']").val());

		this.collection.each(function(model, i) { 
			value = parseFloat(model.get("monto"));

			total_importe_acumulado += value;

			if (model.get("id_caja_tp_concepto")==App.constante.EFECTIVO ) {
				total_efectivo += value;
			} 
			
			total_verificacion += value; 
			
		});

		var monto_total_pagar = parseFloat($("input[name='monto_total_pagar']").val());

		total_restante = monto_total_pagar-total_importe_acumulado;

		monto_total_cambio = total_importe_acumulado-monto_total_pagar;

		if(total_restante<0) {
			total_restante=0;
		}

		if(monto_total_cambio<0){
			monto_total_cambio=0;
		}

		$("input[name='monto_total_importe_acumulado']").val(total_importe_acumulado.toFixed(2));
		$("input[name='monto_total_saldo_restante']").val(total_restante.toFixed(2));
		$("input[name='monto_total_efectivo']").val(total_efectivo.toFixed(2));
		$("input[name='monto_total_cambio']").val(monto_total_cambio.toFixed(2));

	},

	render: function() {
		this.$el.html(this.template(this.model.toJSON()));
		return this;

	}
});

App.views.BancoCuenta = Backbone.View.extend({
	template: _.template($('#template-contenedor-panel-deposito-banco-cuenta-modal').html()),

	events: {
		"change input" : "changeInput",
		"blur input" : "changeInput",
		"change select[name='cod_banco']" : "changeSelect"
	},
	changeSelect: function(ev){
		var cod_banco_seleccionado = $(ev.currentTarget).val();
		var cod_tesor_bandodet = $(ev.currentTarget).find(":selected").data("cod_tesor_bandodet")
		this.model.set("cod_banco",cod_banco_seleccionado);
		this.model.set("cod_tesor_bandodet", cod_tesor_bandodet);
	},
	changeInput: function(ev) {
		var currentTarget = $(ev.currentTarget);
		var nombreElemento  = currentTarget.attr("name");
		var value = currentTarget.val();

		if (currentTarget.data("tipo-campo") == "fecha" ) {
			value =  moment(value,"DD/MM/YYYY").format("YYYY-MM-DD");
		}

		if( currentTarget.data("tipo-campo") == "numero" ) {
			var value_ = parseFloat(value);
			if(_.isNaN(value_)) value = 0;
			if(!_.isNumber(value_)) value = 0;
			value = parseFloat(value);
			currentTarget.val(value);
		}

		this.model.set(nombreElemento, value);
	},

	initialize: function() {},

	render: function() {
		var bancos = JSON.parse($("input[name='listaBancos']").val());
		var self = this;

		this.$el.html(this.template(this.model.toJSON()));
		
		_.each(bancos, function(banco){
			$(self.el).find("select[name='cod_banco']").append("<option data-cod_tesor_bandodet='"+banco.cod_tesor_bandodet+"' value='"+banco.cod_banco+"'>"+banco.descripcion+"  (Nro. de Cuenta: "+banco.nro_cuenta+")</option>");
		});

		if(bancos.length>0){ 
			this.model.set("cod_banco",bancos[0].cod_banco);
			this.model.set("cod_tesor_bandodet",bancos[0].cod_tesor_bandodet);
		}
		
		return this;

	}
});

App.views.Cheque = Backbone.View.extend({
	template: _.template($('#template-contenedor-panel-cheque-modal').html()),


	initialize: function() {},

	render: function() {
		this.$el.html(this.template());
		return this;

	}

});

App.views.ChequeTr = Backbone.View.extend({
	template: _.template($('#template-contenedor-panel-cheque-tr').html()),

	tagName: "tr",

	events: {
		"change input[type='text'],select[name='local']:not(input[name='importe'])" : "actualizarCampo",
		"blur input[name='importe']": "blurInputMonto",
		"keypress input[name='importe']": "keypressInputMonto",
		"click a.agregar-fila": "agregarMovimiento",
		"click a.eliminar-fila": "eliminarMovimiento",
	},

	initialize: function() {},

	actualizarCampo: function(ev) {
		var currentTarget = $(ev.currentTarget);
		var nombreElemento  = currentTarget.attr("name");
		var value = currentTarget.val();
		if(this.validadorCampo_(currentTarget)){
			if (currentTarget.data("tipo-campo") == "fecha" ) {
				value = moment(value,"DD/MM/YYYY").format("YYYY-MM-DD")
			}

			if( currentTarget.data("tipo-campo") == "numero" ) {
				value = parseFloat(value);
				currentTarget.val(value);
			}

			this.model.set(nombreElemento, value);
		} else {
			alert("Valor invalido");

			if( currentTarget.data("tipo-campo") == "numero" ) {
				currentTarget.val(0);
			} 
			
			currentTarget.focus();
		}
		
	},

	validadorCampo_: function(campo) {
		var nombreElemento  = campo.attr("name");
		var value = campo.val();
		if(campo.data("requerido") && _.str.isBlank(value)){
			return false;
		}

		if( campo.data("tipo-campo") == "numero" ){
			var value_ = parseFloat(value);
			if(_.isNaN(value_)) return false;
			if(!_.isNumber(value_)) return false;
		}

		return true;

	},

	blurInputMonto: function(ev) {
		var value = 0;
		var currentTarget = $(ev.currentTarget);
		value = parseFloat($(currentTarget).val());
		this.updateField_(value, currentTarget);
	},

	keypressInputMonto: function(ev) {
		var monto_total_cheque = parseFloat($("input[name='monto_total_cheque']").val());

		var codeEnter = ev.charCode || ev.keyCode;
		var currentTarget = $(ev.currentTarget);
		var value = 0;
		if (codeEnter == 13) {
			value = parseFloat($(currentTarget).val());
			if (parseFloat(this.model.get("importe"))!=value) {
				this.updateField_(value, currentTarget);
			}
		}
	},

	agregarMovimiento: function() {
		var monto_total_cheque = parseFloat($("input[name='monto_total_cheque']").val());
		var sumTotal = 0;
		this.collection.each(function(model, i) { 
			value = parseFloat(model.get("importe"));
			sumTotal += value;
		});

		if (sumTotal<monto_total_cheque) {
			this.agregarFila_();
		}
	},

	eliminarMovimiento: function(){
		this.collection.remove(this.model);
		this.calculoTotal_();
		this.remove();
	},

	agregarFila_: function() {
		var validador = true;
		if(_.str.isBlank(this.model.get("banco"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("fecha_cheque"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("fila_unica"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("importe"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("local"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("localidad"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("numero"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("secuencia"))){
			validador=false;
		}

		if (validador) {
			this.collection.add(new App.models.cheque());
		} else {
			alert("Debe especificar todos los campos");
		}
		
	},

	updateField_: function(value, input) {
		var monto_total_cheque = parseFloat($("input[name='monto_total_cheque']").val());
		if(_.str.isBlank(value)) {
			value=0;
		}

		if(this.validadorMontoPago_(input)) 
		{
			if ( !_.isNaN(value) && _.isNumber(value) ) {
				value = value.toFixed(2);

				this.model.set("importe", value);
				$(input).val(value);
			} else {
				this.model.set("importe", 0);
				$(input).val('0.00').focus();
			}

			var sumTotal = 0;
			this.collection.each(function(model, i) { 
				value = parseFloat(model.get("importe"));
				sumTotal += value;
			});

			if (sumTotal<monto_total_cheque) {
				this.agregarFila_();
			}

		} else {
			this.model.set("importe", 0);
			$(input).val('0.00');
			alert("Monto Superior, verifique");
		}
		this.calculoTotal_();
	},

	validadorMontoPago_: function(input) {
		var monto_total_cheque = parseFloat($("input[name='monto_total_cheque']").val());
		var monto_total_acumulado = parseFloat($("input[name='monto_total_acumulado']").val());
		var monto_total_cheque_saldo = parseFloat($("input[name='monto_total_cheque_saldo']").val());

		var currentValue = parseFloat($(input).val());

		if(currentValue==0) {
			return true;
		}
		
		var sumTotal = 0;
		var self = this;
		this.collection.each(function(model, i) { 
			value = parseFloat(model.get("importe"));
			if ( self.model != model ) {
				sumTotal += value;
			} 
		});

		var caseValidationA = ( sumTotal+currentValue > monto_total_cheque );

		if(caseValidationA) {
			return false;
		}

		return true;

	},

	calculoTotal_: function() {
		var monto_total_cheque = parseFloat($("input[name='monto_total_cheque']").val());
		var monto_total_acumulado = parseFloat($("input[name='monto_total_acumulado']").val());
		var monto_total_cheque_saldo = parseFloat($("input[name='monto_total_cheque_saldo']").val());
		var total_acumulado = 0;
		var total_saldo = 0;

		this.collection.each(function(model, i) { 
			value = parseFloat(model.get("importe"));
			total_acumulado += value; 
		});

		total_saldo = monto_total_cheque-total_acumulado;

		$("input[name='monto_total_acumulado']").val(total_acumulado.toFixed(2));
		$("input[name='monto_total_cheque_saldo']").val(total_saldo.toFixed(2));

	},

	render: function() {

		this.model.set("fila_unica", (this.collection.length==1) ? true : false );

		this.$el.html(this.template(this.model.toJSON()));

		return this;

	}

});

