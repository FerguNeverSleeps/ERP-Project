<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../paginas/funciones_nomina.php";


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
$ruta=$directorio."/REGULAR.txt";
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


$consulta="select count(*) cantidad,sum(monto) total,nnpa.descrip
from nom_movimientos_nomina nnn 
left join nom_nominas_pago nnpa on nnpa.codnom = nnn.codnom and nnpa.codtip = nnn.tipnom and nnpa.status='C' 
where nnn.codnom='".$_GET['codigo_nomina']."' and nnn.tipnom='".$_SESSION['codigo_nomina']."' 
group by nnn.codnom,nnn.tipnom";
$resultado=$movimientos->query($consulta);
$fila=$resultado->fetch_assoc();

$detalles="TOTAL DE REGISTRO ".$fila['descrip']." = ".$fila['cantidad'];
$detalles.="TOTAL SALARIO NETO ".$fila['descrip']." = ".$fila['total'];
fwrite($archivo,$detalles);


fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header('Content-Disposition: attachment; filename="REGULAR.txt"');

?>
