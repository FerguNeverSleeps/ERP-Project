<?php
include('../generalp.config.inc.php');
session_start();

error_reporting(E_ALL);

$db_user   = DB_USUARIO;
$db_pass   = DB_CLAVE;
$db_name   = $_SESSION['bd'];
$db_host   = DB_HOST;
$usuario = $_SESSION['usuario'];

$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');

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

$ruta="marcaciones_18-09_03-10_2020.txt";
$archivo= fopen($ruta,"w");
chmod($ruta,0777);
        
$consulta_marcaciones="SELECT * FROM `data_marcaciones`";

$resultado_marcaciones=mysqli_query($conexion,$consulta_marcaciones);

$cont=1;


while($fila=mysqli_fetch_array($resultado_marcaciones))
{          
   
    
    $Ficha=trim($fila['Ficha']);
    $Fecha=trim($fila['Fecha']);
    $Estado=trim($fila['Estado']);
    
    $fec=explode(" ", $Fecha);
    
    $fecha=$fec[0];
    $fecha=explode("/", $fecha);
    $dia=$fecha[0];
    $mes=$fecha[1];
    $anio=$fecha[2];
    $fecha=$mes."/".$dia."/".$anio;
    
    $hora=str_pad($fec[1], 5, "0", STR_PAD_LEFT).":00";
    
    $tipo=$fec[2];
    
    
    if($tipo=="AM")
        $tipo="a.m.";
    else
        $tipo="p.m.";
    
    $fila.="";
    $fila.=str_pad($Ficha, 6, " ", STR_PAD_RIGHT);
    $fila.="\t";
    
    $fila.="Amaxonia";
    $fila.="\t";
    
    $fila.=$fecha." ".$hora." ".$tipo;
    $fila.="\t";
    
    $fila.="Entrada";
    $fila.="\t";
    
    $fila.=str_pad("RioAbajo", 12, " ", STR_PAD_RIGHT);
    $fila.="\t";
    
    $fila.="0";
    $fila.="               ";
    
    $fila.="\n";
    
    fwrite($archivo,$fila);
    $cont++;
}
$fila.="\r\n";
fwrite($archivo,$fila);
fclose($archivo);
?>

