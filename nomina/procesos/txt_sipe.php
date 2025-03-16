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


if(mkdir($directorio))
{
	$ruta=$directorio."/NOMINA".$mes.substr($_GET['anio'],2,2).".txt";
	$archivo= fopen($ruta,"w");
	chmod($directorio,0777);
	chmod($ruta,0777);
}
else
{
	echo "No se pudo crear el directrorio";
}

$totales=new bd($_SESSION['bd']);





$consulta="select nnn.*, np.apenom, np.telefonos, np.nombres, np.apellidos, np.sexo from nom_nomina_netos nnn join nompersonal np on (np.cedula=nnn.cedula and np.ficha=nnn.ficha)  where nnn.codnom='".$_GET['codigo_nomina']."' and nnn.tipnom='".$_SESSION['codigo_nomina']."'  and np.cuentacob<>'1' order by nnn.cedula";
$resultado2=$totales->query($consulta);

while($fila=$resultado2->fetch_assoc())
{
	$cod=$cod2="";
	$consulta="select monto  from nom_movimientos_nomina  where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."' and codcon=200 and cedula='$fila[cedula]'";
	$resultado3=$totales->query($consulta);	
	$fila3=$resultado3->fetch_assoc();
	
	$consulta="select valor from  nomcampos_adic_personal  where tiponom='".$_SESSION['codigo_nomina']."' and id=3 and ficha='$fila[ficha]'";
	$resultado4=$totales->query($consulta);	
	$fila4=$resultado4->fetch_assoc();
	
	$detalles="27178200";
	$detalles.="XXX";
	if($fila4[valor]!=0)
		$cod=str_replace("-","",$fila4[valor]);
	else
		$cod="       ";
	$LARGO=7;
	for($i=strlen($cod);$i<$LARGO;$i++)
		$cod="0".$cod;	
	$detalles.=$cod;
	$detalles.="XX";
	$detalles.="  ";
	$detalles.="XXXXX";
	
	$total_emp=$proceso->formatear_monto($fila3['monto']);
	$largo_total_emp=strlen($total_emp);

	$LARGO=6;
	for($i=$largo_total_emp;$i<$LARGO;$i++)
		$total_emp="0".$total_emp;
	
	$detalles.=$total_emp;
	
	$fille4="0000";
	$detalles.=$fille4;

	$nombre=str_replace(",","",$fila['nombres']);
	$nombre=trim($nombre);
	
	if(strlen($nombre)<14)
	{	
		$i=strlen($nombre);
		while($i<14)
		{
			$nombre.=" ";
			$i+=1;
		}
	}
	elseif(strlen($nombre)>14)
	{
		$nombre=substr($nombre,0,14);
	}
	$detalles.=$nombre;
	
	
	$apellido=str_replace(",","",$fila['apellidos']);
	$apellido=str_replace("  "," ",$apellido);
	$apellido=str_replace("Ñ"," ",$apellido);
	
	if(strlen($apellido)<14)
	{	
		$i=strlen($apellido);
		while($i<14)
		{
			$apellido.=" ";
			$i+=1;
		}
	}
	elseif(strlen($apellido)>14)
	{
		$apellido=substr($apellido,0,14);
	}
	$detalles.=$apellido;

	$detalles.=substr($fila[sexo],0,1);
	$detalles.="XX";
	
	$detalles.="  ";
	$detalles.="000";
	$detalles.="XXXXXXXXXXXXXX";
	$detalles.="A0";
	$detalles.="X";
	$detalles.=" ";
	$detalles.="000";
	$detalles.="               ";
	$detalles.="X";	
	
	
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
}

//buscamos todas las personas de ese tipo de nomina
/*
$consulta="SELECT SUM(neto) AS total FROM nom_nomina_netos WHERE tipnom='".$_SESSION['codigo_nomina']."' AND codnom=$nomina";
$resultado=$totales->query($consulta);
$fetch_total=$resultado->fetch_assoc();
$total_nom=$proceso->formatear_monto($fetch_total['total']);
$largo_total_nom=strlen($total_nom);


$LARGO=13;
for($i=$largo_total_nom;$i<$LARGO;$i++)
	$total_nom="0".$total_nom;


$consulta="SELECT COUNT(neto) AS cantidad FROM nom_nomina_netos WHERE tipnom='".$_SESSION['codigo_nomina']."' AND codnom=$nomina";
$resultadocant=$totales->query($consulta);
$fetch_total_cant=$resultadocant->fetch_assoc();
$cantidad=$fetch_total_cant['cantidad'];
$largo_cantidad_nom=strlen($cantidad);

$LARGO=7;
for($i=$largo_cantidad_nom;$i<$LARGO;$i++)
	$cantidad="0".$cantidad;


// buscamos el rif de la institucion
// NOMBRE DE LA COMPANIA

$consulta="SELECT nom_emp, rif FROM nomempresa";
$result_nomemp=$totales->query($consulta);
$fetch_nomemp=$result_nomemp->fetch_assoc();
$rif=str_replace("-",'',$fetch_nomemp['rif']);

$cad=$fetch_nomemp['nom_emp'];
$largo_cad=strlen($cad);
$LARGO=40;
for($i=$largo_cad;$i<$LARGO;$i++)
	$cad.=' ';
*/
//FECHA DE PAGO DE LA NOMINA
$consulta="SELECT * FROM nom_nominas_pago WHERE tipnom='".$_SESSION['codigo_nomina']."' AND codnom=$nomina";
$result_fec=$totales->query($consulta);
$fetch_fec=$result_fec->fetch_assoc();
//$fechapago=substr($fetch_fec['fechapago'],8,2)."/".substr($fetch_fec['fechapago'],5,2)."/".substr($fetch_fec['fechapago'],2,2);



/*$fecha=$_GET['anio']."-".$mes."-01";
$num_dias_mes=date("t",strtotime($fecha));
$fecha=$num_dias_mes.$mes.substr($_GET['anio'],2,2);
*/
$anio=substr($fetch_fec['fechapago'],0,4);
$mes=substr($fetch_fec['fechapago'],5,2);



$cabecera="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"."       "."XXXXXXXXXXXXX".strtoupper(mesaletras($mes))."     ".$anio;
$cabecera.="\r\n";
fwrite($archivo,$cabecera);


$nom_arch="SIPE.txt";
fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header("Content-Disposition: attachment; filename=$nom_arch");

?>