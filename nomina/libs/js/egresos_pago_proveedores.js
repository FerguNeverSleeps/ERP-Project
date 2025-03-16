App.collections.instancias.collection_movimientos_principales = new App.collections.SaldoMovimientos();
App.models.instancias.SaldoMovimientos = new App.models.SaldoMovimientos();

var ViewTipoFormaPago = {};

App.collections.instancias.collection_movimientos_principales.add( App.models.instancias.SaldoMovimientos );

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

App.collections.instancias.collection_movimientos_principales.on("sync", function(currentCollection){
	// Limpiar tbody
	$("#tabla_saldos_pendientes").find("tbody tr").remove();
	$("input[name='total_cobro']").val("0.00");
	
	// Caso 1: Cargar un asiento por defecto
	currentCollection.add(App.models.instancias.SaldoMovimientos);

	ViewSaldoPendientePorCliente = new App.views.SaldoPendientePorCliente({
														model: App.models.instancias.SaldoMovimientos, 
														collection: currentCollection
													});

	$("#tabla_saldos_pendientes").find("tbody").append(ViewSaldoPendientePorCliente.render().el);

	// Caso 2: Si el length es mayor a uno quiere decir que el cliente seleccionado tiene asientos.
	//  	   De lo contrario solo Caso 1.
	if ( App.collections.instancias.collection_movimientos_principales.length > 1 ){
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
App.eventos.guardar = function() {
		
		// GUARDAR
		if( App.variablesGlobales.currentPosicionModelosFormaPago == App.collections.instancias.forma_pago.length){
			$("input[name='collection_movimientos_principales_']").val(JSON.stringify(App.collections.instancias.collection_movimientos_principales.models));
		
			App.collections.instancias.forma_pago.each(function(model){ 
					
				if(!_.isUndefined( model.movimientos_bancarios.movimientos)){
					model.movimientos_bancarios.set("movimientos", JSON.stringify(model.movimientos_bancarios.movimientos.models));
					model.set("movimientos_bancarios", JSON.stringify( model.movimientos_bancarios.attributes));
				} else{
					model.set("movimientos_bancarios", "");
				}
				
				var cheques_json = (model.cheques.length > 0) ? JSON.stringify(model.cheques.models) : "";
				model.set("cheques_", cheques_json);

			});

			$("input[name='forma_pago_']").val(JSON.stringify(App.collections.instancias.forma_pago.models));
			
			var  resp = confirm("Desea salvar esta operación?");
			if (resp == true){
				$("#form-egresos-pago-proveedores").submit();
			}
		}else{
			var $model_ = App.collections.instancias.forma_pago.at( App.variablesGlobales.currentPosicionModelosFormaPago );
			var anclador = false;
			var monto = parseFloat($model_.get("monto"));
			var BancoCuenta = {}, 
				Cheque = {},
				BancoTr = {};

			if (monto>0) {

				// Proceso: CHEQUE
				if ($model_.get("id_caja_tp_concepto")==App.constante.CHEQUES_TERCEROS) {
					
					anclador = true;

					var codigo_cliente = $("#codigo_cliente").val();

					App.collections.instancias.cheques = new App.collections.cheques();

					$model_.cheques = App.collections.instancias.cheques;

					App.collections.instancias.cheques.on("sync", function(collections_){

						var ChequeTr={};
						collections_.each(function(model_){ 
							ChequeTr = new App.views.ChequeTr({
								model: model_,
								collection: App.collections.instancias.cheques
							});
							$(Cheque.el).find(".template-tabla_cheque tbody").append(ChequeTr.render().el);
						});
					});

					Cheque = new App.views.Cheque();

					var html = Cheque.render().el;

					$("#contenedor-panel-cheque-modal").html(html);

					mostarVentana({
						title:"Carga de Cheques y Documentos de terceros",
						contentEl: "contenedor-panel-cheque-modal",
						width: 1020,
						height: 500,
						labelOk: 'Aceptar',
						accionOk: function(winContexto){

							var monto_total_cheque = parseFloat($("input[name='monto_total_cheque']").val());
							var monto_total_acumulado = parseFloat($("input[name='monto_total_acumulado']").val());

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
					});

					App.collections.instancias.cheques.fetch({
						data:{
							opt:"cargarChequesDocTerceros"
						}
					});

					$("#contenedor-panel-cheque-modal").find("input[name='cod_cliente']").val(codigo_cliente);

					$("#contenedor-panel-cheque-modal").find("input[name='monto_total_cheque']").val(monto.toFixed(2));
					$("#contenedor-panel-cheque-modal").find("input[name='monto_total_cheque_saldo']").val(monto.toFixed(2));
				}

				// Proceso: Operaciones Bancarias, Emision cheques propio
				if ($model_.get("id_caja_tp_concepto")==App.constante.OPERACIONES_BANCARIAS || $model_.get("id_caja_tp_concepto")==App.constante.TRANSFERENCIAS_BANCARIAS) {
						anclador = true;
						
						App.models.instancias.movimientos_bancarios = new App.models.movimientos_bancarios();

						App.collections.instancias.bancos = new App.collections.bancos();
						App.models.instancias.movimientos_bancarios.movimientos = App.collections.instancias.bancos;
						$model_.movimientos_bancarios = App.models.instancias.movimientos_bancarios;
						var tipo_movimiento = ($model_.get("id_caja_tp_concepto")==App.constante.OPERACIONES_BANCARIAS) ? App.tipo_movimiento_bancario.CHEQUES_PROPIOS : App.tipo_movimiento_bancario.TRANSFERENCIAS_BANCARIAS;

						$model_.movimientos_bancarios.set("tipo_movimiento", tipo_movimiento);

						BancoCuenta = new App.views.BancoCuenta({
							modelParent: $model_,
							collection: App.collections.instancias.bancos
						});

						var html = BancoCuenta.render().el;

						$("#contenedor-panel-movimientos-bancarios-modal").html(html);
						
						var titulo_win_modal = "";
						if($model_.get("id_caja_tp_concepto")==App.constante.OPERACIONES_BANCARIAS){
							titulo_win_modal = "Emisión de Cheques propios";
						}

						if($model_.get("id_caja_tp_concepto")==App.constante.TRANSFERENCIAS_BANCARIAS){
							titulo_win_modal = "Transferencias Bancarias";
						}

						mostarVentana({
							title: titulo_win_modal,
							contentEl: "contenedor-panel-movimientos-bancarios-modal",
							width: 900,
							height: 500,
							labelOk: 'Aceptar',
							accionOk: function(winContexto) {
								var monto_total_pago = parseFloat($("input[name='monto_total_pago']").val());
								var monto_total_acumulado = parseFloat($("input[name='monto_total_acumulado']").val());

								var validados = true;
								
								App.collections.instancias.bancos.each(function(model){ 
									var campos = model.attributes;
									for (campo in campos) {
										if ( campo!="descripcion_causa" && _.str.isBlank(model.get(campo))) {
											alert("Debe llenar todos los campos");
											validados=false;
										}
									}
								});
								if (validados) {
									if ( monto_total_pago!=monto_total_acumulado ) {
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

						//App.collections.instancias.bancos.add(App.models.instancias.banco);
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
	$("#btnBuscarProveedor").click(function(e){
        e.preventDefault();

        var fnCallBack = function(registro){
        	$("input[name='id_proveedor']").val(registro.json.id_proveedor);
		    $("#descripcion_proveedor").text(registro.json.descripcion);
	        $("input[name='codigo_proveedor']").val(registro.json.cod_proveedor);
	        App.collections.instancias.collection_movimientos_principales.fetch({
				data:{
					opt:"saldoPendientePorProveedorModCaja",
					id_proveedor:registro.json.id_proveedor
				}
			});

			//$("#tabla_saldos_pendientes").find("tbody").append(ViewSaldoPendientePorCliente.render().el);
        };

        pBuscarProveedores.main.mostrarWin(fnCallBack);
    });

	$("#guardar").click(function(ev){
    	ev.preventDefault();
		App.collections.instancias.forma_pago.fetch({
			data:{
				opt:"TiposFormaPagoEgresoProveedores"
			}
		});
    	//$("#form-i-cobranza-clientes").submit();
    });
});