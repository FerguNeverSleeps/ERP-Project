if(!_.isUndefined($.datepicker)){
  $.datepicker.regional['es'] = {
       closeText: 'Cerrar',
       prevText: '<Ant',
       nextText: 'Sig>',
       currentText: 'Hoy',
       monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
       monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
       dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
       dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
       dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
       weekHeader: 'Sm',
       dateFormat: 'dd/mm/yy',
       firstDay: 1,
       isRTL: false,
       showMonthAfterYear: false,
       yearSuffix: ''
      };
  $.datepicker.setDefaults($.datepicker.regional['es']);
}

jQuery( document ).ready(function( $ ) {
      /**
      * Valida si la referecia del producto ya existe
      * SUR-3013
      */

	$( "#referencia" ).change(function() {
	   var object = $('.message');
      var message = 'Validando referencia...';

      if ($( "#referencia" ).val()){
         showMessage(false, object, '', '', '', 0);
	  		var object_ref = $( "#referencia" );
         var referencia = object_ref.val();
	  		$.ajax({
         	type: "GET",
       		url:  "../../libs/php/ajax/ajax.validar_referencia.php",
       		dataType: "json",
       		data: "ref="+referencia,
         	beforeSend: function(){
              showMessage(true, object, 'alert-info', message, 0);
           	},
         	success: function(data, status){
           		if(data.success == false){
                  message = data.message;
           			showMessage(true, object, 'alert-danger', message, 0);
                  object_ref.val("");
                  object_ref.focus();
           		}else{
                  showMessage(true, object, 'alert-success', 'La referencia es valida', 1);
               }
        		}
      	});

	  }
	});


   $("#cod_cliente").keydown(function (e) {
           // Allow: backspace, delete, tab, escape, enter and
            // Allow: Ctrl+A
             // Allow: home, end, left, right
           if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 39)) {
               return;
            }
           // Ensure that it is a number and stop the keypress
           if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
               e.preventDefault();
           }
       });

   $("#cod_cliente").blur(function() {
      var object = $('.message');
      var message = 'Validando Codigo...';

      if ($( "#cod_cliente" ).val()){
         showMessage(false, object, '', '', '', 0);
         var object_ref = $( "#cod_cliente" );
         var codigoCustomer = object_ref.val();
         var id = null;
         if ($("#id_cliente").val()){
            id = $("#id_cliente").val();
         }

         $.ajax({
            type: "GET",
            url: "../../libs/php/ajax/ajax.validar_CustomerCod.php",
            dataType: "json",
            data: "cod="+codigoCustomer+"&id="+id,
            beforeSend: function(){
               showMessage(true, object, 'alert-info', message, 0);
            },
            success: function(data, status){
               if(data.success == false){
                  console.log(data);
                  message = data.message;
                  showMessage(true, object, 'alert-danger', message, 0);
                  object_ref.val("");
                  object_ref.focus();
               }else{
                 showMessage(true, object, 'alert-success', data.message, 1);
               }
            }
         });
      }
  });

   /**
   * Muestra un mensaje un mensaje para el objeto creado
   * @param show {Boolean} true, false para mostrar el mensaje
   * @param object {Object} Objeto html que contendre el mensaje
   * @param message {String} texto del mensaje
   * @param seconds {integer} cantidad en entero de los segundo que tendra el mensaje
   * @returns {integer} el codigo de retorno 0
   */
	function showMessage(show, object, type, message, seconds){
      var class_remove = ['alert-danger', 'alert-success', 'alert-info', 'alert-warning'];
      object.removeClass(class_remove.join(' '));
      if(show == true){
         object.addClass(type);
         object.html(message);
         object.fadeIn( "slow" );

         if(seconds != 0){
            setTimeout(function(){
               object.fadeOut( "slow" );
            }, seconds * 1000);
         }

      }else{
         object.fadeOut( "slow" );
      }
   };
   try{
    $(".date").datepicker({
       showOtherMonths: true,
       selectOtherMonths: true,
       dateFormat: 'yy-mm-dd'
    });
   }catch(e){}

});



