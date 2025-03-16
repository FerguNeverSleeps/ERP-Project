var Login = function() {

    var handleLogin = function() {

        var cont_usu = 12;

        $("#empresaSeleccionada").select2({
            language: "es",
            placeholder: "Seleccione una Organización",
            allowClear: true
          });

        $("#txtUsuario").blur(function() {
            var usuario = $('#txtUsuario').val();

            cont_usu++;

            $.ajax({
                url: '../../../includes/assets/ajax/getEmpresas.php',
                type: 'POST',
                data:{usuario: usuario}, 
                async: false,   
                success: function(resultado){                   
                    $('#empresaSeleccionada').html(resultado);
                    $('#empresaSeleccionada').trigger("change.select2");

                    var selemp = $("#empresaSeleccionada");
                    selemp.html('');       
                    selemp.append(resultado);
                }
            });
        });
        function format(state) {
            if (!state.id) return state.text; // optgroup
            return "<img class='flag' src='assets/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
        }

        $('.login-form').validate({
            errorElement: 'span', 
            errorClass: 'help-block', 
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                },
                empresaSeleccionada:{
                    required: true
                }
            },
            messages: {
                username: {
                    required: "Username is required."
                },
                password: {
                    required: "Password is required."
                },
                empresaSeleccionada: {
                    required: "Seleccione una empresa."
                }
            },
            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-danger', $('.login-form')).show();
            },
            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },
            submitHandler: function(form) {
                
                form.submit();
            }
        });

        $('.forget-form').validate({
            errorElement: 'span', 
            errorClass: 'help-block', 
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                usuario_forget: {
                    required: true
                },
                correo_forget: {
                    required: true,
                    email: true
                }
            },
            messages: {
                usuario_forget: {
                    required: "Campo Requerido."
                },
                correo_forget: {
                    required: "Campo Requerido.",
                }
            },
            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-danger', $('.forget-form')).show();
            },
            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },
            submitHandler: function(form) {
                
                form.submit();
            }
        });
        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $('.login-form').submit();
                }
                return false;
            }
        });

        $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });
        $('#forget-password').click(function(){
            $('.login-form').hide();
            $('.forget-form').show();
        });
        $('#back-btn').click(function(){
            $('.login-form').show();
            $('.forget-form').hide();
        });
        $('#enviar-form').click( function() {
            if ($('.forget-form').validate().form()) {
                $('.forget-form').submit();
            }
        });
        $('#btnEnviar').off("click").on( "click", function() {
        	var emp = $("#empresaSeleccionada :selected").val();
        	if (emp == "")
        	{
        		console.log(emp);
        		$('#txt_error').empty();
                $('#txt_error').text("Seleccione una empresa!");
                $('.alert-danger', $('.login-form')).show();
                alert("Selecciones una empresa!");
        		return;
        	}
            if ($('.login-form').validate().form()) 
            {

                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    dataType: 'json',
                    data: $('.login-form').serialize(),
                    success: function(data) {
                        if(data.login == 1)
                        {
                            document.location.href = "../../paginas/validar_nomina.php"                        
                        }
                        else
                        {
                            $('#txt_error').empty();
                            $('#txt_error').text(data.mensaje);
                            $('.alert-danger', $('.login-form')).show();
                        }
                    }
                });
            }
            return false;
        });
    }
    return {
        init: function() {
            handleLogin();
            $('.login-bg').backstretch([
                "../../../includes/assets/img/bg/cliente.jpg",
                "../../../includes/assets/img/bg/login.jpg",
                "../../../includes/assets/img/bg/login-2.jpg"
                ], {
                  fade: 1000,
                  duration: 8000
                }
            );
        }
    };

}();

jQuery(document).ready(function() {
   
    $('.forget-form').hide();
    $("button").click(function(){
        event.preventDefault();

    });
    Login.init();
});