<?php
	$ruta   = dirname(dirname(__FILE__));
	$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';

	if($opcion=='tiempo')
	{
		$radio = (isset($_POST['radio'])) ? $_POST['radio'] : '';
		$parte = (isset($_POST['parte'])) ? $_POST['parte'] : '';

		if($radio=='unica')
			require_once($ruta . "/vistas/_template_fecha_unica.php");
		else if($radio == 'separada')
			require_once($ruta . "/vistas/_template_fecha_hora_separadas.php");
		else if($radio == 'multiple')
		{
			if(empty($parte))
				require_once($ruta . "/vistas/_template_fecha_multiple_1.php");
			else
				require_once($ruta . "/vistas/_template_fecha_multiple_2.php");
		}

	} 
?>