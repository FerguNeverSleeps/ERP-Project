<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
include "../lib/common.php";

ob_clean();
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
$tipo_nomina=$_GET['codtip'];
$nomina=$_GET['codnom'];
$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
/*if($_SESSION['codigo_nomina']=='3')
{
	$codcon=2065;
	//$codcon_pat=3542;
}*/


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
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

//$query="select codnom,descrip,codtip from nom_nominas_pago where codtip='$tipo_nomina' and status='C'";

$constante_bac = 6383;

$consulta="SELECT REPLACE(COALESCE(SUM(nnn.neto),0), '.','') as neto, COALESCE(count(nnn.ficha),0) total, REPLACE(nnpa.fechapago, '-','') fechapago
from nom_nomina_netos nnn 
join nompersonal np on (np.cedula=nnn.cedula  and np.ficha=nnn.ficha) 
left join nombancos ban on ban.cod_ban = np.codbancob 
left join nom_nominas_pago nnpa on nnpa.codnom = nnn.codnom and nnpa.codtip = nnn.tipnom and nnpa.status='C' 
where nnn.codnom='$nomina' and nnn.tipnom='$tipo_nomina' and  np.forcob not in ('Efectivo', 'Cheque') and np.codbancob in ('1', '2');";

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

$consulta="SELECT nnn.*, np.apenom, np.telefonos,nnpa.descrip ,np.codbancob,np.cuentacob,ban.ruta,np.forcob, 
fre.codfre as cod_fre,
fre.descrip as frecuencia,
nnpa.mes,
nnpa.anio,
REPLACE(COALESCE((nnn.neto),0), '.','') as neto_per, 
REPLACE(nnpa.fechapago, '-','') fechapago,
CONCAT( np.nombres, ' ', np.apellidos) as descrip_nomina
from nom_nomina_netos nnn 
join nompersonal np on (np.cedula=nnn.cedula  and np.ficha=nnn.ficha) 
left join nombancos ban on ban.cod_ban = np.codbancob 
left join nom_nominas_pago nnpa on nnpa.codnom = nnn.codnom and nnpa.codtip = nnn.tipnom and nnpa.status='C' 
left join nomfrecuencias fre on fre.codfre = nnpa.frecuencia
where nnn.codnom='$nomina' and nnn.tipnom='$tipo_nomina' and np.forcob not in ('Efectivo', 'Cheque') and np.codbancob in ('1', '2')   order by nnn.cedula";


$resultado=$movimientos->query($consulta);

$ixx = 1;
while($fila=$resultado->fetch_assoc())
{
	$detalles=$linea1=$linea2=$linea3="";
	//LINEA 1
	$linea2.="T";
	$linea2.=str_pad($constante_bac, 4," ", STR_PAD_LEFT);
	$linea2.=str_pad('10', 5," ", STR_PAD_LEFT);
	$linea2.=str_pad($fila["cuentacob"], 20," ", STR_PAD_RIGHT);
	$linea2.=str_pad($ixx, 5,'0', STR_PAD_LEFT);
	$linea2.=str_pad($fila["fechapago"], 8," ", STR_PAD_RIGHT);
	$linea2.=str_pad($fila["neto_per"], 13, '0', STR_PAD_LEFT);
	$linea2.=str_pad("", 5, " ", STR_PAD_LEFT);
	$linea2.=str_pad($fila["frecuencia"]." de ".$meses[$fila["mes"]-1]." de ".$fila["anio"], 30, " ", STR_PAD_RIGHT);
	$linea2.=" ";
	$linea2.=substr($fila["descrip_nomina"],0,30);
	$detalles.=$linea2;
	$detalles.=PHP_EOL;
	fwrite($archivo,$detalles);
	$ixx++;
	//echo $detalles;
}
//echo $detalles;exit;

fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="ACH_BAC.txt"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();
readfile($ruta);

?>