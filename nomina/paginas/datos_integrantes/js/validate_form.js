$(document).ready(function() { 
	//console.log("Validate Forms JS");
    //============================================================================================================
    // jQuery Validate
	$.validator.addMethod("regex", function(value, element, regexpr) {          
		return regexpr.test(value);
	}, "Expresion invalida");    

	var form1  = $('#form_integrantes1');
	var error1 = $('.error', form1);

	form1.validate({
		errorElement: 'span',     
		errorClass:   'help-block', 
		//focusInvalid: false,      
		//ignore: "",
		rules: {
			nombres:      { required: true },
			apellidos:    { required: true },
			cedula:       { required: true },
			estado_civil: { required: true },
			fecnac:       { required: true },
			lugarnac:     { required: true },
			codpro:       { required: true },
			direccion:    { required: true },
			telefonos:    {
				required: true,
				regex: /^\d{3,4}-\d{4}$/
			},
		},

		messages: {
			telefonos: {
				regex: "Debe tener el formato xxxx-xxxx"
			}
		},

		errorPlacement: function(error, element) {
		    if (element.attr("name") == "fecnac") 
		    	error.insertAfter(element.closest('.input-group')); 
		    else
		    	error.insertAfter(element);
		},

        invalidHandler: function (event, validator) {         
            //success1.hide();
            //error1.show();
            App.scrollTo(error1, -200);
        },

		highlight: function (element) { 
			$(element)
				.closest('.form-group').addClass('has-error'); 
		},

		unhighlight: function (element) { 
			$(element)
				.closest('.form-group').removeClass('has-error'); 
		},

		success: function (label, element) {
			label.closest('.form-group').removeClass('has-error'); 
			//label.remove();
		},
		/*
		submitHandler: function (form) {
			console.log("Enviando formulario");
			//return false;
		}*/
	});

	var form2  = $('#form_integrantes2');
	var error2 = $('.error', form2);

	form2.validate({
		errorElement: 'span',     
		errorClass:   'help-block', 
		//focusInvalid: false,      
		//ignore: "",
		rules: {
    		tipnom: { required: true },
    		ficha:  { required: true }, 
    		estado: { required: true },
    		fecing: { required: true },
    		forcob: { required: true },
    		cuentacob: {
    			  required: function() {
    			  	var forcob = $("#forcob").val();
			        return ( forcob!=='Efectivo' && forcob !=='');
			      }
    		},
    		inicio_periodo: {
    			  required: function() {
    			  	var tipemp = $("[name='tipemp']:checked").val();
			        return ( tipemp!=='Fijo');
			      }
    		},
    		fin_periodo: {
    			  required: function() {
    			  	var tipemp = $("[name='tipemp']:checked").val();
			        return ( tipemp!=='Fijo');
			      }
    		},
    		puesto_id: {
    			required: function() {
    				return (TIPO_EMPRESA==2);
    			}
    		},
    		turno_id: { required: true },
    		nomposicion_id: {
    			required: function() {
    				return (TIPO_EMPRESA==1);
    			}    			
    		},
    		suesal: { 
    			required: function() {
    				return ((TIPO_EMPRESA==1) && (USUARIO_ACCESO_SUELDO==1))
    			},
    			max: function(){
    				var max_sueldo = $("#max_sueldo").val();
    				max_sueldo = (max_sueldo!=='') ? parseFloat(max_sueldo) : 99999999;
    				return max_sueldo;
    			}
    		},
    		codcat:   { required: true },
    		codcargo: { required: true},
		},

		errorPlacement: function(error, element) {
		    if (element.attr("name")=="fecing" || element.attr("name")=="inicio_periodo" || element.attr("name")=="fin_periodo") 
		    	error.insertAfter(element.closest('.input-group')); 
		    else
		    	error.insertAfter(element);
		},

		highlight: function (element) { 
			$(element)
				.closest('.form-group').addClass('has-error'); 
		},

        invalidHandler: function (event, validator) {         
            App.scrollTo(error2, -200);
        },

		unhighlight: function (element) { 
			$(element)
				.closest('.form-group').removeClass('has-error'); 
		},

		success: function (label, element) {
			label.closest('.form-group').removeClass('has-error'); 
		},
	});

    //============================================================================================================
    // Select 2 + jQuery Validate
	$("#codpro, #estado_civil, #tipnom, #estado, #forcob, #codbancob, #tipemp, #turno_id, #puesto_id, #codcat, #codcargo").select2()
	.on('change', function() {
	    $(this).valid();
	});

	$('#nomposicion_id').select2({
		minimumInputLength: 3,
	})
	.on('change', function() {
	    $(this).valid();
	});

    //============================================================================================================
    // Datepicker + jQuery Validate  
	$('.input-group.date').datepicker({
		format: "dd-mm-yyyy",
	    language: 'es',
		autoclose: true
	}).on('changeDate', function(ev){
		var elemento = $(this).children().eq(0).attr("id");
		var this_id  = "#"+elemento;
		var fecha    = $(this_id).val();

		if(elemento == 'fecnac')
		{
			$(this_id).valid();
	        //actualizar(fecha);
		}
		else if(elemento == 'fecing')
	    	calcular_antiguedad(fecha);  	
	});
    //============================================================================================================
    // Eventos jQuery
	$("#nomposicion_id").change(function(){
		var posicion = $(this).val();
		if(posicion!='')
		{
	 		$.post("ajax/ajax_sueldo_propuesto.php", { nomposicion_id: posicion }, function(data){
	 			if(data.sueldo_propuesto)
	 				$("#max_sueldo").val(data.sueldo_propuesto);
	        });				
		}
		else
		{
			if(USUARIO_ACCESO_SUELDO==1)
				$("#max_sueldo").val('');
		}
	});

    $("#puesto_id").change(function(){
        var id_puesto = $(this).val();

        $("#horario_puesto").show();

        if(id_puesto !== '')
        {
            $.post("ajax/ajax_vig_puesto.php", { id_puesto: id_puesto }, function(data){
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
    	
    	if(forma_pago == 'Efectivo' || forma_pago=='')
    	{
    		$("#codbancob").select2('val','');
    		$("#codbancob, #cuentacob").val('');    		 		 
    		$("#codbancob, #cuentacob").prop("disabled", true);	
    		$("#cuentacob").valid();	
    	}
    	else
    		$("#codbancob, #cuentacob").prop("disabled", false);
    });

    $("[name='tipemp']").change(function(){
    	var tipo_contrato = $(this).val();

    	if(tipo_contrato=='Fijo')
    	{
    		$("#inicio_periodo, #fin_periodo").val('');
    		$("#inicio_periodo, #fin_periodo").prop("disabled", true);
    		//$("#inicio_periodo").valid(); $("#fin_periodo").valid();
    		$("#inicio_periodo, #fin_periodo").closest('.form-group').removeClass('has-error'); 
    		$("span[for='inicio_periodo'].help-block, span[for='fin_periodo'].help-block").hide();	
    	}
    	else
    	{
        	$("#inicio_periodo, #fin_periodo").prop("disabled", false);		
        	$("#inicio_periodo").valid();
        	$("#fin_periodo").valid();
    	}
    }); 

	$("#codnivel1, #codnivel2, #codnivel3, #codnivel4, #codnivel5, #codnivel6, #codnivel7").change(function(){
		var elemento  = $(this).attr('id');
		var nivel     = parseInt(elemento.replace("codnivel", ""));
		var nivel_sig = nivel + 1;

		if(nivel_sig<=7)
		{
			eval("var NIVEL = NIVEL" + nivel_sig);

			if(NIVEL=='1')
			{
				var codnivel = $(this).val();

		 		$.post("ajax/ajax_niveles.php", { nivel: nivel_sig, codnivel: codnivel}, function(data){
		              $("#codnivel" + nivel_sig).html(data);	              
		              $("#codnivel" + nivel_sig).val(''); 
		              $("#codnivel" + nivel_sig).select2("val", "").change();
		        });	
			}
		}
	});
    //============================================================================================================
    // Editar Integrante
    if(OPERACION==='editar')
    {
        //var fecha_nac = $("#fecnac").val();
        //if(fecha_nac != '') actualizar(fecha_nac);
        var fecing = $("#fecing").val();
        if(fecing != '') calcular_antiguedad(fecing);     
    }
});