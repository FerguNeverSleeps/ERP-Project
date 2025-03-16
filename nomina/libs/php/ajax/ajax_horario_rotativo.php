<?php

	$frecuencia = $_POST['frecuencia'];

	echo "<option value=''>Seleccione</option>";

	if($frecuencia=='Semanal')
	{
		echo "<option value='1'>Lunes</option>";
		echo "<option value='2'>Martes</option>";
		echo "<option value='3'>Miércoles</option>";
		echo "<option value='4'>Jueves</option>";
		echo "<option value='5'>Viernes</option>";
		echo "<option value='6'>Sábado</option>";
		echo "<option value='7'>Domingo</option>";
	}
	elseif ($frecuencia == 'Diaria') 
	{
		for($i=1; $i<=31; $i++)
		{
			echo "<option value='".$i."'>$i</option>";
		}
	}
	elseif ($frecuencia == 'Mensual') 
	{
		$meses = array('1'=>'Enero', 
					   '2'=>'Febrero',
					   '3'=>'Marzo',
					   '4'=>'Abril',
					   '5'=>'Mayo',
					   '6'=>'Junio',
					   '7'=>'Julio',
					   '8'=>'Agosto',
					   '9'=>'Septiembre',
					   '10'=>'Octubre',
					   '11'=>'Noviembre',
					   '12'=>'Diciembre');

		foreach ($meses as $key => $value) 
		{
			echo "<option value='".$key."'>$value</option>";
		}
	}
?>