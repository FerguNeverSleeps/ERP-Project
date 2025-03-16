<?
session_start();
ob_start();
$termino= $_SESSION['termino'];

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sincta.xls");

?>
<?
include "../lib/common.php";
include "../paginas/funciones_nomina.php";


//include "../header.php";
class txt{
	private $temp,$retorno;
	public function completar($cuenta_bancaria,$long)
	{
		$this->temp=$cad;
		for($i=1;$i<=$long;$i++){
			$this->temp="0".$this->temp;	
		}
		return $this->temp;
		
	}
	public function completar2($LARGO,$CADENA)
	{
		$this->temp=$cad;
		for($i=1;$i<=$long;$i++){
			$this->temp="0".$this->temp;
		}
		return $this->temp;
		
	}
	public function formatear_monto($monto){
		$this->temp=number_format($monto,2,'','');
		//$this->retorno=$this->temp;
		//$this->retorno.=$this->temp[1];
		return $this->temp;
	}

}

$proceso=new txt();

$nomina=$_GET['codigo_nomina'];

if($_SESSION['codigo_nomina']=='3')
{
	$codcon=2065;
	//$codcon_pat=3542;
}



$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

$proceso=new txt();
//creamos la cabecera del txt



unset($totales);
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

$consulta="select nnn.*, np.apenom, np.telefonos  from nom_nomina_netos nnn join nompersonal np on (np.cedula=nnn.cedula and np.ficha=nnn.ficha) where nnn.codnom='".$_GET['codigo_nomina']."' and nnn.tipnom='".$_SESSION['codigo_nomina']."' and np.cuentacob='1' order by nnn.cedula";
$resultado=$movimientos->query($consulta);


?>
<table>

<?php

while($fila=$resultado->fetch_assoc())
{
	if($fila['cta_ban']=="")
	{	
?>
<tr>
<?php
	//$detalles="";
	$neto_empleado=$fila['neto'];
	$cuenta_bancaria=$fila['cta_ban'];
	$cedula=$fila['cedula'];
	
	//$identificacion="770";
	//$detalles.=$identificacion;
	//$cuenta_bancaria="010800".$cuenta_bancaria;
	$numero_cuenta=$proceso->completar($cuenta_bancaria,20);
	//$detalles.=$cedula;
	//$detalles.=',';
	$nombre=str_replace(",","",$fila['apenom']);
	/*$detalles.=$nombre;
	$detalles.=',000000071';
	$detalles.=',';
	$detalles.=$fila[cta_ban];
	$detalles.=',04';
	$detalles.=',';
	//$campo_libre=$proceso->completar("",4);
	//$detalles.=$campo_libre;

	//$detalles.=$mes_pago;
	if(strlen($neto_empleado)==6)
		$detalles.=$proceso->completar($neto_empleado,5);
	elseif(strlen($neto_empleado)==5)
		$detalles.=$proceso->completar($neto_empleado,6);
	elseif(strlen($neto_empleado)==7)
		$detalles.=$proceso->completar($neto_empleado,4);
	
	$detalles.=",C,REF*TXT**DEPOSITO DIRECTO DE PLANILLA\ ";

	$detalles.="\r\n";
	fwrite($archivo,$detalles);*/
	echo "<td>".$cuenta_bancaria."</td><td>".$neto_empleado."</td><td>DEPOSITO DIRECTO DE PLANILLA</td><td>".$nombre."</td>";

?>
</tr>

<?php
}
}
?>
</table>