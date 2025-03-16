<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../paginas/funciones_nomina.php";
require_once("../../includes/clases/EnLetras.php");

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

$nomina=$_GET['codigo_nomina'];

/*if($_SESSION['codigo_nomina']=='3')
{
	$codcon=2065;
	//$codcon_pat=3542;
}*/


$directorio="txt/nomina".date("Y_m_d_H_i_s");
if(mkdir($directorio)){
$ruta=$directorio."/CHEQUE-EMPLEADO.txt";
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

//$query="select codnom,descrip,codtip from nom_nominas_pago where codtip='".$_SESSION['codigo_nomina']."' and status='C'";

$consulta="select nnn.*,date_format(nnpa.periodo_fin,'%d%m%y')periodo_fin,np.apenom ,np.sueldopro,np.seguro_social,
sum((case when tipcon='A' then nnn.monto else 0 end)) monto_asignaciones,sum((case when tipcon='D' then nnn.monto else 0 end)) monto_deducciones
,date_format(nnpa.periodo_fin,'%d%m%y')periodo_fin2,np.nomposicion_id 
from nom_movimientos_nomina nnn 
inner join nom_nominas_pago nnpa on nnpa.codnom = nnn.codnom and nnpa.codtip = nnn.tipnom and nnpa.status='C' 
inner join nomcheques ch on ch.codnom = nnn.codnom and ch.tipo = 1  and nnn.cedula = ch.cedula_rif   
join nompersonal np on (np.cedula=nnn.cedula  and np.ficha=nnn.ficha) 
where nnn.codnom='".$_GET['codigo_nomina']."' and nnn.tipnom='".$_SESSION['codigo_nomina']."' 
group by nnn.codnom,nnn.tipnom,nnn.ficha";

$resultado=$movimientos->query($consulta);


while($fila=$resultado->fetch_assoc())
{
	//LINEA 1
	$detalles="\r\n\r\n\r\n                                            ".$fila['cheque']."\r\n\r\n";
	//LINEA 2
	$detalles="                                                   ".$fila['periodo_fin']."\r\n";
	//LINEA 3
	$detalles="                                                           ".$fila['periodo_fin2'].str_pad($_SESSION['codigo_nomina'], 2, "0", STR_PAD_LEFT);
	$detalles.=$fila['ficha']." ";
	$detalles.=$fila['sueldopro']." ";
	$detalles.=$fila['monto_asignaciones'];
	$detalles.="AXX ";
	$detalles.="XXX ";
	$detalles.="XXX\r\n";
	//LINEA 4
	$detalles.=$fila['cedula']."   ".str_pad($fila['nomposicion_id'], 2, "0", STR_PAD_LEFT)." ".$fila['ficha'];
	$detalles.=str_pad(" ", 68, " ", STR_PAD_LEFT)."SIACAP: XX.XX\r\n";
	//LINEA 5
	$detalles.="                                                           ".$fila['apenom']."\r\n";
	//LINEA 6
	$detalles.="          ".$fila['apenom']."           *".($fila['monto_asignaciones']-$fila['monto_deducciones'])."\r\n";
	//LINEA 7
	$detalles.="                                                           ".$fila['cedula']."  ".$fila['seguro_social']."        ".$fila['monto_deducciones']." *".($fila['monto_asignaciones']-$fila['monto_deducciones']). "\r\n";
	//LINEA 8 
	$V=new EnLetras(); 
	$detalles.="         **".strtoupper($V->ValorEnLetras(($fila['monto_asignaciones']-$fila['monto_deducciones']),"balboas"))."**\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
	//LINEA 9
	$detalles.="                                                           "."El Contribuyente es tu amigo, orientalo "."\r\n\r\n\r\n";
	
	fwrite($archivo,$detalles);
	//echo $detalles;
}


fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header('Content-Disposition: attachment; filename="CHEQUE-EMPLEADO.txt"');

?>
