<?php 
session_start();
ob_start();
?>

<?php 
	include ("../../header5.php");
	include("../../lib/common.php") ;
	include("../func_bd.php");	
	$registro_id=$_POST[registro_id];
	$codigo_pry=$_POST[cod_pry];
	$op_tp=$_POST[op_tp];
  $cedula = $_POST[cedula];
	
		
		$query="select * from nompersonal_pry_cc where id='$registro_id'";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);	
		
        $id = $row['id'];
		$codigo           = $row[codigo_proyecto_cc];	
		$fechaInicio = date("d-m-Y", strtotime($row['fecha_inicio']));
		$fechaFin = date("d-m-Y", strtotime($row['fecha_fin']));
		$porcentaje = $row[porcentaje];


	$conexion = new bd($_SESSION['bd']);

	$sql = "SELECT * FROM nompersonal WHERE cedula = '$cedula'";
	$res = $conexion->query($sql, "utf8");
	$fila = $res->fetch_assoc();
	$fecha_ingreso = $fila['fecing'];
	$fecha_ingreso = date('d-m-Y', strtotime($fecha_ingreso));
  echo $fecha_ingreso;
        

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
					        	Editar proyectos
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='proyecto_cc.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<input name="op_tp" type="Hidden" id="op_tp" value="-1">
  								<input name="id" type="Hidden" id="id" value="<?php echo $id; ?>">
								<div class="row">
									&nbsp;
								</div>
                                <div class="row">
									<div class="col-md-4 text-right"><label for="codigo_proyecto">Proyecto:</label></div>


									<div class="col-md-4">
										<select name="codigo_proyecto" id="codigo_proyecto" class="form-control select2" >
                        <?php 
                          $query="select id, codigo, descripcion from proyecto_cc";
                          $result=sql_ejecutar($query);
                          //ciclo para mostrar los datos
                          while ($row = fetch_array($result))
                          { 
                            // Opcion de modificar, se selecciona la situacion del registro a modificar       
                            if($row[codigo]==$codigo_pry)
                            { 
                              ?>
                              <option value="<?php echo $row[codigo];?>" selected> <?php echo $row[codigo]." - ".$row[descripcion];?> </option>
                              <?php 
                              }
                              else // opcion de agregar
                              { 
                                ?>
                                <option value="<?php echo $row[codigo];?>"> <?php echo $row[codigo]." - ".$row[descripcion];?> </option>
                                <?php 
                                } 
                              }//fin del ciclo while
                              ?>
                    </select>
									</div>
								</div>
								<div class="row">
									&nbsp;
								</div>


                                <div class="row">
									<div class="col-md-4 text-right"><label for="fecha-ini">Fecha inicio:</label></div>
									<div class="input-group date date-picker col-md-4" data-provide="datepicker" data-date-format="dd-mm-yyyy" data-date-start-date="<?php echo $fecha_ingreso; ?>">
                                        <input name="fecha-ini" type="text" id="fecha-ini"  class="form-control" value="<?php echo $fechaInicio;?>">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
								</div>
                                <div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-4 text-right"><label for="fecha-fin">Fecha fin:</label></div>
									<div class="input-group date date-picker col-md-4" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecha-fin" type="text" id="fecha-fin"  class="form-control" value="<?php echo $fechaFin;?>">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-4 text-right"><label for="porcentaje">Porcentaje:</label></div>
									<div class="col-md-4"><input name="porcentaje" type="number" id="porcentaje"  class="form-control" value="<?php if ($registro_id!=0){ echo $porcentaje; }  ?>"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
                                                                <div class="row">
									&nbsp;
								</div>
								<div class="row">
									&nbsp;
								</div>


<div class="row">&nbsp;</div>

								<div class="row">
									<div class="col-md-offset-4 col-md-1" style="margin-right:20px;"><?php boton_metronic('ok','Enviar();',2) ?></div>
									<div class="col-md-2"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 	include ("../../footer5.php");
?>


 <script type="text/javascript">
     $(document).ready(function(){

    $('#codigo_proyecto').select2();
});
function Enviar(){
  const cedula = "<?php echo $cedula; ?>";
  const ficha = "<?php echo $fila['ficha']; ?>";
    id = $("#id").val();
	codigoProyecto      = $("#codigo_proyecto").val();
	fechaInicio      = $("#fecha-ini").val();
	fechaFin      = $("#fecha-fin").val();
	porcentaje = $("#porcentaje").val();

	sw = 1;
	 if(codigoProyecto === "" || fechaInicio === "" || fechaFin === "" || porcentaje === "") {
         alert("Por favor, rellene todos los campos requeridos.");
         sw=0;
     }

	else{
		if (sw){
			$.get("proyecto_cc_editar.php", {
			    id: id,
			    codigoProyecto: codigoProyecto,
			    fechaInicio: fechaInicio,
			    fechaFin: fechaFin,
			    porcentaje: porcentaje,
			    cedula: cedula
			}, function (dataController) {
			    if (dataController.resultado) {
			        // Proyecto agregado exitosamente
			        alert(dataController.mensaje);
							location.href=`proyecto_cc.php?cedula=${cedula}&txtficha=${ficha}`;
			    }
			    else {
			        // No es posible agregar el Proyecto / Centro De Costo
			        alert(dataController.mensaje);
			    }
			}, "json");
		}
	}
}
 </script>
</body>
</html>
