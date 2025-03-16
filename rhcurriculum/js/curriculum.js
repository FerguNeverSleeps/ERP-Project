var Profile = function() {
    var dashboardMainChart = null;



    var armarFormularioCurriculum = function(){        
        jQuery('form').submit(function (e) {                    
            //validationForm(data);
            e.preventDefault(); //Evitamos que se mande del formulario de forma convencional*/                                     
        });                    
        return false;
    }




    /*
    Funcion que se encarga de enviar los datos al formulario de validadcion
    autenticar.php.
    Recibe la ruta, data concatenada, la funcion para el sussces
    */
    function EnviarDatos(datos){   
    // Declaramos y almacenamos las variables con los datos a verificar
    // process y content debe ser false cuando sea un formulario
    //$("#bt_login").button('loading');
    //process=false;        
    // alert(user +' '+ pass+' '+ruta+' '+envio);
         
            var url = '../routes/routes_curriculum.php?OPERACION=REGISTRAR';
            $.ajax({
                url: url, //URL destino
                data: datos,
                processData: false, //Evitamos que JQuery procese los datos, daría error
                contentType: false, //No especificamos ningún tipo de datos
                type: 'POST',
                dataType: "text",
                success: function (resultado) {               
                    
                    var sa_title                = 'Regitro de curriculum';
                    var sa_type                 = 'error'; 
                    var sa_allowOutsideClick    = true;
                    var sa_showConfirmButton    = true;
                    var sa_showCancelButton     = false;            
                    var sa_closeOnCancel        = true;
                    var sa_confirmButtonText    = 'Ok.';
                    var sa_cancelButtonText     = '';
                    var sa_popupTitleSuccess    = '';
                    var sa_popupMessageSuccess  = '';
                    var sa_popupTitleCancel     = '';
                    var sa_popupMessageCancel   = '';
                    var sa_confirmButtonClass   = 'btn-danger';
                    var sa_cancelButtonClass    = 'btn-warning';
                    var sa_closeOnConfirm       = false;
                    var sa_message              = 'Error al registrar los datos';
                    if(resultado==1){
                        sa_message="Proceso realizado exitosamente";
                        sa_type                 = 'success';
                        sa_confirmButtonClass   = 'btn-success';
                        //location.reload();
                        $('#fm_curriculum')[0].reset();
                        //$('#fm_curriculum').trigger("reset");
                    }                    
                    else if(resultado==-2){
                        sa_message              = "No se registraron los documentos adjuntos.";
                        sa_type                 = 'warning';
                        sa_confirmButtonClass   = 'btn-warning';
                    }  
                    /*else{
                        //alert(resultado); 
                        sa_message              = "Error al registrar los datos";//Se produjo un error.";
                        sa_type                 = 'danger';
                        sa_confirmButtonClass   = 'btn-danger';
                    } */         
                    alerta(sa_title,sa_message,sa_type,sa_allowOutsideClick,sa_showConfirmButton,sa_showCancelButton,sa_confirmButtonClass,sa_cancelButtonClass,sa_closeOnConfirm,sa_closeOnCancel,sa_confirmButtonText,sa_cancelButtonText,sa_popupTitleCancel,sa_popupMessageCancel,sa_popupTitleSuccess,sa_popupMessageSuccess);

                }
            
                
                
            }).fail(function(resultado) {
                alert(resultado);
    
                //alert(resultado);
                var sa_title                = 'Regitro de curriculum';
                var sa_type                 = 'danger'; 
                var sa_allowOutsideClick    = true;
                var sa_showConfirmButton    = true;
                var sa_showCancelButton     = false;            
                var sa_closeOnCancel        = true;
                var sa_confirmButtonText    = 'Ok.';
                var sa_cancelButtonText     = '';
                var sa_popupTitleSuccess    = '';
                var sa_popupMessageSuccess  = '';
                var sa_popupTitleCancel     = '';
                var sa_popupMessageCancel   = '';
                var sa_confirmButtonClass   = 'btn-danger';
                var sa_cancelButtonClass    = 'btn-danger';
                var sa_closeOnConfirm       = false;
                var sa_message              = 'Error al registrar los datos';
                alerta(sa_title,sa_message,sa_type,sa_allowOutsideClick,sa_showConfirmButton,sa_showCancelButton,sa_confirmButtonClass,sa_cancelButtonClass,sa_closeOnConfirm,sa_closeOnCancel,sa_confirmButtonText,sa_cancelButtonText,sa_popupTitleCancel,sa_popupMessageCancel,sa_popupTitleSuccess,sa_popupMessageSuccess);

            
  })
         
    };
    
    var alerta = function(sa_title,sa_message,sa_type,sa_allowOutsideClick,sa_showConfirmButton,sa_showCancelButton,sa_confirmButtonClass,sa_cancelButtonClass,sa_closeOnConfirm,sa_closeOnCancel,sa_confirmButtonText,sa_cancelButtonText,sa_popupTitleCancel,sa_popupMessageCancel,sa_popupTitleSuccess,sa_popupMessageSuccess){
        swal({
                      title             : sa_title,
                      text              : sa_message,
                      type              : sa_type,
                      allowOutsideClick : sa_allowOutsideClick,
                      showConfirmButton : sa_showConfirmButton,
                      showCancelButton  : sa_showCancelButton,
                      confirmButtonClass: sa_confirmButtonClass,
                      cancelButtonClass : sa_cancelButtonClass,
                      closeOnConfirm    : sa_closeOnConfirm,
                      closeOnCancel     : sa_closeOnCancel,
                      confirmButtonText : sa_confirmButtonText,
                      cancelButtonText  : sa_cancelButtonText,
                    }/*,
                    function(isConfirm){
                        if (isConfirm){
                            //swal(sa_popupTitleSuccess, sa_popupMessageSuccess, "success");
                        } else {
                            //swal(sa_popupTitleCancel, sa_popupMessageCancel, "error");
                        }
                    }*/);
    };

    var validationForm2 = function(){
        var form2 = $('#fm_curriculum');
            //var error2 = $('.alert-danger', form2);
            //var success2 = $('.alert-success', form2);

            form2.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    //sel_profesion
                    //sel_grado
                    //sel_area_desempeno
                    //
                    tx_nombre: {
                        //minlength: 2,
                        required: true
                    },
                    tx_apellido: {
                        //minlength: 2,
                        required: true
                    },
                    tx_cedula: {
                        required: true,
                        //email: true
                    },
                    tx_email: {
                        required: true,
                        email: true
                    },
                    tx_fecha: {
                        required: true,
                        //url: true
                    },
                    tx_lugar: {
                        required: true,
                        //number: true
                    },
                    tx_anho: {
                        required: true,
                        //digits: true
                    },
                    tx_telefono: {
                        required: true,
                        //creditcard: true
                    },
                    
                    tx_direccion: {
                        required: true,
                        //creditcard: true
                    },
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    /*---------------DATOS DEL FORMULARIO------------------------------*/
                    var foto                    = $('#file_foto').val();
                    var documento               = $('#file_documento').val();                    
                    /*----------------------------------------------------------------*/
                    var bandera                 = false;
                    var sa_title                = 'Error en el formulario';
                    var sa_type                 = 'warning'; 
                    var sa_allowOutsideClick    = true;
                    var sa_showConfirmButton    = true;
                    var sa_showCancelButton     = false;            
                    var sa_closeOnCancel        = true;
                    var sa_confirmButtonText    = 'Ok.';
                    var sa_cancelButtonText     = '';
                    var sa_popupTitleSuccess    = '';
                    var sa_popupMessageSuccess  = '';
                    var sa_popupTitleCancel     = '';
                    var sa_popupMessageCancel   = '';
                    var sa_confirmButtonClass   = 'btn-warning';
                    var sa_cancelButtonClass    = 'btn-warning';
                    var sa_closeOnConfirm       = false;
                    var sa_message              = '';

                    if(!foto && !documento){             
                        sa_message             = 'Algunos campos obligatorios  estan vacios, debes adjuntar la foto y documento. Verifique';              
                    }
                    else if(!documento){
                        sa_message             = 'Algunos campos obligatorios  estan vacios y debes adjuntar el documento. Verifique';            
                    }
                    else if(!foto){
                        sa_message             = 'Algunos campos obligatorios  estan vacios y debes adjuntar la foto. Verifique';            
                    }
                    else{
                        sa_message             = 'Algunos campos obligatorios se encuentran en blanco. Verifique';            
                    }        
                                    
                    alerta(sa_title,sa_message,sa_type,sa_allowOutsideClick,sa_showConfirmButton,sa_showCancelButton,sa_confirmButtonClass,sa_cancelButtonClass,sa_closeOnConfirm,sa_closeOnCancel,sa_confirmButtonText,sa_cancelButtonText,sa_popupTitleCancel,sa_popupMessageCancel,sa_popupTitleSuccess,sa_popupMessageSuccess);            
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");  
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    
                },

                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                },

                submitHandler: function (form) {
                    //success2.show();
                    //error2.hide();
                    //form[0].submit(); // submit the form
                    //bandera=true;
                    //
                    var foto                    = $('#file_foto').val();
                    var documento               = $('#file_documento').val();                    
                    /*----------------------------------------------------------------*/
                    var bandera                 = false;
                    var sa_title                = 'Error en el formulario';
                    var sa_type                 = 'warning'; 
                    var sa_allowOutsideClick    = true;
                    var sa_showConfirmButton    = true;
                    var sa_showCancelButton     = false;            
                    var sa_closeOnCancel        = true;
                    var sa_confirmButtonText    = 'Ok.';
                    var sa_cancelButtonText     = '';
                    var sa_popupTitleSuccess    = '';
                    var sa_popupMessageSuccess  = '';
                    var sa_popupTitleCancel     = '';
                    var sa_popupMessageCancel   = '';
                    var sa_confirmButtonClass   = 'btn-warning';
                    var sa_cancelButtonClass    = 'btn-warning';
                    var sa_closeOnConfirm       = false;
                    var sa_message              = '';

                    if(!foto && !documento){             
                        sa_message             = 'Debes adjuntar la foto y documento. Verifique';   
                        alerta(sa_title,sa_message,sa_type,sa_allowOutsideClick,sa_showConfirmButton,sa_showCancelButton,sa_confirmButtonClass,sa_cancelButtonClass,sa_closeOnConfirm,sa_closeOnCancel,sa_confirmButtonText,sa_cancelButtonText,sa_popupTitleCancel,sa_popupMessageCancel,sa_popupTitleSuccess,sa_popupMessageSuccess);            
                    }
                    else if(!documento){
                        sa_message             = 'Debes adjuntar el documento. Verifique';         
                        alerta(sa_title,sa_message,sa_type,sa_allowOutsideClick,sa_showConfirmButton,sa_showCancelButton,sa_confirmButtonClass,sa_cancelButtonClass,sa_closeOnConfirm,sa_closeOnCancel,sa_confirmButtonText,sa_cancelButtonText,sa_popupTitleCancel,sa_popupMessageCancel,sa_popupTitleSuccess,sa_popupMessageSuccess);    
                    }
                    else if(!foto){
                        sa_message             = 'Debes adjuntar la foto. Verifique';          
                        alerta(sa_title,sa_message,sa_type,sa_allowOutsideClick,sa_showConfirmButton,sa_showCancelButton,sa_confirmButtonClass,sa_cancelButtonClass,sa_closeOnConfirm,sa_closeOnCancel,sa_confirmButtonText,sa_cancelButtonText,sa_popupTitleCancel,sa_popupMessageCancel,sa_popupTitleSuccess,sa_popupMessageSuccess);   
                    }   
                    else{
                        var form    = $(form2)[0];        
                        var datos   = new FormData(form); //Creamos los datos a enviar con el formulario 
                        EnviarDatos(datos);
                    }                                                                     
                }
            });
    }

    return {
        //main function
        init: function() {        

            Profile.initMiniCharts();     
                   
            $('.select2').select2();          
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {               
                $('.select2').select2();
            });
            
            $("#btn_registrar").click(function() {
                var $btn = $(this);
            });  

            //cargar datos profesion
            var url = '../routes/routes_curriculum.php';
            $.get(url,{OPERACION:'GETPORFESION'},function(req){
                var data1 =  eval('('+req+')');
                //console.log(req,data);
                var data = new Array();
                //var option = '';
                for (var i =0; i<data1.length; i++) {
                    data[i]= { id: data1[i]['codorg'], text: data1[i]['descrip'] };                                    
                }                
                $('#sel_profesion').select2({data: data});
            });

            $.get(url,{OPERACION:'GETINSTRUCCION'},function(req){
                var data1 =  eval('('+req+')');
                //console.log(req,data);
                var data = new Array();
                //var option = '';
                for (var i =0; i<data1.length; i++) {
                    data[i]= { id: data1[i]['codigo'], text: data1[i]['descripcion'] };    
                }       
                $('#sel_grado').select2({data: data});
            });

            $.get(url,{OPERACION:'GETDESEMPENO'},function(req){
                var data1 =  eval('('+req+')');
                //console.log(req,data);
                var data = new Array();
                //var option = '';
                for (var i =0; i<data1.length; i++) {
                    data[i]= { id: data1[i]['codigo'], text: data1[i]['descripcion'] };    
                }       
                $('#sel_area_desempeno').select2({data: data});
            });

            $("#tx_cedula").inputmask({
                "mask": "9",
                "repeat": 10,
                "greedy": false
            });

            $("#tx_fecha").inputmask("d/m/y", {
                autoUnmask: true
            }); //direct mask    

            $("#tx_anho").inputmask({
                "mask": "9",
                "repeat": 3,
                "greedy": false
            }); //direct mask   

            $("#tx_telefono").inputmask("mask", {
                "mask": "(999) 999-9999"
            }); //specifying fn & options
        
            //<button class="btn btn-default mt-sweetalert" data-title="Sweet Alerts" data-message="Beautiful popup alerts" data-allow-outside-click="true" data-confirm-button-class="btn-default">Default Alert</button>
                

            
                
            //$('.select2-container').children().css('border-radius','0px')
        },
        initMiniCharts: function() {

            // IE8 Fix: function.bind polyfill
            if (App.isIE8() && !Function.prototype.bind) {
                Function.prototype.bind = function(oThis) {
                    if (typeof this !== "function") {
                        // closest thing possible to the ECMAScript 5 internal IsCallable function
                        throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
                    }

                    var aArgs = Array.prototype.slice.call(arguments, 1),
                        fToBind = this,
                        fNOP = function() {},
                        fBound = function() {
                            return fToBind.apply(this instanceof fNOP && oThis ? this : oThis,
                                aArgs.concat(Array.prototype.slice.call(arguments)));
                        };

                    fNOP.prototype = this.prototype;
                    fBound.prototype = new fNOP();

                    return fBound;
                };
            }

            $("#sparkline_bar").sparkline([8, 9, 10, 11, 10, 10, 12, 10, 10, 11, 9, 12, 11], {
                type: 'bar',
                width: '100',
                barWidth: 6,
                height: '45',
                barColor: '#F36A5B',
                negBarColor: '#e02222'
            });

            $("#sparkline_bar2").sparkline([9, 11, 12, 13, 12, 13, 10, 14, 13, 11, 11, 12, 11], {
                type: 'bar',
                width: '100',
                barWidth: 6,
                height: '45',
                barColor: '#5C9BD1',
                negBarColor: '#e02222'
            });
        },
        armarFormulario:function(){
            armarFormularioCurriculum();
        },
        validationForm:function(){
            validationForm2();
        }

    }
    
}();

if (App.isAngularJsApp() === false) { 
    jQuery(document).ready(function() {
        Profile.init();
        Profile.armarFormulario();
        Profile.validationForm();
    });
}