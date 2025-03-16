<?php
session_start();
ob_start();
$nivel1      = ($_GET["nivel1"]!='') ? $_GET["nivel1"] : NULL ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);
$query     ="SELECT * from nomnivel1
LEFT JOIN nomnivel2 on (nomnivel1.codorg = nomnivel2.gerencia)
WHERE nomnivel2.gerencia =".$nivel1;
$result    = $db->query($query);
 $cant =  $result->num_rows;
if($cant > 0)
{
echo "<div class='form-group'>
	<label class='col-md-3 control-label' for='txtcodigo'>Departamento</label>
        <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center'>
		<select name='nivel2' id='nivel2'  class='form-control'>";
	echo "	<option value='0'>TODOS</option>";
		while ($datos    = $result->fetch_assoc()) 
		{

			echo "	<option value='".$datos["codorg"]."'>".$datos["descrip"]."</option>";

		}
echo "	</select>
	</div></div>
";
}else
{
echo "<div class='form-group'>
	<label class='col-md-3 control-label' for='txtcodigo'>Departamento</label>
        <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center'>
		<select name='nivel2' id='nivel2'  class='form-control'>";

	echo "	<option value='0'>TODOS</option>";

echo "	</select></div></div>";
}

?>