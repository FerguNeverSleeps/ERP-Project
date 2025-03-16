<?php 
session_start();
include_once('clases/database.class.php');
include('obj_conexion.php');

require_once '../lib/common.php';
include ("../paginas/funciones_nomina.php");


$cedula=$_GET['cedula'];
$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$pagina=@$_GET['pagina'];

$db->query("SET names utf8;");

$consultap="SELECT * FROM nompersonal WHERE cedula='$_GET[cedula]'";
//echo $consultap;             
$resultadop=$db->query($consultap);
$fetchCon=$db->fetch_array($resultadop);
$nombre=$fetchCon['apenom'];

//echo $cedula;
$consultaExpTipo="SELECT * FROM expediente_tipo ORDER BY nombre_tipo";
 $resultadoExpTipo = $db->query($consultaExpTipo);
$tipos_exp=$db->fetch_all_array($consultaExpTipo);
$in_tipo_exp = [];

foreach ($tipos_exp as $tipo_exp ) {

	switch ($tipo_exp['id_expediente_tipo']) 
	{
		case '1':
			if(isset($_SESSION['exp_est']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '2':
			if(isset($_SESSION['exp_cap']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '4':
			if(isset($_SESSION['exp_per']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '5':
			if(isset($_SESSION['exp_amo']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '6':
			if(isset($_SESSION['exp_sus']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '7':
			if(isset($_SESSION['exp_ren']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '8':
			if(isset($_SESSION['exp_des']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '9':
			if(isset($_SESSION['exp_mov']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '10':
			if(isset($_SESSION['exp_eva']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '11':
			if(isset($_SESSION['exp_vac']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '12':
			if(isset($_SESSION['exp_tie']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '13':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '14':
			if(isset($_SESSION['exp_exp']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '15':
			if(isset($_SESSION['exp_liccs']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '16':
			if(isset($_SESSION['exp_licss']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '17':
			if(isset($_SESSION['exp_lices']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '18':
			if(isset($_SESSION['exp_orb']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '19':
			if(isset($_SESSION['exp_baj']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '20':
			if(isset($_SESSION['exp_susp']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '21':
			if(isset($_SESSION['exp_cat']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '22':
			if(isset($_SESSION['exp_eta']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '23':
			if(isset($_SESSION['exp_vig']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '24':
			if(isset($_SESSION['exp_rea']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '25':
			if(isset($_SESSION['exp_rot']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '26':
			if(isset($_SESSION['exp_apo']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '27':
			if(isset($_SESSION['exp_aju']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '28':
			if(isset($_SESSION['exp_mis']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '29':
			if(isset($_SESSION['exp_cer']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '30':
			if(isset($_SESSION['exp_asc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '31':
			if(isset($_SESSION['exp_aum']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '32':
			if(isset($_SESSION['exp_rev']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '33':
			if(isset($_SESSION['exp_mod']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '34':
			if(isset($_SESSION['exp_sob']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '35':
			if(isset($_SESSION['exp_jub']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '36':
			if(isset($_SESSION['exp_pro']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '37':
			if(isset($_SESSION['exp_ini']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '38':
			if(isset($_SESSION['exp_nomap']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '39':
			if(isset($_SESSION['exp_rei']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '40':
			if(isset($_SESSION['exp_rei']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '41':
			if(isset($_SESSION['exp_rc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '42':
			if(isset($_SESSION['exp_aumh']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '43':
			if(isset($_SESSION['exp_lib']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '44':
			if(isset($_SESSION['exp_cam']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '45':
			if(isset($_SESSION['exp_ret']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '46':
			if(isset($_SESSION['exp_ters']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '47':
			if(isset($_SESSION['exp_apl']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '48':
			if(isset($_SESSION['exp_pen']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '49':
			if(isset($_SESSION['exp_terl']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '50':
			if(isset($_SESSION['exp_terp']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '51':
			if(isset($_SESSION['exp_tern']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '52':
			if(isset($_SESSION['exp_ces']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '53':
			if(isset($_SESSION['exp_int']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '54':
			if(isset($_SESSION['exp_aba']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '56':
			if(isset($_SESSION['exp_aat']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '57':
			if(isset($_SESSION['exp_baja']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '58':
			if(isset($_SESSION['exp_reint']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '59':
			if(isset($_SESSION['exp_acm']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '60':
			if(isset($_SESSION['exp_inv']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '61':
			if(isset($_SESSION['exp_nom']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '62':
			if(isset($_SESSION['exp_ext']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '63':
			if(isset($_SESSION['exp_dia']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '64':
			if(isset($_SESSION['exp_ant']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '65':
			if(isset($_SESSION['exp_trb']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '66':
			if(isset($_SESSION['exp_con']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '67':
			if(isset($_SESSION['exp_car']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '68':
			if(isset($_SESSION['exp_con']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '69':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '70':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '71':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '72':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '73':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '74':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '75':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '76':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '77':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '78':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '79':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '80':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '81':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;
		case '82':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;		
		case '83':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;	
		case '84':
			if(isset($_SESSION['exp_doc']))
				array_push($in_tipo_exp,$tipo_exp['id_expediente_tipo']);
			break;		
		default:
			# code...
			break;
	}
}
$in = implode(',', $in_tipo_exp);

$sqlExp = "";

if (count($in_tipo_exp) > 0) {
	$in = implode(',', $in_tipo_exp);
	$sqlExp = "SELECT a. * , b. * , c. * 
                    FROM expediente_tipo AS b, expediente AS a
                    LEFT JOIN expediente_subtipo AS c ON c.id_expediente_subtipo = a.subtipo
                    WHERE a.tipo = b.id_expediente_tipo
                    AND a.cedula LIKE '$_GET[cedula]'
                    AND b.id_expediente_tipo in ( " . $in . " )
                    ORDER BY a.fecha DESC, a.cod_expediente_det DESC";
} else {
	$sqlExp = "SELECT a. * , b. * , c. * 
                    FROM expediente_tipo AS b, expediente AS a
                    LEFT JOIN expediente_subtipo AS c ON c.id_expediente_subtipo = a.subtipo
                    WHERE a.tipo = b.id_expediente_tipo
                    AND a.cedula LIKE '$_GET[cedula]'
                    ORDER BY a.fecha DESC, a.cod_expediente_det DESC";
}

$rows = $db->query($sqlExp);
$resp = $db->fetch_all_array($sqlExp);

function esImagen($path)
{
    $data = explode("/",$path);
    $i = count($data);//$data[($i-1)]
    return exif_imagetype ( $path );
}

?>

<?php include("../header4.php"); // <html><head></head><body> ?>
<script type="text/javascript">
	function agregar(cedula)
    {
//           alert(cedula);
        if(cedula!='')
        {
            window.location.href="expediente_agregar.php?cedula="+cedula;
        }
        else
        {
            alert("No tiene permisos de acceso. Contacte al Administrador de Sistemas");
            return false;
        }
    }
    
    function eliminar(codigo)
    {
//                alert(codigo);
            if (confirm("\u00BFSeguro desea eliminar este registro?") == true)
                    window.location.href="expediente_eliminar.php?cod_eliminar="+codigo;
    }
    
    function aprobar(codigo)
    {
//                alert(codigo);
            if (confirm("\u00BFSeguro desea aprobar este movimiento?") == true)
                    window.location.href="expediente_aprobar.php?cod_aprobar="+codigo;
    }
    
    function desaprobar(codigo)
    {
//                alert(codigo);
            if (confirm("\u00BFSeguro desea desaprobar este movimiento?") == true)
                    window.location.href="expediente_desaprobar.php?cod_desaprobar="+codigo;
    }
    
    function generar_word(codigo,cedula)
    {
         window.location.href='expediente_word.php?codigo='+codigo+'&cedula='+cedula;
         //location.href='../../reportes/word/toma_posesion_asamblea.php?ficha='+ficha+'&tipnom='+planilla;

    }
</script>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
	.btn_regresar {
		display: none;
	}
</style>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12" >
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<!-- <i class="fa fa-globe"></i> -->
                                                            <img src="../imagenes/21.png" width="22" height="22" class="icon"> Expediente: <?php echo utf8_encode($nombre); echo " / "; echo $cedula ?>
							</div>
							<div class="actions">
                                                                <div class="btn-group">
								  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								      <img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Reportes Generales <span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu">
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe"
									href="../../tcpdf/reportes/reporte_cambios_cedula.php?id=<?= $fila['ficha'] ?>" title="" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Estudios Academicos</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_baja.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Capacitacion</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_activacion_baja.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Permisos</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_traslados.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Amonestaciones</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_solicitud_descuentos.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Suspensiones</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Renuncias</a></li>
									<!--Nuevos Reportes -->
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Destituciones</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Ajustes Planilla</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Imputaciones</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Reclamos</a></li>
								  </ul>
								</div>
                                                                <?php if (isset($_SESSION['add_exp'])): ?>
								<a class="btn btn-sm blue"  onclick="javascript: agregar('<? echo $cedula ?>')">
									<i class="fa fa-plus"></i>
									<!-- <img src="../imagenes/add.gif" width="16" height="16"> --> Agregar
								</a>
                                                                <?php endif ?>
                                                                <?php if (!isset($_SESSION['add_exp'])): ?>
								<a class="btn btn-sm blue" onclick="javascript: agregar('<? echo '' ?>')">
									<i class="fa fa-plus"></i>
									<!-- <img src="../imagenes/add.gif" width="16" height="16"> --> Agregar
								</a>
                                                                <?php endif ?>
								<a class="btn btn-sm blue btn_regresar"  onclick="javascript: window.location='../paginas/datos_integrantes/listado_integrantes_contraloria.php'">
									<!-- <img src="../imagenes/atras.gif" width="16" height="16"> -->
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
								
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-toolbar" style="display: none">
								<div class="btn-group">&nbsp;
									<!--<button id="sample_editable_1_new" class="btn green">
									Add New <i class="fa fa-plus"></i>
									</button>-->
								</div>
								<div class="btn-group pull-right">
									<button class="btn btn-sm blue" onclick="javascript: window.location='usuarios_add.php'">
									<i class="fa fa-plus"></i>
									<!-- <img src="../../includes/imagenes/icons/add.png"  width="16" height="16">--> Agregar
									</button>
									<!--
									<button class="btn dropdown-toggle" data-toggle="dropdown">Tools <i class="fa fa-angle-down"></i>
									</button>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="#">
												 Print
											</a>
										</li>
										<li>
											<a href="#">
												 Save as PDF
											</a>
										</li>
										<li>
											<a href="#">
												 Export to Excel
											</a>
										</li>
									</ul>
									-->
								</div>
							</div>
							<table class="table table-striped table-bordered table-hover" id="table_datatable_expediente">
							<thead>
							
                                                                
                                                              <th>Codigo</th>
								<th>Tipo</th>
								<th>Sub-Tipo</th>
                                                            
                                                                <th>N° Certificado</th>
                                                                <th>Artículo</th>
                                                                <th>Numeral</th>
								<th>Descripcion</th>
                                                                <th>Fecha</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>  
                                                                

							</thead>
							<tbody>
                                                            
							<?php
                                                                
                                                                
								for($i=0;$i<count($resp);$i++)
								{  										 						
								?>
									<tr class="odd gradeX row-editable">
                                                                                <td data-field-name="id"><?php echo $resp[$i]['cod_expediente_det']; ?></td>
                                                                                <td>
                                                                                    <?php  
                                                                                    
                                                                                      $caracter="<br>";
                                                                                        $tipo=wordwrap(utf8_encode($resp[$i]['nombre_tipo']), 10, $caracter, false);
//                                                                                        $lineas = explode($caracter, $descripcion);
//                                                                                        $i=0;
//                                                                                        foreach ($lineas as $line) 
//                                                                                        { 
//                                                                                            
//                                                                                        }
                                                                                        echo $tipo; 
                                                                                    ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php
                                                                                    if($resp[$i]['nombre_subtipo']) 
                                                                                    {                                                                                                                                                                                
                                                                                        $caracter="<br>";
                                                                                        $subtipo=wordwrap($resp[$i]['nombre_subtipo'], 10, $caracter, false);
//                                                                                        $lineas = explode($caracter, $descripcion);
//                                                                                        $i=0;
//                                                                                        foreach ($lineas as $line) 
//                                                                                        { 
//                                                                                            
//                                                                                        }
                                                                                        echo $subtipo; 
                                                                                    
                                                                                    
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        echo '-'; 
                                                                                        
                                                                                    }?>
                                                                                </td>
<!--                                                                                <td>
                                                                                    <?php 
//                                                                                    if($resp[$i]['tipo']==12 || $resp[$i]['tipo']==27) 
//                                                                                    {
//                                                                                    	$tipo_ajuste = $resp[$i]['tipo_ajuste'];
//                                                                                        if($resp[$i]['tipo_ajuste']==1) 
//                                                                                        {
//                                                                                            echo '(+)';  
//                                                                                            $tipo_ajuste = 1;                                                                                  
//                                                                                        }
//                                                                                        else 
//                                                                                        {
//                                                                                            echo '(-)';  
//                                                                                            $tipo_ajuste = 0;                                                                                       
//                                                                                        }
//                                                                                    }
//                                                                                    else
//                                                                                    {
//                                                                                        echo ' ';
//                                                                                    }
                                                                                    ?>
                                                                                </td>-->
<!--                                                                                <td><?php if($resp[$i]['posicion_anterior']) {echo $resp[$i]['posicion_anterior'];}else {echo '-'; }?></td>
                                                                                <td><?php if($resp[$i]['posicion_nueva']) {echo $resp[$i]['posicion_nueva'];}else {echo '-'; }?></td>-->
                                                                                <td><?php if($resp[$i]['numero_resolucion']) {echo $resp[$i]['numero_resolucion'];}else {echo '-'; }?></td>
                                                                                <td><?php if($resp[$i]['articulo']) {echo $resp[$i]['articulo'];}else {echo '-'; }?></td>
                                                                                <td><?php if($resp[$i]['numeral']) {echo $resp[$i]['numeral'];}else {echo '-'; }?></td>
                                                                                <td contenteditable="true" class="field-editable" data-field-name="descripcion" id-expediente ="<?= $resp[$i]['cod_expediente_det'] ?>">
                                                                                    <?php 
                                                                                        $caracter="<br>";
                                                                                        $descripcion=wordwrap($resp[$i]['descripcion'], 30, $caracter, false);
//                                                                                        $lineas = explode($caracter, $descripcion);
//                                                                                        $i=0;
//                                                                                        foreach ($lineas as $line) 
//                                                                                        { 
//                                                                                            
//                                                                                        }
                                                                                        echo $descripcion; 
                                                                                    ?>
                                                                                </td>
                                                                                <td><?php $fecha = explode('-',$resp[$i]['fecha']);
                                                                                           //$fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0]; 
                                                                                           $fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0]; 
                                                                                           echo $resp[$i]['fecha']; 
                                                                                    ?></td>
                                                                                <?php if (isset($_SESSION['edit_exp']) && $resp[$i][estatus]==0): ?>
                                                                                    <td>
                                                                                        <a href="expediente_agregar.php?codigo=<?php echo $resp[$i]['cod_expediente_det']; ?>&aprobado=0&cedula=<?php echo $_GET[cedula]; ?>" title="Editar">
                                                                                            <img src="../imagenes/editar-icono.png" width="22" height="22">
                                                                                        </a>
                                                                                    </td>
                                                                                <?php endif ?>
                                                                                <?php if (!isset($_SESSION['edit_exp']) || $resp[$i][estatus]==1): ?>
                                                                                    <td >
                                                                                        <a href="" title="Editar" onclick="javascript: agregar('<? echo '' ?>')">
                                                                                            
                                                                                        </a>
                                                                                    </td>
                                                                                <?php endif ?>
                                                                                <?php
                                                                                if( $resp[$i]['tipo']=='13' || $resp[$i]['tipo']=='82' || ($resp[$i]['tipo']>=69 && $resp[$i]['tipo']<= 79) )
                                                                                {

                                                                                    $sql = "SELECT nombre_documento, descripcion, url_documento, fecha_registro, fecha_vencimiento 
                                                                                                    FROM   expediente_documento 
                                                                                                    WHERE  cod_expediente_det='{$resp[$i]['cod_expediente_det']}'";

                                                                                    $res=query($sql,$conexion);
                                                                                    $documento = $res->fetch_object();
                                                                                    if( esImagen($documento->url_documento) != FALSE){      
                                                                                        echo '<td><a data-target="#modal_documento'.$i.'" data-toggle="modal">'.
                                                                                             '<img title="Ver documento" src="../imagenes/documento-icono.png" width="22" height="22" border="0"></a></td>';
                                                                                        echo '<div id="modal_documento'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">'.
                                                                                                '<div class="modal-dialog">'.
                                                                                                  '<div class="modal-content">'.
                                                                                                      '<div class="modal-body">'.
                                                                                                          '<img src="'.$documento->url_documento.'" class="img-responsive">'.
                                                                                                      '</div>'.
                                                                                                      '<div class="modal-footer">'.
                                                                                                        '<button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>'.                                                                                                        
                                                                                                      '</div>'.
                                                                                                  '</div>'.
                                                                                                '</div>'.
                                                                                              '</div>';
                                                                                    }else{
                                                                                        echo '<td><a data-target="#modal_documento'.$i.'" data-toggle="modal">'.
                                                                                             '<img title="Ver documento" src="../imagenes/documento-icono.png" width="22" height="22" border="0"></a></td>';
                                                                                        echo '<div id="modal_documento'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">'.
                                                                                                '<div class="modal-dialog">'.
                                                                                                  '<div class="modal-content">'.
                                                                                                      '<div class="modal-body">'.
                                                                                                          '<embed src="'.$documento->url_documento.'" width="550" height="300">'.
                                                                                                      '</div>'.
                                                                                                      '<div class="modal-footer">'.
                                                                                                        '<button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>'.                                                                                                        
                                                                                                      '</div>'.
                                                                                                  '</div>'.
                                                                                                '</div>'.
                                                                                              '</div>';
                                                                                    }
                                                                                }
                                                                                else
                                                                                {
                                                                                    if (isset($_SESSION['adjuntar_exp']))
                                                                                    {                                                                                    
                                                                                        icono("expediente_adjunto.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Adjuntar", "adjuntar-icono.png");
                                                                                    }
                                                                                    else
                                                                                    {?>
                                                                                        <td >
                                                                                        
                                                                                        </td>
                                                                                    <?}                                                                                    
                                                                                } 
                                                                                   $codigo=$resp[$i]['cod_expediente_det'];
                                                                                   $tipo_expediente=$resp[$i]['tipo'];
                                                                                   $subtipo_expediente=$resp[$i]['subtipo'];
                                                                                   $app_number=$resp[$i]['app_number'];
                                                                                   //icono("expediente_pdf.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "PDF", "pdf-icono.png");
                                                                                   if($resp[$i][estatus]==1 || $resp[$i][estatus]==0)
                                                                                   {
                                                                                        if($tipo_expediente==5 || $tipo_expediente==6 || $tipo_expediente==17 || $tipo_expediente==11 || $tipo_expediente==12 || $tipo_expediente==21 || $tipo_expediente==22 || $tipo_expediente==23 ||
                                                                                           $tipo_expediente==52 || $tipo_expediente==54 || $tipo_expediente==57 || $tipo_expediente==59 || $tipo_expediente==64 || $tipo_expediente==80 || $tipo_expediente==81)
                                                                                        {
                                                                                            if($tipo_expediente==5 && $resp[$i]['subtipo']==24)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                            
                                                                                                icono("word/amonestacion_verbal_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==5 && $resp[$i]['subtipo']==25)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                            
                                                                                                icono("word/amonestacion_escrita_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==5 && $resp[$i]['subtipo']==134)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                            
                                                                                                icono("word/amonestacion_suspension_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==6)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                            
                                                                                                icono("word/suspension_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if( $tipo_expediente==12 && $app_number != NULL )
                                                                                            { 
                                                                                                echo '<td></td>';                                                                                         
                                                                                                icono("../../formatos/processmaker/acciones_personal.php?codigo_caso=".$resp[$i]['app_number']."&tabla=PMT_COMPENSATORIO", "PDF", "pdf-icono.png");
                                                                                            }elseif( $tipo_expediente==12 && $app_number == NULL )
                                                                                            { 
                                                                                                echo '<td></td>';  
                                                                                                echo '<td></td>';
                                                                                            }elseif( $tipo_expediente==11 && ( $subtipo_expediente==114 || $subtipo_expediente==115 ) )
                                                                                            { 
                                                                                                echo '<td></td>';                                                                                         
                                                                                                icono("../../formatos/processmaker/acciones_personal.php?codigo_caso=".$resp[$i]['app_number'], "PDF", "pdf-icono.png");
                                                                                            }elseif($tipo_expediente==11)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                         
                                                                                                icono("word/resuelto_vacaciones_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==17 && $subtipo_expediente==52)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                         
                                                                                                icono("word/resuelto_licencia_especial_gravidez.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==21 || $tipo_expediente==22 || $tipo_expediente==23)
                                                                                            { 
                                                                                                echo '<td><a data-target="#modal_pdf'.$i.'" data-toggle="modal">'.
                                                                                                          '<img title="PDF" src="../imagenes/pdf-icono.png" width="22" height="22" border="0"></a></td>';
                                                                                                     echo '<div id="modal_pdf'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true" >'.
                                                                                                             '<div class="modal-dialog" style="width:850px; height:650px;">'.
                                                                                                               '<div class="modal-content">'.
                                                                                                                   '<div class="modal-body">'.
                                                                                                                       ' <embed src="expediente_pdf.php?cedula='.$cedula.'&codigo='.$codigo.'" width="800" height="600">'.
                                                                                                                   '</div>'.
                                                                                                                   '<div class="modal-footer">'.
                                                                                                                     '<button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>'.                                                                                                        
                                                                                                                   '</div>'.
                                                                                                               '</div>'.
                                                                                                             '</div>'.
                                                                                                           '</div>';
                                                                                                echo '<td></td>';
                                                                                            }                                                                                        
                                                                                            if($tipo_expediente==52 || $tipo_expediente==57)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                         
                                                                                                icono("word/cese_labores_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==54)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                            
                                                                                                icono("word/abandono_cargo_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==59)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                            
                                                                                                icono("word/acreditacion_carrera_migratoria_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
//                                                                                            if($tipo_expediente==59 && $resp[$i]['cm_tipo_proceso']=='A')
//                                                                                            { 
//                                                                                                 echo '<td></td>';                                                                                            
//                                                                                                icono("word/acreditacion_migratoria_especial_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
//                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
//                                                                                            }
//                                                                                            if($tipo_expediente==59 && $resp[$i]['cm_tipo_proceso']=='B')
//                                                                                            { 
//                                                                                                 echo '<td></td>';                                                                                            
//                                                                                                icono("word/acreditacion_migratoria_ordinario_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
//                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
//                                                                                            }
                                                                                             if($tipo_expediente==17)
                                                                                            { 
                                                                                                 echo '<td></td>'
                                                                                                . '<td></td>';                                                                                
                                                                                                //icono("word/resuelto_licencia_especial_gravidez.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==64)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                            
                                                                                                icono("excel/evaluacion_antecedentes_excel.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Excel", "excel-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==80)
                                                                                            { 
                                                                                                 echo '<td></td>';                                                                                         
                                                                                                icono("word/formulario_entrega_kit.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Word", "word-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                            if($tipo_expediente==81)
                                                                                            { 
                                                                                                echo '<td></td>';                                                                                         
                                                                                                icono("excel/reporte_incidente.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det'], "Excel", "excel-icono.png");
                                                                                                //icono("expediente_word.php?cedula=".$cedula."&codigo=".$resp[$i]['cod_expediente_det']."&tipo=".$tipo_expediente, "Word", "word-icono.png");
                                                                                            }
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                             echo '<td></td>'
                                                                                                . '<td></td>';
                                                                                        }
                                                                                   }
//                                                                                   else
//                                                                                   {
//                                                                                         echo '<td></td>'
//                                                                                            . '<td></td>';
//                                                                                   }
                                                                                   if (isset($_SESSION['ver_exp']))
                                                                                   {        
                                                                                    	if( $tipo_expediente!=12 )
                                                                                        {
                                                                                        echo '<td><a data-target="#modal_ver'.$i.'" data-toggle="modal" data-ver=1>'.
                                                                                                  '<img title="Ver" src="../imagenes/ver-icono.png" width="22" height="22" border="0"></a></td>';
                                                                                             echo '<div id="modal_ver'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true" >'.
                                                                                                     '<div class="modal-dialog" style="width:1050px; height:800;">'.
                                                                                                       '<div class="modal-content">'.
                                                                                                           '<div class="modal-body">'.

                                                                                                               ' <embed src="expediente_agregar.php?cedula='.$cedula.'&codigo='.$codigo.'&opt=3&aprobado=1" width="1024" height="768">'.
                                                                                                           '</div>'.
                                                                                                           '<div class="modal-footer">'.
                                                                                                             '<button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>'.
                                                                                                           '</div>'.
                                                                                                       '</div>'.
                                                                                                     '</div>'.
                                                                                                   '</div>';
                                                                                        }elseif( $tipo_expediente==12 && $app_number == NULL )
                                                                                        {
                                                                                        echo '<td><a data-target="#modal_ver'.$i.'" data-toggle="modal" data-toggle="modal" data-ver=1>'.
                                                                                                  '<img title="Ver" src="../imagenes/ver-icono.png" width="22" height="22" border="0"></a></td>';
                                                                                             echo '<div id="modal_ver'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true" >'.
                                                                                                     '<div class="modal-dialog" style="width:1050px; height:800;">'.
                                                                                                       '<div class="modal-content">'.
                                                                                                           '<div class="modal-body">'.

                                                                                                               ' <embed src="expediente_agregar.php?cedula='.$cedula.'&codigo='.$codigo.'&opt=3&aprobado=1" width="1024" height="768">'.
                                                                                                           '</div>'.
                                                                                                           '<div class="modal-footer">'.
                                                                                                             '<button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>'.
                                                                                                           '</div>'.
                                                                                                       '</div>'.
                                                                                                     '</div>'.
                                                                                                   '</div>';
                                                                                        }else{
                                                                                        	echo '<td></td>';
                                                                                        }
                                                                                   }
                                                                                    else
                                                                                    {?>
                                                                                        <td>
                                                                                        <a href="" title="Ver" onclick="javascript: agregar('<? echo '' ?>')">
                                                                                            <img src="../imagenes/ver-icono.png" width="22" height="22">
                                                                                        </a>
                                                                                        </td>
                                                                                    <?}      
                                                                                   if($resp[$i][estatus]==0)
                                                                                   {
                                                                                       if (isset($_SESSION['aprobar_exp']))
                                                                                       {
                                                                                            icono("javascript:aprobar('$codigo')", "Aprobar", "aprobar-icono.png");
                                                                                       }
                                                                                        else
                                                                                        {?>
                                                                                            <td>
                                                                                            <a href="" title="Aprobar" onclick="javascript: agregar('<? echo '' ?>')">
                                                                                                <img src="../imagenes/aprobar-icono.png" width="22" height="22">
                                                                                            </a>
                                                                                            </td>
                                                                                        <?}      
                                                                                   }
                                                                                   else
                                                                                   {
                                                                                       if (isset($_SESSION['aprobar_exp']))
                                                                                       {
//                                                                                           if($resp[$i]['tipo']!='12' && $resp[$i]['subtipo']!='57')
//                                                                                           {
                                                                                                icono("javascript:desaprobar('$codigo')", "Desaprobar", "desaprobar-icono.png");
//                                                                                           }
//                                                                                           else
//                                                                                           {?>
<!--                                                                                                <td>
                                                                                               
                                                                                                </td>-->
                                                                                            <?//}
                                                                                       }
                                                                                       else
                                                                                        {?>
                                                                                            <td>
                                                                                            <a href="" title="Desaprobar" onclick="javascript: agregar('<? echo '' ?>')">
                                                                                                <img src="../imagenes/desaprobar-icono.png" width="22" height="22">
                                                                                            </a>
                                                                                            </td>
                                                                                        <?}
                                                                                   }
                                                                                   if (isset($_SESSION['delete_exp'])&& $resp[$i][estatus]==0)
                                                                                   {
//                                                                                        if($resp[$i]['tipo']!='12' && $resp[$i]['subtipo']!='57')
//                                                                                        {
                                                                                            icono("javascript:eliminar('$codigo')", "Eliminar", "eliminar-icono.png");
//                                                                                        }
//                                                                                        else
//                                                                                        {?>
                                                                                            
                                                                                         <?//}
                                                                                        
                                                                                   }
                                                                                   else
                                                                                    {?>
                                                                                        <td >
                                                                                        
                                                                                        </td>
                                                                                    <?}
                                                                                   
                                                                                ?>
                                                                               
										
										
									</tr>
								  <?php									
								}
							?>
							</tbody>
							</table>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">

      
    
	 $(document).ready(function() { 
		$(() => {
			$(".row-editable td.field-editable").click((e) => {
				const elem = $(e.currentTarget);
				elem.prop("contenteditable", true);
			});

			$(".row-editable td.field-editable").on("focusout", function (e) {

				const elem = $(e.currentTarget);

				if (elem.data("field-name") == "descripcion") {
					var id_expediente = $(this).attr("id-expediente");
					var descripcion = $.trim(elem.text());
					
					
					var datos =  new FormData();

					datos.append("accion", "guardarDescripcionExpediente" );
					datos.append("id_expediente", id_expediente)
					datos.append("descripcion", descripcion)
							
					
					$.ajax({
						url : "ajax/expediente.ajax.php",
						method: "POST",
						data: datos,
						cache: false,
						contentType : false,
						processData : false,
						dataType : "json" ,
						success: function(resp){
							
						}
					});
				} 

				elem.prop("contenteditable", false);
			});												
		});
	 		if(!parent.PARENT){
	 			$(".btn_regresar").removeClass("btn_regresar");
	 		}
        
            $('#table_datatable_expediente').DataTable({
            	//"oSearch": {"sSearch": "Escriba frase para buscar"},
            	"iDisplayLength": 10,
//                "sScrollX": "100%", 
//                "sScrollXInner": "100%", 
//                "bScrollCollapse": true,
//                "responsive": true,
                //"sPaginationType": "bootstrap",               
              "sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[7, 'desc'],[1, 'asc'],[0, 'desc']], 
//                "sScrollX": "",
//                "sScrollY": "100",
//                "autoWidth": false,
                "oLanguage": {
                    "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                    "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    /*"oPaginate": {
                        "sPrevious": "Página Anterior",
                        "sNext": "Página Siguiente"
                    }*/
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de"//"of"
                    }
                },
                /*
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                */
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],
               
                "AutoWidth": false,
                "aoColumnDefs": [
//                     { "sWidth": "10%", "aTargets": [ 0 ] },
//                        { "sWidth": "15%", "aTargets": [ 1 ] },
//                        { "sWidth": "15%", "aTargets": [ 2 ] },
//                        { "sWidth": "10%", "aTargets": [ 3 ] },
//                        { "sWidth": "10%", "aTargets": [ 4 ] },
//                        { "sWidth": "10%", "aTargets": [ 5 ] },
//                        { "sWidth": "15%", "aTargets": [ 6 ] },
//                        { "sWidth": "15%", "aTargets": [ 7 ] },
//                        { "sWidth": "15%", "aTargets": [ 8 ] },
//                        { "sWidth": "50%", "aTargets": [ 9 ] },
//                        { "sWidth": "15%", "aTargets": [10 ] },
//                        { "sWidth": "5%", "aTargets": [ 11 ] },
//                        { "sWidth": "5%", "aTargets": [ 12] },
//                        { "sWidth": "5%", "aTargets": [ 13 ] },
//                        { "sWidth": "5%", "aTargets": [ 14 ] },
//                        { "sWidth": "5%", "aTargets": [ 15] },
//                        { "sWidth": "5%", "aTargets": [ 16] },
//                        { "sWidth": "5%", "aTargets": [ 17 ] },
                    { "bSearchable": false, "aTargets": [8,9,10,11,12,13,14]  },
                    { 'bSortable': false,"aTargets": [8,9,10,11,12,13,14] },
                    { 'bVisible': false, 'aTargets': [4,5] }
                ],
                 
//                "aoColumns" : [
//                                null,
//                                null,
//                                null,
//                                null,
//                                null,
//                                null,
//                                null,
//                                null,
//                                null,
//                                { sWidth: "30%" },
//                                ],
              
		"fnDrawCallback": function() {
                    $('#table_datatable_expediente_filter input').attr("placeholder", "Escriba frase para buscar");
                }
            });

            $('#table_datatable_expediente').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_expediente_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_expediente_wrapper .dataTables_length select').addClass("form-control input-xsmall");
	 });       
         
        
        
       
        
</script>
</body>
</html>
