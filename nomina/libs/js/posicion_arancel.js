jQuery(document).ready(function($){

   $("#formulario").submit(function(){
      if($("#codigo").val()==""){
         alert("Debe Ingresar el Codigo!!.");
         $("#codigo").focus();
         return false;
      }
      if($("#descripcion").val()==""){
         alert("Debe Ingresar la Descripcion!!.");
         $("#descripcion").focus();
         return false;
      }
   });

});