<?php
session_start();
ob_start();
$termino= $_SESSION['termino'];

include "../lib/common.php";
//require_once("../../includes/clases/EnLetras.php");

//include "../header.php";
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
		//$this->retorno=$this->temp;
		//$this->retorno.=$this->temp[1];
		return $this->temp;
	}

}

$proceso=new txt();

$codnom = isset($_GET['codnom']) ? $_GET['codnom'] : '' ;
$tipnom = isset($_GET['tipnom']) ? $_GET['tipnom'] : '' ;



$directorio="txt/nomina".date("Y_m_d_H_i_s");
if(mkdir($directorio)){
$ruta=$directorio."/AMAXONIA_TO_INNOVA.txt";
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
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

$consulta_movimientos = "SELECT np.ficha,nmn.codnom, date_format(nnp.periodo_ini,'%d%m%Y') periodo_ini, date_format(nnp.periodo_fin,'%d%m%Y') periodo_fin,date_format(nnp.fechapago,'%d%m%Y') fechapago,np.cedula, CONCAT(np.nombres, ' ',np.apellidos) apenom,  nmn.codcon, nc.ctacon,
nmn.descrip descrip_concepto, np.forcob, nmn.tipnom, nnp.frecuencia,nmn.codnivel1,nv1.descrip descrip_nivel1, nc.reserva, nc.cta_contab_reserva,np.cuentacob,nmn.tipcon,nmn.monto,nb.cod_ban banco,ntn.descrip tipo_planilla,nf.descrip frecuencia, nnn.neto
FROM nom_nominas_pago nnp
INNER JOIN nom_movimientos_nomina nmn on (nnp.codnom = nmn.codnom AND nnp.codtip = nmn.tipnom)
INNER JOIN nompersonal np on (np.ficha = nmn.ficha)
INNER JOIN nomconceptos nc on (nc.codcon = nmn.codcon)
INNER JOIN nomnivel1 nv1 on (nv1.codorg = nmn.codnivel1)
INNER JOIN nombancos nb on (nb.cod_ban = np.codbancob)
INNER JOIN nomtipos_nomina ntn on (ntn.codtip = nnp.codtip)
INNER JOIN nomfrecuencias nf on (nf.codfre = nnp.frecuencia)
INNER JOIN nom_nomina_netos nnn on (nnn.codnom = nmn.codnom AND nnn.tipnom = nmn.tipnom AND nnn.ficha = nmn.ficha)
where nmn.codnom  = '{$codnom}' AND nmn.tipnom = '{$tipnom}' 
ORDER BY nmn.ficha, nmn.codcon";

$data = 0;
$personal = array();
$resultado=$movimientos->query($consulta_movimientos);
/*$detalles = "X|--NOMINA--|--FECHA DESDE--|--FECHA HASTA--|--FECHA PAGO--|------IDFISCAL------|------------------NOMBRE APELLIDO--------------------------|---CONCEPTO---|T|----CTA CONTABLE----|----------------DESCRIPCION MOVIMIENTO---------------------|---DEBITO---|---CREDITO---|-----TIPO PAGO-----|-----BANCO PAGO-----|-----CTA PAGO-----|-----TIPO NOMINA-----|-----FRECUENCIA-----|--------------------NOMBRE NIVEL 1--------------------------|----CTA CONTABLE----|
";	$detalles   .="\r\n";*/
//echo $detalles;
$ficha_anterior = "";
$linea1 = 1;
$total_neto = 0;
while ($row = $resultado->fetch_assoc()) {
	$detalles           = "";
	$tipreg             = 1;
	$codnom             = $row['codnom'];
	$fecha_inicio       = $row['periodo_ini'];
	$periodo_fin        = $row['periodo_fin'];
	$fechapago          = $row['fechapago'];
	$apenom             = $row['apenom'];
	$codcon             = $row['codcon'];
	$tipcon             = ($row[reserva]=="1") ? "X" : $row['tipcon'] ;
	$cedula             = $row['cedula'];
	$ctacon1            = $row['ctacon'];
	$descrip_concepto   = $row['descrip_concepto'];
	$forcob             = $row['forcob'];
	$tipnom             = $row['tipnom'];
	$frecuencia         = $row['frecuencia'];
	$cuentacob          = $row['cuentacob'];
	$descrip_nivel1     = $row['descrip_nivel1'];
	$banco              = $row['banco'];
	$reserva            = $row['reserva'];
	$tipo_planilla      = $row['tipo_planilla'];
	$cta_contab_reserva = ($row[reserva]=="1") ? $row['cta_contab_reserva'] : "" ;
	$debito             = ($row[tipcon]=="A") ? $row[monto] : "" ;
	if ($row[tipcon] == "D" || $row[tipcon] == "P"){
		$credito = $row[monto];
	}
	else{
		$credito = "";

	}
	$ficha              = $row['ficha'];
	if($ficha != $ficha_anterior AND $linea1 == 0)
	{
		$total_neto    += $neto_anterior;
		$ficha_anterior = $row['ficha'];
		$detalles   .= str_pad("2", 1, "0", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad(str_pad($codnom, 6, "0", STR_PAD_LEFT), 10, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($fecha_inicio, 15, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($periodo_fin, 15, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($fechapago, 14, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($cedula_anterior, 20, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad(utf8_decode($nombre_anterior), 59, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad(str_pad("", 6, "0", STR_PAD_LEFT), 14, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad("Z", 1, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad("", 20, " ", STR_PAD_LEFT);
		$detalles   .= str_pad("NETO", 58, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($neto_anterior, 12, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad("", 13, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($forcob_anterior, 19, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($banc_anterior, 20, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($cuentacob, 18, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($tipo_planilla, 21, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($frecuencia, 20, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad(utf8_decode($descrip_nivel1_anterior), 60, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($cta_contab_reserva, 20, " ", STR_PAD_RIGHT);
		$detalles   .="\r\n";
		
		//fwrite($archivo,$detalles);
		//echo $detalles;
		
	}		
		$ficha_anterior  = $row['ficha'];		
		$nombre_anterior = $row['apenom'];
		$forcob_anterior = $row['forcob'];
		$banc_anterior   = $row['banco'];
		$cedula_anterior = $row['cedula'];
		$descrip_nivel1_anterior = $row['descrip_nivel1'];
		$neto_anterior   = $row['neto'];

		$detalles   .= str_pad($tipreg, 1, "0", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad(str_pad($codnom, 6, "0", STR_PAD_LEFT), 10, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($fecha_inicio, 15, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($periodo_fin, 15, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($fechapago, 14, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($cedula, 20, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($apenom, 59, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad(str_pad($codcon, 6, "0", STR_PAD_LEFT), 14, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($tipcon, 1, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($ctacon1, 20, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($descrip_concepto, 57, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($debito, 12, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($credito, 13, " ", STR_PAD_LEFT);
		$detalles	.= " ";
		$detalles   .= str_pad($forcob, 19, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($banco, 20, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($cuentacob, 18, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($tipo_planilla, 21, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($frecuencia, 20, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($descrip_nivel1, 60, " ", STR_PAD_RIGHT);
		$detalles	.= " ";
		$detalles   .= str_pad($cta_contab_reserva, 20, " ", STR_PAD_RIGHT);
		$detalles   .="\r\n";		
		$linea1      = 0;

	
	fwrite($archivo,$detalles);
	//echo $detalles;
	
	$data = 1;

}
$detalles = "";
if($ficha == $ficha_anterior AND $linea1 == 0)
{
	$total_neto    += $neto_anterior;
	$ficha_anterior = $row['ficha'];
	$detalles   .= str_pad("2", 1, "0", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad(str_pad($codnom, 6, "0", STR_PAD_LEFT), 10, " ", STR_PAD_LEFT);
	$detalles	.= " ";
	$detalles   .= str_pad($fecha_inicio, 15, " ", STR_PAD_LEFT);
	$detalles	.= " ";
	$detalles   .= str_pad($periodo_fin, 15, " ", STR_PAD_LEFT);
	$detalles	.= " ";
	$detalles   .= str_pad($fechapago, 14, " ", STR_PAD_LEFT);
	$detalles	.= " ";
	$detalles   .= str_pad($cedula_anterior, 20, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad(utf8_decode($nombre_anterior), 59, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad(str_pad("", 6, "0", STR_PAD_LEFT), 14, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad("Z", 1, " ", STR_PAD_LEFT);
	$detalles	.= " ";
	$detalles   .= str_pad("", 20, " ", STR_PAD_LEFT);
	$detalles   .= str_pad("NETO", 58, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad($neto_anterior, 12, " ", STR_PAD_LEFT);
	$detalles	.= " ";
	$detalles   .= str_pad("", 13, " ", STR_PAD_LEFT);
	$detalles	.= " ";
	$detalles   .= str_pad($forcob_anterior, 19, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad($banc_anterior, 20, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad($cuentacob, 18, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad($tipo_planilla, 21, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad($frecuencia, 20, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad(utf8_decode($descrip_nivel1_anterior), 60, " ", STR_PAD_RIGHT);
	$detalles	.= " ";
	$detalles   .= str_pad("", 20, " ", STR_PAD_RIGHT);
	$detalles   .="\r\n";
	
	fwrite($archivo,$detalles);
	//echo $detalles;
	
}
$detalles = "";
$linea1         = 0;
$ficha_anterior = $row['ficha'];
$debito         = $total_neto;
$detalles   .= str_pad("3", 1, "0", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad(str_pad($codnom, 6, "0", STR_PAD_LEFT), 10, " ", STR_PAD_LEFT);
$detalles	.= " ";
$detalles   .= str_pad($fecha_inicio, 15, " ", STR_PAD_LEFT);
$detalles	.= " ";
$detalles   .= str_pad($periodo_fin, 15, " ", STR_PAD_LEFT);
$detalles	.= " ";
$detalles   .= str_pad($fechapago, 14, " ", STR_PAD_LEFT);
$detalles	.= " ";
$detalles   .= str_pad("", 20, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad(utf8_decode(""), 59, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad(str_pad("", 6, "0", STR_PAD_LEFT), 14, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad("Z", 1, " ", STR_PAD_LEFT);
$detalles	.= " ";
$detalles   .= str_pad("", 20, " ", STR_PAD_LEFT);
$detalles   .= str_pad(utf8_decode("TOTAL NETO"), 58, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad($debito, 12, " ", STR_PAD_LEFT);
$detalles	.= " ";
$detalles   .= str_pad("", 13, " ", STR_PAD_LEFT);
$detalles	.= " ";
$detalles   .= str_pad($forcob, 19, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad($banco, 20, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad($cuentacob, 18, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad($tipo_planilla, 21, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad($frecuencia, 20, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad(utf8_decode($descrip_nivel1), 60, " ", STR_PAD_RIGHT);
$detalles	.= " ";
$detalles   .= str_pad("", 20, " ", STR_PAD_RIGHT);
$detalles   .="\r\n";

fwrite($archivo,$detalles);
//echo $detalles;
fclose($archivo);

$totales=new bd($_SESSION['bd']);
$consultar = "
SELECT date_format(periodo_fin,'%Y%m%d') fecha_archivo 
FROM nom_nominas_pago 
WHERE codnom  = '{$codnom}' AND tipnom = '{$tipnom}'
";
$resultado_fecha=$totales->query($consultar);
$fechax = $resultado_fecha->fetch_array();
//echo "Content-Disposition: attachment; filename=".$ruta; 
$nombre_archivo = "AMX_TO_INN_".$fechax['fecha_archivo'].".txt";

header('Content-Type: application/txt');
header("Content-Disposition: attachment; filename=$nombre_archivo");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();
readfile($ruta);
$mensaje = ($data == "0") ? "Error al generar el archivo" : "Archivo generado exitosamente" ;
$array = array("mensaje" => $mensaje,"data" => $data);
return json_encode($array);
?>
