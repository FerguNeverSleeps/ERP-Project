<?php 
session_start();
ob_start();
	include ("../header4.php");
	include("../lib/common.php");
	include("func_bd.php");	
?>

<script>
function Enviar(){					
			
	if (document.frmPrincipal.registro_id.value==0){ 
		document.frmPrincipal.op_tp.value=1}
	else{ 
		document.frmPrincipal.op_tp.value=2}		
	
	if (document.frmPrincipal.cod_dispositivo.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");}
}
</script>

<?php 
$registro_id=$_POST[registro_id];
$bandera=$_POST['bandera'];
if (isset($HTTP_GET_VARS[txtficha]))
{
	$ficha=$HTTP_GET_VARS[txtficha];
	$cedula=$HTTP_GET_VARS[cedula];	
}
else
{
	$ficha=$_POST[txtficha];
	$cedulatrab=$_POST[cedula];
}
	
	$op_tp=$_POST[op_tp];
	$validacion=0;
	
	if ($registro_id==0) 
	{// Si el registro_id es 0 se va a agregar un registro nuevo
		if ($op_tp==1)
		{
		
		
		$query="INSERT INTO reloj_info 
                        (id_dispositivo,
                        cod_dispositivo,
                        nombre,
                        codnivel1,
                        codnivel2,
                        codnivel3,
                        ubicacion,
                        marca,
                        modelo,
                        serial)
                        values 
                        ('',
                        '$_POST[cod_dispositivo]',"
                        . "'$_POST[nombre]',"
                        . "'$_POST[codnivel1]',"
                        . "'$_POST[codnivel2]',"
                        . "'$_POST[codnivel3]',"
                        . "'$_POST[ubicacion]',"
                        . "'$_POST[marca]',"
                        . "'$_POST[modelo]',"
                        . "'$_POST[serial]')";
		
		$result=sql_ejecutar($query);	
		activar_pagina("dispositivos_list.php");
		/*}
		else
		{
		mensaje("La cedula introducida ya esta siendo usada por otra persona. Por favor verifique los datos");
		}*/
		}
	}
	else {// Si el registro_id es mayor a 0 se va a editar el registro actual		
	
		$query="SELECT * FROM reloj_info WHERE id_dispositivo='$registro_id'";		
		$result=sql_ejecutar($query);	
		$row = fetch_array ($result);	
                $cod_dispositivo=$row[cod_dispositivo];
		$nombre=$row[nombre];                
                $codnivel1=$row[codnivel1];
                $codnivel2=$row[codnivel2];
                $codnivel3=$row[codnivel3];
		$marca=$row[marca];
                $modelo=$row[modelo];
		$ubicacion=$row[ubicacion];
		$serial=$row[serial];

	}	
		
	if ($op_tp==2){					
		
		
		$query="UPDATE reloj_info SET
		cod_dispositivo='$_POST[cod_dispositivo]',
		nombre='$_POST[nombre]',
		codnivel1='$_POST[codnivel1]',
		codnivel2='$_POST[codnivel2]',
		codnivel3='$_POST[codnivel3]',
		marca='$_POST[marca]',	
		modelo='".$_POST['modelo']."',
		ubicacion='".$_POST['ubicacion']."',
		serial='".$_POST['serial']."'  
		where id_dispositivo='$registro_id'";	
		//exit(0);
		$result=sql_ejecutar($query);				
		activar_pagina("dispositivos_list.php");						
					
	}

?>
<body class="page-full-width"  marginheight="0">

<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                <?php    
                  if ($registro_id==0)
                  {
                   echo "Agregar Dispositivo";
                  }
                  else
                  {
                   echo "Modificar Dispositivo";
                  }
                ?>
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='dispositivos_list.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            
        
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                <input name="registro_id" type="hidden" id="registro_id" value="<?php echo $registro_id; ?>">
                <input name="nombre_tabla" type="hidden" value="<?php echo $nombre_tabla; ?>">
                
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="dispositivo">Código:</label></div>
                  <div class="col-md-6"><input class="form-control" name="cod_dispositivo" type="text" id="cod_dispositivo" value="<?php if ($registro_id!=0){ echo $cod_dispositivo; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="fecha">Nombre:</label></div>
                  <div class="col-md-6"><input class="form-control" name="nombre" type="text" id="nombre" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>"></div>
                </div>
<!--                <br>
                <div class="row">
                  <div class="col-md-3"><label for="hora">Nivel 1:</label></div>
                  <div class="col-md-6"><input class="form-control" name="hora" type="text" id="hora" value="<?php if ($registro_id!=0){ echo $concepto; }  ?>"></div>
                </div>-->
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="tipo">Ubicación:</label></div>
                  <div class="col-md-6"><input class="form-control" name="ubicacion" type="text" id="ubicacion" value="<?php if ($registro_id!=0){ echo $ubicacion; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="estado">Marca:</label></div>
                  <div class="col-md-6"><input class="form-control" name="marca" type="text" id="marca" value="<?php if ($registro_id!=0){ echo $marca; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="estado">Modelo:</label></div>
                  <div class="col-md-6"><input class="form-control" name="modelo" type="text" id="modelo" value="<?php if ($registro_id!=0){ echo $modelo; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="estado">Serial:</label></div>
                  <div class="col-md-6"><input class="form-control" name="serial" type="text" id="serial" value="<?php if ($registro_id!=0){ echo $serial; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                &nbsp;
                </div>
                <div class="row">
                  <div class="col-md-offset-4 col-md-2"><?php boton_metronic('ok','Enviar(); document.frmPrincipal.submit();',2) ?></div>
                  <div class="col-md-1"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php   include ("../footer4.php");
 ?>
 <script type="text/javascript">

</script>
</body>
</html>


