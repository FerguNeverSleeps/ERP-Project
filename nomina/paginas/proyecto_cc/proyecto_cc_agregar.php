<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	session_start();
	ob_start();
	require_once '../../lib/common.php';
	include ("../func_bd.php");

  $fechaInicio = date('Y-m-d', strtotime($_GET['fechaInicio']));
  $fechaFin = date('Y-m-d', strtotime($_GET['fechaFin']));
	$nuevoPorcentaje = $_GET[porcentaje];

	$conexion = new bd($_SESSION['bd']);
	$db = new bd(SELECTRA_CONF_PYME);

	$sql = "SELECT cedula, SUM(porcentaje) AS porcentaje_total
							FROM nompersonal_pry_cc
							WHERE ('$fechaInicio' BETWEEN fecha_inicio AND fecha_fin
									   OR '$fechaFin' BETWEEN fecha_inicio AND fecha_fin)
							  AND cedula = '$_GET[cedula]'
							GROUP BY cedula";
							// echo $sql;
	$res = $conexion->query($sql, "utf8");

	// Verificar si se obtuvo un resultado
	if ($res && $res->num_rows > 0) {
	    // Obtener la fila de resultado
	    $fila = $res->fetch_assoc();

	    // Acceder a los valores de las columnas
	    $cedula = $fila['cedula'];
	    $porcentajeTotalActual = $fila['porcentaje_total'];

			// Suma el porcentaje total actual con el nuevo porcentaje
			$porcentajeTotal = $porcentajeTotalActual + $nuevoPorcentaje;

			// Validación
			if ($porcentajeTotal <= 100.00) {
				$query="INSERT INTO nompersonal_pry_cc
				(cedula,codigo_proyecto_cc,fecha_inicio,fecha_fin, porcentaje)
				values ('$_GET[cedula]', '$_GET[codigoProyecto]','$fechaInicio','$fechaFin','$_GET[porcentaje]')";
				$result=sql_ejecutar($query);

		    $responseData = array(
		        'resultado' => true,
		        'mensaje' => 'Proyecto agregado exitosamente'
		    );

			}
			else {
				$porcentajeAceptado = 100.00 - $porcentajeTotalActual;

				if ($porcentajeAceptado <= 0) {
				  // Aquí notificas al usuario que ya se alcanzó el límite de porcentaje
				  $mensaje = 'ya que se ha alcanzado el límite de porcentaje para el rango de fechas ingresadas.';
				}
				else {
				  $mensaje = 'ya que el porcentaje enviado supera el límite de porcentaje para el rango de fechas ingresadas, porcentaje permitido para la acción: '.$porcentajeAceptado;
				}

		    $responseData = array(
		        'resultado' => false,
		        'mensaje' => 'No es posible agregar el Proyecto / Centro De Costo, '.$mensaje
		    );
			}

	}
	else {
		if ($nuevoPorcentaje <= 100) {
			$query="INSERT INTO nompersonal_pry_cc
			(cedula,codigo_proyecto_cc,fecha_inicio,fecha_fin, porcentaje)
			values ('$_GET[cedula]', '$_GET[codigoProyecto]','$fechaInicio','$fechaFin','$_GET[porcentaje]')";
			$result=sql_ejecutar($query);

	    $responseData = array(
	        'resultado' => true,
	        'mensaje' => 'Proyecto agregado exitosamente'
	    );
		}
		else {
			$porcentajeAceptado = 100.00 - $nuevoPorcentaje;

			if ($porcentajeAceptado <= 0) {
			  // Aquí notificas al usuario que ya se alcanzó el límite de porcentaje
			  $mensaje = 'ya que se ha alcanzado el límite de porcentaje para el rango de fechas ingresadas.';
			}
			else {
			  $mensaje = 'ya que el porcentaje enviado supera el límite de porcentaje para el rango de fechas ingresadas, porcentaje permitido para la acción: '.$porcentajeAceptado;
			}

	    $responseData = array(
	        'resultado' => false,
	        'mensaje' => 'No es posible editar el Proyecto / Centro De Costo, '.$mensaje
	    );
		}
	}

	// Codificar los datos en formato JSON
	$responseJSON = json_encode($responseData);

	// Establecer las cabeceras para indicar que la respuesta es JSON
	header('Content-Type: application/json');

	// Enviar la respuesta JSON al cliente
	echo $responseJSON;


