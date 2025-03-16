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
							  AND cedula = '$_GET[cedula]' AND id != $_GET[id]
							GROUP BY cedula";

	$res = $conexion->query($sql, "utf8");

	// Verifica si se obtuvo un resultado
	if ($res && $res->num_rows > 0) {
	    // Obtiene la fila de resultado
	    $fila = $res->fetch_assoc();

	    // Accede a los valores de las columnas
	    $cedula = $fila['cedula'];
	    $porcentajeTotalActual = $fila['porcentaje_total'];

			// Suma el porcentaje total actual con el nuevo porcentaje
			$porcentajeTotal = $porcentajeTotalActual + $nuevoPorcentaje;

			// Validación
			if ($porcentajeTotal <= 100.00) {
				$query           = "UPDATE nompersonal_pry_cc
				SET codigo_proyecto_cc='$_GET[codigoProyecto]',
				fecha_inicio      = '$fechaInicio',
				fecha_fin      = '$fechaFin',
				porcentaje = '$_GET[porcentaje]'
				WHERE id = '$_GET[id]'";
				$result=sql_ejecutar($query);

		    $responseData = array(
		        'resultado' => true,
		        'mensaje' => 'Proyecto editado exitosamente'
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
		        'mensaje' => 'No es posible editar el Proyecto / Centro De Costo, '.$mensaje
		    );
			}

	}
	else {
		if ($nuevoPorcentaje <= 100) {
			$query           = "UPDATE nompersonal_pry_cc
			SET codigo_proyecto_cc='$_GET[codigoProyecto]',
			fecha_inicio      = '$fechaInicio',
			fecha_fin      = '$fechaFin',
			porcentaje = '$_GET[porcentaje]'
			WHERE id = '$_GET[id]'";
			$result=sql_ejecutar($query);

	    $responseData = array(
	        'resultado' => true,
	        'mensaje' => 'Proyecto editado exitosamente'
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
















?>
