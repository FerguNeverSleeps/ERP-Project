<?php 
session_start();
ob_start();
?>
<?php 
require_once '../lib/common.php';
include ("../header.php");
include ("funciones_nomina.php");
include ("func_bd.php") ;
$url="movimientos_agregar_masivo";
$modulo="Agregar Movimientos a la Nomina";
$tabla="nomconceptos";

$titulos=array("Ficha","Cedula","Nombre","Valor");
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

if(isset($_POST['opcion']) and $_POST['opcion']=="Eliminar")
{
	$temp_des=$_POST['ficha'];
	$i=0;
	foreach($temp_des as $des)
	{
		$ficha[$i]=$des;
		$i++;
	}
	foreach($_POST['seleccion'] as $valor)
	{
		$conexion=conexion();
		$consulta="DELETE FROM nom_movimientos_nomina WHERE ficha='".$ficha[$valor]."' AND codnom='".$nombre_nomina."' AND tipnom='".$_SESSION['codigo_nomina']."' AND codcon=$concepto";
		$resultado_nom=query($consulta,$conexion);
	}
}
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

	$temp_des2=$_POST['valor'];
	$i=0;
	foreach($temp_des2 as $des2)
	{
		$valor2[$i]=$des2;
		$i++;
	}

	$temp_seleccion=$_POST['valor2'];

	foreach($temp_seleccion as $valor)
	{
	if($valor2[$valor]!='')
	{
		$conexion=conexion();
	$consulta="DELETE FROM nom_movimientos_nomina WHERE ficha='".$ficha[$valor]."' AND codnom='".$nombre_nomina."' AND tipnom='".$_SESSION['codigo_nomina']."' AND codcon=$concepto";
	$resultado_nom=query($consulta,$conexion);
	
	$consulta="update nomcampos_adic_personal set valor='".$valor2[$valor]."' where ficha='".$ficha[$valor]."' and tiponom='".$_SESSION['codigo_nomina']."' and id='$_POST[campo]'";
	$resultadox=query($consulta,$conexion);
	
	$consulta="select * from nompersonal where ficha='".$ficha[$valor]."' and tipnom='".$_SESSION['codigo_nomina']."'";
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
			$REF=$valor2[$valor];
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
$consulta="SELECT ficha, cedula, apenom FROM nompersonal WHERE tipnom='$_SESSION[codigo_nomina]' and estado  in ('Activo', 'Pensionado', 'Reposo')";
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
	var val1=document.getElementById('codcon')
	//var val3=document.getElementById('referencia')
	
	if((val1.value==0)||(val1.value==''))
	{
		alert("DEBE INTRODUCIR DATOS VALIDOS... VERIFIQUE");
		return
	}
	document.frmPrincipal.submit();
}


</script>
<FORM name="frmPrincipal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self">
<?
titulo_mejorada($modulo,"21.png","btn('ok',\"MarcarTodos('seleccion[]');\",2,'Marcar o Desmarcar Todos');|btn('cancel','window.close()',2);","");
?>
<input name="marcar_todos" type="hidden" value="1">
<input name="opcion" id="opcion" type="hidden" value="">
<input name="nomina" id="nomina" type="hidden" value="<?echo $nombre_nomina?>">
<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
<tbody>
<tr>
<td width="20%" height="25"><strong><font color='#000066'>CONCEPTO:</font></strong></td>
<td>
<input type="text" name="codcon" id="codcon" maxlength="5" size="16" onblur="javascript:cargar_concepto();">
<a href="javascript:buscar_concepto();"> <img src="images/search.gif" name="buscar" id="buscar" border="0" /></a>
</td>
<td><div id="concepto"></div></td>
</tr>
<tr class='tb-fila' >
<td width="20%" height="25"><strong><font color='#000066'>CAMPO ADICIONAL:</font></strong></td>
<td>
<input type="text" name="campo" id="campo" maxlength="5" size="16" onblur="javascript:cargar_campo();">
<a href="javascript:buscar_campo();"> <img src="images/search.gif" name="buscar" id="buscar" border="0" /></a>
</td>
<td><div id="campoAdicional"></div></td>
</tr>
<!--<tr >
<td width="20%" height="25"><strong><font color='#000066'>VALOR:</font></strong></td>
<td><input type="text" name="referencia" id="referencia" maxlength="8" size="16"></td>
<td></td>
</tr>-->
</tbody>
</table>
<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
<tbody>
<tr class="tb-head" >

<?
foreach($titulos as $nombre)
{
	echo "<td><STRONG>$nombre</STRONG></td>";
}
?>
</tr>
<?
$i=0; 
while($fila=fetch_array($result))
{
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
			echo "<td><INPUT size=\"50\" type=\"hidden\" name=\"ficha[]\" value=\"$var\">$var</td>";
		if($campo=='cedula')
			echo "<td><INPUT size=\"50\" type=\"hidden\" name=\"cedula[]\" value=\"$var\">$var</td>";
		if($campo=='apenom')
			echo "<td><INPUT size=\"50\" type=\"hidden\" name=\"apenom[]\" value=\"$var\">$var</td>";		
	}
	?>
	<td><INPUT type="text" name="valor[]" value="" maxlength="8" size="8">
	<INPUT type="hidden" name="valor2[]" value="<?echo ($i-1)?>" >
	</td>
	<?	
	echo "</tr>";
}

?>

<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
<tbody>
<tr><td colspan="2" height="50" align="center" class="tb-tit"><INPUT type="button" name="guardar" value="Guardar" onclick="javascript:enviar('Guardar');"><INPUT type="submit" name="eliminar" value="Eliminar" onclick="javascript:enviar('Eliminar');">
</td></tr>
</tr>
</tbody>
</table>
</FORM>
</BODY>
</html>
<?cerrar_conexion($conexion);?>
