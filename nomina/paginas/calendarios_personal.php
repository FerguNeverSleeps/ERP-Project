<?php 
session_start();
ob_start();
?>
<?
require_once '../lib/common.php';
include ("../header4.php");
$_GET['estado'];

function vista_dia($dia,$mes,$ano,$ficha)
{
	$conexion=conexion();
	$laborable="lightgray";
	$nolaborable="red";
	$mediajornada="white";
	$feriado="lightgreen";
	
	$fecha=$ano."-".$mes."-".$dia;
	$consulta="select ncp.*, tur.descripcion from nomcalendarios_personal ncp left join nomturnos tur on (ncp.turno_id=tur.turno_id) where fecha='".$fecha."' AND ficha='$ficha'";
	$resultado=query($consulta,$conexion);
	
	$fila=fetch_array($resultado);
	$color=$laborable;
	if($fila['dia_fiesta']=="1")
	{
		$color=$nolaborable;
	}
	elseif($fila['dia_fiesta']=="2")
	{
		$color=$mediajornada;
	}
	elseif($fila['dia_fiesta']=="3")
	{
		$color=$feriado;
	}
	?>
	<td onclick="javascript:alerta_personal('<?php echo $fecha; ?>','<?php echo $dia; ?>','<?php echo $ano; ?>','<?php echo $ficha; ?>');" id="<?php echo $fecha;?>" align="center" style="cursor : pointer; font-size : 12pt;" title="<?php echo utf8_encode($fila['descripcion_dia_fiesta']);?>" bgcolor="<?php echo $color;?>"><?php echo $dia;?><div style=" font-size : 9pt; valign:bottom; color: blue;"><?php echo $fila[descripcion];?></div></td>
	<?php
}

function vista_calendario($mes,$ano,$ficha)
{
//estados
$fecha_lunes= $ano."-".$mes."-01";
$num_dias_mes=date("t",strtotime($fecha_lunes));
$dia_inicio=date("N",strtotime($fecha_lunes));

?>

<table cellspacing="10" border="2"  cellpadding="4" > 
<tbody valign="top">
<tr>
<TD colspan="7" class="portlet box blue" border="1" align="center">
<?php 
if($mes==1)
	echo "<strong>Enero</strong>";
elseif($mes==2)
	echo "<strong>Febrero</strong>";
elseif($mes==3)
	echo "<strong>Marzo</strong>";
elseif($mes==4)
	echo "<strong>Abril</strong>";
elseif($mes==5)
	echo "<strong>Mayo</strong>";
elseif($mes==6)
	echo "<strong>Junio</strong>";
elseif($mes==7)
	echo "<strong>Julio</strong>";
elseif($mes==8)
	echo "<strong>Agosto</strong>";
elseif($mes==9)
	echo "<strong>Septiembre</strong>";
elseif($mes==10)
	echo "<strong>Octubre</strong>";
elseif($mes==11)
	echo "<strong>Noviembre</strong>";
elseif($mes==12)
	echo "<strong>Diciembre</strong>";
?>
</TD>
</tr>
<TR align="center"><TD>L</TD><TD>M</TD><TD>Mi</TD><TD>J</TD><TD>V</TD><TD>S</TD><TD>D</TD></TR>

<?
	$dia=1;
	echo "<tr>";
	for($i=1;$i<$dia_inicio;$i++)
	{
		echo "<TD></TD>";
	}
	for($i=1; $i<=$num_dias_mes;$i++)
	{
		$marca=0;
		if($dia_inicio<=7)
		{
			vista_dia($i,$mes,$ano,$ficha);			
		}
		else
		{
			$marca=1;
			echo "</TR><TR>";
			$dia_inicio=1;
			$i--;
		}
		if($marca==0)
		{
			$dia_inicio++;
		}		
	}
	for($i=$dia_inicio;$i<=7;$i++)
	{
		echo "<TD></TD>";
	}
	echo "</tr>";
?>
</tbody>
</table>
<br>
<?
}
$ano=$_GET['ano'];
$ficha=$_GET['ficha'];
$turnoss=$_GET['turnoss'];



$conexion=conexion();

$consulta="SELECT apenom, turno_id FROM nompersonal WHERE ficha='$ficha'";
$result=query($consulta,$conexion);
$fetch=fetch_array($result);
$apenom = $fetch[apenom];
$turno = $fetch[turno_id];

$consulta="SELECT fecha FROM nomcalendarios_personal WHERE ficha='$ficha' and YEAR(fecha)='$ano'";
 
$result=query($consulta,$conexion);
$fetch=fetch_array($result);
if(($fetch[fecha]=="")||(isset($_POST[reiniciar])))
{
	$conexion=conexion();
	$consulta="DELETE FROM nomcalendarios_personal WHERE ficha='$ficha' and YEAR(fecha)='$ano'";
	$resultado=query($consulta,$conexion);
	$consulta="INSERT INTO nomcalendarios_personal (cod_empresa,ficha,fecha,dia_fiesta,descripcion_dia_fiesta, turno_id)  select 0, '$ficha', fecha, dia_fiesta, descripcion_dia_fiesta, '$turno' from nomcalendarios_tiposnomina where year(fecha)='$ano' group by fecha";
	$resultado=query($consulta,$conexion);
}


$bloques=3;



$consulta = "SELECT DISTINCT(YEAR(fecha)) as ano FROM nomcalendarios_tiposnomina WHERE cod_tiponomina=".$_SESSION['codigo_nomina']."";
$resultado = query($consulta,$conexion);


?>
<script type="text/javascript">

function cambioanos() 
{

var anosx = document.getElementById("anos").value ;
var fichas = '<?php echo $ficha ; ?>' ;
var turno =  '<?php echo $turnoss ; ?>' ;

document.location.href = "?ano="+anosx+"&ficha="+fichas+"&turnoss="+ turno ;

}

</script>

<form action="" method="post" name="calper" id="calper" onsubmit=" if(confirm('Seguro desea reiniciar el calendario?') == false) return false;" >
<div class="page-container">
        <!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
<div class="page-content">
	<div class="row">
        <div class="col-md-12">
            <div class="portlet box blue">
            	<div class="portlet-title">
					<div class="caption">
						<label>Ver y modificar calendario</label>
					</div>
                    <div class="actions">                   
						<a class="btn btn-sm blue"  onclick="javascript: window.location='maestro_personal.php'">
							<i class="fa fa-plus"></i>
								Volver
						</a>
					</div>
				</div> 
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-6">
							<label><strong>CALENDARIO:</strong><?php echo $ano;?> &nbsp;&nbsp;&nbsp; <strong>FICHA: </strong> <?php echo $ficha;?> &nbsp;&nbsp;&nbsp;<strong>EMPLEADO: </strong> <?php echo $apenom;?></label>
						</div>
					</div>
					<br>
					<div class="row">
                      	<div class="col-md-2">
                            <label>A&#241;o:&nbsp;&nbsp;&nbsp;&nbsp;</label>
                      	</div> 
                      	<div class="col-md-3"> 
							<select name="anos" id="anos" onchange ="cambioanos();" class="form-control select2">
								<option >Seleccione a&#241;o</option>
									<?php
									while($fetch = fetch_array($resultado))
									{
									?>
										<option value="<?php echo $fetch['ano'];?>" <?php if($fetch['ano'] == $ano) echo 'selected'  ;   ?>><?php echo $fetch['ano'];?></option>
									<?php
									}
									?>
							</select>  
                      	</div> 
                    </div>
                    <br>
                    <div class="row">
                      	<div class="col-md-2">
                            <label>Dia:</label>
                      	</div> 
                      	<div class="col-md-3"> 
							<select name="estado" id="estado" class="form-control select2">
								<option value="0" <?php if($_GET['estado']==0) echo "selected='true'" ?> >Laborable</option>
								<option value="1" <?php if($_GET['estado']==1) echo "selected='true'" ?> >No laborable</option>
								<option value="2" <?php if($_GET['estado']==2) echo "selected='true'" ?> >Media jornada</option>
								<option value="3" <?php if($_GET['estado']==3) echo "selected='true'" ?> >Feriado</option>
							</select>	
                      	</div> 
                    </div>
                    <br>
                    <div class="row">
                      	<div class="col-md-2">
                            <label>Turno:</label>
                      	</div> 
                      	<div class="col-md-3">	
						    <?php
								$conexion=conexion();
								$consulta="select turno_id,descripcion, date_format(entrada,'%h:%i:%s %p') as entrada, date_format(salida,'%h:%i:%s %p') as salida from nomturnos";
								$result=query($consulta,$conexion);
							?>
							<select name="turno" id="turno" class="form-control select2">
						 		<option value="">--Sin-Turno -----------------</option>
						    	<?php 
								while($row = fetch_array($result))
						  		{ 		
								?>
						    		<option <?php if($row[turno_id]==$turnoss) echo "selected";?> value="<?php echo $row[turno_id];?>"><?php printf("%s: Entrada: %s / Salida: %s", $row[descripcion],$row[entrada],$row[salida] ) ?></option>
						    	<?php 
								}
								?>
							</select> 
                      	</div> 
                    </div>
                    <br>
                    <div class="row">
                      	<div class="col-md-12">
			                           <table border="0" cellspacing="1" align="center" >
										<tbody valign="top">
										<?
										$i=0;
										for($mes=1;$mes<=12;$mes++)
										{	
											echo "<td style='padding: 0px 5px 0px 20px'>";
											vista_calendario($mes,$ano,$ficha);
											echo "</td>";
											$i++;
											if($i==$bloques){
												echo "</tr><tr valign=\"top\" align=\"center\">";
												$i=0;
											}	
										}
										echo "</tr>";
										?>
										</tbody>
										</table>
                      	</div> 
                    </div>
                    <div class="actions" align="right">
                    	<input class="btn btn-sm blue" type="submit" name="reiniciar"  value="Reiniciar Calendario">
                    </div>
				</div>          
            </div>
        </div>
    </div> 
</div>
</div>
</div>
</form>
</body>
</html>
