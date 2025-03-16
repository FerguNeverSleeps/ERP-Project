var Login = function () {

	var handleLogin = function() {
		var cont_usu = 0;
		$("#txtUsuario").blur(function() {
			var usuario = $('#txtUsuario').val();

			$('#s2id_empresaSeleccionada').select2('val','');

			cont_usu++;

		    $.ajax({
		        url: '../../../includes/assets/ajax/getEmpresas.php',
		        type: 'POST',
		        data:{usuario: usuario}, 
		        async: false,   
		        success: function(resultado){ 			        
		        	$('#empresaSeleccionada').html(resultado);      
		        }
		    });
		});
		
		var error = localStorage.getItem("error");
		var mensaje = localStorage.getItem("mensaje_bd");
		if(error === "1"){
			$(".alert-success", $('.login-form')).show();
			$("#mensaje_registro").empty();
			$("#mensaje_registro").html("Empresa creada exitosamente");
			localStorage.setItem("error","");
			localStorage.setItem("mensaje_empresa","");

		}
		else
		{
			if(error === "0")
			{
				$(".alert-danger", $('.login-form')).show();
				$("#mensaje_registro").empty();
				$("#mensaje_registro").html("Hubo errores al crear la empresa");
				localStorage.setItem("error","");
				localStorage.setItem("mensaje_empresa","");
			}

		}
		localStorage.clear();

		function format(state) {
            if (!state.id) return state.text; // optgroup
            return "<img class='flag' src='assets/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
        }
        

		$("#empresaSeleccionada").select2({
		 	placeholder: 'Organizaci&oacute;n', // <i class="fa fa-map-marker"></i>&nbsp;
            allowClear: true,
            //formatResult: format,
            //formatSelection: format,
            /*escapeMarkup: function (m) {
                return m;
            }*/
        });

		$('.login-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            rules: {
	                txtUsuario: {
	                    required: true
	                },
	                txtContrasena: {
	                    required: true
	                },
	                empresaSeleccionada: {
	                    required: true
	                },
	            },

	            messages: {
	                txtUsuario: {
	                    required: "Este campo es obligatorio" // "Usuario es requerido."
	                },
	                txtContrasena: {
	                    required: "Este campo es obligatorio" // "Password es requerido."
	                },
	                empresaSeleccionada: {
	                	required: "Este campo es obligatorio" 
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit   
	               // $('.alert-danger', $('.login-form')).show();
	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                error.insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
	              //  form.submit();
	            }
	        });

	        $('.login-form input').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.login-form').validate().form()) {
	                  //  $('.login-form').submit();
	                }
	                return false;
	            }
	        });

			$("#empresaSeleccionada").on("select2-open", function() { 
			    var usuario = $('#txtUsuario').val();
				var empresa = $('#empresaSeleccionada').val();

				if(usuario!=='' && empresa == '' && cont_usu==0)
				{
					 $('#txtUsuario').blur();

					 $('#empresaSeleccionada').select2('close');
				}

			});

			// $("#empresaSeleccionada").on("change", function (e) { console.log('Cambio'); });

			$('#btnEnviar').click( function() {
				/*
			    $.post('login.php', $('.login-form').serialize(), function(data) {
			         console.log(data);
			       },
			       'json' 
			    );
				*/				

                if ($('.login-form').validate().form()) 
                {

				 	$.ajax({
				        url: 'login.php',
				        type: 'POST',
				        dataType: 'json',
				        data: $('.login-form').serialize(),
				        success: function(data) {
							// Object {success: true, login: "1"}

				             if(data.login == 1)
				             {
				             	//document.location.href = "../../paginas/seleccionar_nomina1.php"
				             	document.location.href = "../../paginas/validar_nomina.php"
				             	
				             }
				             else
				             {
				             	// console.log('No Autorizado');
				             	$('.alert-danger', $('.login-form')).show();
				             }
				        }
				    });

                }
                return false;
			});
	}
    
    return {
        //main function to initiate the module
        init: function () {
        	
            handleLogin();       
	       /*
	       	$.backstretch([
	       		"../../../includes/assets/img/bg/login.jpg",
		        //"../../../includes/assets/img/bg/1.jpg",
		        ], {
		          fade: 1000,
		          duration: 2000
		    });
*/
        }

    };

}();