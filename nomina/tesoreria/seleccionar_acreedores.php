<?php
$url="seleccionar_acreedores";
$modulo="Seleccione Acreedores";
$tabla="nom_movimientos_nomina";
$titulos=array("Codigo","Descripcion","Monto");
$indices=array("1","2","0");
$filtro="Ac";
session_start();
ob_start();

require_once '../lib/config.php';
require_once '../lib/common.php';
include ('../header.php');

$conexion=conexion();

$codnom=@$_GET['codnom'];
$tipnom=@$_GET['tipnom'];


$consulta="SELECT sum(monto), codcon, descrip FROM nom_movimientos_nomina WHERE codnom='$codnom' and tipnom='$tipnom' and tipcon='D' and codcon between 500 and 599 group by codcon order by codcon";

$num_paginas=obtener_num_paginas($consulta,100);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta, 100);
?>
<FORM name="frmPrincipal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self">
<?php
	titulo_mejorada($modulo,"21.png","btn('ok',\"MarcarTodos('codcon[]');\",2,'Marcar o Desmarcar Todos');","seleccionar_nomina.php");
	$array=array("Cheque","Orden","beneficiario");
	//busqueda($url,"",$array);
?>

<BR>
<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
  <tbody>
    <tr class="tb-head" >
    	<input name="marcar_todos" type="hidden" value="1">	
    	<td><strong>Forma de emision de cheques: </strong>&nbsp;
    	<select name="tipo" id="tipo">
    	<option value="0">Cheques por empleado</option>
    	<option value="1">Cheques agrupado por acreedor</option>
    	</select>
    	</td>
    </tr>
   </tbody>
  </table>

<BR>
<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
  <tbody>
    <tr class="tb-head" >
<?
foreach($titulos as $nombre){
	echo "<td><STRONG>$nombre</STRONG></td>";
}
?>

    <td></td>
	<td></td>
    </tr>

<?php
	
	if($num_paginas!=0){
	$i=0; 
	while($fila=mysqli_fetch_array($resultado)){
   	$i++;
	if($i%2==0){
?>
    	<tr class="tb-fila" onclick="javascript:cuentas()">
<?
	}else{
		echo "<tr>";
	}
	foreach($indices as $campo){
		$nom_tabla=mysqli_fetch_field_direct($resultado, $campo)->name;
		$var=$fila[$nom_tabla];
		if ($nom_tabla=="fecha"){
			if($fila['status']=='An'|| $fila['status']=='Da'){
				echo "<td>".fecha($fila['anulacion'])."</td>";
			}else{
				echo "<td>".fecha($var)."</td>";
			}
		}elseif ($nom_tabla=="monto"){
			echo "<td align=right>".number_format($var, 2, ',', '.')."</td>";
			
		}elseif ($nom_tabla=="cuenta"){
			echo "<td align=left>".substr($fila['cuenta'], -10)."</td>";
			
		}
		else{
			echo "<td title='".$fila['concepto']."'>$var</td>";
		}
	}
	$codigo=$fila['codnom'];
	$tipnom=$fila['codtip'];

	?>
	<td><input type="checkbox" name="codcon[]" value="<?php echo $codigo?>"></td>
	<?php
    echo "</tr>";
	}
}else{
	echo "<tr><td>No existen registro con la busqueda especificada</td></tr>";
}
//$codigo=0;

cerrar_conexion($conexion);

?>
<script language="JavaScript" type="text/javascript">
function mostrar_paginas(num_paginas){

}
</script>
</tbody>
</table>

<table width="100%"  class="tb-head">
<tr>

<td></td>
<td></td>
<td></td>
<td></td>

<td align="right"><?php btn('ok','document.form1.submit();',2,'Enviar') ?></td>
</tr>
</table>
<br>
</FORM>
</BODY>
</html>