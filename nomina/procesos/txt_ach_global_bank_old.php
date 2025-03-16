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
	private $temp,$retorno,$cad;
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
        
        public function eliminar_acentos($cadena){
		
		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);
 
		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );
 
		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );
 
		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );
 
		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );
 
		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena);
                
                $cadena = str_replace(
                array("\\", "¨", "º", "-", "~",
                    "#", "@", "|", "!", "\"",
                    "·", "$", "%", "&", "/",
                    "(", ")", "?", "'", "¡",
                    "¿", "[", "^", "<code>", "]",
                    "+", "}", "{", "¨", "´",
                    ">", "< ", ";", ",", ":",
                    "."),
                    '',
                    $cadena
                );
                
		$this->cad=$cadena;
		return $this->cad;
	}
        
        public function eliminar_espacios($cadena){
		
		//Reemplazamos la A y a
		$cad=str_replace(" ", "", $cadena);
		$this->cad=$cad;                        
		return $this->cad;
	}
        
        public function eliminar_comas($cadena){
		
		//Reemplazamos la A y a
		$cad=str_replace(",", "", $cadena);
		$this->cad=$cad;
		return $this->cad;
	}
        
        public function mayusculas($cadena){
		
		//Reemplazamos la A y a
		$cad=strtoupper($cadena);
		$this->cad=$cad;
		return $this->cad;
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
$ruta=$directorio."/ach_banco_global_bank.txt";
$archivo= fopen($ruta,"w");
chmod($directorio,0777);
chmod($ruta,0777);
}else{
echo "No se pudo crear el directrorio";

}
$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

$proceso=new txt();

//creamos la cabecera del txt

unset($totales);
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);


$consulta="SELECT nnn.*, np.apenom, CONCAT(np.nombres,' ', np.apellidos) nombre_apellidos, np.telefonos,nnn.forcob  
FROM nom_nomina_netos nnn 
JOIN nompersonal np ON (np.cedula=nnn.cedula  AND np.ficha=nnn.ficha) 
        LEFT JOIN nomnivel1 n1 on (n1.codorg=nnn.codnivel1) 
        WHERE nnn.codnom='".$_GET['codnom']."' AND nnn.tipnom='".$_SESSION['codigo_nomina']."' 
        AND nnn.forcob<>'Efectivo' AND np.codbancob = '1'   
        ORDER BY n1.markar,nnn.cedula";
$resultado=$movimientos->query($consulta);
ob_clean();

while($fila=$resultado->fetch_assoc())
{
	$detalles="";
	$neto_empleado=$fila['neto'];
	$cuenta_bancaria=trim($fila['cta_ban']);
	$cedula=$fila['cedula'];
	// Si es cuenta corriente o cuenta ahorros
	if ($fila['forcob'] == "Cuenta Corriente") {
		$forcob = "D";
	} elseif ($fila['forcob'] == "Cuenta Ahorro"){
		$forcob = "S";
	}
	//$detalles.=str_pad($char,15, " ", STR_PAD_RIGHT);
	$detalles.=substr($cedula,0,15);
	$detalles.=",";
	$nombre=$proceso->eliminar_acentos(utf8_encode($fila['nombre_apellidos']));
    //$nombre=$proceso->eliminar_espacios($nombre);
    $nombre=$proceso->eliminar_comas($nombre);
    $nombre=$proceso->mayusculas($nombre);
    if(strlen($nombre) >= 22){
    	$nombre = substr(trim($nombre), 0,22);
    }
	$detalles.= $nombre;
	$detalles.=",";
	$detalles.=substr($neto_empleado,0,11);
	$detalles.=",";
	$detalles.="000001151";	
	$detalles.=",";
	$detalles.=substr(trim($fila['cta_ban']),0,17);
	$detalles.=","; 
	$detalles.=$forcob;

	$detalles.=",";
	$detalles.="D";
                
	$detalles.=",REF TXT DEPOSITO DIRECTO DE PLANILLA ";

	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	//echo $detalles;
}
fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header('Content-Disposition: attachment; filename="ach_global_bank.txt"');

?>
