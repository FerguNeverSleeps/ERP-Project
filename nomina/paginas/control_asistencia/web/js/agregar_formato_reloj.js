$(document).ready(function() {

    $('#dispositivo').select2({
            placeholder: "-",
            allowClear: true
    });

	$('#formPrincipal').validate({
            rules: {
                descripcion: { required: true },
                formato:     { required: true },
                tipo_reloj:  { required: true },
                numero:      { required: true }
            },
            messages: {
                descripcion: { required: " " },
                formato:     { required: " " },
                tipo_reloj:  { required: " " },
                numero:      { required: " " }
            },
            highlight: function (element) { 
                $(element)
                    .closest('.label-validate').addClass('has-error'); 
            },
            success: function (label) {
                label.closest('.label-validate').removeClass('has-error');
                label.remove();
            },
    });

    $("input[name=fecha_hora]:radio").change(function () {

    	var valor = this.value;

		$('.tiempo-validate').each(function(i, obj) {
		    $(this).removeClass('has-error'); 
		});

    	$("#div_fecha_hora1, #div_fecha_hora2, #div_fecha_hora3, #div_fecha_hora4").hide().html('');

		$.ajax({
		    method:   "POST",
		    url: 	  "ajax/agregar_formato_reloj.php",
		    data: { opcion: "tiempo", radio: valor },
		    success: function(data){
		        if(valor=='unica')
		        	$("#div_fecha_hora1").html(data).show();
		        if(valor=='separada')
		        	$("#div_fecha_hora2").html(data).show()
		        if(valor=='multiple')
		        {
		        	$("#div_fecha_hora3").html(data).show();

		        	$.post("ajax/agregar_formato_reloj.php", { opcion: "tiempo", radio: valor, parte: "2" }, function(data) {
						 $("#div_fecha_hora4").html(data).show();
					});
		        }
		    }
		});
    });

    $("#btn-guardar").click(function(){

		$('.tiempo-validate').each(function(i, obj) {
		    $(this).removeClass('has-error'); 
		});

        var validacion = $('#formPrincipal').valid();

        //if(!validacion) return false;

    	var elementos = document.getElementsByName("fecha_hora");

		for (var i=0 ; i< elementos.length ; i++)
		{
			  if( elementos[i].checked ) 
			  {
			  		if(elementos[i].value == 'unica')
			  		{
			  			var posicion_tiempo = $("#posicion_tiempo").val();
			  			var formato_tiempo  = $("#formato_tiempo").val();

			  			if(posicion_tiempo=='' || formato_tiempo=='')
			  			{
				  			if(posicion_tiempo=='') 
				  				$("#posicion_tiempo").closest('.tiempo-validate').addClass('has-error'); 
				  			if(formato_tiempo=='')
				  				$("#formato_tiempo").closest('.tiempo-validate').addClass('has-error'); 

				  			return false;
			  			}
			  		}
			  		else if(elementos[i].value == 'separada')
			  		{
			  			var posicion_fecha = $("#posicion_fecha").val();
			  			var formato_fecha  = $("#formato_fecha").val();
			  			var posicion_hora  = $("#posicion_hora").val();
			  			var formato_hora   = $("#formato_hora").val();

			  			if(posicion_fecha=='' || formato_fecha=='' || posicion_hora=='' || formato_hora=='')
			  			{
			  				if(posicion_fecha=='' || formato_fecha=='')
			  					$("#posicion_fecha").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_hora=='' || formato_hora=='')
			  					$("#posicion_hora").closest('.tiempo-validate').addClass('has-error'); 

			  				return false;
			  			}
			  		}
			  		else if(elementos[i].value == 'multiple')
			  		{
			  			var posicion_dia     = $("#posicion_dia").val();
			  			var posicion_mes     = $("#posicion_mes").val();
			  			var posicion_anio    = $("#posicion_anio").val();
			  			var posicion_hora    = $("#posicion_hora").val();
			  			var posicion_minutos = $("#posicion_minutos").val();

			  			if(posicion_dia=='' || posicion_mes=='' || posicion_anio=='' || posicion_hora=='' || posicion_minutos=='')
			  			{
			  				if(posicion_dia=='') 	 $("#posicion_dia").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_mes=='') 	 $("#posicion_mes").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_anio=='') 	 $("#posicion_anio").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_hora=='') 	 $("#posicion_hora").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_minutos=='') $("#posicion_minutos").closest('.tiempo-validate').addClass('has-error'); 

			  				return false;
			  			}
			  		}
			  }
		}

	    if (!confirm("\u00BFEst\u00E1 seguro de querer guardar los datos?")) return false; // onload="scroll(0,0);"

    });

});