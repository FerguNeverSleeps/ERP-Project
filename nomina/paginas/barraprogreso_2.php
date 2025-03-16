<?php 
session_start();
ob_start();
?>
<?php
include("../lib/common.php");
include ("../header.php");
include ("func_bd.php");
include ("funciones_nomina.php");

ini_set("memory_limit", "-1");
set_time_limit(0);
?>
<html>
<head>
<title>Importando Asistencia</title>

<script type="text/javascript">

function CerrarVentana(){
	javascript:location.href="importar_asistencia.php";
}
function Enviar(){
	$('#go-process').hide(); $('#td_ok').hide(); $('#td_cerrar').hide();

	$('#in-process').show();
	
	//document.getElementById('progressBarMsg').innerHTML='Generando N&oacute;mina, Espere por favor...';
	var txtFechaInicio  = $("#txtFechaInicio").val();
	var txtFechaFin= $("#txtFechaFin").val();
	$("#progressBar").load("procesar_control_asistencia.php", {txtFechaInicio: txtFechaInicio, txtFechaFin: txtFechaFin}, 
		function(response, status, xhr){
			console.log(response);
			$('#td_cerrar').show();
			if(status == "error"){
				$("#progressBar").html('<p>Ha ocurrido un error</p>');
 				// var msg = "Error!, algo ha sucedido: ";
                // $("#capa").html(msg + xhr.status + " " + xhr.statusText);
			}else{
				$("#progressBar").html('<div class="center"><br><img src="../imagenes/ok.gif" align="absmiddle"> Se ha importado exitosamente la Asistencia</div>');
			}
		});
}
	
</script>
<style type="text/css">
	.center{
		text-align: center;
	}

	.hide{
		display: none;
	}

	#td_ok{
		padding-left:40px;
	}

	#table_btn{
		margin:40px auto 10px
	}
</style>
</head>
<body>
<form id="frmPrincipal" name="frmPrincipal" method="post" action="">
	  <input type="hidden" name="txtFechaInicio" id="txtFechaInicio" value="<?php echo $_GET['txtFechaInicio']; ?>">
	  <input type="hidden" name="txtFechaFin" id="txtFechaFin" value="<?php echo $_GET['txtFechaFin']; ?>">
	  	  <input type="hidden" name="codigo_nomina" id="codigo_nomina" value="<?php echo $_SESSION['codigo_nomina'];?>">
	  <div id="progressBar">
	  		<div id="go-process" class="center"><br><br>Presione Aceptar para Importar Control de Asistencia...</div>

	  		<div id="in-process" class="center hide">	  			
	  			<br><br><br>
	  			<img src="../imagenes/loader.gif"> 
	  			<p><b>Importando Control de Asistencia</b></p>
	  			<!--
	  			<br><br><br>
	  			<img src="../imagenes/loader2.gif" align="absmiddle">&nbsp;&nbsp;&nbsp;<b>Generando...</b>
	  			-->
	  			<!--
	  			<br><br>
	  			<p><b>Generando...</b></p>
	  			<img src="../imagenes/loader3.gif"> 
				-->

	  		</div>
	  </div>
	  <table width="280" border="0" id="table_btn">
	    <tr>
	      <td width="140" id="td_ok"><?php btn('ok','Enviar();',2); ?></td>
	      <td width="140" id="td_cerrar" ><?php btn('cerrar','CerrarVentana();',2); ?></td>
	    </tr>
	  </table>	
</form>
</body>
</html>