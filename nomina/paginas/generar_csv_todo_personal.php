<?php 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	session_start();
	ob_start();
	require_once '../lib/common.php';
	require_once '../libs/phpseclib/Net/SFTP.php';
	
	use phpseclib\Net\SFTP;

	// Función para generar y guardar un archivo CSV
	function generateCSV($filename, $data) {
	    $file = fopen($filename, 'w');


	    if ($file === false) {
	        return false; // No se pudo abrir el archivo
	    }

	    foreach ($data as $row) {
	        $row = array_map(function($value) {
	            return str_replace(array('"', "'"), '', $value);
	        }, $row);

	        if (fputcsv($file, $row, ',', ' ') === false) {
	            fclose($file);
	            return false; // Hubo un problema al escribir en el archivo
	        }
	    }

	    if (fclose($file) === false) {
	        return false; // No se pudo cerrar el archivo
	    }

	    return true; // El archivo se generó correctamente
	}

	// Función para eliminar las tildes de una cadena
	function removeTildes($string) {
	    $tildes = array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ã');
	    $noTildes = array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'A');
	    return str_replace($tildes, $noTildes, $string);
	}

	include ("func_bd.php");
	//error_reporting(E_ALL ^ E_DEPRECATED);
	$conexion = new bd($_SESSION['bd']);
	$db = new bd(SELECTRA_CONF_PYME);
	$sql = "SELECT * FROM nompersonal WHERE estado NOT IN ('Personal Externo')";
	$res = $conexion->query($sql, "utf8");

	$personnelBasicInfo = [];
	$personnelAddress = [];
	$personnelSupplier = [];
	$personnelHR = [];

	while ($fila = $res->fetch_assoc()) {
		$ficha = $fila['ficha'];
		$fecingCSV = date("d/m/Y", strtotime($fila['fecing']));
	  	$cedulaEmpleado = $fila['cedula'];
	  	$tipoContrato = $fila['tipemp'];
	  	$estadoEmpleado = $fila['estado'];
	  	$parts = explode(",", $fila['apenom']);

		if (count($parts) == 2) {
		    $apellidos = trim($parts[0]); // Eliminar espacios en blanco alrededor
		    $nombres = trim($parts[1]); // Eliminar espacios en blanco alrededor
		} 
		else {
		    // Manejar el caso en que el formato no sea válido
		    $apellidos = "";
		    $nombres = "";
		}

		$nombreCompleto = removeTildes($nombres) ." ". removeTildes($apellidos);
	  	$direccionEmpleado = removeTildes($fila['direccion']);
	  	$departamentoEmpleado = '';
	  	$ciudadEmpleado = '';
	  	$emailEmpleado = $fila['email'];
	  	$cuentaBancaria = $fila['cuentacob'];

	  	// Verifica si $nombre tiene más de 50 caracteres
	  	if (strlen($nombres) > 50) {
	  	    // Encuentra la posición del primer espacio en blanco
	  	    $primerEspacio = strpos($nombres, ' ');
	
	  	    // Si se encontró un espacio en blanco, recorta el nombre hasta ese punto
	  	    if ($primerEspacio !== false) {
      	    	$nombres = substr($nombres, 0, $primerEspacio);
	  	    }
	  	    else {
      	    	// Si no se encontró un espacio en blanco, simplemente recorta el nombre a 50 caracteres
      	    	$nombres = substr($nombres, 0, 50);
	  	    }
	  	}

	  	// Verifica si $apellidos tiene más de 50 caracteres
	  	if (strlen($apellidos) > 50) {
	  	    // Encuentra la posición del primer espacio en blanco
	      	$primerEspacio = strpos($apellidos, ' ');
	
	      	// Si se encontró un espacio en blanco, recorta el nombre hasta ese punto
	      	if ($primerEspacio !== false) {
	      	    $apellidos = substr($apellidos, 0, $primerEspacio);
	      	}
	      	else {
	      	    // Si no se encontró un espacio en blanco, simplemente recorta el nombre a 50 caracteres
	      	    $apellidos = substr($apellidos, 0, 50);
	      	}
	  	}

	 	// Divide el nombre completo en palabras
	 	$palabras = explode(' ', $nombreCompleto);

	 	// Inicializa una variable para las iniciales
	 	$iniciales = '';

	 	// Itera a través de las palabras y obtén la primera letra de cada una
	 	foreach ($palabras as $palabra) {
	 	    $iniciales .= strtoupper(substr($palabra, 0, 1));
	 	}

	 	if (strlen($ficha) < 5) {
	 	    $colaborador_codigo = 'PAN1'.str_pad($ficha, 4, '0', STR_PAD_LEFT);
	 	}
	 	else {
	 	    $colaborador_codigo = 'PAN'.$ficha;
	 	}

	 	if ($tipoContrato == 'Fijo') {
	 	    $fecfinCSV = '31/12/2099';
	 	}
	 	else {
			$fecfinCSV = date("d/m/Y", strtotime($fila['fecing']));
	 	}

	 	// if ($estadoEmpleado == 'Activo') {
	 	//     $status = 'N';
	 	// }
	 	// else {
	 	//     $status = 'C';
	 	// }
	 	$status = 'N';


	  	// Valida y recorta la dirección si es necesario
	  	if (strlen($direccionEmpleado) > 160) {
	  	    $direccionEmpleado = substr($direccionEmpleado, 0, 160);
	  	}

    	// Reemplaza las comas por espacios en la dirección
    	$direccionEmpleado = str_replace(',', ' ', $direccionEmpleado);

	  	// Valida y recorta el departamento si es necesario
	  	if (strlen($departamentoEmpleado) > 40) {
	  	    $departamentoEmpleado = substr($departamentoEmpleado, 0, 40);
	  	}
	
	  	// Valida y recorta la ciudad si es necesario
	  	if (strlen($ciudadEmpleado) > 40) {
	  	    $ciudadEmpleado = substr($ciudadEmpleado, 0, 40);
	  	}
	
	  	// Valida y recorta el correo electrónico si es necesario
	  	if (strlen($emailEmpleado) > 255) {
	  	    $emailEmpleado = substr($emailEmpleado, 0, 255);
	  	}
	
	  	// Valida la cuenta bancaria si es necesario
	  	if (strlen($cuentaBancaria) > 35) {
	  	    $cuentaBancaria = 'supera los 35 caracteres';
	  	}

	  	// Datos para el archivo Personnel_BasicInfo.csv
	    $personnelBasicInfo[] = [
	        $colaborador_codigo,
	        removeTildes($nombres),
	        removeTildes($apellidos),
	        $iniciales,
	        $fecingCSV,
	        $fecfinCSV,
	        $cedulaEmpleado,
	        $status,
	        'ES',
	        'EM',
	        $colaborador_codigo,
	        $status,
	    ];

	    // Datos para el archivo Personnel_Address_1.csv
	    $personnelAddress[] = [
	        $colaborador_codigo,
	        1,
	        'PA',
	        removeTildes($direccionEmpleado),
	        removeTildes($departamentoEmpleado),
	        removeTildes($ciudadEmpleado),
	        $emailEmpleado,
	        0,
	    ];

	    // Datos para el archivo Personnel_Supplier_1.csv
	    $personnelSupplier[] = [
	        $colaborador_codigo,
	        'PAN',
	        'TR',
	        $cuentaBancaria,
	    ];

	    // Datos para el archivo Personnel_HR_ADINFO_1.csv
	    $personnelHR[] = [
	        $colaborador_codigo,
	        '1001',
	    ];
	}

	array_unshift($personnelBasicInfo, ['ResourceId', 'FirstName', 'Surname', 'ShortName', 'DateFrom', 'DateTo', 'SocialSec', 'Status', 'Language', 'ResourceTyp', 'SupplierId', 'PayStatus']);
	array_unshift($personnelAddress, ['RelatedId', 'AddressType', 'CountryCode', 'Address', 'Place', 'Province', 'EMail', 'SequenceNo']);
	array_unshift($personnelSupplier, ['RelatedId', 'SupplierGroupId', 'PayMethod', 'BankAccount']);
	array_unshift($personnelHR, ['@$$##RelatedId##$$@', 'ceco_fx']);

  	// Generar y guardar los archivos CSV
  	$crearArchivoPersonnel_C0_1 = generateCSV('Personnel_C0_1.csv', $personnelBasicInfo);
  	$crearArchivoPersonnel_Address_1 = generateCSV('Personnel_Address_1.csv', $personnelAddress);
  	$crearArchivoPersonnel_Supplier_1 = generateCSV('Personnel_Supplier_1.csv', $personnelSupplier);
  	$crearArchivoPersonnel_HR_ADINFO_1 = generateCSV('Personnel_HR_ADINFO_1.csv', $personnelHR);

 	if (!$crearArchivoPersonnel_C0_1) {
 		echo 'Hubo un problema al generar el archivo Personnel_C0_1.csv <br>';
 	}
 	else if (!$crearArchivoPersonnel_Address_1) {
 		echo 'Hubo un problema al generar el archivo Personnel_Address_1.csv <br>';
 	}
 	else if (!$crearArchivoPersonnel_Supplier_1) {
 		echo 'Hubo un problema al generar el archivo Personnel_Supplier_1.csv <br>';
 	}
 	else if (!$crearArchivoPersonnel_HR_ADINFO_1) {
 		echo 'Hubo un problema al generar el archivo Personnel_HR_ADINFO_1.csv <br>';
 	}
 	else {

		// Información para la conexión SFTP
		$ftpHost = 'uss-gateway.unit4cloud.com';
		$ftpPort = 41667;
		$ftpUser = 'SFTP-IMP-SES-PROD';
		$ftpPass = 'HTkcW0hFRjMq8DOvmiGE';
		$remotePath = '/Panama/';

		$sftp = new Net_SFTP($ftpHost, $ftpPort);

		// Inicia sesión SSH
		if (!$sftp->login($ftpUser, $ftpPass)) {
		    die('Error de autenticación en el servidor SFTP.');
		}

		// Lista de archivos que deseas eliminar
		$filesToDelete = [
		    'Personnel_C0_1.csv',
		    'Personnel_Address_1.csv',
		    'Personnel_Supplier_1.csv',
		    'Personnel_HR_ADINFO_1.csv',
		];

       	// Recorrer los archivos a eliminar
		foreach ($filesToDelete as $fileName) {
		    // Construye la ruta completa del archivo
		    $filePath = $remoteFolder . $fileName;

		    // Verifica si el archivo existe antes de intentar eliminarlo
		    if ($sftp->file_exists($filePath)) {
		        // Elimina el archivo
		        if ($sftp->delete($filePath)) {
		            echo 'Archivo ' . $fileName . ' eliminado correctamente.<br>';
		        } else {
		            echo 'Error al eliminar el archivo ' . $fileName . '.<br>';
		        }
		    }
		}


		// Sube los archivos generados al servidor SFTP
		if ($sftp->put($remotePath . 'Personnel_C0_1.csv', file_get_contents('Personnel_C0_1.csv')) &&
		    $sftp->put($remotePath . 'Personnel_Address_1.csv', file_get_contents('Personnel_Address_1.csv')) &&
		    $sftp->put($remotePath . 'Personnel_Supplier_1.csv', file_get_contents('Personnel_Supplier_1.csv')) &&
		    $sftp->put($remotePath . 'Personnel_HR_ADINFO_1.csv', file_get_contents('Personnel_HR_ADINFO_1.csv'))) {

		    echo 'Archivos subidos con éxito al servidor SFTP';
		}
		else {
		    echo 'Error al subir archivos al servidor SFTP';
		}

		// Cierra la conexión sftp
		$sftp->disconnect();

  	}


  	?>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<?php

		echo '<script type="text/javascript">
				    // Muestra la notificación de carga
				    alert("Cargando, por favor espere...");

				    // Espera 5 segundos antes de cerrar la pestaña
				    setTimeout(function() {
				        window.close();
				    }, 3000);
				</script>';

		 ?>
	</head>
	<body>
		
	</body>
</html>