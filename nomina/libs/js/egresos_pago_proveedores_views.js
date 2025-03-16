App.views.SaldoPendientePorCliente = Backbone.View.extend({

	tagName: "tr",

	template: _.template($('#template-caja-saldoPendiente').html()),
	events: {
		"blur input.a_pagar" : "changeInputAPagar",
		"change input.a_pagar" : "changeInputAPagar",
		"keypress input.a_pagar" : "keypressInputAPagar"
	},

	initialize: function() {
		// this.listenTo(this.model, "change", this.render);
	},

	changeInputAPagar: function(ev) {
		var value = parseFloat($(ev.currentTarget).val());
		if(_.str.isBlank(value)) {
			value=0;
		}

		if (!_.isNaN(value) && _.isNumber(value)) {
			value = value.toFixed(2);
			this.model.set("a_pagar", value);
			$(ev.currentTarget).val(value);
			$("input[name='total_cobro']").val( this.totalizarMontosxPagar_() );
		}else{
			this.model.set("a_pagar", 0);
			$(ev.currentTarget).val('0.00').focus();
			$("input[name='total_cobro']").val( this.totalizarMontosxPagar_() );
		}
	},

	keypressInputAPagar: function(ev) {
		var codeEnter = ev.charCode || ev.keyCode;
		var value = 0;
		if (codeEnter == 13) {
			value = parseFloat($(ev.currentTarget).val());

			if(_.str.isBlank(value)) {
				value=0;
			}

			if (!_.isNaN(value) && _.isNumber(value)) {
				value = value.toFixed(2);
				this.model.set("a_pagar", value);
				$(ev.currentTarget).val(value);
				$("input[name='total_cobro']").val( this.totalizarMontosxPagar_() );
			}else{
				this.model.set("a_pagar", 0);
				$(ev.currentTarget).val('0.00').focus();
				$("input[name='total_cobro']").val( this.totalizarMontosxPagar_() );
			}
		}

	},

	totalizarMontosxPagar_: function() {
		var suma_montos = 0;
		this.collection.each(function(model) { 
			suma_montos += parseFloat(model.get("a_pagar"));  
		});
		return suma_montos.toFixed(2);

	},

	render: function() {
		this.$el.html(this.template(this.model.toJSON()));
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
			}else{
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

		if(caseValidationA||caseValidationB) {
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


App.views.BancoCuentaTr = Backbone.View.extend({

	tagName: "tr",

	template: _.template($('#template-tabla_movimiento-tr').html()),

	events: {
		"change input[name='fecha_movimiento'], input[name='a_orden_de']" : "updateField",
		"blur input[name='numero']" : "updateFieldNumero",
		// "change input[name='numero'], input[name='fecha_movimiento'], input[name='a_orden_de']" : "updateField",
		"blur input[name='importe']": "blurInputMonto",
		"keypress input[name='importe']": "keypressInputMonto",
		"click a.agregar-fila": "agregarMovimiento",
		"click a.eliminar-fila": "eliminarMovimiento",
	},

	initialize: function(opt) {
		this.modelParent = opt.modelParent;
	},

	validarChequeIngresado: function(numeroCheque){
		var cod_tesor_bandodet = $("select[name='cod_banco'] :selected").data("cod-tesor-bandodet");
		this__ = this;
		$.ajax({
	        	url:  "../../libs/php/ajax/ajax.php",
				type: "GET",
				data: {
					opt: 'verificarChequePorTipoCuentaBanco',
					"cod_tesor_bandodet": cod_tesor_bandodet,
					"numeroCheque": numeroCheque
				},
				complete: function(xhr, status){
					var response = xhr.responseText;
					var reg_ultimo_cheque_activo = {};
					if(!_.str.isBlank(response)) {
							var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
						reg_ultimo_cheque_activo = JSON.parse(response);
						this__.model.set("numero", reg_ultimo_cheque_activo[0].cheque);//-reg_ultimo_cheque_activo[0].cheque
						this__.model.set("cod_cheque", reg_ultimo_cheque_activo[0].cod_cheque);//reg_ultimo_cheque_activo[0].cheque
						this__.model.set("cod_chequera", reg_ultimo_cheque_activo[0].cod_chequera);
						this__.model.set("a_orden_de", $("#descripcion_proveedor").val());
						this__.model.set("importe", monto_total_pago.toFixed(2));
					}else{
						alert("El numero de cheque no esta disponible.");
						this__.$el.find("input[name='numero']").val(this__.model.get("numero"))
					}
				}
		});
	},

	updateFieldNumero: function(ev) {
		var currentTarget = $(ev.currentTarget);
		var nombreElemento  = currentTarget.attr("name");
		var value = currentTarget.val();

		if(this.validadorCampo_(currentTarget)){
			if( currentTarget.data("tipo-campo") == "numero" ) {
				value = parseFloat(value);
				this.validarChequeIngresado(value);
			}
		} else {
			alert("Valor invalido");
			if( currentTarget.data("tipo-campo") == "numero" ) {
				currentTarget.val(0);
			} 
			currentTarget.focus();
		}
		
	},

	updateField: function(ev) {
		var currentTarget = $(ev.currentTarget);
		var nombreElemento  = currentTarget.attr("name");
		var value = currentTarget.val();

		if(this.validadorCampo_(currentTarget)){
			if (currentTarget.data("tipo-campo") == "fecha" ) {
				value = moment(value,"DD/MM/YYYY").format("YYYY-MM-DD")
			}

			this.model.set(nombreElemento, value);
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
			if(_.isNaN(value_) || !_.isNumber(value_)) return false;
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
		var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());

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
		var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
		var sumTotal = 0;
		this.collection.each(function(model, i) { 
			value = parseFloat(model.get("importe"));
			sumTotal += value;
		});

		if (sumTotal<monto_total_pago) {
			this.agregarFila_();
		}
	},

	eliminarMovimiento: function(){
		this.collection.remove(this.model);
		this.calculoTotal_();
		this.remove();
	},

	agregarFila_: function(){
		var validador = true;
		if(_.str.isBlank(this.model.get("numero"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("importe"))){
			validador=false;
		}
		if(_.str.isBlank(this.model.get("fecha_movimiento"))){
			validador=false;
		}

		if (validador) {
			this.collection.add(new App.models.banco());
		} else {
			alert("Debe especificar todos los campos");
		}
		
	},

	updateField_: function(value, input) {
		var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
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

			// if (sumTotal<monto_total_pago) {
			// 	this.agregarFila_();
			// }

		} else {
			this.model.set("importe", 0);
			$(input).val('0.00');
			alert("Monto Superior, verifique");
		}
		this.calculoTotal_();
	},

	validadorMontoPago_: function(input) {
		var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
		var monto_total_acumulado = parseFloat($("input[name='monto_total_acumulado']").val());
		var monto_total_saldo_restante = parseFloat($("input[name='monto_total_saldo_restante']").val());

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

		var caseValidationA = ( sumTotal+currentValue > monto_total_pago );

		if(caseValidationA) {
			return false;
		}

		return true;

	},

	calculoTotal_: function() {
		var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
		var monto_total_acumulado = parseFloat($("input[name='monto_total_acumulado']").val());
		var monto_total_saldo_restante = parseFloat($("input[name='monto_total_saldo_restante']").val());
		var total_acumulado = 0;
		var total_saldo = 0;

		this.collection.each(function(model, i) { 
			value = parseFloat(model.get("importe"));
			total_acumulado += value; 
		});

		total_saldo = monto_total_pago-total_acumulado;

		$("input[name='monto_total_acumulado']").val(total_acumulado.toFixed(2));
		$("input[name='monto_total_saldo_restante']").val(total_saldo.toFixed(2));

		this.modelParent.movimientos_bancarios.set("monto_total_acumulado", total_acumulado.toFixed(2));
		this.modelParent.movimientos_bancarios.set("monto_total_saldo_restante",total_saldo.toFixed(2));
	},

	render: function() {

		this.model.set("fila_unica", (this.collection.length==1) ? true : false );

		this.$el.html(this.template(this.model.toJSON()));
		return this;
	}
});

App.views.BancoCuenta = Backbone.View.extend({
	template: _.template($('#template-contenedor-panel-movimientos-bancarios-modal').html()),

	events: {
		"change select[name='cod_banco']" : "changeSelect",
		"blur input[name='descripcion_causa']" : "updateFieldCausa"
	},

	initialize: function(opt) {
		this.modelParent = opt.modelParent;
		var self = this;
		var previous_model={};
		this.collection.on("add", function(modelCurrent){

			if(this.length>1){
				var cod_tesor_bandodet = self.$el.find(":selected").data("cod-tesor-bandodet");
				var correlativos = "";
				var selfCollection = this;
				var num_cheques_excluir = "";

				//Obtener el siguiente correlativo del cheque
				_.each(this.models, function(model, index){ 
					var separador="";
					if(model.get("numero")>0){ 
						model_= selfCollection.at(index+1);
						separador = (model_.get("numero")!="0") ? "," : "";
						num_cheques_excluir += model.get("numero")+separador;
					} 
				});

				$.ajax({
		        	url:  "../../libs/php/ajax/ajax.php",
					type: "GET",
					data: {
						opt: 'chequesPorTipoCuentaBanco',
						"cod_tesor_bandodet": cod_tesor_bandodet,
						"num_cheques_excluir": num_cheques_excluir
					},
					complete: function(xhr, status){
						var response = xhr.responseText;
						var reg_ultimo_cheque_activo = {};
						if(!_.str.isBlank(response)){
							var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
							reg_ultimo_cheque_activo = JSON.parse(response);
							modelCurrent.set("numero", reg_ultimo_cheque_activo[0].cheque);
							modelCurrent.set("cod_cheque", reg_ultimo_cheque_activo[0].cod_cheque);
							modelCurrent.set("cod_chequera", reg_ultimo_cheque_activo[0].cod_chequera);
							modelCurrent.set("a_orden_de", $("#descripcion_proveedor").val());
							modelCurrent.set("importe", monto_total_pago.toFixed(2));
							self.agregarRow(modelCurrent);
						}
					}
				});
			}else{
				self.agregarRow(modelCurrent);
			}
		});
	},

	agregarRow: function(modelCurrent){
		var BancoTr = new App.views.BancoCuentaTr({
					model: modelCurrent,
					modelParent: this.modelParent,
					collection: App.collections.instancias.bancos
		});

		this.$el.find(".template-tabla_movimientos tbody").append(BancoTr.render().el);

		$(BancoTr.el).find("input[name='fecha_movimiento']").datepicker({
	        changeMonth: true,
	        changeYear: true,
	        showOtherMonths:true,
	        selectOtherMonths: true,
	        numberOfMonths: 1,
	        dateFormat: "dd/mm/yy"
	    });
	},

	changeSelect: function(ev) {
		var currentTarget = $(ev.currentTarget);
		var cod_banco = currentTarget.val();
		var cod_tesor_bandodet = currentTarget.find(":selected").data("cod-tesor-bandodet");
		var montoTotal = parseFloat(this.modelParent.get("monto"));

		this.modelParent.movimientos_bancarios.set("cod_banco", cod_banco);
		this.modelParent.movimientos_bancarios.set("cod_tesor_bandodet", cod_tesor_bandodet);

		App.models.instancias.banco = new App.models.banco();
		self_BancoCuenta = this;

		this.collection.remove(this.collection.models);
		this.$el.find(".template-tabla_movimientos tbody tr").remove();
		this.$el.find("input[name='monto_total_pago']").val(montoTotal.toFixed(2));
		this.$el.find("input[name='monto_total_acumulado']").val(montoTotal.toFixed(2));

		if(cod_tesor_bandodet) {
			$.ajax({
	         url:  "../../libs/php/ajax/ajax.php",
				type: "GET",
				data: {
					opt: 'chequesPorTipoCuentaBanco',
					"cod_tesor_bandodet": cod_tesor_bandodet
					// "num_cheques_excluir": num_cheques_excluir
				},
				complete: function(xhr, status){
					var response = xhr.responseText;
					var reg_ultimo_cheque_activo = {};
					if(!_.str.isBlank(response)){
							var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
						reg_ultimo_cheque_activo = JSON.parse(response);
						App.models.instancias.banco.set("numero", reg_ultimo_cheque_activo[0].cheque);
						App.models.instancias.banco.set("cod_cheque", reg_ultimo_cheque_activo[0].cod_cheque);
						App.models.instancias.banco.set("cod_chequera", reg_ultimo_cheque_activo[0].cod_chequera);
						App.models.instancias.banco.set("a_orden_de", $("#descripcion_proveedor").text());
						App.models.instancias.banco.set("importe", monto_total_pago.toFixed(2));
						self_BancoCuenta.collection.add(App.models.instancias.banco);
					}
				}
			});
		}
	},

	updateFieldCausa: function(ev){
		var currentTarget = $(ev.currentTarget);
		var value = currentTarget.val();
		this.modelParent.movimientos_bancarios.set("descripcion_causa", value);
	},

	render: function() {
		var bancos = JSON.parse($("input[name='listaBancos']").val());
		var self = this;

		this.$el.html(this.template());
		
		_.each(bancos, function(banco){
			descripcion = banco.descripcion_banco + ", " + banco.descripcion_cuenta + " Nro. Cuenta: " + banco.nro_cuenta;
			$(self.el).find("select[name='cod_banco']").append("<option data-cod-tesor-bandodet='"+banco.cod_tesor_bandodet+"' value='"+banco.cod_banco+"'>"+descripcion+"</option>");
		});

		var montoTotal = parseFloat(this.modelParent.get("monto"));

		$(this.el).find("input[name='monto_total_pago']").val(montoTotal.toFixed(2));
		$(this.el).find("input[name='monto_total_acumulado']").val(montoTotal.toFixed(2));

		var lb_forma_pago = _.str.sprintf("%s %s",this.modelParent.get("cuenta_contable"),this.modelParent.get("descripcion"));
		
		$(this.el).find("#lb_forma_pago").text(lb_forma_pago);	

		if(bancos.length>0){ 
			this.modelParent.movimientos_bancarios.set("cod_banco", bancos[0].cod_banco);
			this.modelParent.movimientos_bancarios.set("cod_tesor_bandodet", bancos[0].cod_tesor_bandodet);
		}

		this.modelParent.movimientos_bancarios.set("monto_total_pago", montoTotal.toFixed(2));
		this.modelParent.movimientos_bancarios.set("monto_total_acumulado", montoTotal.toFixed(2));
		this.modelParent.movimientos_bancarios.set("monto_total_saldo_restante",0);

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
		"click input[name='seleccion']": "seleccionado"
	},

	initialize: function() {
		var self = this;
		this.model.on("change", function(){
			if (self.model.hasChanged("seleccionado")) {
				self.calculoTotal_();
			}

		})
	},

	seleccionado: function(ev) {
		console.log("changed")
		var currentTarget = $(ev.currentTarget);
		var nombreElemento  = currentTarget.attr("name");
		var value = currentTarget.is(":checked");

		this.model.set("seleccionado", value);
	},

	calculoTotal_: function() {
		var monto_total_cheque = parseFloat($("input[name='monto_total_cheque']").val());
		var monto_total_acumulado = parseFloat($("input[name='monto_total_acumulado']").val());

		var monto_total_cheque_saldo = parseFloat($("input[name='monto_total_cheque_saldo']").val());
		var total_acumulado = 0;
		var total_saldo = 0;
		var value = 0;

		var esModelSeleccionado = false;
		this.collection.each(function(model, i) {
			value = parseFloat(model.get("importe"));
			if (model.get("seleccionado")) {
				esModelSeleccionado=true;
				total_acumulado += parseFloat(model.get("importe"));
			}
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

