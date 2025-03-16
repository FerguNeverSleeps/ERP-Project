<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../header.php";
$conexion=conexion();

$opcion=$_GET['opcion'];


if($_FILES["archivo"]["name"]!="")
{
	$allowedExts = array("txt");
	$temp = explode(".", $_FILES["archivo"]["name"]);
	$extension = end($temp);
	if ((($_FILES["archivo"]["type"] == "text/plain"))
	&& ($_FILES["archivo"]["size"] < 2000000)
	&& in_array($extension, $allowedExts))
	{
		if ($_FILES["archivo"]["error"] > 0)
	   {
	   	$mensajefoto="Error Numero: " . $_FILES["archivo"]["error"] . "<br>";
	   }
	  	else
	   {
		   move_uploaded_file($_FILES["archivo"]["tmp_name"],"txt/" . $_FILES["archivo"]["name"]);
	      $archivo="txt/" . $_FILES["archivo"]["name"];
	    }
	}
	else
	{
		$mensajefoto="Imagen invalida";
	}
	$f = fopen($archivo, "r");
	while(!feof($f))
	{ 
		$data = explode("	", fgets($f));
	  	$data1 = explode(" ", substr($data[1],0,-2));
	  
		$hora=explode(":",$data1[1]);
		if(($data1[2])=="p.m." && ($hora[0] < 12 ))
		{
			$horas=($hora[0]+12).":".$hora[1].":".$hora[2];
		}
		else 
		{
			$horas=$hora[0].":".$hora[1].":".$hora[2];
		}	
	  
	  	$fecha=explode("/",$data1[0]);
	  
	  	$fechas = $fecha[2]."-".$fecha[0]."-".$fecha[1];

		$date = $fechas." ".$horas;	  
	  if(($data[0]!='')&&($date!=''))
	  {
	  		echo $query="INSERT INTO reloj (ficha, fecha) values (" . $data[0] . ", '" . $date."')";
			query($query,$conexion);
	  		echo "<br>";
	  }
	  
	}

	fclose($f);
	
/*
	$data = file($archivo, FILE_SKIP_EMPTY_LINES);
	// make sure you have valid database connection prior to this point.
	// otherwise mysqli_real_escape_string won't work
	echo $values = "('". implode("'), ('", array_map('mysqli_real_escape_string', $data)). "')";
	exit;
	$query = "INSERT INTO `TABLE1` (`COLUMN1`) VALUES $values";
	query($query,$conexion);
	*/
?>
<script type="text/javascript">
alert("Importacion realizada con exito!");
</script>
<?php	
header("Location: ../paginas/menu_procesos.php");
}



?>

<form id="form1" name="form1" method="post" action="importar.php" enctype="multipart/form-data">
<?php titulo_mejorada("Parametros","","","../paginas/home.php")?>


 <table width="100%" height="229" align="center" border="0">
 <tr>
 <td width="489" height="190" >
 <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
 <table width="100%" align="center" border="0">
 <tr align="center">
 <td width="467" height="40" colspan="4" align="center" valign="middle">
 <div align="left">Archivo: <font size="2" face="Arial, Helvetica, sans-serif">
<input type="file" name="archivo" id="archivo">
 </font></div></td>
 </tr>
 
 </table>

<p>&nbsp;</p>
<table width="467" border="0">
<tr>
<td width="466"><div align="right">
<?btn('ok','form1',1); ?>
</div></td>
</tr>
</table>
</td>
</tr>
</table>
</form>
