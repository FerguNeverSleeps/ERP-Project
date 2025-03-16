<?php
session_start();
ob_start();
$termino= $_SESSION['termino'];
include "../lib/common.php";
include "../paginas/funciones_nomina.php";

function limpiar_cadena($cadena)
{ 
    $cadena = trim($cadena); 
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    ); 
    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena
    ); 
    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena
    ); 
    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena
    ); 
    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena
    ); 
    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $cadena
    ); 
    //Esta parte se encarga de eliminar cualquier caracter extraño
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
    return $cadena;
}
$planilla = $_GET[tipos];
$planilla =explode("_",$planilla);
$codnom = $planilla[0];
$codtip = $planilla[1];
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

$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

$proceso=new txt();
//creamos la cabecera del txt
unset($totales);
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

/** LISTADO DE EMPLEADOS **/
$consulta="SELECT nmn.*, np.apenom, np.telefonos,np.cuentacob,
SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(UPPER(LEFT(nombres, 1)), LOWER(SUBSTRING(nombres, 2))), ' ', 1), ' ', -1) AS primer_nombre, 
SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(UPPER(LEFT(nombres, 1)), LOWER(SUBSTRING(nombres, 2))), ' ', 3), ' ', -1) AS segundo_nombre, 
SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(UPPER(LEFT(apellidos, 1)), LOWER(SUBSTRING(apellidos, 2))), ' ', 1), ' ', -1) AS primer_apellido, 
SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(UPPER(LEFT(apellidos, 1)), LOWER(SUBSTRING(apellidos, 2))), ' ', 3), ' ', -1) AS segundo_apellido
FROM nom_movimientos_nomina nmn 
INNER JOIN nompersonal np ON (np.ficha=nmn.ficha) 
WHERE nmn.codnom='".$codnom."' AND nmn.tipnom='".$codtip."' AND np.codbancob='2' 
AND np.codbancob='2' AND (np.forcob LIKE 'Deposito' OR np.forcob LIKE 'Tarjeta' OR np.forcob LIKE 'Cuenta Ahorro' OR np.forcob LIKE 'Cuenta Corriente')
AND ((nmn.codcon >=100 AND nmn.codcon <=209) OR (nmn.codcon>=500 AND nmn.codcon<=599) OR (nmn.codcon>=3000 AND nmn.codcon<=3002))
GROUP BY nmn.ficha
ORDER BY nmn.ficha,nmn.codcon";
$resultado=$movimientos->query($consulta);

//echo "<table>";

header('Content-type: text/csv');
header("Content-Disposition: attachment; filename=BANITSMO1.csv");
$output = fopen('php://output', 'w');

while($fila=$resultado->fetch_assoc())
{

    /** ASIGNACIONES **/
    $bd_asig=new bd($_SESSION['bd']);
    $sql_asig = "SELECT SUM(nmn.monto) as monto
    FROM nom_movimientos_nomina nmn 
    INNER JOIN nompersonal np ON (np.ficha=nmn.ficha) 
    WHERE nmn.codnom='".$codnom."' AND nmn.tipnom='".$codtip."' AND nmn.ficha='{$fila[ficha]}' AND nmn.tipcon='A'
    AND ((nmn.codcon >=100 AND nmn.codcon <=209) OR (nmn.codcon>=500 AND nmn.codcon<=599) OR (nmn.codcon>=3000 AND nmn.codcon<=3002))
    ORDER BY nmn.ficha,nmn.codcon";

    $res_asig=$bd_asig->query($sql_asig);
    $monto_asig = $res_asig->fetch_assoc();

    /** DEDUCCIONES **/
    $bd_deduc=new bd($_SESSION['bd']);
    $sql_deduc = "SELECT SUM(nmn.monto) as monto
    FROM nom_movimientos_nomina nmn 
    INNER JOIN nompersonal np ON (np.ficha=nmn.ficha) 
    WHERE nmn.codnom='".$codnom."' AND nmn.tipnom='".$codtip."' AND nmn.ficha='{$fila[ficha]}' AND nmn.tipcon='D'
    AND ((nmn.codcon >=100 AND nmn.codcon <=209) OR (nmn.codcon>=500 AND nmn.codcon<=599) OR (nmn.codcon>=3000 AND nmn.codcon<=3002))
    ORDER BY nmn.ficha,nmn.codcon";
    
    /** Cálculo del neto**/
    $res_deduc=$bd_deduc->query($sql_deduc);
    $monto_deduc = $res_deduc->fetch_assoc();
    $neto = $monto_asig[monto] - $monto_deduc[monto];
    $neto_empleado=number_format($neto,2,'.','');
    $apellido = $fila['primer_nombre'];
    $name = $fila['primer_apellido'];
    $cuenta_bancaria=$fila['cuentacob'];
    $cedula=$fila['cedula'];
    settype($cuenta_bancaria, "string");        
    $nombre=limpiar_cadena(utf8_encode($apellido." ".$name));
    $texto = 'deposito de planilla';
    $texto = str_replace('"', '', $texto);
    $nombre = str_replace('"', '', $nombre);


    /** Línea de ACH **/
    //echo "<tr>";
    
    //echo "<td class='text'>".$cuenta_bancaria."</td><td class='num'>".$neto_empleado."</td><td></td><td>".$nombre."</td>";

    fputcsv($output, array($cuenta_bancaria, $neto_empleado, $texto,$nombre),";",chr(0));
    //echo "</tr>";
}
//echo "</table>";



?>