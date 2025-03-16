var Login = function () {

	var handleLogin = function() {
		$("#mensaje_registro").hide();
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
		var fileinput = $('.fileinput').fileinput();
		fileinput.on('change.bs.fileinput', function(e, files){
			$("#archivo").closest('.form-group').removeClass('has-error');
			$(".help-block").closest('.form-group').removeClass('has-error');
			$(".help-block").text('');
		})

		$('.reg-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            rules: {
					txtUser: {
						required: true
					},txtPass : {
						minlength : 5
					},
					txtRepeat : {
						minlength : 5,
						equalTo : "#txtPass"
					},
					txtEmpresa: {
	                    required: true
	                },
					txtRuc: {
	                    required: true
	                },
					txtDireccion: {
	                    required: true
	                },
					txtTlf: {
	                    required: true
	                }
	            },

	            messages: {
	                txtUser: {
	                    required: "Este campo es obligatorio" // "Usuario es requerido."
	                },
	                txtPass: {
	                    required: "Este campo es obligatorio" ,
						minlength: "Su contraseña debe contener al menos 5 caracteres"
	                },
	                txtRepeat: {
	                    required: "Este campo es obligatorio" ,
						minlength: "Su contraseña debe contener al menos 5 caracteres",
						equalTo: "Por favor, ingrese el mismo valor"

	                },
	                txtEmpresa: {
	                    required: "Este campo es obligatorio" // "Password es requerido."
	                },
	                txtRuc: {
	                    required: "Este campo es obligatorio" // "Usuario es requerido."
	                },
	                txtDireccion: {
	                    required: "Este campo es obligatorio" // "Password es requerido."
	                },
	                txtTlf: {
	                    required: "Este campo es obligatorio" // "Usuario es requerido."
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

			// $("#empresaSeleccionada").on("change", function (e) { console.log('Cambio'); });

			$('#btnReg').click( function() {
				/*
			    $.post('login.php', $('.login-form').serialize(), function(data) {
			         console.log(data);
			       },
			       'json' 
			    );
				*/				

                if ($('#reg-form').validate().form()) 
                {


					$.blockUI({ message: '<span><img src="../../imagenes/loader.gif"></span><br><span>Generando Empresas...</b></span>' });
					var txtLogo = $('#txtLogo').prop("files")[0];
					var txtEmpresa = $('#txtEmpresa').val();
					var txtUser = $('#txtUser').val();
					var txtRuc = $('#txtRuc').val();
					var txtDireccion = $('#txtDireccion').val();
					var txtTlf = $('#txtTlf').val();
					var txtEmail = $('#txtEmail').val();
					var txtPass = $('#txtPass').val();

					/*var data_form = {};
					txtLogo=txtLogo.name;
					txtEmpresa=txtEmpresa;
					txtUser=txtUser;
					txtRuc=txtRuc;
					txtDireccion=txtDireccion;
					txtTlf=txtTlf;
					txtEmail=txtEmail;
					data_form.txtPass=txtPass;*/
					var form_data = new FormData();
					form_data.append("txtLogo",txtLogo);
					form_data.append("txtEmpresa",txtEmpresa);
					form_data.append("txtUser",txtUser);
					form_data.append("txtRuc",txtRuc);
					form_data.append("txtDireccion",txtDireccion);
					form_data.append("txtTlf",txtTlf);
					form_data.append("txtEmail",txtEmail);
					form_data.append("txtPass",txtPass);
				 	$.ajax({
						  method: "POST",
						  url: "login_registro.php",
						  dataType: "json",
						  processData:  false,
						  contentType:  false,
						  cache:        false,
						  data:   form_data
						}).done(function(resp){

							$(document).ajaxStop($.unblockUI);
				             if(resp.error == 1)
				             {
								localStorage.setItem("error",resp.error);
								localStorage.setItem("mensaje_bd",resp.mensaje_bd);
								localStorage.setItem("mensaje_correo",resp.mensaje_correo);
								localStorage.setItem("mensaje_empresa",resp.mensaje_empresa);
								localStorage.setItem("mensaje_foto",resp.mensaje_foto);
								document.location.href = "index.php";
				             	//document.location.href = "../../paginas/seleccionar_nomina1.php"
				             	//document.location.href = "../../paginas/validar_nomina.php"
				             	
				             }
				             else
				             {
								 // console.log('No Autorizado');
								 if(resp.error == 2)
								 {
									$("#mensaje_registro").show();
									$("#mensaje_registro").empty();
									$("#mensaje_registro").append(resp.mensaje);

								 }
				             	$('.alert-danger', $('.login-form')).show();
				             }
				        }).fail(function(resp){
							$(document).ajaxStop($.unblockUI);
							if(resp.error == 2)
							{
							   $("#mensaje_registro").show();
							   $("#mensaje_registro").empty();
							   $("#mensaje_registro").append(resp.mensaje);

							}
							alert("Hubo errores en el proceso");
						});

                }
                return false;
			});
	}
    
    return {
        //main function to initiate the module
        init: function () {
			localStorage.clear();
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