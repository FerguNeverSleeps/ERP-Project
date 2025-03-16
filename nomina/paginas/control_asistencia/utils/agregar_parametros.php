<?php
foreach ($campos as $campo) 
{
    if(isset($_POST[$campo]) && !empty($_POST[$campo]))
    {
    	$parametro = str_replace('posicion_', '', $campo);
    	$posicion  = $_POST[$campo];
    	$formato_p = isset($_POST['formato_'.$parametro]) ? $_POST['formato_'.$parametro] : NULL;

    	if($campo=='dispositivo')
    	{
    		foreach ($posicion as $valor) 
		    	$conexion->insert('caa_parametros', array('nombre' => $parametro, 'posicion' => $valor, 'formato' => $formato_p, 'configuracion' => $id) );		    		
    	}
    	else
	    	$conexion->insert('caa_parametros', array('nombre' => $parametro, 'posicion' => $posicion, 'formato' => $formato_p, 'configuracion' => $id) );
    }
}
?>