<?php
include("../../lib/common.php") ;
include("../func_bd.php");
ob_clean();
if($_POST["op"] == "agregar"){

    $datetime1 = new DateTime($_POST['inicio']);
    $inicio = $datetime1->format('Y-m-d H:i:s');

    $datetime2 = new DateTime($_POST['vencimiento']);
    $vencimiento = $datetime2->format('Y-m-d H:i:s');


	echo $query="INSERT INTO noticias 
	(id,titulo,descripcion, fecha_inicio, fecha_vencimiento, estatus, fecha_creacion)
	values ('','$_POST[titulo]','$_POST[descripcion]','$inicio','$vencimiento','$_POST[estado]', now())";
	$result=sql_ejecutar($query);
}


if($_POST["op"] == "editar"){

    $datetime1 = new DateTime($_POST['inicio']);
    $inicio = $datetime1->format('Y-m-d H:i:s');

    $datetime2 = new DateTime($_POST['fecha_vencimiento']);
    $vencimiento = $datetime2->format('Y-m-d H:i:s');
	$query = "UPDATE noticias 
	SET titulo      = '$_POST[titulo]',
	    descripcion = '$_POST[descripcion]',
	    fecha_inicio ='$inicio',
	    fecha_vencimiento ='$vencimiento',
	    estatus      = '$_POST[estado]'
	WHERE id = '$_POST[id]'";	
	$result=sql_ejecutar($query);	
}