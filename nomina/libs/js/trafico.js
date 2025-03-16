jQuery( document ).ready(function( $ ) {

	$("#salida").click(function() {
      var url = "/selectra_planilla/selectraerp/modulos/principal/?iframe&opt_menu=5&opt_seccion=229&opt_subseccion=ListarFacturas&action=salida";
      window.location.href = url;
	});

   $("#entrada").click(function(){
      var url = "/selectra_planilla/selectraerp/modulos/principal/?iframe&opt_menu=5&opt_seccion=229&opt_subseccion=entrada";
       window.location.href = url;
   });

   $("#traspaso").click(function() {
      var url = "/selectra_planilla/selectraerp/modulos/principal/?iframe&opt_menu=5&opt_seccion=229&opt_subseccion=ListarFacturas&action=traspaso";
      window.location.href = url;
   });

    $("#documento").click(function() {
      var url = "/selectra_planilla/selectraerp/modulos/principal/?iframe&opt_menu=5&opt_seccion=229&opt_subseccion=documentos";
      window.location.href = url;
   });

    $("#reempaque").click(function() {
      var url = "/selectra_planilla/selectraerp/modulos/principal/?iframe&opt_menu=5&opt_seccion=208";
      window.location.href = url;
   });

    $("#reempaque_realizado").click(function() {
      var url = "/selectra_planilla/selectraerp/modulos/principal/?iframe&opt_menu=5&opt_seccion=218";
      window.location.href = url;
   });


});



