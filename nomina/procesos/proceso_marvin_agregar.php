<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<link rel="stylesheet" type="text/css" href="dialog_box.css" />

<?php
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");
$conexion=conexion();

function formatearHora($hora)
{
	$horas = explode(":",$hora);
	if(trim($horas[0])=="00")
		$hora = "24:".$horas[1];
	else
		$hora = $horas[0].":".$horas[1];
	return $hora;
}

class tiempo{
	private $temp,$min,$horas;
	public function aminutos($cad)
	{
		$this->temp = explode(":",$cad);
		$this->min = ($this->temp[0]*60)+($this->temp[1]);
		return $this->min;
	}
	public function ahoras($cad)
	{
		$this->temp = $cad;
		if($this->temp>59)
		{
			$this->temp = $this->temp/60;
			$this->temp = explode(".",number_format($this->temp,2,".",""));
			$this->temp[0] = strlen($this->temp[0])==1 ? "0".$this->temp[0] : $this->temp[0];
			$this->temp[1] = (((substr($this->temp[1],0,2))*60)/100);
			$this->temp[1] = round($this->temp[1]);
			//$this->horas = $this->temp[0].":".(strlen($this->temp[1][0])==1 ? "0".$this->temp[1][0] : round(substr($this->temp[1][0],0,2).'.'.substr($this->temp[1][0],2,1)));
			$this->horas = $this->temp[0].":".(strlen($this->temp[1])==1 ? "0".$this->temp[1] : $this->temp[1]);
		}
		elseif(($this->temp=="")||($this->temp==0))
		{
			$this->horas = "00:00";
		}
		else
		{
			$this->horas = "00:".(strlen($this->temp)==1 ? "0".$this->temp : $this->temp);//$this->temp;
		}
		return $this->horas;
	}
}

$select = "select capital from nomempresa";
$result = query($select,$conexion);
$filaemp = fetch_array($result);
$empresa = $filaemp['capital'];
$fechaini = $_POST['txtFechaInicio'];
$fechafin = $_POST['txtFechaFin'];
 

if ($_POST["procesar"]) 
{	
        $fechaInicio = new DateTime($_POST['txtFechaInicio']);
        $fechaFin = new DateTime($_POST['txtFechaFin']);

        $fechaI = $fechaInicio->format('Y-m-d');
        $fechaF = $fechaFin->format('Y-m-d');

        $insert = "INSERT INTO marvin_encabezado 
                   (id, 
                   fecha, 
                   fecha_inicio, 
                   fecha_fin, 
                   estatus, 
                   usuario_creacion, 
                   fecha_creacion)
                   VALUES('',
                   '" . date("Y-m-d") . "' ,"
                . "'{$fechaInicio->format('Y-m-d')}',"
                . "'{$fechaFin->format('Y-m-d')}',"
                . "0,"
                . "'".$_SESSION['usuario']."',"
                . "'".date("Y-m-d")."')";
    $result = query($insert,$conexion);

    $select = "SELECT MAX(id) as id FROM marvin_encabezado";
    $result = query($select,$conexion);
    $row = fetch_array($result);
    $idenc=$row[id];
	

    if($_FILES["archivo"]["name"]!="")
    {
	if(((isset($_POST['txtFechaFin']) && isset($_POST['txtFechaInicio'])) or isset($_POST["cod_enca"])) && $_FILES["archivo"]["name"]!="") 
	{
            //echo "ENTRO 1<br>";
            $allowedExts = array("txt","log","dat","csv");
		$temp = explode(".", $_FILES["archivo"]["name"]);
		$extension = end($temp);
		//(($_FILES["archivo"]["type"] == "text/plain"))&& 
                //if (($_FILES["archivo"]["size"] < 2000000) && in_array($extension, $allowedExts))
		if (($_FILES["archivo"]["size"] < 2000000) )
		{
                    //echo "ENTRO 2<br>";
                    if ($_FILES["archivo"]["error"] > 0)
                    {
                         $mensajefoto="Error Numero: " . $_FILES["archivo"]["error"] . "<br>";
                    }
                    else
                    {
                        move_uploaded_file($_FILES["archivo"]["tmp_name"],"txt/" . $_FILES["archivo"]["name"]);
                        $archivo="txt/" . $_FILES["archivo"]["name"];
                        $archivo_nombre=$_FILES["archivo"]["name"];
                        //echo "ENTRO 3<br>";
                    }
		}
		else
		{
			//echo "ENTRO 4<br>";
                        $mensajefoto="Archivo invalido";
			exit;
		}
//		echo $mensajefoto;
//		exit;
		$linecount = 0;
		$handle = fopen($archivo, "r");
		while(!feof($handle)){
		  $line = fgets($handle);
		  $linecount++;
		}
		fclose($handle);
		//echo $linecount;
		//exit;
		$f = fopen($archivo, "r");
		$i=0;
                
                $encabezado_id=$idenc;
//                echo "ENTRO";
                
                $insert_detalle = "INSERT INTO marvin_detalle
                                    (id_encabezado, 
                                    id_detalle, 
                                    ficha, 
                                    regular, 
                                    dia_libre_nacional_150, 
                                    dia_feriado_250, 
                                    compensatorio_050,
                                    extra_125,
                                    extra_150,
                                    extra_175,
                                    extra_188,
                                    extra_219,
                                    extra_225,
                                    extra_263_regular,
                                    extra_263_libre,
                                    extra_306,
                                    extra_313,
                                    extra_329,
                                    extra_375,
                                    extra_394,
                                    extra_438,
                                    extra_459,
                                    extra_547,
                                    extra_656,
                                    extra_766,
                                    certificado_medico,
                                    tardanza,
                                    ausencia,
                                    permiso
                                    )
                                    VALUES ";
                
                            
                while(($linea = fgets($f)) !== false):
                    //Dividir la linea por tabuladores
                    $linea=explode(",",$linea);
                    $ficha=ltrim(trim($linea[0]), "0");
                    $regular=trim($linea[1]);
                    $dia_libre_nacional_150=trim($linea[2]);
                    $dia_feriado_250=trim($linea[3]);
                    $compensatorio_050=trim($linea[4]);
                    $extra_125=trim($linea[5]);
                    $extra_150=trim($linea[6]);
                    $extra_175=trim($linea[7]);
                    $extra_188=trim($linea[8]);
                    $extra_219=trim($linea[9]);
                    $extra_225=trim($linea[10]);
                    $extra_263_regular=trim($linea[11]);
                    $extra_263_libre=trim($linea[12]);
                    $extra_306=trim($linea[13]);
                    $extra_313=trim($linea[14]);
                    $extra_329=trim($linea[15]);
                    $extra_375=trim($linea[16]);
                    $extra_394=trim($linea[17]);
                    $extra_438=trim($linea[18]);
                    $extra_459=trim($linea[19]);
                    $extra_547=trim($linea[20]);
                    $extra_656=trim($linea[21]);
                    $extra_766=trim($linea[22]);
                    $certificado_medico=trim($linea[23]);
                    $tardanza=trim($linea[24]);
                    $ausencia=trim($linea[25]);
                    $permiso=trim($linea[26]);
                    
                    $insert_detalle .= "('". $encabezado_id."' ,
                                        '',
                                        '" .$ficha . "' ,"
                                        . "'{$regular}',"
                                        . "'{$dia_libre_nacional_150}',"
                                        . "'{$dia_feriado_250}',"
                                        . "'{$compensatorio_050}',"
                                        . "'{$extra_125}',"
                                        . "'{$extra_150}',"
                                        . "'{$extra_175}'," 
                                        . "'{$extra_188}',"        
                                        . "'{$extra_219}',"        
                                        . "'{$extra_225}',"        
                                        . "'{$extra_263_regular}',"        
                                        . "'{$extra_263_libre}',"        
                                        . "'{$extra_306}',"        
                                        . "'{$extra_313}',"        
                                        . "'{$extra_329}',"        
                                        . "'{$extra_375}',"        
                                        . "'{$extra_394}',"        
                                        . "'{$extra_438}',"        
                                        . "'{$extra_459}',"        
                                        . "'{$extra_547}',"
                                        . "'{$extra_656}',"        
                                        . "'{$extra_766}',"
                                        . "'{$certificado_medico}',"        
                                        . "'{$tardanza}',"
                                        . "'{$ausencia}',"
                                        . "'{$permiso}'),";
                endwhile;
		$insert_detalle.= ";";
                $insert_detalle=str_replace(",;",";",$insert_detalle);	
//                echo $insert_detalle;
//                exit;
                $result = query($insert_detalle,$conexion);
			
		fclose($f);
		

		if(isset($_POST["cod_enca"])){
			print '<script type="text/javascript">
				window.onload=function(){
					window.opener.document.control_acceso_detalle2.submit();
					window.close();
				}
			</script>';
			exit;
		}
		else
			header("Location:proceso_marvin.php");



        } 
        else 
        {
            ?>
            <script type="text/javascript">
                alert("ALERTA:\nDebe introducir todos los datos.");
            </script>
            <?php
        }
    }
    else 
    {
        header("Location:proceso_marvin.php");
    }
}

?>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
	        <div class="row">
	            <div class="col-md-12">
	                <div class="portlet box blue">
	                    <div class="portlet-title">
	                        <div class="caption">
	                            Marvin - Importar
	                        </div>
	                    </div>
	                    <div class="portlet-body">
	                        


	                        <div class="row">
	                        <div class="col-md-2"></div> 
	                        <div class="col-md-8">
	                        <div class="panel panel-info"> 
	                            <div class="panel-body">
	                                <div class="row">
	                                    <div class="col-md-4">
	                                            Fecha Inicio:  
	                                    </div>
	                                    <div class="col-md-5">
	                                        <div class="input-group date date-picker" data-provide="datepicker">  
	                                             <input name="txtFechaInicio" type="text"  class="form-control" placeholder="Inserte fecha" id="txtFechaInicio" value="" maxlength="10">
	                                             <span class="input-group-btn">
	                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
	                                             </span>
	                                        </div>
	                                    </div> 
	                                </div>
	                                <br>
	                                <div class="row">  
	                                    <div class="col-md-4">
	                                            Fecha Fin:   
	                                    </div>
	                                    <div class="col-md-5">
	                                        <div class="input-group date date-picker" data-provide="datepicker">  
	                                             <input name="txtFechaFin" type="text"  class="form-control" placeholder="Inserte fecha" id="txtFechaFin" value="" maxlength="10">
	                                             <span class="input-group-btn">
	                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
	                                             </span>
	                                        </div>
	                                    </div> 
	                                </div>
	                                
	                                <br>
	                                <div class="row">
	                                    <div class="col-md-4">
	                                            Archivo:   
	                                    </div>
	                                    <div class="col-md-5">
	                                        <input type="file" name="archivo" id="archivo">  
	                                    </div> 
	                                </div>
	                                <br>

	                                <div class="row">
	                                    <div class="col-md-5"></div>
	                                    <div class="col-md-5">
	                                        <input type="submit" name="procesar" id="procesar" value="Procesar">  
	                                    </div> 
	                                </div>
	                            </div>
	                        </div>
	                        </div>
	                        </div>



	                    </div>
	                </div>
	            </div>
	        </div>
        </div>
    </div>
</div>
</form>

</body>
</html>
