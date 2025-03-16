<?php 
session_start();
ob_start();
include ("../header4.php");
include("../lib/common.php") ;
include("func_bd.php");	
?>

<script>

function Enviar(){					
	if (document.frmAgregarCargo.registro_id.value==0){ 
		document.frmAgregarCargo.op_tp.value=1
	}
	else{ 	
		document.frmAgregarCargo.op_tp.value=2
	}		
	
	if (document.frmAgregarCargo.txtdescripcion.value==0){
		document.frmAgregarCargo.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");			
	}				
	
}


</script>
<script language="javascript" type="text/javascript" src="datetimepicker.js">
//Date Time Picker script- by TengYong Ng of http://www.rainforestnet.com
//Script featured on JavaScript Kit (http://www.javascriptkit.com)
//For this script, visit http://www.javascriptkit.com 

</script>


<?php 
	
	
	
	$registro_id=$_POST[registro_id];
	$op_tp=$_POST[op_tp];
	$validacion=0;
    
    $strsql_tipo = "SELECT id_expediente_tipo, nombre_tipo FROM expediente_tipo ORDER BY id_expediente_tipo, nombre_tipo";
    $result_tipo = sql_ejecutar($strsql_tipo);  
    $tipo_expediente = [];
    while( $fila = mysqli_fetch_array($result_tipo) ) // $fila=fetch_array($res)
    { 
        $tipo_expediente[] = $fila;
     
    }
	
	if ($registro_id==0) // Si el registro_id es 0 se va a agregar un registro nuevo
	{			
		
		if ($op_tp==1)
		{
		$codigo_nuevo=AgregarCodigo("expediente_subtipo","id_expediente_subtipo");
		
		$query="insert into expediente_subtipo (id_expediente_subtipo, nombre_subtipo) values 
		($codigo_nuevo,
		'".$_POST['txtdescripcion']."')";
				 
		$result=sql_ejecutar($query);	
		activar_pagina("expediente_subtipo.php");	
		$id_expediente_tipo="";			
		}
	}
	else // Si el registro_id es mayor a 0 se va a editar el registro actual
	{	
		$query="select * from expediente_subtipo where id_expediente_subtipo=$registro_id";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);
		$codigo=$row["id_expediente_subtipo"];
		$nombre=$row["nombre_subtipo"];
		$id_expediente_tipo=$row["id_expediente_tipo"];
	}	
		
		
	if ($op_tp==2)
		{							
				$query="UPDATE expediente_subtipo set 
				nombre_subtipo='$_POST[txtdescripcion]',
                id_expediente_tipo = '$_POST[id_expediente_tipo]'
				where id_expediente_subtipo=$registro_id";				
				
				$result=sql_ejecutar($query);				
				activar_pagina("expediente_subtipo.php");										
		{			
	}
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
										echo "Agregar Expediente Subtipo";
									}
									else
									{
										echo "Modificar Expediente Subtipo";
									}
								?>
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='expediente_subtipo.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						
				
						<div class="portlet-body">
							<form action="" method="post" name="frmAgregarCargo" id="frmAgregarCargo" enctype="multipart/form-data" autocomplete="off">
								<input name="op_tp" type="Hidden" id="op_tp" value="-1">
								<input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
								<div class="row">
									<div class="col-md-3">C&oacute;digo:</div>
									<div class="col-md-3">
									<input class="form-control" name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $codigo; }  ?>"></div>
								</div>
								<div class="row">
								&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3"><label for="id_expediente_tipo">Expediente - Tipo</label></div>
									<div class="col-md-3"><select class="form-control select2" id="id_expediente_tipo" name="id_expediente_tipo">
                                    <?php

                                    foreach ($tipo_expediente as $key => $value) {
                                        $selected = ( $id_expediente_tipo == $value["id_expediente_tipo"] ) ? " selected" : "";
                                        echo "<option value='".$value['id_expediente_tipo']."' $selected>".$value["nombre_tipo"]."</option>";
                                        # code...
                                    }
                                    ?>
                                    
                                    </select>
                                    </div>
								</div>
								<div class="row">
								&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3"><label for="txtdescripcion">Descripci&oacute;n:</label></div>
									<div class="col-md-6"><input name="txtdescripcion" type="text" id="txtdescripcion"  class="form-control" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>"></div>
								</div>
								<div class="row">
								&nbsp;
								</div>
								<div class="row">
									<div class="col-md-offset-3 col-md-3"><?php boton_metronic('ok','Enviar(); document.frmAgregarCargo.submit();',2) ?></div>
									<div class="col-md-6"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 	include ("../footer4.php");
 ?>
</body>
</html>



