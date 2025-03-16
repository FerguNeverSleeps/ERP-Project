<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';

include ("../paginas/funciones_nomina.php");
$conexion=conexion();

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


$id = $_REQUEST['id'];

$consulta_id= "SELECT * "
          . " FROM ach_sipe_sysmeca "
          . " WHERE id='$id' ";
$resultado_id=query($consulta_id,$conexion);
$fila_id=fetch_array($resultado_id,$conexion);
$correlativo=$fila_id['correlativo'];
$total_salario_bruto=$fila_id['total_salario'];
$total_isr=$fila_id['total_isr'];
$total_xiii=$fila_id['total_xiii'];
$total_x=$fila_id['total_x'];
$numero_patronal=$fila_id['numero_patronal'];
$total_excep=$fila_id['total_excep'];
$mes=$fila_id['mes'];
$anio=$fila_id['anio'];

$consulta= "SELECT a.*, b.* "
          . " FROM ach_sipe_sysmeca as a"
          . " LEFT JOIN ach_sipe_detalle_sysmeca as b ON (a.id=b.id)"
          . " WHERE a.id='$id' "
          . " ORDER BY b.cedula ASC ";
$resultado=query($consulta,$conexion);


while ($fila=fetch_array($resultado,$conexion))
{ 
	$cod=$cod2="";
        
        
        $cod_adicion = $fila[cod_adicion];
        $detalles = $cod_adicion;
        
        $correlativo = $fila[correlativo];     
        $detalles .= $correlativo; 
        
        $ficha = $fila[sec_empleado];
        $detalles .= $ficha;
        
        $seguro_social = $fila[seguro_social];        
        $detalles .= $seguro_social;
        
        $cedula = $fila[cedula];  
        $cedula_transformada = explode('-', $fila[cedula_transformada]);
        
        
        
        if($cedula_transformada[0]=="E" || $cedula_transformada[0]=="PE")
        {    
            $detalles .="  ";
            $detalles .=$cedula;
            
        }
        else
        {
            $detalles .=$cedula;
        }
        
        $apellido=str_replace(",","",$fila['apellido']);
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
        $detalles.=str_pad($apellido, 14, " ", STR_PAD_RIGHT);
        
        $nombre=str_replace(",","",$fila['nombre']);
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
        $detalles.=str_pad($nombre, 14, " ", STR_PAD_RIGHT);
        
        $sexo=substr($fila[sexo],0,1);
        $excep=$fila[cod_excepcion];
        $detalles .= $sexo;        
        $detalles .= str_pad($excep, 2, "0", STR_PAD_LEFT);
	$detalles .= "  ";
       
		
	$salario_bruto=$proceso->formatear_monto($fila['salario']);
	$detalles.=str_pad($salario_bruto, 9, "0", STR_PAD_LEFT);
        
        $isr=$proceso->formatear_monto($fila['isr']);
	$detalles.=str_pad($isr, 7, "0", STR_PAD_LEFT);
        
        $clave_ir = $fila[clave_ir];
        if($clave_ir==NULL || $clave_ir==0 || $clave_ir=='' || strlen($clave_ir)>2)
            $clave_ir='A0';
	$detalles .= $clave_ir;
        
        
        if($fila['x']==1)
        {
            $detalles.="X ";
        }
        else
        {
            $detalles.="  ";
        }
	
	$detalles.="000";
	$detalles.="                ";
	
		
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
}



$cabecera="";
$cabecera.=0;
$cabecera.=$correlativo;
$cabecera.=0;

$total_salario_bruto=$proceso->formatear_monto($total_salario_bruto);
$cabecera.=str_pad($total_salario_bruto, 20, "0", STR_PAD_LEFT);

$total_isr=$proceso->formatear_monto($total_isr);
$cabecera.=str_pad($total_isr, 16, "0", STR_PAD_LEFT);

$cabecera.=str_pad($total_excep, 6, "0", STR_PAD_LEFT);

$total_xiii=$proceso->formatear_monto($total_xiii);
$cabecera.=str_pad($total_xiii, 10, "0", STR_PAD_LEFT);

$cabecera.="       ";
$cabecera.=str_pad($total_x, 4, "0", STR_PAD_LEFT);


$cabecera.=$numero_patronal;
$cabecera.=str_pad(strtoupper(mesaletras($mes)), 10, " ", STR_PAD_RIGHT);
//$cabecera.="   ";
$cabecera.=$anio;
//$cabecera="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"."       "."XXXXXXXXXXXXX".strtoupper(mesaletras($mes))."     ".$anio;
$cabecera.="\r\n";
fwrite($archivo,$cabecera);


$nom_arch="sipe_itesa_".mesaletras($mes)."-".$anio.".txt";
fclose($archivo);
//echo $ruta;
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header("Content-Disposition: attachment; filename=$nom_arch");
//exit;
//?>