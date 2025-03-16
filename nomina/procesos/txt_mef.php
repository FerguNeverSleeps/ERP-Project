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

if($_SESSION['codigo_nomina']=='3')
{
	$codcon=2065;
	//$codcon_pat=3542;
}


$directorio="txt/nomina".date("Y_m_d_H_i_s");
if(mkdir($directorio)){
$ruta=$directorio."/nomina_mef.txt";
$archivo= fopen($ruta,"w");
chmod($directorio,0777);
chmod($ruta,0777);
}else{
echo "No se pudo crear el directrorio";

}
$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

//buscamos los datos del tipo de nomina
/*
$consulta="select * from nomtipos_nomina where codtip='".$_SESSION['codigo_nomina']."'";
$resultado=$totales->query($consulta);
$fila=$resultado->fetch_assoc();
$codigo_empresa=$fila['codigo_banco'];
//buscamos los datos de la nomina
$consulta="select * from nom_nominas_pago where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado=$totales->query($consulta);
$fila=$resultado->fetch_assoc();


$consulta_temp="select count(*) as num,sum(neto) as total from nom_nomina_netos where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";

$resultado_temp=$totales->query($consulta_temp);
$fila_temp=$resultado_temp->fetch_assoc();
$num_registros=$fila_temp['num'];//$resultado_temp->num_rows;
$num_registros+=1;
*/
//llamamos a la clase de proceso

$proceso=new txt();



//creamos la cabecera del txt



unset($totales);
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

$consulta="select np.ficha,np.cedula,np.apenom,nnp.codnom,nnp.fechapago,np.cuentacob, np.codbancob,nnp.descrip from nompersonal np inner join nom_movimientos_nomina nmn on (nmn.ficha=np.ficha) inner join nom_nominas_pago nnp on (nnp.codnom=nmn.codnom) where  nnp.codnom='".$_GET['codigo_nomina']."' and nnp.tipnom='".$_SESSION['codigo_nomina']."' group by np.ficha";
$resultado=$movimientos->query($consulta);
//$fetchh=$resultado->fetch_assoc();

//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO ASIGNACION QUE EXISTEN EN ESTA NOMINA
//$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."'  AND tipnom='".$_SESSION['codigo_nomina']."' AND tipcon='A' ORDER BY codcon";	
//$result10=$movimientos->query($consulta,$conexion);

while($fetch_con=$resultado->fetch_assoc())
{
	$conexion=conexion();
	$consulta="SELECT neto FROM nom_nomina_netos WHERE ficha='".$fetch_con['ficha']."'  AND tipnom='".$_SESSION['codigo_nomina']."' AND cedula='".$fetch_con['cedula']."' AND codnom=".$fetch_con['codnom']."";
	$result_suma=$movimientos->query($consulta,$conexion);
	$fetch_suma=$result_suma->fetch_assoc();

		
	$detalles="L0000";	
	$cedula=str_replace("-","00",$fetch_con['cedula']);
	$detalles.=$cedula;
	$nombre=str_replace(",","",$fetch_con['apenom']);
	if(strlen($nombre)>22)
		$nombre=substr(utf8_decode($nombre),0,22);
	else
		$nombre=str_pad($nombre,22," ",STR_PAD_RIGHT);
	
	$detalles.=$nombre;
	
	$detalles.=str_pad($fetch_suma[neto],11,"0",STR_PAD_LEFT);
	
	$fecha=str_replace("-","",$fetch_con['fechapago']);
	$detalles.=$fecha;
	
	if ($fetch_con[codbancob]==1)
		$detalles.='0000000013';
	elseif ($fetch_con[codbancob]==4)
		$detalles.='0000000071';
	else
		$detalles.='0000000770';
	
	
	$detalles.=str_pad($fetch_con[cuentacob],17,' ',STR_PAD_RIGHT);
	$detalles.="SC          ";
	$detalles.="\r\n";
	$detalles.="A".str_pad($fetch_con[descrip],99,' ',STR_PAD_RIGHT);
	
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	$i++;

}


fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header('Content-Disposition: attachment; filename="mef.txt"');

?>
