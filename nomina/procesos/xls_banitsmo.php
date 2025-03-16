<?php
session_start();
ob_start();
$termino= $_SESSION['termino'];

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=BANITSMO.xls");

?>

<style type="text/css">
<!--
.num
 {mso-style-parent:style0;
 mso-number-format:"\#\,\#\#0\.00_ \;\[Red\]\-\#\,\#\#0\.00\ ";}

 .text{
  mso-number-format:"\@";/*force text*/
}

-->
</style>
 

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
        
        public function limpiar_cadena($cadena){
		
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

$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

unset($totales);
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

$consulta="select nnn.*, np.apenom, np.telefonos  from nom_nomina_netos nnn join nompersonal np on (np.cedula=nnn.cedula and np.ficha=nnn.ficha) where nnn.codnom='".$_GET['codigo_nomina']."' and nnn.tipnom='".$_SESSION['codigo_nomina']."' and np.codbancob=2 order by nnn.cedula";
$resultado=$movimientos->query($consulta);


?>
<table>

<?php

while($fila=$resultado->fetch_assoc())
{
	if($fila['cta_ban']<>"")
	{	
?>
<tr>
<?php
	
	
	$neto_empleado=number_format($fila['neto'],2,'.','');
	$cuenta_bancaria=$fila['cta_ban'];
	$cedula=$fila['cedula'];
	settype($cuenta_bancaria, "string");
	
//	$nombre=str_replace(",","",$fila['apenom']);
//        $nombre=limpiar_cadena(utf8_encode($fila['apenom']));
	$nombre=$proceso->limpiar_cadena($fila['apenom']);
//        $nombre=$proceso->eliminar_espacios($nombre);
        $nombre=$proceso->eliminar_comas($nombre);
        $nombre=$proceso->mayusculas($nombre);
        if(strlen($nombre)>=25)
            $nombre=substr($nombre,0,25);
        
        
	echo "<td class='text'>".$cuenta_bancaria."</td><td class='num'>".$neto_empleado."</td><td>DEPOSITO DIRECTO DE PLANILLA</td><td>".$nombre."</td>";

?>
</tr>

<?php
}
}
?>
</table>