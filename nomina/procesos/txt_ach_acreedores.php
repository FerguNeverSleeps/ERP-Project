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
$ruta=$directorio."/ach_banco_general.txt";
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
//sql="
//        SELECT
//                nnn.*,
//                np.apenom,
//                np.telefonos,
//                nnn.forcob,
//                n1.markar,
//                n1.descripcion_completa,
//                n1.descripcion_corta
//  FROM nom_nomina_netos nnn
//        JOIN nompersonal np ON (np.cedula=nnn.cedula  AND np.ficha=nnn.ficha)
//        LEFT JOIN nomnivel1 n1 on n1.codorg=nnn.codnivel1
//  WHERE
//        nnn.codnom='$codnom' AND
//        nnn.tipnom='$codtip'
//  ORDER BY
//        n1.markar,
//        nnn.cedula";

//$consulta="SELECT nnn.*, np.apenom, np.telefonos,nnn.forcob  "
//        . "FROM nom_nomina_netos nnn "
//        . "JOIN nompersonal np ON (np.cedula=nnn.cedula  AND np.ficha=nnn.ficha) "
//        . "LEFT JOIN nomnivel1 n1 on (n1.codorg=nnn.codnivel1) "
//        . "WHERE nnn.codnom='".$_GET['codigo_nomina']."' AND nnn.tipnom='".$_SESSION['codigo_nomina']."' "
//        . "AND nnn.forcob<>'Efectivo' AND np.codbancob = '1'   "
//        . "ORDER BY n1.markar,nnn.cedula";

$consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, "
                            . "pc.codigopr,p.descrip,p.formula,pc.ruta_destino,pc.cuenta_destino,pc.producto_destino   "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN nomprestamos AS p ON (c.codcon=p.formula) "
                            . "LEFT JOIN nomprestamos_cabecera AS pc ON (nm.numpre=pc.numpre) "
                            . "WHERE nm.codnom='".$_GET['codnom']."' AND nm.tipnom='".$_SESSION['codigo_nomina']."' "
                            . "AND (nm.codcon>500 AND nm.codcon<523) OR (nm.codcon>523 AND nm.codcon<590) "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";

$resultado=$movimientos->query($consulta);


while($fila=$resultado->fetch_assoc())
{
	$detalles="";
	$neto=$fila['suma'];
	$cuenta_bancaria=trim($fila['cuenta_destino']);
	$cedula=$fila['codigopr'];
	
	
	
	//$identificacion="770";
	//$detalles.=$identificacion;
	//$cuenta_bancaria="010800".$cuenta_bancaria;
	//$numero_cuenta=$proceso->completar($cuenta_bancaria,20);
	$detalles.=$cedula;
	$detalles.="\t";
	$nombre=$proceso->eliminar_acentos(utf8_encode($fila['descrip']));
        $nombre=$proceso->eliminar_espacios($nombre);
        $nombre=$proceso->eliminar_comas($nombre);
        $nombre=$proceso->mayusculas($nombre);
	$detalles.=$nombre;
	$detalles.="\t";
        $detalles.=$fila[ruta_destino];
	$detalles.="\t";
	$detalles.=trim($fila[cuenta_destino]);
	$detalles.="\t".$fila[producto_destino]; 

	$detalles.="\t";
	//$campo_libre=$proceso->completar("",4);
	//$detalles.=$campo_libre;

	//$detalles.=$mes_pago;
	if(strlen($neto)==6)
		$detalles.=$proceso->completar($neto,5);
	elseif(strlen($neto)==5)
		$detalles.=$proceso->completar($neto,6);
	elseif(strlen($neto)==7)
		$detalles.=$proceso->completar($neto,4);
	elseif(strlen($neto)==4)
		$detalles.=$proceso->completar($neto,7);
	elseif(strlen($neto)==3)
		$detalles.=$proceso->completar($neto,8);
	
	$detalles.="\tC\tREF*TXT**DEPOSITO DIRECTO DE PLANILLA\ ";

	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	//echo $detalles;
}
fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header('Content-Disposition: attachment; filename="ach_acreedores_banco_general.txt"');

?>
