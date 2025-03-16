jQuery( document ).ready(function( $ ){
  var msgRequired = "Este campo es Requerido";
  var formSalida = $('#trafico_salida');
  var now = new Date();

  $("#fecha_registro").datepicker({dateFormat: 'yy-mm-dd'}).datepicker("setDate", now);
  $("#clave-operacion").blur(function(){
      var object_clave = $("#clave-operacion");

      if (object_clave.val() != ''){
         var object_message = $('#message-clave');
         var message = "Comprobando clave";

         $.ajax({
               type: "GET",
               url:  "trafico/validaciones/ajax.ValidarClaveOperacion.php",
               dataType: "json",
               data: "clave="+object_clave.val(),
               beforeSend: function(){
                 showMessage(true, object_message, 'alert-info', message, 0);
               },
               success: function(data, status){
                  console.log(data);
                  if(data.success == false){
                     message = data.message;
                     showMessage(true, object_message, 'alert-danger', message, 1);
                     object_clave.val("");
                     object_clave.focus();
                  }else{
                     showMessage(true, object_message, 'alert-success', 'La clave es valida', 1);
                     console.log(data);
                     $('#entrada').val(data.empresa.descripcion);

                  }
               }
         });
      }
   });

  $('#loader').hide();
  formSalida.validate({
      rules : {
         tipo_carga :"required",
         pais_destino : "required",
         puerto_salida : "required",
         puerto_destino : "required"
      },
      messages : {
         tipo_carga : msgRequired,
         pais_destino : msgRequired,
         puerto_salida : msgRequired,
         puerto_destino : msgRequired
      },
      showErrors: function (errorMap, errorList){
         $.each(this.successList, function (index, value) {
            $('#'+value.id+'').tooltip('destroy');
         });

         $.each(errorList, function (index, value) {
            $('#'+value.element.id+'').attr('title',value.message).tooltip({
               placement: 'bottom',
               trigger: 'manual',
               delay: { show: 500, hide: 5000 }
            }).tooltip('show');
         });
      },
      submitHandler: function(form) {
         var data = _.every(formSalida[0].elements, function(input, key){ return input.type == 'hidden' && input.value !==undefined })
         console.log(data);
           
         _.each(formSalida[0].elements, function(input, key){
            if (input.type == 'hidden' && input.value !==undefined){
               console.log(input.name);
               console.log(input.value);
               form.submit();
            }
         });
          
      }
  });

   $("#search-empresas").fancybox({
      maxWidth : 700,
      minWidth : 700,
      width :'70%'
   });
   $("#buttom-facturas").fancybox({
      maxWidth : 1000,
      minWidth : 1000,
      width :'70%'
   });
   $("#buttom-compras").fancybox({
      maxWidth : 1000,
      minWidth : 1000,
      width : '70%',
   });

   $(".close-fancy").click(function() {
      $.fancybox.close( true );
   });

   $("#sendOrden").click(function() {
      var object = $('.message');
      var message = '<strong>Por favor espere!</strong>, Procesando...';
      var form = $('#ordenCompra')[0];
      var data = _.find(form.elements, function(input, key){ return input.checked==true });
      if (data != undefined ){
         showMessage(true, object, 'alert-info', message, 0);
         form.submit();
      }else{
         message = 'Debe seleccionar un elemento !';
         showMessage(true, object, 'alert-warning', message, 0);
      }
   });

   /**
   * Muestra un mensaje un mensaje para el objeto creado
   * @param show {Boolean} true, false para mostrar el mensaje
   * @param object {Object} Objeto html que contendre el mensaje
   * @param message {String} texto del mensaje
   * @param seconds {integer} cantidad en entero de los segundo que tendra el mensaje
   */
   function showMessage(show, object, type, message, seconds){
      var class_remove = ['alert-danger', 'alert-success', 'alert-info', 'alert-warning'];
      object.removeClass(class_remove.join(' '));
      if(show == true){
         object.addClass(type);
         object.html(message);
         object.css('display', 'block');
         if(seconds != 0){
            setTimeout(function(){
               object.fadeOut( "slow" );
            }, seconds * 1000);
         }
      }else{
         object.fadeOut( "slow" );
      }
   };
});


