<?php
session_start();
ob_start();
$termino= $_SESSION['termino'];
include "../lib/common.php";

ob_clean();
class txt{
	private $temp,$retorno;
	public function completar($cad,$long)
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
		return $this->temp;
	}
}

$proceso = new txt();
$tipnom = $_SESSION['codigo_nomina'];
$nomina = $_GET['codnom'];
$mes    = (empty($_GET['mes'])) ? 0:   $_GET['mes'];
$tipos  = (empty($_GET['tipos'])) ? 0: $_GET['tipos'];
$anio   = (empty($_GET['anio'])) ? 0:  $_GET['anio'];
$meses  = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

$directorio="txt/nomina".date("Y_m_d_H_i_s");
if(mkdir($directorio)){
$ruta=$directorio."/bacpanama.txt";
$archivo= fopen($ruta,"w");
chmod($directorio,0777);
chmod($ruta,0777);
}else{
echo "No se pudo crear el directrorio";

}
$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

$proceso=new txt();

unset($totales);

$movimientos=new bd($_SESSION['bd']);

$constante_bac = 6383;

$consulta="SELECT
		replace(coalesce(SUM(nmn.monto), 0), '.', '') as neto,
		coalesce(count(nmn.ficha), 0) total,
		replace(nnp.fechapago, '-', '') fechapago
	from
		nom_movimientos_nomina nmn
	inner join nom_nominas_pago nnp on
		nmn.codnom = nnp.codnom
		and nmn.tipnom = nnp.codtip
	left join nomprestamos_cabecera nc on
		nc.numpre = nmn.numpre
		and nc.ficha = nmn.ficha
	where

		 month(nnp.fechapago) = '{$mes}'
		and year(nnp.fechapago) = '{$anio}'
		and estadopre not like '%Anulad%'

		and nmn.tipnom = '{$tipnom}';";

$resultado=$movimientos->query($consulta);
$totales = [];
$total = $resultado->fetch_assoc();
$totales["neto"] = $total["neto"];
$totales["total"] = $total["total"];
$totales["fechapago"] = $total["fechapago"];
$linea1.=str_pad("B", 1,' ', STR_PAD_LEFT);
$linea1.=str_pad($constante_bac, 4,' ', STR_PAD_LEFT);
$linea1.=str_pad('10', 5,' ', STR_PAD_LEFT);
$linea1.=str_pad("", 20,' ', STR_PAD_RIGHT);
$linea1.=str_pad("", 5,' ', STR_PAD_RIGHT);
$linea1.=str_pad($totales["fechapago"], 8,' ', STR_PAD_RIGHT);
$linea1.=str_pad($totales["neto"], 13, ' ', STR_PAD_LEFT);
$linea1.=str_pad($totales["total"], 5, "0", STR_PAD_LEFT);
$detalles.=$linea1;
$detalles.=PHP_EOL;

fwrite($archivo,$detalles);

$consulta="SELECT
		nmn.ficha,
		replace(coalesce(SUM(nmn.monto), 0), '.', '') monto,
		nc.ruta_destino ,
		nc.cuenta_destino ,
		nc.producto_destino,
		nnp.mes,
		nnp.anio,
		REPLACE(nnp.fechapago, '-','') fechapago,
		nc.detalle as detalle
	from
		nom_movimientos_nomina nmn
	inner join nom_nominas_pago nnp on
		nmn.codnom = nnp.codnom
		and nmn.tipnom = nnp.codtip
	left join nomprestamos_cabecera nc on
		nc.numpre = nmn.numpre
		and nc.ficha = nmn.ficha
	where

		 month(nnp.fechapago) = '{$mes}'
		and year(nnp.fechapago) = '{$anio}'
		and estadopre not like '%Anulad%'

		and nmn.tipnom = '{$tipnom}'
	group by
		nmn.ficha;";

$resultado=$movimientos->query($consulta);

$ixx = 1;
while($fila=$resultado->fetch_assoc())
{
	$detalles=$linea1=$linea2=$linea3="";
	//LINEA 1
	$linea2.="T";
	$linea2.=str_pad($constante_bac, 4," ", STR_PAD_LEFT);
	$linea2.=str_pad('10', 5," ", STR_PAD_LEFT);
	$linea2.=str_pad($fila["cuenta_destino"], 20," ", STR_PAD_RIGHT);
	$linea2.=str_pad($ixx, 5,'0', STR_PAD_LEFT);
	$linea2.=str_pad($fila["fechapago"], 8," ", STR_PAD_RIGHT);
	$linea2.=str_pad($fila["monto"], 13, '0', STR_PAD_LEFT);
	$linea2.=str_pad("", 5, " ", STR_PAD_LEFT);
	$linea2.=str_pad($meses[$fila["mes"]-1]." de ".$fila["anio"], 30, " ", STR_PAD_RIGHT);
	$linea2.=" ";
	$linea2.=substr($fila["detalle"],0,30);
	$detalles.=$linea2;
	$detalles.=PHP_EOL;
	fwrite($archivo,$detalles);
	$ixx++;

}

fclose($archivo);

header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="ACH_BAC.prn"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();
readfile($ruta);

?>
