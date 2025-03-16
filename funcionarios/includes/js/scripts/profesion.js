$(document).ready(function(){

 	//Listar();
 	$("#Agregar").on("click",function(){
 		Agregar();
 	});
 	$("#Buscar").on("click",function(){
 		Buscar();
 	});

});

function Buscar()
{
	var accion='Buscar';
	buscar = $("#buscar").val();
	$.get("../../includes/clases/controladores/ControlProfesion.php",{Accion:accion,buscar:buscar},function(profesion)
	{
		$("#profesion").empty();
		$("#profesion").append(profesion);

	});

}

function Listar()
{
	var accion='Listar';
	$.get("../../includes/clases/controladores/ControlProfesion.php",{Accion:accion},function(profesion)
	{
		$("#profesion").empty();
		$("#profesion").append(profesion);
		$("table").tableSorter();

	});

}

function Agregar()
{
	profesion = $("#AgregarDescripcion").val();
	var accion='Agregar';
	$.get("../../includes/clases/controladores/ControlProfesion.php",{Accion:accion,descrip:profesion},function(profesion)
	{
		$("#mensajes").empty();
		$("#AgregarDescripcion").val('');
		$("#mensajes").append(profesion);
		$('#myModal').modal('toggle');


	});
}

function Editar()
{

	var accion='Editar';
	
	/*$("td#linea").each(function()
 	{ 		
 		$("a#editar").each(function(){
 			$(this).on("click",function(){
	 			var inc = $(this).attr("idedit");
				alert(inc);	
			});
 		});		
 	});*/
	/*$.get("../../includes/clases/controladores/ControlProfesion.php",{Accion:accion},function(profesion)
	{
		$("#profesion").empty();
		$("#profesion").append(profesion);

	});*/
}
function Eliminar()
{
	var accion='Eliminar';
	$.get("../../includes/clases/controladores/ControlProfesion.php",{Accion:accion},function(profesion)
	{
		$("#profesion").empty();
		$("#profesion").append(profesion);

	});
}