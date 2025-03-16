<?php 
session_start();
ob_start();
error_reporting(0);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
error_reporting(0);

$id  = (isset($_GET['id'])) ? $_GET['id'] : '';

$consulta1="SELECT * FROM grado ";                        
$resultado1=query($consulta1,$conexion);

$consulta2="SELECT * FROM etapa ";                        
$resultado2=query($consulta2,$conexion);

if( isset($_POST['btn-guardar']) ){
	
	
        $grado = ( isset($_POST['grado']) ) ? $_POST['grado'] : 0;
        $etapa = ( isset($_POST['etapa']) ) ? $_POST['etapa'] : 0;
        $monto = ( isset($_POST['monto']) ) ? $_POST['monto'] : 0;

	if( empty($id) )
	{
		$sql = "INSERT INTO grado_etapa (id_grado_etapa,grado,etapa, monto) "
                        . "VALUES ('','{$grado}','{$etapa}','{$monto}')";
                $res = query($sql, $conexion);
            
                if($etapa==1)
                {
                    
                    $consulta3="SELECT MAX(numero) FROM etapa";                    
                    $resultado3=mysqli_query($conexion,$consulta3); 
                    $fila = mysqli_fetch_row($resultado3);
                    $num_etapas=$fila[0];
//                    echo $num_etapas; echo " ";                    
                    
                    $consulta4="SELECT ajuste FROM grado WHERE id_grado=" . $grado;
//                    echo $consulta4;
                    $resultado4=query($consulta4,$conexion);
                    $fila=fetch_array($resultado4);
                    $ajuste=$fila['ajuste'];                    
                    
//                    echo $ajuste; echo " ";
                    
                    if($grado==1)
                        $monto=325;
                    if($grado==2)
                        $monto=365; 
                     
//                    echo $monto; echo " ";
//                    exit;                   

                    for($i=1;$i<=$num_etapas;$i++)
                    {
//                        echo $ajuste;
                        $monto_ajuste=$ajuste*(float)$i;
//                        echo $monto_ajuste;
                        $monto_etapa=$monto+$monto_ajuste;
//                        echo $monto_etapa;
//                        exit; 
                        $etapa=$i+1;
                        $sql_etapa = "INSERT INTO grado_etapa (id_grado_etapa,grado,etapa, monto) "
                        . "VALUES ('','{$grado}','{$etapa}','{$monto_etapa}')";
                        $res_etapa = query($sql_etapa, $conexion);
                    }
                }
	}
	else
	{
		$sql = "UPDATE grado_etapa SET 
				grado  = '{$grado}',
                                etapa  = '{$etapa}',
                                monto  = '{$monto}'
				WHERE id_grado_etapa=" . $id;
                $res = query($sql, $conexion);
	}

	
        //echo $res;

	activar_pagina("grados_etapas_lista.php");	 
}


if(isset($_GET['edit']))
{
	$sql = "SELECT * FROM grado_etapa WHERE id_grado_etapa=" . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		
		
                $grado = $fila['grado'];
                $etapa = $fila['etapa'];
                $monto = $fila['monto'];
                
	}
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
}

.portlet > .portlet-title > .caption {
    font-size: 13px;
    font-weight: bold;
    font-family: helvetica, arial, verdana, sans-serif;
    margin-bottom: 3px;
}

.form-horizontal .control-label {
    text-align: left;
    padding-top: 3px;
}

.form-control {
    border: 1px solid silver;
}

.btn{
	border-radius: 3px !important;
}

.form-body{
	padding-bottom: 5px;
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
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Grado / Etapa
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">	
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label" for="txtcodigo">Grado: </label>                            
                                                                            <div class="col-md-4">
                                                                                <select name="grado" id="grado" class="form-control">
                                                                                   <option value="">Seleccione</option>
                                                                                    <?php                        
                   
                                                                                        while($fila=fetch_array($resultado1))
                                                                                        {
                                                                                           
                                                                                            if (!isset($_GET['edit']))
                                                                                            {                        
                                                                                                {?>
                                                                                                    <option  value="<?=$fila['id_grado'];?>"><?=$fila['numero']. " - " . utf8_encode($fila['descripcion']) . " - AJUSTE: " . $fila['ajuste'] ;?></option>
                                                                                                <?}

                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                if($grado==$fila['id_grado'])
                                                                                                {?>
                                                                                                     <option  value="<?=$fila['id_grado'];?>" selected><?=$fila['numero']. " - " . utf8_encode($fila['descripcion']) . " - AJUSTE: " . $fila['ajuste'] ;?></option> 
                                                                                                <?}
                                                                                                else
                                                                                                {?>
                                                                                                     <option  value="<?=$fila['id_grado'];?>"><?=$fila['numero']. " - " . utf8_encode($fila['descripcion']) . " - AJUSTE: " . $fila['ajuste'] ;?></option>
                                                                                                <?}         
                                                                                            }

                                                                                        }

                                                                                    ?>
                                                                                
                                                                                </select>
                                                                            </div>
									</div>
                                                                        
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label" for="txtcodigo">Etapa: </label>                            
                                                                            <div class="col-md-4">
                                                                                <select name="etapa" id="etapa" class="form-control">
                                                                                   <option value="">Seleccione</option>
                                                                                    <?php                        
                   
                                                                                        while($fila=fetch_array($resultado2))
                                                                                        {
                                                                                           
                                                                                            if (!isset($_GET['edit']))
                                                                                            {                        
                                                                                                {?>
                                                                                                    <option  value="<?=$fila['id_etapa'];?>"><?=$fila['numero']. " - " . utf8_encode($fila['descripcion']) . " - PERIODO: ( " . $fila['minimo'] . " - ". $fila['maximo'] . " ) AÑOS" ;?></option>
                                                                                                <?}

                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                if($etapa==$fila['id_etapa'])
                                                                                                {?>
                                                                                                     <option  value="<?=$fila['id_etapa'];?>" selected><?=$fila['numero']. " - " . utf8_encode($fila['descripcion']) . " - PERIODO: ( " . $fila['minimo'] . " - ". $fila['maximo'] . " ) AÑOS" ;?></option> 
                                                                                                <?}
                                                                                                else
                                                                                                {?>
                                                                                                     <option  value="<?=$fila['id_etapa'];?>"><?=$fila['numero']. " - " . utf8_encode($fila['descripcion']) . " - PERIODO: ( " . $fila['minimo'] . " - ". $fila['maximo'] . " ) AÑOS" ;?></option>
                                                                                                <?}         
                                                                                            }

                                                                                        }

                                                                                    ?>
                                                                                
                                                                                </select>
                                                                            </div>
									</div>
                                                                        <div class="form-group">
										<label class="col-md-2 control-label" for="cuenta">Monto:</label>
										<div class="col-md-1">
											<input type="text" class="form-control input-sm" 
                                                                                               id="monto" name="monto" value="<?php echo utf8_encode ($monto); ?>" required>
										</div>
									</div>                                                                        
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='grados_etapas_lista.php'">Cancelar</button>
									
								</div>
							</form>
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
		$( "#btn-guardar" ).click(function() {
			var cuenta      = $('#cuenta').val();
		    var descripcion = $('#descrip').val();

		    if( cuenta == '' || descripcion == '' )
		    {
		    	alert('Debe llenar los campos obligatorios');
		    	return false;
		    }
		});
	});
</script>
</body>
</html>