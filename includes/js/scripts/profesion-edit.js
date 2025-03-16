$(document).ready(function(){
 	$("#Editar").on("click",function(){
		var accion='Editar';
		var codorg = $("#codorg").val();
		var descrip = $("#descrip").val();
		$.get("../../includes/clases/controladores/ControlProfesion.php",
			{
				Accion:accion,
				codorg:codorg,
				descrip:descrip,
			},
			function(profesion)
			{
				$("#mensajes").empty();
				$("#mensajes").append(profesion);

			}); 	
	});
 	
    

});
