<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../paginas/funciones_nomina.php";

function meses( $mes ) 
{
    $meses  = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $mes_letras=$meses[$mes-1];
    return $mes_letras; 
} 

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

$mes=$_GET['mes'];
$anio=$_GET['anio'];

$directorio="txt/nomina".date("Y_m_d_H_i_s");
if(mkdir($directorio)){
$ruta=$directorio."/ach_acreedores_banco_general_mensual_".meses($mes)."_".$anio.".txt";
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

 $consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, "
                . "p.codigopr,p.descrip,p.formula,pc.ruta_destino,pc.cuenta_destino,pc.producto_destino   "
                . "FROM nom_movimientos_nomina AS nm "
                . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                . "LEFT JOIN nomprestamos AS p ON (c.codcon=p.formula) "
                . "LEFT JOIN nomprestamos_cabecera AS pc ON (nm.numpre=pc.numpre) "
                . "LEFT JOIN nompersonal AS np ON (nm.ficha=np.ficha) "
                . "WHERE nm.anio='".$anio."' AND nm.mes='".$mes."' AND nm.tipnom='".$_SESSION['codigo_nomina']."' "
                . "AND (nm.codcon>500 AND nm.codcon<523) OR (nm.codcon>524 AND nm.codcon<527) "
                . "GROUP BY nm.codcon, nm.ficha "  
                . "ORDER BY c.descrip ASC, nm.ficha ASC";
//exit;
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
	$detalles.=",";
	$nombre=$proceso->eliminar_acentos(utf8_encode($fila['descrip']));
        $nombre=$proceso->eliminar_espacios($nombre);
        $nombre=$proceso->eliminar_comas($nombre);
        $nombre=$proceso->mayusculas($nombre);
	$detalles.=$nombre;
	$detalles.=",";
        $detalles.=$fila[ruta_destino];
	$detalles.=",";
	$detalles.=trim($fila[cuenta_destino]);
        $producto_destino=$fila[producto_destino];
        if($fila[producto_destino]=="03-1" || $fila[producto_destino]=="03-2")
            $producto_destino=3;
	$detalles.=",".$producto_destino; 

	$detalles.=",";
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
	
	$detalles.=",C,REF*TXT**DEPOSITO DIRECTO DE PLANILLA\ ";

	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	//echo $detalles;
}
fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
$filename="ach_acreedores_banco_general_mensual_".meses($mes)."_".$anio.".txt";
header('Content-Disposition: attachment; filename="'.$filename.'"');
?>
