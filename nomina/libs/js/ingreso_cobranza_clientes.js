App.collections.instancias.collection_caja_ing_cob_sal = new App.collections.caja_ing_cob_sal_x_cli();
App.models.instancias.model_caja_ing_cob_sal = new App.models.caja_ing_cob_sal_x_cli();

var ViewTipoFormaPago = {};

App.collections.instancias.collection_caja_ing_cob_sal.add( App.models.instancias.model_caja_ing_cob_sal );

var ViewSaldoPendientePorCliente = {};

App.collections.instancias.forma_pago = new App.collections.forma_pago();
App.models.instancias.forma_pago = new App.models.forma_pago();


App.models.instancias.cheque = new App.models.cheque();
App.collections.instancias.cheques = new App.collections.cheques();

App.collections.instancias.bancos = new App.collections.bancos();

// Run
App.limpiarPanelTotales = function(){
	$("#template-tabla_forma_pagos").find("tbody").html("");

	var total_cobro = parseFloat($("input[name='total_cobro']").val());

	$("input[name='monto_total_pagar']").val(total_cobro.toFixed(2));
	$("input[name='monto_total_importe_acumulado']").val("0.00");
	$("input[name='monto_total_saldo_restante']").val(total_cobro.toFixed(2));
	$("input[name='monto_total_efectivo']").val("0.00");
	$("input[name='monto_total_cambio']").val("0.00");
}

App.collections.instancias.forma_pago.on("sync", function(currentCollection){

	App.limpiarPanelTotales();
	currentCollection.each(function(currentModel, i){
		ViewTipoFormaPago = new App.views.TipoFormaPago({
										model: currentModel, 
										collection: currentCollection
									});

		$("#template-tabla_forma_pagos").find("tbody").append(ViewTipoFormaPago.render().el);
	});
	
	mostarVentana({
    		title:"Forma de Pago",
    		contentEl: "contenedor-panel-forma-pago-modal",
    		width: 460,
    		height: 400,
    		labelOk: 'Guardar',
    		accionOk: function(){
    			App.variablesGlobales.currentPosicionModelosFormaPago=0;
    			App.eventos.guardar();
    		}
    });

});



App.collections.instancias.collection_caja_ing_cob_sal.on("sync", function(currentCollection){
	// Limpiar tbody
	$("#tabla_saldos_pendientes").find("tbody tr").remove();
	$("input[name='total_cobro']").val("0.00");
	
	// Caso 1: Cargar un asiento por defecto
	currentCollection.add(App.models.instancias.model_caja_ing_cob_sal);

	ViewSaldoPendientePorCliente = new App.views.SaldoPendientePorCliente({
														model: App.models.instancias.model_caja_ing_cob_sal, 
														collection: currentCollection
													});

	$("#tabla_saldos_pendientes").find("tbody").append(ViewSaldoPendientePorCliente.render().el);

	// Caso 2: Si el length es mayor a uno quiere decir que el cliente seleccionado tiene asientos.
	//  	   De lo contrario solo Caso 1.
	if ( App.collections.instancias.collection_caja_ing_cob_sal.length > 1 ){
		currentCollection.each(function(currentModel, i){

			if ( !_.isUndefined(currentModel.get("id")) ) { // no cargar valor por defecto.
				ViewSaldoPendientePorCliente = new App.views.SaldoPendientePorCliente({
											model: currentModel, 
											collection: currentCollection
										});

				$("#tabla_saldos_pendientes").find("tbody").append(ViewSaldoPendientePorCliente.render().el);
			}
		});

	}
	
	
});

var mostarVentana = function(param){

	var viewExterno = (!_.isUndefined(param.contentEl)) ? true : false;

	var winClave = new Ext.Window({
	    title: param.title,
	    height: param.height,
	    width: param.width,
	    autoScroll:true,
	    modal:true,
	    bodyStyle:'padding-right:10px;padding-left:10px;padding-top:5px;background:white;',
	    closeAction:'hide',
	    contentEl: param.contentEl,
	    buttonAlign:'center',
	    buttons:[
		    {
		        text: param.labelOk,
		        icon: '../../libs/imagenes/save.gif',
		        handler:function()
		        {
		        	param.accionOk(winClave);
		        }
		    },
		    {
		        text:'Cerrar',
		        icon: '../../libs/imagenes/cancel.gif',
		        handler:function()
		        {
		            winClave.hide();
		        }
		    }
	    ]
	}).show();

}

// doSave
var Cheque = {};
App.eventos.guardar = function() 
{
		
		// GUARDAR
		if( App.variablesGlobales.currentPosicionModelosFormaPago == App.collections.instancias.forma_pago.length){

			App.collections.instancias.forma_pago.each(function(model){ 
				var cheques_json = (model.cheques.length > 0) ? JSON.stringify(model.cheques.models) : ""; 
				model.set("cheques_", cheques_json);

				var bancos_json = (!_.isUndefined(model.bancos.attributes)) ? JSON.stringify(model.bancos.attributes) : ""; 
				model.set("bancos_", bancos_json);

			});

			$("input[name='collection_caja_ing_cob_sal_']").val(JSON.stringify(App.collections.instancias.collection_caja_ing_cob_sal.models));
			$("input[name='forma_pago_']").val(JSON.stringify(App.collections.instancias.forma_pago.models));
			
			var  resp = confirm("¿Desea salvar esta operación?");
			if (resp == true) {
				$("#form-i-cobranza-clientes").submit();
			}
			
		} else {
			var $model_ = App.collections.instancias.forma_pago.at( App.variablesGlobales.currentPosicionModelosFormaPago );
			var anclador = false;
			var monto = parseFloat($model_.get("monto"));

			if (monto>0) {

				// Proceso: CHEQUE
				if ($model_.get("id_caja_tp_concepto")==App.constante.CHEQUES_TERCEROS) {
					
					anclador = true;

					var codigo_cliente = $("#codigo_cliente").val();

					App.collections.instancias.cheques = new App.collections.cheques();

					$model_.cheques = App.collections.instancias.cheques;

					App.collections.instancias.cheques.on("add", function(modelCurrent){
 						var secuencia_cheque = parseInt($("input[name='contador_cheque']").val());
 						secuencia_cheque++;
 						modelCurrent.set("secuencia", secuencia_cheque++);
 						$("input[name='contador_cheque']").val(secuencia_cheque);

						var ChequeTr = new App.views.ChequeTr({
							model: modelCurrent,
							modelParent: $model_,
							collection: App.collections.instancias.cheques
						});

						$(Cheque.el).find(".template-tabla_cheque tbody").append(ChequeTr.render().el);

						$(ChequeTr.el).find("input[name='fecha_cheque']").datepicker({
				            changeMonth: true,
				            changeYear: true,
				            showOtherMonths:true,
				            selectOtherMonths: true,
				            numberOfMonths: 1,
				            dateFormat: "dd/mm/yy"
				        });
					});

					Cheque = new App.views.Cheque();

					var html = Cheque.render().el;

					$("#contenedor-panel-cheque-modal").html(html);

					mostarVentana({
						title:"Carga de Cheques y Documentos",
						contentEl: "contenedor-panel-cheque-modal",
						width: 980,
						height: 500,
						labelOk: 'Aceptar',
						accionOk: function(winContexto){

							var monto_total_cheque = $("input[name='monto_total_cheque']").val()
							var monto_total_acumulado = $("input[name='monto_total_acumulado']").val();
							var validados = true;

							App.collections.instancias.cheques.each(function(model){ 
								var campos = model.attributes;
								for (campo in campos) { 
									if (_.str.isBlank(model.get(campo))) {
										alert("Debe llenar todos los campos");
										validados=false;
										return false;
									}
								}
							});

							if(validados){
								if(monto_total_cheque!=monto_total_acumulado){
									alert("Debe igualar el importe a pagar!");
								} else {
									winContexto.hide();

									if( App.variablesGlobales.currentPosicionModelosFormaPago < App.collections.instancias.forma_pago.length){
										App.variablesGlobales.currentPosicionModelosFormaPago++;
									}
									App.eventos.guardar();
								}
							}
						}
					});

					App.collections.instancias.cheques.add(new App.models.cheque());

					$("#contenedor-panel-cheque-modal").find("input[name='cod_cliente']").val(codigo_cliente);

					$("#contenedor-panel-cheque-modal").find("input[name='monto_total_cheque']").val(monto.toFixed(2));
					$("#contenedor-panel-cheque-modal").find("input[name='monto_total_cheque_saldo']").val(monto.toFixed(2));
				}

				// Proceso: BANCO
				if ($model_.get("id_caja_tp_concepto")==App.constante.TRANSFERENCIAS_BANCARIAS) {
					anclador = true;
					
					App.models.instancias.banco = new App.models.banco();

					$model_.bancos = App.models.instancias.banco;

					App.collections.instancias.bancos.add(App.models.instancias.banco);

					App.models.instancias.banco.set("importe", monto.toFixed(2));

					var BancoCuenta = new App.views.BancoCuenta({
						model: App.models.instancias.banco,
						collection: App.collections.instancias.bancos
					});

					var html = BancoCuenta.render().el;

					$(html).find("input[name='fecha_deposito_bancario']").datepicker({
			            changeMonth: true,
			            changeYear: true,
			            showOtherMonths:true,
			            selectOtherMonths: true,
			            numberOfMonths: 1,
			            dateFormat: "dd/mm/yy"
			        });

					$("#contenedor-panel-deposito-banco-cuenta-modal").html(html);

					mostarVentana({
						title:"Depositos en cuentas de Bancos",
						contentEl: "contenedor-panel-deposito-banco-cuenta-modal",
						width: 500,
						height: 300,
						labelOk: 'Aceptar',
						accionOk: function(winContexto) {
							
							var campos = App.models.instancias.banco.attributes;
							var validados = true;
							for (campo in campos) { 
								if (_.str.isBlank(App.models.instancias.banco.get(campo))) {
									alert("Debe llenar todos los campos");
									validados=false;
									return false;
								}
							}

							if(validados){
								winContexto.hide();
								if( App.variablesGlobales.currentPosicionModelosFormaPago < App.collections.instancias.forma_pago.length){
									App.variablesGlobales.currentPosicionModelosFormaPago++;
								}
								App.eventos.guardar();
							}
						}
					});


				}
			}

			// Entra en un ciclo repetitivo hasta que termine de recorrer la coleccion de formas de pagos
			if( (anclador == false) && App.variablesGlobales.currentPosicionModelosFormaPago < App.collections.instancias.forma_pago.length){
				App.variablesGlobales.currentPosicionModelosFormaPago++;
				App.eventos.guardar();
			}
		}
}


$(function(){
	$("#btnBuscarCliente").click(function(e){
        e.preventDefault();

        var fnCallBack = function(registro){
        	$("input[name='id_cliente']").val(registro.json.id_cliente);
		    $("#descripcion_cliente").text(registro.json.nombre);
	        $("input[name='codigo_cliente']").val(registro.json.cod_cliente);
	        App.collections.instancias.collection_caja_ing_cob_sal.fetch({
				data:{
					opt:"saldoPendientePorClienteModCaja",
					id_cliente:registro.json.id_cliente
				}
			});

			//$("#tabla_saldos_pendientes").find("tbody").append(ViewSaldoPendientePorCliente.render().el);
        };

        pBuscarCliente.main.mostrarWin(fnCallBack);
    });

	$("#guardar").click(function(ev){
    	ev.preventDefault();
		App.collections.instancias.forma_pago.fetch({
			data:{
				opt:"TiposFormaPago"
			}
		});
    	//$("#form-i-cobranza-clientes").submit();
    });
});