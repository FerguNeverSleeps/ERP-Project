<?php
// Última moficacion: 27/02/2008 - 9:20am - César III
// Rutina para asignar el número del cheque próximo a guardar

$url="cheques_trabajador";
$modulo="cheques_trabajador";

session_start();
ob_start();


require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
//include ("../ewconfig.php");
//include ("../db.php");
//include ("bancos_info.php");
//include ("../advsecu.php");
//include ("../phpmkrfn.php");
//include ("../ewupload.php");
//include ("../header.php");
//if (!IsLoggedIn()) {
//	ob_end_clean();
//	header("Location: login.php");
//	exit();
//}

/*$conexion_conf=conexion_conf();
$consulta="select * from parametros";
$resultado=query($consulta,$conexion_conf);
$parametros=fetch_array($resultado);
cerrar_conexion($conexion_conf);
*/

$conexion=conexion();

$odp=@$_GET['odp'];
if(isset($_POST['guardar']))
{

//se verifica si la persona natural o juridica ya tiene algun cheque asignado
$conChe = "SELECT * FROM nomcheques WHERE cedula ='".$_POST['numero_identificacion']."' AND status = 'Im' ";
$resChe = query($conChe, $conexion);
$regChe = num_rows($resChe);
if($regChe > 0)
{
	echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	alert(\"Este beneficiario ya tiene asignado un cheque, VERIFIQUE!! \")
	</SCRIPT>";
}



//echo $_POST['codigo_banco'];
$resta=($_POST['saldo']-$_POST['monto_pagar']);
$consulta="UPDATE bancos SET saldo='".$resta."' WHERE codigo = '".$_POST['codigo_banco']."' ";

$resultado=query($consulta,$conexion);
$consulta="UPDATE cheques SET status='Ac', orden='".$_POST['ORP']."', beneficiario='".$_POST['beneficiario']."',cedula='".$_POST['numero_identificacion']."', consecutivo_E='".$_POST['consecutivo_E']."', monto='".$_POST['monto_pagar']."',concepto='".$_POST['concepto']."',cuenta='".$_POST['numero_cuenta']."', fecha='".fecha_sql($_POST['fecha'])."',log_usr='".$_SESSION['nombre']."' WHERE cheque='".$_POST['numero_cheque']."' AND chequera='".$_POST['numero_chequera']."' AND banco='".$_POST['codigo_banco']."'";

$resultado=mysqli_query($conexion,$consulta) or die("No se pudo actualizar la chequera");

cerrar_conexion($conexion);
//modificar consecutivo del configurador
$conexion_conf=conexion_conf();
$consulta="update parametros set consecutivo_E='".$_POST['consecutivo_E']."'";
$query_parametros=query($consulta,$conexion_conf);
cerrar_conexion($conexion_conf);

$conexion=conexion();

$consulta="UPDATE ordenes_pago SET cheque='".$_POST['numero_cheque']."',cuenta='".$_POST['numero_cuenta']."',chequera='".$_POST['numero_chequera']."', fecche='".fecha_sql($_POST['fecha'])."', estado='PAGADATER' WHERE numero_odp='".$_POST['numero_odp']."'";
$resultado=mysqli_query($conexion,$consulta) or die("No se pudo actualizar las ordenes de pago");

$consulta="insert into impuestos (banco, cheque, fecha, numord, beneficiario, cedula, monto) values ('".$_POST['codigo_banco']."','".$_POST['numero_cheque']."','".fecha_sql($_POST['fecha'])."','".$_POST['numero_odp']."','".$_POST['beneficiario']."','".$_POST['numero_identificacion']."','".$_POST['monto_pagar']."')";
$resultado=mysqli_query($conexion,$consulta) or die("No se pudo insertar los datos correspondientes a impuestos");

$consulta="insert into movimientos_bancarios values ('','".$_POST['codigo_banco']."','".fecha_sql($_POST['fecha'])."', 'Cheque','".$_POST['numero_cheque']."', '".$_POST['monto_pagar']."', '".$_POST['concepto']."','no', 'desc', '".$_POST['cuenta_contable_fiscal']."','','')";
$resultado=mysqli_query($conexion,$consulta) or die("No se pudo insertar los datos correspondientes movimientos bancarios");

	$consultab="select * from beneficiario where identificacion='".$_POST['numero_identificacion']."'";
	$resultado_eje=query($consultab,$conexion);
	$filab=fetch_array($resultado_eje);
	$descue=$filab['nombre_beneficiario'];
	$i=1;
	//if($odp!=0){
		$consulta="insert into bauche_det (banco,chequera,cheque,correl,ctaban,fecha,cuenta,descue,debitos,descripcion) values ('".$_POST['codigo_banco']."','".$_POST['numero_chequera']."','".$_POST['numero_cheque']."','$i','".$_POST['numero_cuenta']."','".fecha_sql($_POST['fecha'])."','".$filab['cuenta_contable']."','".$descue."','".$_POST['monto_pagar']."','".$_POST['beneficiario']."')";
	//}
		//echo $consulta." Linea 68 <br>";
	$resultado=query($consulta,$conexion);
	$i++;
	
	$consulta="select * from bancos where codigo='".$_POST['codigo_banco']."'";
	$resultado_ban=query($consulta,$conexion) ;
	//$datos_banco=fetch_array($resultado);
	
	while($fila2=fetch_array($resultado_ban))
	{
		$consulta="insert into bauche_det (banco,chequera,cheque,correl,ctaban,fecha,cuenta,descue,creditos,descripcion) values ('".$_POST['codigo_banco']."','".$_POST['numero_chequera']."','".$_POST['numero_cheque']."','$i','".$_POST['numero_cuenta']."','".fecha_sql($_POST['fecha'])."','".$fila2['cuenta_contable']."','".$fila2['descripcion']."','".$_POST['monto_pagar']."','".$fila2['descripcion']."')";
		$resultado=query($consulta,$conexion);
		$i++;
	}

//verificamos si la chequera esta consumida
	$proc=new procesos;
	$status=$proc->final_chequera($_POST['codigo_banco'],$_POST['numero_chequera'],$_POST['numero_cheque']);
	if($status){
		echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
		alert(\"La chequera se ha consumido. Recuerde activar una nueva chequera\")
		</SCRIPT>";
	}


// 1.
//Update en Banco Mes....
	
	$fecha = fecha_sql($_POST['fecha']);
	list($dia,$mes_mov,$anio) = explode('[/.-]',$fecha);
	//echo "Dia: ".$dia." Mes: ".$mes." Anio: ".$anio;
	//exit(0);

	$consulta="select month(periodo_banco) as valor, monto_credito,saldo_actual,saldo_anterior,monto_debito,monto_credito from banco_mes where codigo='".$_POST['codigo_banco']."' and month(periodo_banco)='".$mes_mov."'";
	$resultado=query($consulta, $conexion);
	$i=0;
	$fila=fetch_array($resultado);
	//$periodo_banco=$fila['mes'];
	$saldo_anterior=$fila['saldo_anterior'];
	$monto_debito=$fila['monto_debito'];
	$monto_credito=$fila['monto_credito'];
	
	//$monto_credito=$fila['monto_pagar'];
	$mes=$fila['valor'];
	//$monto_credito=$_POST['monto_pagar'];

	if($fila['monto_credito']==0)
	{
		$monto_credito=$_POST['monto_pagar'];
	}else if ($fila['monto_credito']>0)
	{
		$monto_credi=$fila['monto_credito'];
		$monto_credito=$monto_credi+$_POST['monto_pagar'];
		//echo "Pase por aqui".$monto_credito."<br>";
	}
	$saldo_actual=($saldo_anterior+$monto_debito)-$monto_credito;
	//Actualiza el mes de del cheque
	//echo "<br>I: ".$i."Mes Cheque: ".$mes_cheque." Monto Credito: ".$monto_credito;
	$consulta1="update banco_mes set monto_credito='".$monto_credito."',saldo_actual='".$saldo_actual."' where codigo='".$_POST['codigo_banco']."' and month(periodo_banco)='".$mes_mov."'";
	$resultado1=query($consulta1,$conexion);
	//echo "Consulta: ".$consulta1;

	//echo "<br>Consul1: ".$consulta1;
	//Actualiza los saldos actual y anterior del resto del año
	$cont=$mes+1;
	$b=0;
	for($i=$cont; $i<=12; $i++)
	{
		//echo "<br>I: ".$i."<br>";
		//$c=$mes_mov+$b;
		$consulta="select month(periodo_banco) as valor, monto_credito,saldo_actual,saldo_anterior,monto_debito,monto_credito from banco_mes where codigo='".$_POST['codigo_banco']."' and month(periodo_banco)='".($i)."'";
		$resultado=query($consulta, $conexion);
		//echo "<br> consulta1: ".$consulta;
		$fila=fetch_array($resultado);
		//$saldo_actual=$fila['saldo_actual'];
		$saldo_anterior=$saldo_actual;//$fila['saldo_actual'];
		$monto_debito=$fila['monto_debito'];
		$monto_credito=$fila['monto_credito'];

		$saldo_actual=($saldo_anterior+$monto_debito)-$monto_credito;
		//echo "<br> Actual: ".$saldo_actual.", Anterior: ".$saldo_anterior.", Debito: ".$monto_debito.", Credito: ".$monto_credito.", I: ".$c;

		$consulta2="update banco_mes set saldo_actual='".$saldo_actual."', saldo_anterior='".$saldo_anterior."' where codigo='".$_POST['codigo_banco']."' and month(periodo_banco)='".($i)."'";
		$resultado2=query($consulta2,$conexion);
		//echo "<br>Consul2: ".$consulta2;
		$b++;
	}
//Fin de banco mes




	cerrar_conexion($conexion);
	echo"<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	alert(\"Se ha registrado un nuevo cheque\")
	parent.cont.location.href=\"seleccionar_chequera.php?pagina=1&codigo=".$_POST['codigo_banco']."\"
	</SCRIPT>";

//exit(0);
}

$banco=@$_GET['banco'];
$chequera=@$_GET['codigo'];



$consulta="select * from nombancos where cod_ban='".$banco."'";
$resultado=mysqli_query($conexion,$consulta) or die("No se pueden extraer los datos del banco");
$datos_banco=mysqli_fetch_array($resultado);

$consulta="select * from nomchequera where banco=".$banco." AND situacion='A' AND numero='".$chequera."' order by numero";
$resultado=mysqli_query($conexion,$consulta) or die("No se pueden extraer los datos de la chequera");
$datos_chequera=mysqli_fetch_array($resultado);

/*$consulta="select * from cheques where banco=".$banco." AND chequera='".$datos_chequera["numero"]."' AND status='A' order by cheque";
$resultado=mysqli_query($consulta) or die("No se pueden extraer los datos de los cheques");
$datos_cheque=mysqli_fetch_array($resultado);*/

$consulta="select min(consecutivo_cheque) as minimo from nomcheques where banco=".$banco." AND chequera='".$datos_chequera["numero"]."' AND status='A'";
$resultado=query($consulta,$conexion);
$datos_cheque=fetch_array($resultado);

$con = "SELECT * FROM nomcheques WHERE consecutivo_cheque='".$datos_cheque["minimo"]."' AND banco=".$banco." AND chequera='".$datos_chequera["numero"]."' AND status='A'";
$res = query($con, $conexion);
$fil = fetch_array($res);
$chequeProximo = $fil['cheque'];






?>
<html class="fondo">
<HEAD>

<link href="../estilos.css" rel="stylesheet" type="text/css">
<TITLE></TITLE>
<SCRIPT language="JavaScript" type="text/javascript" src="../lib/common.js"></script>
<script language="javascript" src="cal2.js"></script>
<script language="javascript" src="cal_conf2.js"></script>
<SCRIPT language="JavaScript" type="text/javascript">

function cargar_cheque(){

alert("efrrdd")
//var ben=document.getElementById('beneficiario')
//var nom=document.getElementById('nombre_cheque')

}

</SCRIPT>
</HEAD>

<BODY >
<FORM name="sampleform" action="<?php echo $_SERVER['PHP_SELF']; ?>?odp=<?echo $odp;?>" method="POST" target="_self">
<?
	titulo($datos_banco["des_ban"].", Nro. Cuenta: ".$datos_banco["cuentacob"], "","seleccionar_chequera.php?pagina=1&codigo=".$banco,"284");
	boton1("10","Seleccionar Beneficiario","seleccionar_beneficiario.php?pagina=1&codigo=".$banco."&chequera=".$chequera);
	//boton1("15","Seleccionar Orden de Pago","seleccionar_orden.php?tipo=1&pagina=1&codigo=".$banco."&chequera=".$chequera);
	echo "<br><br><br><br><br><br><br>";
?>
<BR>
<table cellpadding="1" border="0" cellspacing="0" width="100%">
  <tbody>
    <tr>
      <td colspan="2" height="20" class="tb-head"><strong>Datos Generales</strong></td>
    </tr>
    <tr>
      <td><strong>Chequera:</strong></td>
<?//Datos necesarios para realizar las actualizaciones de las tablas?>
<INPUT type="hidden" name="numero_cuenta" value="<?echo $datos_banco["cuenta"]?>">
<INPUT type="hidden" name="codigo_banco" value="<?echo $banco?>">
<INPUT type="hidden" name="nombre_banco" value="<?echo $datos_banco["descripcion"]?>">
<INPUT type="hidden" name="nombre_banco" value="<?echo $datos_banco["descripcion"]?>">
<INPUT type="hidden" name="saldo" value="<?echo $datos_banco["saldo"]?>">
<?//-----------------*************************-------------------?>
      <td><INPUT size="50" type="text" name="numero_chequera" value="<?echo $datos_chequera["numero"]?>" readonly="true"></td>
    </tr>
    <tr>
      <td><strong>Cheque:</strong></td>
      <td><INPUT type="text" name="numero_cheque" size="50" value="<?echo $chequeProximo?>" readonly="true"></td>
    </tr>
    <tr>
      <td><strong>Orden de Pago:</strong></td>
      <td><INPUT type="text" name="ORP" id="ORP" size="50" value="<?echo $odp?>" ></td>
    </tr>
<tr>
      <td><strong>Fecha:</strong></td>
      <td><table>
     <TR>
       <td width="90" align="center"><input name="fecha" id="fecha" type="text"  value="<?php echo date('d/m/Y')?>" size="12" maxlength="12" /></td>
       <td width="32"><input  name="b_fecha" type="image" id="b_fecha" src="../lib/jscalendar/cal.gif" />
       <script type="text/javascript"> 
       Calendar.setup( {inputField:"fecha",ifFormat:"%d/%m/%Y",button:"b_fecha",firstDay:1,weekNumbers:false,showOthers:true} );
       </script></td>

     </TR>
 	
   </table></td>
</tr>
<tr>
		<??>
      		<td><strong>N° Egreso:</strong></td>
      		<td><INPUT type="text" name="consecutivo_E" size="12" value="<?echo $parametros['consecutivo_E']+1?>" ></td>
	</tr>
<tr class="tb-head">
      <td height="20"><strong>Datos del Beneficiario</strong></td><td></td>
</tr>

</td>
    </tr>
<?if(!$odp){?>
<tr>
      <td><strong>Numero de Identificaci&oacute;n:</strong></td>
      <td><INPUT type="text" name="numero_identificacion" size="50" value="<?echo @$_GET['identificacion']?>"></td>
    </tr>
<tr>
      <td><strong>Nombre o Denominaci&oacute;n </strong></td>
      <td><INPUT type="text" name="beneficiario" size="50" value="<?echo @$_GET['nombre']?>"></td>
    </tr>
    <tr>
      <td><strong>Monto a Pagar:</strong></td>
      <td><INPUT type="text" name="monto_pagar" size="50"></td>
    </tr>
<tr>
      <td><strong>Concepto</strong></td>
      <td><textarea  id="text" name="concepto" cols="100"></textarea></td>
    </tr>
<?}else{
$conex=conexion();
$consulta="select * from ordenes_pago where numero_odp='".$odp."'";
$resultado1=query($consulta,$conex);
$datos_orden=fetch_array($resultado1);

?>
<tr>
	  <INPUT type="hidden" name="numero_odp" size="50" value="<?echo $odp?>">
      <td><strong>Numero de Identificaci&oacute;n:</strong></td>
      <td><INPUT type="text" name="numero_identificacion" size="50" value="<?echo $datos_orden["rif"]?>"></td>
    </tr>
<tr>
      <td><strong>Nombre o Denominaci&oacute;n </strong></td>
      <td><INPUT type="text" name="beneficiario" size="50" value="<?echo $datos_orden["bene"]?>"></td>
    </tr>
    <tr>
      <td><strong>Monto a Pagar:</strong></td>
      <td><INPUT type="text" readonly="true" name="monto_pagar" size="50" value="<?echo $datos_orden["montopago"]?>"></td>
    </tr>
<INPUT type="hidden" name="resultado" value="<?echo ($datos_banco["saldo"]-$datos_orden["montopago"])?>">
<tr>
      <td><strong>Concepto</strong></td>
      <td><INPUT type="text" readonly="true" name="concepto" size="70" value="<?echo $datos_orden["concepto"]?>"></td>
    </tr>
<?}?>
    <tr>
      <td colspan="2" width="900"></td>
    </tr>
  </tbody>
</table>
<table width="700"><TR><TD align="center"><INPUT type="submit" name="guardar" value="GUARDAR CHEQUE"></TD></TR></table>
</FORM>
</BODY>
</html>