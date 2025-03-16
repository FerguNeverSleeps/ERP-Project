<?php
session_start();
ob_start();
require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=new bd($_SESSION['bd']);
$db = new bd(SELECTRA_CONF_PYME);
/* Permisos*/
$sql_user = "SELECT COUNT(*)
FROM nomusuarios as a LEFT JOIN roles as b on (b.id = a.id_rol)
WHERE a.coduser = '{$_SESSION[cod_usuario]}' and b.estado ='1' and a.id_rol='1'";
$es_admin    = $db->query($sql_user)->num_rows;
$es_admin    = ($es_admin == 1) ? 1 : 0 ;
$termino     = (isset($_SESSION['termino'])) ? $_SESSION['termino'] : '';
$registro_id = (isset($_POST['registro_id'])) ? $_POST['registro_id'] : '';
$op          = (isset($_POST['op'])) ? $_POST['op'] : '';
function get_client_ip() {
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP'))
	  $ipaddress = getenv('HTTP_CLIENT_IP');
  else if(getenv('HTTP_X_FORWARDED_FOR'))
	  $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if(getenv('HTTP_X_FORWARDED'))
	  $ipaddress = getenv('HTTP_X_FORWARDED');
  else if(getenv('HTTP_FORWARDED_FOR'))
	  $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if(getenv('HTTP_FORWARDED'))
	  $ipaddress = getenv('HTTP_FORWARDED');
  else if(getenv('REMOTE_ADDR'))
	  $ipaddress = getenv('REMOTE_ADDR');
  else
	  $ipaddress = 'UNKNOWN';
  return $ipaddress;
}

if ($op==3) //Se presiono el boton de Eliminar
{
	$sql = "DELETE FROM nom_nominas_pago
	        WHERE codnom='{$registro_id}' AND tipnom='{$_SESSION['codigo_nomina']}'
	        AND   codtip='{$_SESSION['codigo_nomina']}'";
	$conexion->query($sql);

	$sql = "DELETE FROM nom_movimientos_nomina
	        WHERE codnom='{$registro_id}'
	        AND   tipnom='{$_SESSION['codigo_nomina']}'";
	$conexion->query($sql);
	
	//LOG TRANSACCIONES -ELIMINAR PLANILLA            
	$descripcion_transaccion = 'ELIMINAR PLANILLA: ' . $_POST['registro_id'] . ', ' . $_POST['op']  . ', ' . $_SESSION['usuario']  ;
	$sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
	VALUES ('', '".$descripcion_transaccion."', now(), 'ELIMINAR PLANILLA', 'nomina_de_pago.php', 'ELIMINAR','".$_SESSION['usuario']."','".$_SESSION['usuario']."','".get_client_ip()."')";
	
	$conexion->query($sql_transaccion);

	activar_pagina("nomina_de_pago.php");
}


$add="";
if(isset($_REQUEST["search_anio"]))
    $add=" (periodo_ini like '".$_REQUEST["search_anio"]."-%' or periodo_fin like '".$_REQUEST["search_anio"]."-%') AND ";


$sql = "SELECT *
		FROM   nom_nominas_pago
        WHERE  $add tipnom='{$_SESSION['codigo_nomina']}' AND codtip='{$_SESSION['codigo_nomina']}'
        AND    frecuencia not in ('8','10')
        ORDER BY codnom DESC";
$res = $conexion->query($sql, "utf8");

$sql_max = "SELECT max(codnom) as maximo
            FROM nom_nominas_pago
            WHERE tipnom='{$_SESSION['codigo_nomina']}'  AND  (frecuencia<>6 and frecuencia<>10)";
$res_max  = $conexion->query($sql_max);
$fila_max = $res_max->fetch_assoc();
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
  .text-middle{
  	vertical-align: middle !important
  }

  .ajustar-texto
  {
  	white-space: normal !important;
  }

  td.icono
  {
  	padding-left: 0px !important;
  	padding-right: 0px !important;
  	width: 10px !important;
  }
</style>
<script type="text/javascript" src="../lib/common.js"></script>
<script type="text/javascript">
function GenerarNomina()
{
	AbrirVentana('barraprogreso_1.php', 150, 500, 0);
}

function CerrarVentana()
{
	javascript:window.close();
}

function showProcesando(){/*App.blockUI({
        target: '#blockui_portlet_body',
        boxed: true,
        message: 'Procesando'
	});*/
	
	$('#blockui_portlet_body').block({ 
		boxed: true,
        message: 'Procesando'
	});
}
var fnGet = function (url, data) {
	var objeto;
	$.ajax({
		url: url,
		data: data,
		dataType: "JSON",
		success: function (resultado) {
			objeto = resultado;
		},
		"type": "GET",
		"cache": false,
		"async": false,
		"error": function (resultado) {
			alert("Error");
		}
	});
	return objeto;
};

function enviar(op,id,nomina,codtip)
{
	if (op==1){		// Opcion de Agregar
		//document.frmAgregar.registro_id.value=id;
		document.frmPrincipal.op.value=op;
		document.frmPrincipal.action="ag_nomina_pago.php";
		document.frmPrincipal.submit();
	}
	if (op==2){	 	// Opcion de Modificar
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.op.value=op;
		document.frmPrincipal.action="ag_nomina_pago.php";
		document.frmPrincipal.submit();
	}
	if (op==3){		// Opcion de Eliminar
		if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
		{
			document.frmPrincipal.registro_id.value=id;
			document.frmPrincipal.op.value=op;
  			document.frmPrincipal.submit();
		}
	}

	if (op==4){		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('barraprogreso_1.php?registro_id='+id+'&codigo_nomina='+nomina,250,900,0);
	}

	if (op==5){	// Movimiento de Nómina
		document.location.href="movimientos_nomina_pago.php?codigo_nomina="+id+"&codt="+codtip

	}
	if (op==6){	//CERRAR NÓMINA
			showProcesando();
		if (confirm("\u00BFEst\u00E1 seguro que desea cerrar esta <?php echo $termino; ?>?"))
		{
			var data = {};
			data.codigo_nomina = id;
			var Fichas = fnGet("cerrar_nomina.php",data);
			console.log(Fichas);
			if (Fichas.Estado === "0") {
				//ModalListaFichas();
				var lista = $('#listarFichasNegativo');
				var tablaFichas = '<table class="table table-striped table-bordered table-hover">';
				tablaFichas += '<thead><tr><td>FICHA</td><td>NOMBRE</td><td>NETO</td><td>NETO 3</td></tr><thead><tbody>';

				$('#listarFichasNegativo').empty();
				Fichas.data.forEach(function (item, index, array) {
					tablaFichas += '<tr> <td>' + item.ficha + '</td><td>' + item.apenom + '</td><td>' + item.neto + '</td><td>' + item.neto3 + '</td></tr>';
				});
				tablaFichas += '</tbdoy></table>';
				$('#listarFichasNegativo').html(tablaFichas);
            	$('#ModalFichas').modal('show');
				$('#blockui_portlet_body').unblock(); 
				$("#botonCerrar").on("click",function(){
					$("#blockui_portlet_body").unblock();
					location.href="nomina_de_pago.php";

				});
			}
			else {
//				window.open("../procesos/zoho_contable/index.php?codnom="+id+"&codtip="+codtip);
				location.href="nomina_de_pago.php";
			}
			$("#blockui_portlet_body").unblock();

			/*var cerrar_nomina=abrirAjax()
			cerrar_nomina.open("GET", "cerrar_nomina.php?codigo_nomina="+id, true)
			cerrar_nomina.onreadystatechange=function()
			{
				if (cerrar_nomina.readyState==4)
				{
					//municipio.parentNode.innerHTML =
					//alert(cerrar_nomina.responseText)
					document.location.href="nomina_de_pago.php"
				}
			}
			cerrar_nomina.send(null);*/
		}
			$("#blockui_portlet_body").unblock();

	}
	if (op==7){	//CERRAR NÓMINA
		var nomina=abrirAjax()
		nomina.open("GET", "abrir_nomina.php?codigo_nomina="+id, true)
		nomina.onreadystatechange=function()
		{
			if (nomina.readyState==4)
			{
					//municipio.parentNode.innerHTML =
						//alert(cerrar_nomina.responseText)
				document.location.href="nomina_de_pago.php"
			}
		}
		nomina.send(null);

	}
	if (op==8){		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo.php?nomina='+id,250,950,0);
	}
	if(op==9)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom.php?nomina='+id,500,950,0);
	}
	if(op==10)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom_desc.php?nomina='+id,300,710,0);
	}
	if(op==11)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom2.php?nomina='+id,500,580,0);
	}
	if(op==12)
	{		// Masivos Conceptos
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom3.php?nomina='+id,500,580,0);
	}
        if(op==13)
	{		// GENERAR TXT
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_generar_nom_txt.php?nomina='+id,500,580,0);
	}
        if(op==14)
	{		// GENERAR PDF
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_generar_nom_pdf.php?nomina='+id,500,580,0);
	}
        if(op==15)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom_excel.php?nomina='+id,600,800,0);
	}
        if(op==100)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_nomina_patronales_recalcular.php?registro_id='+id+'&codigo_nomina='+nomina,250,900,0);
	}
}
function accion(opcion, cod)
{
	var nomina=abrirAjax()
	nomina.open("GET", "ajax/preaprobar_nomina.php?codigo_nomina="+cod, true)
	nomina.onreadystatechange=function()
	{
		//console.log(nomina);
		if (nomina.readyState==4)
		{
				//municipio.parentNode.innerHTML =
			alert(nomina.responseText)
			document.location.href="nomina_de_pago.php"
		}
	}
	nomina.send(null);
}

function AbrirVentana(Ventana,Largo,Alto,Modal)
{
	if (Modal==1)
	{
	mainWindow = showModalDialog(Ventana,'mainWindow','dialogWidth:'+Alto+'px;dialogHeight:'+Largo+'px;resizable:yes;toolbar:no;menubar:no;scrollbars:yes;help: no');
	}
	else
	{

	mainWindow = window.open(Ventana,'mainWindow','menub ar=no,resizable=no,width='+Alto+',height='+Largo+',left=0,top=0,titlebar=yes,alwaysraised=yes,status=no,scrollbars=yes');
	}


}
</script>

<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<a href="nomina_de_pago.php"><img src="images/Clipboard.png" width="23" height="21"/></a> Lista de <?php echo $termino; ?>s de Pago: <?php echo ($_SESSION['nomina']); ?>
							</div>
							<div class="actions">
								<?php if (isset($_SESSION['add_planilla'])): ?>	
								<a class="btn btn-sm blue"  onclick="javascript: window.location='ag_nomina_pago.php'">
									<i class="fa fa-plus"></i>
									Agregar
								</a>
								<?php endif ?>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_transacciones.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body" id="blockui_portlet_body">
							<div id="div_search_situ" style="display: inline;">
                                        <select id="search_anio" class="form-control input-inline input-small" onchange="cargar_anio()">
                                                <option value="Todos" value="">Año</option>
                                                <?php 
                                                        $sql_anio = "SELECT DISTINCT(YEAR(fecha_reg)) as anio FROM reloj_encabezado";
                                                        $res_anio = $conexion->query($sql_anio, "utf8");
                                                        //$res_anio  =mysqli_query($conexion,$sql_anio); 

                                                        while($fila = $res_anio->fetch_assoc())
                                                        { ?>
                                                                <option value="<?php echo $fila['anio']; ?>" <?php if($_REQUEST["search_anio"]==$fila['anio']) echo "selected";?> ><?php echo $fila['anio']; ?></option>
                                                          <?php
                                                        }
                                                ?>										
                                        </select>
                                </div>
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
							<table class="table table-striped table-bordered table-hover" id="table_datatable">
							<thead>
							<tr>
								<th class="text-center text-middle"><?php echo $termino; ?></th>
								<th class="text-center text-middle ajustar-texto" style=" max-width: 75px !important;">Tipo <?php echo $termino; ?></th>
								<th class="text-center text-middle">Descripci&oacute;n</th>
								<th class="text-center text-middle">Estado</th>
								<th class="text-center text-middle">Inicio</th>
								<th class="text-center text-middle">Final</th>
								<th class="text-center text-middle">Pago</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
                                                                <th>&nbsp;</th>
								<!--<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>-->
								<th>&nbsp;</th>
								<th>&nbsp;</th>
                                                                <th>&nbsp;</th>
							</tr>
							</thead>
							<tbody>
							<?php
								while( $fila = $res->fetch_assoc() )
								{
								?>
									<tr class="odd gradeX">
										<td class="text-center ajustar-texto"><?php echo $fila['codnom']; ?></td>
										<td class="text-center ajustar-texto"><?php echo $fila['tipnom']; ?></td>
										<td class="ajustar-texto" style="max-width: 310px !important;"><?php echo $fila['descrip']; ?></td>
										<td class="text-center ajustar-texto" style="max-width: 40px !important;"><?php echo $fila['status']; ?></td>
										<td class="text-center ajustar-texto" style="max-width: 80px !important;"><?php echo date("d/m/Y",strtotime($fila['periodo_ini']));?></td>
										<td class="text-center ajustar-texto" style="max-width: 80px !important;"><?php echo date("d/m/Y",strtotime($fila['periodo_fin']));?></td>
										<td class="text-center ajustar-texto" style="max-width: 80px !important;"><?php echo date("d/m/Y",strtotime($fila['fechapago']));?></td>
										<?php if (isset($_SESSION['ver_planilla'])){ ?>
										<td class="text-center icono"><a href="javascript:enviar(5,<?php echo($fila['codnom']); ?>,<?php echo $_SESSION['codigo_nomina'];?>,<?php echo $fila['codtip']; ?>);" title="Movimientos"><img src="images/view.gif" width="16" height="16"></a></td>
										<?php }else{ ?>
                  						<td class="text-center icono"></td>
                  						<?php } ?>
										<?php
										if(($fila["status"]=="A"))
										{ 
											/* Opciones en comentarios de html porque se necesitan arreglar*/
										?>
											<?php if (isset($_SESSION['generar_planilla'])){ ?>
											<td class="text-center icono"><a href="javascript:enviar(4, <?php echo $fila['codnom']; ?>,<?php echo $_SESSION['codigo_nomina']?>,0);" title="Generar <?php echo $termino; ?>"><img src="img_sis/ico_propiedades.gif" width="15" height="15" ></a></td>
											<?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>
                                                                                                                <td class="text-center icono"><a href="javascript:enviar(8, <?php echo $fila['codnom']; ?>,<?php echo $_SESSION['codigo_nomina']?>,0);" title="Agregar variantes" ><img src="img_sis/ico_add.gif" width="15" height="15"></a></td>
                                                                                                                <td class="text-center icono"><a href="javascript:enviar(9, <?php echo $fila['codnom']; ?>,<?php echo $_SESSION['codigo_nomina']?>,0);" title="Agregar variantes"><img src="img_sis/ico_est2.gif" width="15" height="15"></a> </td>
                                                                                                                <td class="text-center icono"><a href="javascript:enviar(15, <?php echo $fila['codnom']; ?>,<?php echo $_SESSION['codigo_nomina']?>,0);" title="Agregar Excel"><img src="img_sis/ico_excel.gif" width="15" height="15"></a> </td>
                                                                                                                <!--<td class="text-center icono"><a href="javascript:enviar(11,<?php echo $fila['codnom']; ?>,<?php echo $_SESSION['codigo_nomina']?>,0);" title="Agregar variantes (valor)"><img src="img_sis/ico_est2.gif" width="15" height="15" ></a></td>
                                                                                                                <td class="text-center icono"><!--<a href="javascript:enviar(12,<?php echo $fila['codnom']; ?>,<?php echo $_SESSION['codigo_nomina']?>,0);" title="Cargar varios conceptos ( Diferente valor)"><img src="img_sis/conceptos.gif" width="15" height="15"></a></td>
                                                                                                                <td class="text-center icono"><!--<a href="javascript:enviar(10,<?php echo $fila['codnom']; ?>,<?php echo $_SESSION['codigo_nomina']?>,0);" title="Agregar variantes"><img src="img_sis/ico_est4.gif" width="15" height="15"></a></td>-->
                                                                                                                <?php if (isset($_SESSION['consultar_planilla'])){ ?>
                                                                                                                <td class="text-center icono"><a href="javascript:enviar(2, <?php echo $fila['codnom']; ?>,0,0);" title="Consutar <?php echo $termino; ?>"><img src="img_sis/ico_list.gif"   width="15" height="15"></a></td>
                                                                                                                <?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>
                                                                                                                <?php if (isset($_SESSION['eliminar_planilla'])){ ?>
                                                                                                                <td class="text-center icono"><a href="javascript:enviar(3, <?php echo $fila['codnom']; ?>,0,0);" title="Eliminar <?php echo $termino; ?>"><img src="../imagenes/delete.gif" width="16" height="16"></a></td>
                                                                                                                <?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>
                                                                                        <!--<?php if (isset($_SESSION['txt_planilla'])){ ?>
                                                                                                                <td class="text-center icono"><a href="javascript:enviar(13, <?php echo $fila['codnom']; ?>,0,0);" title="Generar TXT <?php echo $termino; ?>"><img src="img_sis/txt.png" width="16" height="16"></a></td>
                                                                                                                <?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>
                                                                                        <?php if (isset($_SESSION['pdf_planilla'])){ ?>
                                                                                                                <td class="text-center icono"><a href="javascript:enviar(14, <?php echo $fila['codnom']; ?>,0,0);" title="Generar PDF <?php echo $termino; ?>"><img src="img_sis/pdf.png" width="16" height="16"></a></td>
                                                                                                                <?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>-->
                                                                                        <?php if (isset($_SESSION['preaprobar_planilla']) AND ($fila["status"]=="A")){ ?>
                                                                                        <td class="text-center icono"><a href="javascript:accion(1,<?php echo $fila['codnom']; ?>);" title="Pre-Aprobar <?php echo $termino; ?>"><img src="imagenes/1.png" width="16" height="16"></a></td>
                                                                                        <?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>
                                                                                        <?php if (isset($_SESSION['cerrar_planilla'])){ ?>
                                                                                        <td class="text-center icono"><a href="javascript:enviar(6, <?php echo $fila['codnom']; ?>,0,<?php echo $fila['codtip']; ?>);" title="Aprobar <?php echo $termino; ?>"><img src="imagenes/thumbs-up-icon.png" width="16" height="16"></a></td>
                                                                                        <?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>
                                                                                        <?php if (isset($_SESSION['cerrar_planilla'])){ ?>
                                                                                        <td class="text-center icono"><a href="javascript:enviar(100, <?php echo $fila['codnom']; ?>,0,0);" title="Recalcular Patronales <?php echo $termino; ?>"><img src="imagenes/txt.png" width="16" height="16"></a></td>
                                                                                        <?php }else{ ?>
                                                                                        <td class="text-center icono"></td>
                                                                                        <?php } ?>
										<?php
										}
										elseif($fila["status"]=="P")
										{
											if($fila_max['maximo']==$fila['codnom'] OR $es_admin)
											{
												if (isset($_SESSION['abrir_planilla']) AND $_SESSION['abrir_planilla'] == 1){
												?> <td class="text-center icono"><a href="javascript:enviar(7,<?php echo $fila['codnom']; ?>,0,0)" title="Abrir <?php echo $termino; ?>"><img src="../imagenes/ok.gif" width="16" height="16"></a></td> <?php
												}
												else{ ?>
                                                    <td class="text-center icono"></td>
                                                <?php }
											}
											else{ ?><td>&nbsp;</td><?php } ?>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
                                                                                        <td>&nbsp;</td>
                                                                                       
											<?php if (isset($_SESSION['consultar_planilla'])){ ?>
											<td class="text-center icono"><a href="javascript:enviar(2, <?php echo $fila['codnom']; ?>,0,0);" title="Consutar <?php echo $termino; ?>"><img src="img_sis/ico_list.gif"   width="15" height="15"></a></td>
											<?php }else{ ?>
                                                                                    <td class="text-center icono"></td>
                                                                                    <?php } ?>
                                                                                                            <td>&nbsp;</td>
                                                                                                            <td>&nbsp;</td>
                                                                                                            <!---<td>&nbsp;</td>
                                                                                                            <td>&nbsp;</td>
                                                                                                            <td>&nbsp;</td>
                                                                                                            <td>&nbsp;</td>-->
                                                                                    <?php if (isset($_SESSION['cerrar_planilla'])){ ?>
                                                                                    <td class="text-center icono"><a href="javascript:enviar(6, <?php echo $fila['codnom']; ?>,0,0);" title="Aprobar <?php echo $termino; ?>"><img src="imagenes/thumbs-up-icon.png" width="16" height="16"></a></td>
                                                                                    <?php }else{ ?>
                                                                                    <td class="text-center icono"></td>
                                                                                    <?php } ?>	
                                                                                    <?php if (isset($_SESSION['cerrar_planilla'])){ ?>
                                                                                    <td class="text-center icono"><a href="javascript:enviar(100, <?php echo $fila['codnom']; ?>,0,0);" title="Recalcular Patronales <?php echo $termino; ?>"><img src="imagenes/txt.png" width="16" height="16"></a></td>
                                                                                    <?php }else{ ?>
                                                                                    <td class="text-center icono"></td>
                                                                                    <?php } ?>
                                                                                <?php
										}
										else
										{
											if (isset($_SESSION['abrir_planilla']) AND $_SESSION['abrir_planilla'] == 1)
											{
												if($fila_max['maximo']==$fila['codnom'] OR $es_admin)
												{
													?> <td class="text-center icono"><a href="javascript:enviar(7,<?php echo $fila['codnom']; ?>,0,0)" title="Abrir <?php echo $termino; ?>"><img src="../imagenes/ok.gif" width="16" height="16"></a></td> <?php
												}
												else{ ?><td>&nbsp;</td><?php }
											}
											else{ ?>
                                                <td class="text-center icono"></td>
                                            <?php } ?>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
                                                                                        <td>&nbsp;</td>
											<!---<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>-->
											<td>&nbsp;</td>
									        <?php
									        if(isset($_SESSION['cerrar_planilla']))
											{
									            ?> 
									                <td class="text-center icono"><a href="javascript:enviar(100, <?php echo $fila['codnom']; ?>,0,0);" title="Recalcular Patronales <?php echo $termino; ?>"><img src="imagenes/txt.png" width="16" height="16"></a></td>
									            <?php
									        
									        }
											else{ ?>
									            <td class="text-center icono"></td>
									        <?php }
										}


										?>
									</tr>
								  <?php
								}
							?>
							</tbody>
							</table>
							    <input name="codigo_nomina" type="hidden" value="">
								<input name="registro_id" type="hidden" value="">
								<input name="op" type="hidden" value="">
								<input name="marcar_todos" type="hidden" value="1">
							</form>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
			<div class="row"><div class="col-md-12">
				<div id="listarFichas"></div>
			</div></div>
		</div>
	</div>
	<!-- END CONTENT -->
</div>

<!-- Modal -->
<div class="modal fade" id="ModalFichas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">CIERRE DE PLANILLA - FICHAS CON NETO EN NEGATIVO</h4>
      </div>
      <div class="modal-body">
		<div id="listarFichasNegativo"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="botonCerrar" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">
	function cargar_anio(){
            var search_anio=$("#search_anio").val();
            window.location.href='?search_anio='+search_anio;
        }
$(document).ready(function() {
   	$('#table_datatable').DataTable({
    	"iDisplayLength": 25,
    	"bStateSave" : true,
    	"sPaginationType": "bootstrap_extended",
        "aaSorting": [[0, 'desc']],
        "oLanguage": {
        	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
            "sInfo":"Total _TOTAL_ registros",
            "sInfoFiltered": "",
  		    "sEmptyTable":  "No hay datos disponibles", // No hay datos para mostrar
            "sZeroRecords": "No se encontraron registros",
            "oPaginate": {
                "sPrevious": "P&aacute;gina Anterior",//"Prev",
                "sNext": "P&aacute;gina Siguiente",//"Next",
                "sPage": "P&aacute;gina",//"Page",
                "sPageOf": "de",//"of"
            }
        },
        "aLengthMenu": [ // set available records per page
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],
        "aoColumnDefs": [
        	{ 'bSortable': false, 'aTargets': [1, 3] }, // 'bVisible': false,
            { 'bSortable': false, 'bSearchable': false, 'aTargets': [7, 8, 9, 10, 11, 12, 13, 14,15] }
        ],
		"fnDrawCallback": function() {
		    $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
		}
	});

   	$('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");
	$('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline");
	$('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");
});
</script>
<script type="text/javascript">
$(document).ready(function() {

});
</script>
</body>
</html>
