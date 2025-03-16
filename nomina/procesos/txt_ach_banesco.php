<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
include "../lib/common.php";



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
$ruta=$directorio."/banconacionalpanama.txt";
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


echo $consulta="select nnn.*, np.apenom, np.telefonos,nnpa.descrip ,np.codbancob,np.cuentacob,ban.ruta,np.forcob
from nom_nomina_netos nnn 
join nompersonal np on (np.cedula=nnn.cedula  and np.ficha=nnn.ficha) 
left join nombancos ban on ban.cod_ban = np.codbancob 
left join nom_nominas_pago nnpa on nnpa.codnom = nnn.codnom and nnpa.codtip = nnn.tipnom and nnpa.status='C' 
where nnn.codnom='".$_GET['codigo_nomina']."' and nnn.tipnom='".$_SESSION['codigo_nomina']."' and np.forcob<>'Efectivo' and np.codbancob = '2'   order by nnn.cedula";
$resultado=$movimientos->query($consulta);
exit(0);

while($fila=$resultado->fetch_assoc())
{
	$detalles=$linea1=$linea2=$linea3="";
	$neto_empleado=$fila['neto'];
	$cuenta_bancaria=$fila['cta_ban'];
	$cedula=$fila['cedula'];	
	//LINEA 1
	$detalles.="C";	
	$val1 = 1;
	$linea1.=str_pad($val1, 9, "0", STR_PAD_LEFT);
	$linea1.=str_pad($neto_empleado, 12, "0", STR_PAD_LEFT);
	$detalles.=str_pad($linea1, 22,"0",STR_PAD_LEFT);
	$detalles.='\r\n';
	if(strlen($fila['apenom']) > 22 ){
		$nombre=substr($fila['apenom'], 0, 22);
	}
	else{
		$nombre=str_pad($fila['apenom'], 22, " ", STR_PAD_RIGHT);
	}

	if ($fila['forcob'] == "Cuenta Ahorro") {
		$siglas = "SC";
	}
	elseif ($fila['forcob'] == "Cuenta Corriente") {
		$siglas = "DC";
	}
	//LINEA 2

	$linea2 = $siglas;
	$linea2.=str_pad($cuenta_bancaria, 17, " ", STR_PAD_RIGHT);

	$linea2.=$nombre;
	$linea2.=str_pad($neto_empleado, 11, "0", STR_PAD_LEFT);
	$linea2.=str_pad($fila['ruta'], 9, "0", STR_PAD_LEFT);
	$linea2.=str_pad($cuenta_bancaria, 17, " ", STR_PAD_RIGHT);
	$detalles.=str_pad($linea2, 71,"0",STR_PAD_LEFT);
	$detalles.='\r\n';
	//LINEA 3
	$val2 = 1;
	$linea3="T";
	$linea3.=str_pad($val2, 9, "0", STR_PAD_LEFT);
	$linea3.=str_pad($neto_empleado, 12, "0", STR_PAD_LEFT);
	$detalles.=$linea3;
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	//echo $detalles;
}


fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="ach_banesco.txt"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();
readfile($ruta);

?>
