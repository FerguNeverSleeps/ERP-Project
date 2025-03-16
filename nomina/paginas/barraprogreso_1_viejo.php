<?php
session_start();
ob_start();
?>
<?php
include("../lib/common.php");
include ("../header.php");
include ("func_bd.php");
include ("funciones_nomina.php");
include("../../includes/dependencias.php");


ini_set("memory_limit", "-1");
set_time_limit(0);
?>
<html>
<head>
<title>Generando Planilla</title>

<script type="text/javascript">
function CerrarVentana(){
	javascript:window.close();
}

function Enviar(){
	$('#go-process').hide(); $('#td_ok').hide(); $('#td_cerrar').hide();
	$('#in-process').show();

	//document.getElementById('progressBarMsg').innerHTML='Generando N&oacute;mina, Espere por favor...';
	var registro_id  = $("#registro_id").val();
	var codigo_nomina= $("#codigo_nomina").val();
	$("#progressBar").load("barra_query.php", {registro_id: registro_id, codigo_nomina: codigo_nomina},
		function(response, status, xhr){
			console.log(response);
			$('#td_cerrar').show();
			if(status == "error"){
				$("#progressBar").html('<p>Ha ocurrido un error</p>');
 				// var msg = "Error!, algo ha sucedido: ";
                // $("#capa").html(msg + xhr.status + " " + xhr.statusText);
			}else{
				$("#progressBar").html('<div class="alert alert-success" role="alert"><br><img src="../imagenes/ok.gif" align="absmiddle"> Se ha generado exitosamente la Planilla</div>');
			}
		});
}
</script>
</head>
<body>
<form id="frmPrincipal" name="frmPrincipal" method="post" action="">
	  <input type="hidden" name="registro_id" id="registro_id" value="<?php echo $_GET['registro_id']; ?>">
	  <input type="hidden" name="codigo_nomina" id="codigo_nomina" value="<?php echo $_SESSION['codigo_nomina'];?>">
	  <div class="form-control" id="progressBar">
	  		<!-- <div id="go-process" class="form-control center"><br><br>Presione Aceptar para Generar Planilla...</div> -->
	  		<div id="in-process" class="form-control center" style="display: none">
	  			<br><br><br><br><br><br>
	  			<center><img src="../imagenes/loader.gif"><center>
	  			<center><p><b>Generando Planilla</b></p><center>
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
        <center>Presione aceptar para generar la planilla</center><br><br>
        <center>
            <input class="btn btn-primary" value="Aceptar" onclick="javascript:Enviar();">
            <input class="btn btn-primary" value="Cerrar" onclick="javascript:CerrarVentana();">
       </center>
	  </div>
        <!--
	      <td class="form-control" width="140" id="td_ok"><?php btn('ok','Enviar();',2); ?></td>
	      <td class="form-control" width="140" id="td_cerrar" ><?php btn('cerrar','CerrarVentana();',2); ?></td> -->
</form>
</body>
</html>
