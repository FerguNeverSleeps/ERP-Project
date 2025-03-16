<?php 
//session_start();
//ob_start();
?>
<style type="text/css">

	* {
		padding: 0px;
		margin: 0px;
		border: 0px;
	}
	.Base {
		position: absolute;
		top: 45px;
		left: 0px;
		width: 99%;
	}
	.CapaCabecera {
		position: absolute;
		top:45px;
		left: 0px;
		width: 100%;
		
	}
	.TablaCabecera {
		width: 100%;
	}
	.CapaContenido {
		position: absolute;
		top: 40px;
		left: 0px;
		width: 100%;
		height: 400px;
		overflow: auto;
		border: 1px solid #000000;
	}

	.TablaContenido {
		width: 100%;
		
	}
</style>
<?php 
require_once '../lib/common.php';
include ("../header.php");
include ("funciones_nomina.php");
include ("func_bd.php") ;
$url="movimientos_agregar_masivo";
$modulo="Agregar Movimientos a la Nomina";
$tabla="nomconceptos";

$titulos=array("Ficha","Cedula","Nombre");
$indices=array("ficha","cedula","apenom");

echo $_POST['seleccion'] ;

$ficha=$_GET['ficha'];
$todo=$_GET['todo'];

if(!isset($_POST['nomina']))
{
	$nombre_nomina=$_GET['nomina'];
}
else
{
	$nombre_nomina=$_POST['nomina'];
}
if(!isset($_POST['ficha']))
{
	$ficha=$_GET['ficha'];
}
else
{
	$ficha=$_POST['ficha'];
}

$referencia=$_POST['referencia'];
$concepto=$_POST['codcon'];

$conexion=conexion();


$conexion=conexion();
$consulta="select * from nom_nominas_pago where codnom='".$nombre_nomina."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado_nom=query($consulta,$conexion);
$fila_nom=fetch_array($resultado_nom);
$CODNOM=$nombre_nomina;
$FECHANOMINA=$fila_nom['periodo_ini'];
$FECHAFINNOM=$fila_nom['periodo_fin'];
$LUNES=lunes($FECHANOMINA);	
$LUNESPER=lunes_per($FECHANOMINA,$FECHAFINNOM);
$conexion=conexion();
$consulta="select monsalmin from nomempresa";
$resultado_salmin=query($consulta,$conexion);
$fila_salmin=fetch_array($resultado_salmin);

if(isset($_POST['opcion']) and $_POST['opcion']=="Guardar")
{
	
	
	$temp_des=$_POST['ficha'];
	$i=0;
	foreach($temp_des as $des)
	{
		$ficha[$i]=$des;
		$i++;
	}
	/*
	$temp_des2=$_POST['valor'];
	$i=0;
	foreach($temp_des2 as $des2)
	{
		$valor99[$i]=$des2;
		$i++;
	}*/
		
	//TRAER VALOR DE CADA CONCEPTO
	$nc=$_POST[numcon];
	$conc=1;
	while($conc<=$nc){
		$valores_seleccion[$conc-1]=$_POST['valor'.$conc];
		$conc++;
		
	}
	//CODIGOS DE CONCEPTOS
	$codcon2=$_POST[codcon];
	$i=0;
	foreach($codcon2 as $des2)
	{
		$codcon[$i]=$des2;
		$i++;
	}
	//echo $codcon[0].'-'.$valores_seleccion[0][0];exit;
	
	$conc=0;
	while($conc<$nc){//RECORRIDO POR CONCEPTO
	$concepto=$codcon[$conc];//codigo de cada concepto
	
	$temp_des2=$valores_seleccion[$conc];
	$i=0;
	foreach($temp_des2 as $des2)
	{
		$valor99[$i]=$des2;
		$i++;
	}
	
	
	//RECORRIDO POR PERSONA
	$temp_seleccion=$_POST['valor99'];

	foreach($temp_seleccion as $valor)
	{
		
	if($valor99[$valor]!='' && $valor99[$valor]!=0)
	{



	$conexion=conexion();
	$consulta="DELETE FROM nom_movimientos_nomina WHERE ficha='".$ficha[$valor]."' AND codnom='".$nombre_nomina."' AND tipnom='".$_SESSION['codigo_nomina']."' AND codcon=$concepto";
	$resultado_nom=query($consulta,$conexion);
	
	if ($_POST['n1']=="todos" or !isset($_POST['n1']) or empty($_POST['n1'])){
	
	$consulta="select * from nompersonal where ficha='".$ficha[$valor]."' and tipnom='".$_SESSION['codigo_nomina']."'";
	}else{
	$consulta="select * from nompersonal where ficha='".$ficha[$valor]."' and tipnom='".$_SESSION['codigo_nomina']."' and codnivel1='".$_POST['n1']."'";

	}
	$resultado=query($consulta,$conexion);
	$fila=fetch_array($resultado);
	$CEDULA = $fila[cedula];
	$FICHA = $fila[ficha];
	$SUELDO=$fila[suesal];//LISTO
	$SEXO=".".$fila[sexo]."'";
	$HORABASE = $fila[hora_base];
	$FECHANACIMIENTO=date("d/m/Y",strtotime($fila[$fecnac]));
	$EDAD=date("Y")-date("Y",$fila[$fecnac]);
	$TIPONOMINA=$fila[tipnom];//LISTO
	$FECHAINGRESO=$fila[fecing];//LISTO
	$CODPROFESION=$fila[codpro];
	$CODCATEGORIA=$fila[codcat];
	$CODCARGO=$fila[codcargo];
	$SITUACION=$fila[estado];
	$SUELDOPROPUESTO=$fila[sueldopro];
	$TIPOCONTRATO=$fila[contrato];
	$FORMACOBRO=$fila[forcob];
	$NIVEL1=$fila[codnivel1];
	$NIVEL2=$fila[codnivel2];
	$NIVEL3=$fila[codnivel3];
	$NIVEL4=$fila[codnivel4];
	$NIVEL5=$fila[codnivel5];
	$NIVEL6=$fila[codnivel6];
	$NIVEL7=$fila[codnivel7];
	$FECHAAPLICACION=$fila[fechaplica];
	$TIPOPRESENTACION=$fila[tipopres];
	$FECHAFINSUS=$fila[fechasus];
	$FECHAINISUS=$fila[fechareisus];
	$FECHAFINCONTRATO=$fila[fecharetiro];
	$FECHAVAC=$fila[fechavac];
	$FECHAREIVAC=$fila[fechareivac];
	$CONTRACTUAL=$fila[contractual];
	$PRT=$fila[proratea];
	$REF=0;
	$SALARIOMIN=$fila_salmin['monsalmin'];
        $CODINSTRUCCION=$fila['nominstruccion_id'];
	if($SITUACION!="Inactivo")
	{
		
		$conexion=conexion();
		$consulta_mov="select * from nom_movimientos_nomina where codcon='".$concepto."' and codnom='".$nombre_nomina."' and ficha ='".$ficha[$valor]."' and tipnom='".$_SESSION['codigo_nomina']."'";
		$resultado_mov=query($consulta_mov,$conexion);
		
		if(num_rows($resultado_mov)==0)
		{
			$conexion=conexion();
			$consulta="select * from nomconceptos where codcon='".$concepto."'";
			$resultado_con=query($consulta,$conexion);
			$fila=fetch_array($resultado_con);
			$REF=$valor99[$valor];
			//echo $formula[$valor];
			eval($fila['formula']);
			
			if($MONTO<=0 && $fila['montocero']==1)
			{
				$entrar=0;
			}
			else
			{
				$entrar=1;
			}
			if($entrar==1)
			{
					$conexion=conexion();
				$consulta="insert into nom_movimientos_nomina (codnom, codcon,ficha,mes,anio,tipcon,valor,monto,cedula,unidad,descrip,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual) values ('".$_POST['nomina']."', '".$concepto."','".$ficha[$valor]."','".$fila_nom['mes']."','".$fila_nom['anio']."','".$fila['tipcon']."','".$REF."','".$MONTO."','$CEDULA','".$fila['unidad']."','".$fila['descrip']."','$NIVEL1','$NIVEL2','$NIVEL3','$NIVEL4','$NIVEL5','$NIVEL6','$NIVEL7','".$_SESSION['codigo_nomina']."','$fila[contractual]')";
				if(!$resultado=query($consulta,$conexion))
				{
					echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
					alert('No se puede calcular conceptos a esta persona')
					</SCRIPT>";
				}else{
						$query="select cedula from nom_movimientos_historico where tipnom='".$_SESSION['codigo_nomina']."' and codnom='".$_POST['nomina']."' and cedula='$CEDULA'";
						$resulthhis=mysqli_query($conexion, $query);
						$fetchhhis=fetch_array($resulthhis);		       
						if($fetchhhis[cedula]=='')
						{
							if($HISTORICO=='' || $HISTORICO!=$CEDULA)
							{
								$HISTORICO=$CEDULA;
								$insert="insert into nom_movimientos_historico values ('','".$_POST['nomina']."','".$_SESSION['codigo_nomina']."','$NIVEL1','$NIVEL2','$NIVEL3','$NIVEL4','$NIVEL5','$NIVEL6','$NIVEL7','".$ficha[$valor]."','$SUELDO','$CODCARGO','$SITUACION','$CEDULA')";
								$result_insert=mysqli_query($conexion, $insert);
							}
						}
				}
			}
		}	
	}	
	}
	
	}
	$conc++;}//fin recorrido por concepto
}
/*
else
{
	echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	alert('No se puede calcular')
	</SCRIPT>";
}
*/
$conexion=conexion();
if ($_POST['n1']=="todos" or !isset($_POST['n1']) or empty($_POST['n1'])){
	
$consulta="SELECT ficha, cedula, apenom,codnivel1, nn1.descrip as n1 FROM nompersonal left join nomnivel1 nn1 on codnivel1=nn1.codorg WHERE tipnom='$_SESSION[codigo_nomina]' and estado  in ('Activo', 'Pensionado', 'Reposo') order by codnivel1,apenom";
}
else{
$consulta="SELECT ficha, cedula, apenom,codnivel1, nn1.descrip as n1 FROM nompersonal left join nomnivel1 nn1 on codnivel1=nn1.codorg WHERE tipnom='$_SESSION[codigo_nomina]' and estado  in ('Activo', 'Pensionado', 'Reposo') and codnivel1='".$_POST['n1']."' order by codnivel1,apenom";

}
$result=query($consulta,$conexion);
?>
<script language="JavaScript" type="text/javascript">

function buscar_empleado()
{
	AbrirVentana('buscar_empleado_acumulados.php',660,700,0);
}

function buscar_concepto()
{
	AbrirVentana('buscar_concepto.php',660,700,0);
}

function buscar_campo()
{
	AbrirVentana('buscar_campo.php',660,700,0);
}

function enviar(op)
{
	document.frmPrincipal.opcion.value=op;

	document.frmPrincipal.submit();
}


</script>
<FORM name="frmPrincipal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self">
<?
titulo_mejorada($modulo,"21.png","btn('cancel','window.close()',2);","");
?>
<input name="marcar_todos" type="hidden" value="1">
<input name="opcion" id="opcion" type="hidden" value="">
<input name="nomina" id="nomina" type="hidden" value="<?echo $nombre_nomina?>">

<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">

<tr class="tb-head" >
<td>
		<select   name="n1" id="n1" onchange="this.form.submit()">
				<? if (isset($_POST['n1'])){?>
		<? if ($_POST['n1']=="todos"){?>
		<option value='todos'>TODAS</option>
		
		<?}else{
		$consultan="select * from nomnivel1 where codorg='".$_POST['n1']."'";
		
		$resultado2n=sql_ejecutar($consultan,$conexion);
		$filan=fetch_array($resultado2n)?>

		<option value="<?php echo $filan['codorg'];?>"><?php echo $filan['descrip'];?></option>

		<?php }}else{ ?>
		<option value='todos'>TODAS</option>

		<?}
		$consulta="select * from nomnivel1";
		
		$resultado2=sql_ejecutar($consulta,$conexion);
		$i=0;
		while($fila=fetch_array($resultado2))
		{
			
			?>
			<option <?if($i%2==0){echo " class='tb-fila' ";}?> value="<?php echo $fila['codorg'];?>"><?php echo $fila['descrip'];?></option>
			<?php
			$i++;
		}
		?>
		</select></td>
</tr>
</table>
<div class="Base">
	<div class="CapaCabecera"></div>
<table class="TablaCabecera">

<?
/*
foreach($titulos as $nombre)
{
	echo "<td><STRONG>$nombre</STRONG></td>";
}*/
echo "<td width=20><STRONG>FICHA</STRONG></td>";
echo "<td width=40><STRONG>CEDULA</STRONG></td>";
echo "<td width=160><STRONG>NOMBRE Y APELLIDO</STRONG></td>";

//BUSCAR CONCEPTOS MASIVOS
$conexion=conexion();
$ccon='Select * from nomconceptos where carga_masiva="S"';
$qcon=query($ccon,$conexion);$cont=1;$numcon=num_rows($qcon);
while($conconcepto=fetch_array($qcon)){
	echo "<td width=60><STRONG>$conconcepto[descrip]</STRONG>";
	echo "<INPUT size=\"50\" type=\"hidden\" name=\"codcon[$cont]\" value=\"$conconcepto[codcon]\"></td>";
	//$conceptos_masivos[$cont]=$conconcepto[codcon];
	$cont++;
}
?>
</table>
	
<div class="CapaContenido">
		<table class="TablaContenido">

	
<?
$i=0; $gerencia="";
echo "<tr><td><INPUT size=\"50\" type=\"hidden\" name=\"numcon\" value=\"$numcon\"></td></tr>";

while($fila=fetch_array($result))
{
	$colspan=3+$numcon;
	if ($gerencia!=$fila[codnivel1]){
		echo "<tr class='tb-head'><td colspan=$colspan>".$fila[codnivel1]." - ".$fila[n1]."</td></tr>";
		$gerencia=$fila[codnivel1];
	}
  	$i++;
	if($i%2==0)
	{
	?>
    	<tr class="tb-fila">
	<?
	}
	else
	{
		echo "<tr>";
	}
	foreach($indices as $campo)
	{
		//$nom_tabla=field_name($result,$campo);
		$var=$fila[$campo];
		if($campo=='ficha')
			echo "<td width=20><INPUT  type=\"hidden\" name=\"ficha[]\" value=\"$var\">$var</td>";
		if($campo=='cedula')
			echo "<td width=40><INPUT  type=\"hidden\" name=\"cedula[]\" value=\"$var\">$var</td>";
		if($campo=='apenom')
			echo "<td width=160><INPUT  type=\"hidden\" name=\"apenom[]\" value=\"$var\">$var</td>";		
	}
	$contc=1;
	while($contc<=$numcon){
	?>
	<td width="60" align="center"><INPUT type="text" name="valor<?echo $contc;?>[]" value="" maxlength="8" size="9">
	
	</td>
	<?	
		$contc++;
	}
	
	echo '</tr><tr><td><INPUT type="hidden" name="valor99[]" value="'.($i-1).'" ></td></tr>';
}

?>

<tr><td colspan="<?echo 3+$numcon;?>" height="50" align="center" class="tb-tit"><INPUT type="button" name="guardar" value="Guardar" onclick="javascript:enviar('Guardar');">
</td></tr>

</table>
</div>
</div>
</FORM>
</BODY>
</html>
<?cerrar_conexion($conexion);?>