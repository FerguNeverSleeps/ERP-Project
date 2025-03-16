$(document).ready(function() { 
    //============================================================================================================
    // Select2
    //console.log("Cantidad de Posiciones: " + NUMERO_POSICIONES + "\nMinimumInputLength: " + LONGITUD_POSICION);
//    if(NUMERO_POSICIONES>1000)
//    {
//        $('#nomposicion_id').select2({
//            minimumInputLength: LONGITUD_POSICION,
//        })
//        .on('change', function() {
//            $(this).valid();
//        });
//    }
//    else
//    {
//        $('#nomposicion_id').select2()
//        .on('change', function() {
//            $(this).valid();
//        });
//    }

    // Select 2 + jQuery Validate
    $("#codpro, #estado_civil, #tipnom, #estado, #forcob, #codbancob, #tipemp, #turno_id, #puesto_id, #codcat, #codcargo, #proyecto, #tipo_identificacion, #tipo_trabajador, #subtipo_trabajador, #tipo_contrato, #dir_trabajo_provincia, #dir_trabajo_distrito" ).select2()
    .on('change', function() {
        $(this).valid();
    });

    //============================================================================================================
    // date-picker

    $('.date-picker').datepicker({
        language: 'es',
		autoclose: true
    }).on('changeDate', function(ev){

    	var elemento = $(this).children().eq(0).attr("id");    // console.log('date-picker => ' + elemento);
        var this_id  = "#"+elemento;
    	var fecha    = $(this_id).val();

    	if(elemento == 'fecnac')
        {
            $(this_id).valid();
	        actualizar(fecha);
        }
    	else if(elemento == 'fecing')
        {
            $(this_id).valid();

            fecha = fecha.replace("/", "-");
            par   = fecha.split("-");
            fecha = par[2]+'-'+par[1]+'-'+par[0] + ' 00:00:00';

            var now = moment({hours:0, minutes:0, seconds:0, milliseconds:0});

            var antiguedad =  now.preciseDiff(fecha);
            //console.log("Antiguedad: ");
            //console.log(antiguedad);

	    	$("#antiguedad").html(antiguedad);	
        }
    });

    //============================================================================================================
    // jQuery Validae

	$.validator.addMethod("regex", function(value, element, regexpr) {          
    	return regexpr.test(value);
   	}, "Expresion invalida");    

    $("#form-integrantes").validate({
        ignore: ".ignore",
        /*invalidHandler: function(e, validator){
            if(validator.errorList.length)
                $('#tab_6 a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show')
        },*/
        errorElement: 'span', //default input error message container
        errorClass: 'help-block', // default input error message class
    	rules: {
    		tipnom: { required: true }, 
    		ficha:  { required: true },    		
    		apellidos: { required: true },
    		nombres: { required: true },
                email: { email: true },
    		cedula: { required: true },
                estado: { required: true },
                fecing: { required: true },                
               /* posicion_estructura:{ required: true },*/
                /*cuentacontable_estructura:{ required: true },*/
                planilla_estructura:{ required: true },                
                tipoempleado_estructura:{ required: true },
                cargo_estructura:{ required: true },
                funcion_estructura:{ required: true },
                salario_estructura:{ required: true },               
                fechainicio_estructura:{ required: true },
                codnivel1:{ required: true },
                nacionalidad:{required: true},
                sexo:{required: true},
                tipemp:{required: true},                 
//                usuario_aprueba:{required: true},                
    		estado_civil: { required: function() { return (TIPO_EMPRESA!=1); } },
    		fecnac: { required: function() { return (TIPO_EMPRESA!=1); } },
    		lugarnac: { required: function() { return (TIPO_EMPRESA!=1); } },
    		codpro: { required: function() { return (TIPO_EMPRESA!=1); } },
    		direccion: { required: function() { return (TIPO_EMPRESA!=1); } },    		
    		telefonos: {
                required:false /*function() { return (TIPO_EMPRESA!=1); }*/
    			/*required: function() { return (TIPO_EMPRESA!=1); },
    			regex: /^\d{6,11}$|^$/*/
    		},
    		estado: { required:true /*function() { return (TIPO_EMPRESA!=1); }*/ },
    		fecing: { required: true /*function() { return (TIPO_EMPRESA!=1); }*/ },
    		forcob: { required: true/*function() { return (TIPO_EMPRESA!=1); }*/ },
    		cuentacob: {
    			  required: function() {
    			  	var forcob = $("#forcob").val();
			        return ( forcob!=='Efectivo' && forcob!=='Cheque' && forcob !=='');
			      }
    		},
    		inicio_periodo: {
    			  required: function() {
    			  	var tipemp = $("[name='tipemp']:checked").val();
			        return ( tipemp!=='Fijo');
			      }
    		},
//    		fin_periodo: {
//    			  required: function() {
//    			  	var tipemp = $("[name='tipemp']:checked").val();
//			        return ( tipemp!=='Fijo');
//			      }
//    		},
    		puesto_id: {
    			required: function() {
    				return (TIPO_EMPRESA==2);
    			}
    		},
    		turno_id: { required: function() { return (TIPO_EMPRESA!=1); } },
    		nomposicion_id: {
    			required: function() {
    				return (TIPO_EMPRESA==1);
    			}    			
    		},
    		suesal: { 
    			required: function() {
    				return (USUARIO_ACCESO_SUELDO==1) // (TIPO_EMPRESA==1) && (USUARIO_ACCESO_SUELDO==1)
    			},
    			max: function(){
    				var max_sueldo = $("#max_sueldo").val();

    				max_sueldo = (max_sueldo!=='') ? parseFloat(max_sueldo) : 99999999;
    				//console.log("Max sueldo: " + max_sueldo);
    				return max_sueldo;
    			}
    		},
    		codcat: { required: true /*function() { return (TIPO_EMPRESA!=1); }*/ },
    		codcargo: { required: true},
    	},
    	messages: {
    		/*telefonos: {
    			//regex: "Debe tener el formato xxxx-xxxx"
                regex: "Debe entre 6 y 11 digitos"
    		},*/
            'email': {  email: 'Ingresar el correo electrónico con el formato correcto. tu@mail.com' }
            ,'sexo':  {required: " "}
            ,'nacionalidad':{required: " "}
            ,'tipemp': {required: " "}
    	},
        highlight: function (element) { 
            $(element)
                .closest('.form-group').addClass('has-error'); 
        },
        unhighlight: function (element) { 
            $(element)
                .closest('.form-group').removeClass('has-error'); 
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
		errorPlacement: function(error, element) {
		    if (element.attr("name") == "fecnac" || element.attr("name") == "fecing" ) {
		       error.insertAfter(element.closest('.input-group')); // input-group-btn
		    } else {
		      error.insertAfter(element);
		    }
		  }
        // errorPlacement: function (error, element) {
        //     //error.insertAfter(element.closest('.input-icon'));
        // },
    });

    //============================================================================================================
    // Eventos jQuery

	$("#nomposicion_id").change(function(){
		var posicion = $(this).val();
        //console.log("nomposicion_id =>" + posicion);
		if(posicion!='')
		{
	 		$.post("ajax_sueldo_propuesto.php", { nomposicion_id: posicion }, function(data){
	 			//console.log(data);
	 			if(data.sueldo_propuesto)
	 			{
	 				$("#max_sueldo").val(data.sueldo_propuesto);
	 			}
	        });				
		}
		else
		{
			if(USUARIO_ACCESO_SUELDO==1)
			{
				$("#max_sueldo").val('');
			}
		}
	});

    $("#puesto_id").change(function(){
        var id_puesto = $(this).val();

        //console.log("id_puesto: "+id_puesto);

        $("#horario_puesto").show();

        if(id_puesto !== '')
        {
            $.post("ajax_vig_puesto.php", { id_puesto: id_puesto }, function(data){
                //console.log(data);
                if(data.registro)
                {
                    var tbody = obtener_tabla_horario_puesto(data.registro);

                    $("tbody.mostrarHorario").html(tbody);
                }
                else
                    $("#horario_puesto").hide();
            });
        }
        else
            $("#horario_puesto").hide();
    });

    $("#forcob").change(function(){
    	var forma_pago = $(this).val();

    	if(forma_pago == 'Efectivo' || forma_pago == 'Cheque' || forma_pago=='')
    	{
    		$(".forma-pago").hide();
    		$("#codbancob").val('');	//document.getElementById("codbancob").selectedIndex = "0";
    		$("#codbancob").select2('val',''); 		
    		$("#cuentacob").val('');   
    		$("#codbancob, #cuentacob").prop("disabled", true);		

    		$("#cuentacob").closest('.form-group').removeClass('has-error'); 
    		$("label[for='cuentacob'].error").hide();
    	}
    	else
    	{
    		$(".forma-pago").show();
    		$("#codbancob, #cuentacob").prop("disabled", false);
    	}
    });

    $("[name='tipemp']").change(function(){
    	var tipo_contrato = $(this).val();

    	if(tipo_contrato=='Fijo')
    	{
    		$("#inicio_periodo, #fin_periodo").val('');
    		$("#inicio_periodo, #fin_periodo").prop("disabled", true);	

    		$("#inicio_periodo, #fin_periodo").closest('.form-group').removeClass('has-error'); 
    		$("label[for='inicio_periodo'].error, label[for='fin_periodo'].error").hide();	
    	}
    	else
        	$("#inicio_periodo, #fin_periodo").prop("disabled", false);		
    });

	$("#codnivel1, #codnivel2, #codnivel3, #codnivel4, #codnivel5, #codnivel6, #codnivel7").change(function(){
		var elemento  = $(this).attr('id');
		var nivel     = parseInt(elemento.replace("codnivel", ""));
		var nivel_sig = nivel + 1; 
                        
                         
                //alert('nivel_sig:'+nivel_sig+' elemento:'+elemento);
 
		if(nivel_sig<=7)
		{
			eval("var NIVEL = NIVEL" + nivel_sig);

			if(NIVEL=='1')
			{
				var codnivel = $(this).val();

		 		$.post("ajax_niveles.php", { nivel: nivel_sig, codnivel: codnivel}, function(data){
		 			  console.log(data);
		              $("#codnivel" + nivel_sig).html(data);	              
		              $("#codnivel" + nivel_sig).val(''); 
		              $("#codnivel" + nivel_sig).select2("val", "").change();
		        });	
			}
			// else
			// 	console.log("Inactivo el nivel siguiente => " + nivel_sig);
		}
	});

    //============================================================================================================
    //  Mostrar Tabs (Pestañas)

	$("#tab-calendario").click(function(){
        if(OPERACION==='editar')
        {
    		var anio = (new Date().getFullYear());

            var url  = "calendarios_personal_ajax.php?anio=" + anio + "&ficha=" + FICHA_ACTUAL;

            $("#iframe_tab_1").attr("src", url);
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_2").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");
            $("#iframe_tab_6").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");

          // var url  = "calendarios_personal_ajax.php"; //?anio="+anio+"&ficha="+FICHA_ACTUAL;
          //       $( "#tab_1" ).load( url, { "anio": anio, "ficha": FICHA_ACTUAL }, 
          //           function( response, status, xhr ) {
          //             if ( status == "error" ) {
          //               console.log( "Error tab_1: " + xhr.status + " " + xhr.statusText );
          //             }
          //       });
        }
	});
        

	$("#tab-cargas-familiares").click(function(){
        if(OPERACION==='editar')
        {
            var url  = "familiares_ajax.php?cedula=" + CEDULA_ACTUAL + "&txtficha=" + FICHA_ACTUAL;

            $("#iframe_tab_2").attr("src", url);
            
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");
            $("#iframe_tab_6").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");
    	  // var url  = "familiares_ajax.php"; //?cedula="+CEDULA_ACTUAL+"&txtficha="+FICHA_ACTUAL;
          //       $( "#tab_2" ).load( url, { "cedula": CEDULA_ACTUAL, "txtficha": FICHA_ACTUAL }, 
          //           function( response, status, xhr ) {
          //             if ( status == "error" ) {
          //               console.log( "Error tab_2: " + xhr.status + " " + xhr.statusText );
          //             }
          //       });
        }
	});
        
        $("#tab-estudios").click(function(){
        if(OPERACION==='editar')
        {
            var url  = "estudios_ajax.php?cedula=" + CEDULA_ACTUAL;
            
            $("#iframe_tab_8").attr("src", url);
            //alert(url);
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_2").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");
            $("#iframe_tab_6").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");
    	  // var url  = "familiares_ajax.php"; //?cedula="+CEDULA_ACTUAL+"&txtficha="+FICHA_ACTUAL;
          //       $( "#tab_2" ).load( url, { "cedula": CEDULA_ACTUAL, "txtficha": FICHA_ACTUAL }, 
          //           function( response, status, xhr ) {
          //             if ( status == "error" ) {
          //               console.log( "Error tab_2: " + xhr.status + " " + xhr.statusText );
          //             }
          //       });
        }
	});


$("#tab-vacaciones").click(function(){
        if(OPERACION==='editar')
        {
            var url  = "vacaciones_ajax.php?cedula=" + CEDULA_ACTUAL;
            
            $("#iframe_tab_9").attr("src", url);
            //alert(url);
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_2").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");
            $("#iframe_tab_6").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");
            $("#iframe_tab_8").attr("src", "about:blank");
          // var url  = "familiares_ajax.php"; //?cedula="+CEDULA_ACTUAL+"&txtficha="+FICHA_ACTUAL;
          //       $( "#tab_2" ).load( url, { "cedula": CEDULA_ACTUAL, "txtficha": FICHA_ACTUAL }, 
          //           function( response, status, xhr ) {
          //             if ( status == "error" ) {
          //               console.log( "Error tab_2: " + xhr.status + " " + xhr.statusText );
          //             }
          //       });
        }
    });

        

	$("#tab-campos-adicionales").click(function(){
        if(OPERACION==='editar')
        {
            var url  = "otrosdatos_integrantes_ajax.php?txtficha=" + FICHA_ACTUAL;

            $("#iframe_tab_3").attr("src", url);
            
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_2").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");;
            $("#iframe_tab_6").attr("src", "about:blank");;
            $("#iframe_tab_7").attr("src", "about:blank");;
    		// var url  = 'otrosdatos_integrantes_ajax.php'; // ?txtficha='+FICHA_ACTUAL;
            //       $( "#tab_3" ).load( url, { "txtficha": FICHA_ACTUAL }, 
            //           function( response, status, xhr ) {
            //             if ( status == "error" ) {
            //               console.log( "Error tab_3: " + xhr.status + " " + xhr.statusText );
            //             }
            //       });
        }
	});

	$("#tab-expediente").click(function(){
        if(OPERACION==='editar')
        {
    		var url  = "../expediente/expediente_list.php?cedula=" + CEDULA_ACTUAL;

            $("#iframe_tab_4").attr("src", url);
            
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_2").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");
            $("#iframe_tab_6").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");
            // var url  = '../expediente/expediente_list_ajax.php'; //?cedula='+CEDULA_ACTUAL;
            // $( "#tab_4" ).load( url, { "cedula": CEDULA_ACTUAL, "ruta": '../expediente/' }, 
            //     function( response, status, xhr ) {
            //       if ( status == "error" ) {
            //         console.log( "Error tab_4: " + xhr.status + " " + xhr.statusText );
            //       }
            // });
        }
	});

    //============================================================================================================
    // Editar integrante
    if(OPERACION==='editar')
    {
        var fecha_nac = $("#fecnac").val();
        if(fecha_nac != '') actualizar(fecha_nac);

        var fecing = $("#fecing").val();
        if(fecing != '') antiguedad(fecing);  

        //console.log("Ejecutar change");
        //$("#puesto_id").trigger("change");    
    }
    
    $("#tab-tiempos").click(function(){
        if(OPERACION==='editar')
        {
    		var anio = (new Date().getFullYear());
        //alert(USR_UID);
            var url  = "../../funcionarios/tiempos.php?anio=" + anio + "&cedula=" + CEDULA_ACTUAL;

            $("#iframe_tab_5").attr("src", url);    
            
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_2").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_6").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");

        }
	});
        
    $("#tab-salarios-acumulados").click(function(){
        if(OPERACION==='editar')
        {
    		var anio = (new Date().getFullYear());
        //alert(USR_UID);
            var url  = "salarios_acumulados_ajax.php?cedula=" + CEDULA_ACTUAL + "&txtficha=" + FICHA_ACTUAL;

            $("#iframe_tab_6").attr("src", url);    
            
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_2").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");

        }
	});

    $("#tab-proyecto-centro-costo").click(function(){
        
         if(OPERACION==='editar')
        {
            var url  = "proyecto_cc/proyecto_cc.php?cedula=" + CEDULA_ACTUAL + "&txtficha=" + FICHA_ACTUAL;

            $("#iframe_tab_8").attr("src", url);
            
            $("#iframe_tab_0").attr("src", "about:blank");
            $("#iframe_tab_1").attr("src", "about:blank");
            $("#iframe_tab_3").attr("src", "about:blank");
            $("#iframe_tab_4").attr("src", "about:blank");
            $("#iframe_tab_5").attr("src", "about:blank");
            $("#iframe_tab_6").attr("src", "about:blank");
            $("#iframe_tab_7").attr("src", "about:blank");
    	  // var url  = "familiares_ajax.php"; //?cedula="+CEDULA_ACTUAL+"&txtficha="+FICHA_ACTUAL;
          //       $( "#tab_2" ).load( url, { "cedula": CEDULA_ACTUAL, "txtficha": FICHA_ACTUAL }, 
          //           function( response, status, xhr ) {
          //             if ( status == "error" ) {
          //               console.log( "Error tab_2: " + xhr.status + " " + xhr.statusText );
          //             }
          //       });
        }
	});
});