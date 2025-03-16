<?php

    require_once '../lib/common.php';
    require_once '../libs/phpseclib/Net/SFTP.php';
    
    use phpseclib\Net\SFTP;
//=================================================================================================================
// Subir foto del integrante
$host_ip = $_SERVER['REMOTE_ADDR'];
$archivo_foto = isset($_FILES['foto']['name']) ? $_FILES['foto']['name'] : '';
$setFoto = "";

if($archivo_foto!='')
{
    $nombre_foto  = $_POST['ficha']."_".$archivo_foto;
    $dir_fotos    = "fotos/";
    if (copy($_FILES['foto']['tmp_name'], $dir_fotos . $nombre_foto)) {
        chmod( $dir_fotos . $nombre_foto, 0777);
        $insertFoto = $dir_fotos . $nombre_foto;
        $setFoto    = "foto='". $dir_fotos . $nombre_foto."', ";

        //crear imagen miniatura
        $path    = str_replace("\\","/",dirname(__FILE__)); 
        $fuente  = $path."/".$dir_fotos . $nombre_foto;
        $destino = $path."/".$dir_fotos . $nombre_foto.".min";
        include_once("../../includes/phpthumb/phpthumb.class.php");

        $phpThumb = new phpThumb();
        $imagen_data = explode(".", $fuente);
        $extension = strtolower(end($imagen_data));
        $calidad_foto = 85;
        
        $phpThumb->resetObject();
        $phpThumb->setSourceFilename($fuente);
        $phpThumb->setParameter('h',200);
        $phpThumb->setParameter('w',200);
        $phpThumb->setParameter('ar','x');
        $phpThumb->setParameter('config_allow_src_above_docroot', true);
        $phpThumb->setParameter('config_cache_directory', $path."/../../includes/phpthumb/cache/");
        $phpThumb->setParameter('config_output_format', $extension);
        $phpThumb->setParameter('q', $calidad_foto);
        if ($phpThumb->GenerateThumbnail()){
            $phpThumb->RenderToFile($destino);
            if(file_exists($destino)){
            	chmod( $destino, 0777);
            }        	
        }
        else{
        	//print " no genero ";
	        //print "".$phpThumb->phpThumbDebug();
	        //exit;
        }
    } 
    else{
        throw new Exception("Error al subir la foto", 1);            				
    }
}

// Imagen cedula del integrante
$archivo_cedula  = isset($_FILES['imagen_cedula']['name']) ? $_FILES['imagen_cedula']['name'] : '';
$setImagenCedula = "";

if($archivo_cedula!='') {
    $nombre_imagen  = "cedula_".$_POST['ficha']."_".$archivo_cedula;
    $dir_imagen     = "fotos/";
    if (copy($_FILES['imagen_cedula']['tmp_name'], $dir_imagen . $nombre_imagen)) 
    {
        chmod( $dir_imagen  . $nombre_imagen, 0777);
        $insertImagenCedula = $dir_imagen . $nombre_imagen;
        $setImagenCedula    = "imagen_cedula='fotos/".$nombre_imagen."', ";
    } 
    else {
        throw new Exception("Error al subir la imagen de la cedula", 1);            				
    }
}

$niveles = array('codnivel1', 'codnivel2', 'codnivel3', 'codnivel4', 'codnivel5', 'codnivel6', 'codnivel7');

foreach ($niveles as $nivel) {
	$$nivel = 0;

	if(isset($_POST[$nivel])  &&  $_POST[$nivel] != '')
	{
		$$nivel = $_POST[$nivel];
	}
}

$suesal               = (isset($_POST['suesal']) ? $_POST['suesal'] : 0);
$sueldopro            = (isset($_POST['suesal']) ? $_POST['suesal'] : 0);
$sueldo_original      = (isset($_POST['sueldo_original'])) ? $_POST['sueldo_original'] : 0 ;

$fecnac               = DateTime::createFromFormat('d-m-Y', $_POST['fecnac']); // 16-01-1962
$fecnac               = ($fecnac !== false) ? $fecnac->format('Y-m-d') : '';	

$fecing               = DateTime::createFromFormat('d-m-Y', $_POST['fecing']);
$fecing               = ($fecing !== false) ? $fecing->format('Y-m-d') : '';
$fecfin               = DateTime::createFromFormat('d-m-Y', $_POST['fecfin']);
$fecfin               = ($fecfin !== false) ? $fecfin->format('Y-m-d') : '';

$fecha_decreto        = DateTime::createFromFormat('d-m-Y', $_POST['fecha_decreto']);
$fecha_decreto        = ($fecha_decreto !== false) ? $fecha_decreto->format('Y-m-d') : '';	

$fecha_decreto_baja   = DateTime::createFromFormat('d-m-Y', $_POST['fecha_decreto_baja']);
$fecha_decreto_baja   = ($fecha_decreto_baja !== false) ? $fecha_decreto_baja->format('Y-m-d') : '';	

$inicio_periodo       = (isset($_POST['inicio_periodo'])) ? $_POST['inicio_periodo']  : '';
$inicio_periodo       = DateTime::createFromFormat('d-m-Y', $inicio_periodo);
$inicio_periodo       = ($inicio_periodo !== false) ? $inicio_periodo->format('Y-m-d') : '';	

$fin_periodo          = (isset($_POST['fin_periodo'])) ? $_POST['fin_periodo']  : '';
$fin_periodo          = DateTime::createFromFormat('d-m-Y', $fin_periodo);
$fin_periodo          = ($fin_periodo !== false) ? $fin_periodo->format('Y-m-d') : '';	

$fin_probatorio          = (isset($_POST['fin_probatorio'])) ? $_POST['fin_probatorio']  : '';
$fin_probatorio          = DateTime::createFromFormat('d-m-Y', $fin_probatorio);
$fin_probatorio          = ($fin_probatorio !== false) ? $fin_probatorio->format('Y-m-d') : '';	

$fecharetiro            = '0000-00-00';
$marca_reloj            = ($_POST['marcar_reloj']) ? 1 : 0 ;
$incapacidad            = ($_POST['incapacidad']) ? 1 : 0 ;
$familiar_incapacidad   = ($_POST['familiar_incapacidad']) ? 1 : 0 ;
$alto_riesgo_pension    = ($_POST['alto_riesgo_pension']) ? 1 : 0 ;
$salario_integral       = ($_POST['salario_integral']) ? 1 : 0 ;
//=================================================================================================================

if($operacion=='agregar')
{
	$sql ="INSERT INTO nompersonal (
                foto, 
                nomposicion_id, 
                nacionalidad, 
                cedula, 
                apellidos, 
                nombres, 
                apellido_materno,
                nombres2,
                apellido_casada,
                apenom, 
                sexo,
                estado_civil, 
                fecnac, 
                lugarnac, 
                codpro, 
                direccion, 
                telefonos, 
                email, 
                estado, 
                fecing, 
                ficha, 
                tipopres,
                forcob, 
                codbancob, 
                cuentacob, 
                codbanlph, 
                cuentalph, 
                tipemp, 
                suesal, 
                tipnom,
                codcat,
                codcargo, 
                codnivel1,
                codnivel2,
                codnivel3,
                codnivel4,
                codnivel5,
                codnivel6,
                codnivel7,
                inicio_periodo,
                fin_periodo, 
                turno_id,
                seguro_social,
                hora_base,
                segurosocial_sipe,
                clave_ir,
                dv,
                siacap,
                puesto_id,
                imagen_cedula,
                sueldopro,
                fecharetiro,
                marca_reloj,
                tiene_discapacidad,
                tiene_familiar_disca, 
                proyecto, 
                IdDepartamento,
                jefe,
                uid_user_aprueba,
                fin_probatorio,
				usuario_workflow,
				usr_password) 
			VALUES 
			(NULLIF('". (isset($insertFoto) ? $insertFoto : '') ."',''),
	 		 NULLIF('". (isset($_POST['nomposicion_id']) ? $_POST['nomposicion_id'] : '' ) ."',''),
			 '". $_POST['nacionalidad'] ."',
			 '". $_POST['cedula'] ."',
			 '". $_POST['apellidos'] ."',
			 '". $_POST['nombres'] ."',
                         '". $_POST['apellidos2'] ."',
                         '". $_POST['nombres2'] ."',
                         '". $_POST['apellido_casada'] ."',
			 '". $_POST['apellidos'] . " ". $_POST['apellidos2'] . ", " . $_POST['nombres'] ." " . $_POST['nombres2'] ."',
			 '". $_POST['sexo'] ."',
			 '". $_POST['estado_civil'] ."',
			 '". $fecnac ."',
			 '". $_POST['lugarnac'] ."',
			 '". $_POST['codpro'] ."',
			 '". $_POST['direccion'] ."',
			 '". $_POST['telefonos'] ."',
			 NULLIF('". $_POST['email'] ."',''),
			 '". $_POST['estado'] ."',
			 '". $fecing ."',
			 '". $_POST['ficha'] ."',
			 NULLIF('". (isset($_POST['tipopres']) ? $_POST['tipopres'] : '' ) ."',''),
			 '". $_POST['forcob'] ."',
			 NULLIF('". (isset($_POST['codbancob']) ? $_POST['codbancob'] : '') ."',''),
			 NULLIF('". (isset($_POST['cuentacob']) ? $_POST['cuentacob'] : '') ."',''),
			 NULLIF('". (isset($_POST['codbanlph']) ? $_POST['codbanlph'] : '') ."',''),
			 NULLIF('". (isset($_POST['cuentalph']) ? $_POST['cuentalph'] : '') ."',''),
			 '". $_POST['tipemp'] ."',
			 '". $suesal ."',
			 '". $_POST['tipnom'] ."',
			 '". $_POST['codcat'] ."',
			 '". $_POST['codcargo'] ."',
			 '". $codnivel1 ."',
			 '". $codnivel2 ."',
			 '". $codnivel3 ."',
			 '". $codnivel4 ."',
			 '". $codnivel5 ."',
			 '". $codnivel6 ."',
			 '". $codnivel7 ."',
			 '". $fecing ."',
			 NULLIF('". $fin_periodo ."',''),
			 NULLIF('". $_POST['turno_id'] ."',''),
			 NULLIF('". $_POST['seguro_social'] ."',''),
			 '". $_POST['hora_base'] ."',
			 NULLIF('". $_POST['segurosocial_sipe'] ."',''),
			 '". $_POST['clave_ir'] ."',
                         NULLIF('". $_POST['dv'] ."',''),
			 NULLIF('". $_POST['siacap'] ."',''),
			 NULLIF('". (isset($_POST['puesto_id']) ? $_POST['puesto_id'] : '') ."',''),
			 NULLIF('". (isset($insertImagenCedula) ? $insertImagenCedula : '') ."',''), 
			 '". $sueldopro ."',
			 '". $fecharetiro ."',
			 $marca_reloj,
			 $incapacidad,
			 $familiar_incapacidad,
                         '". $_POST['proyecto'] ."',
                         '".$_POST['departamento_estructura']."',
                         '".$_POST['aprueba_solicitud']."',
                         '".$_POST['usuario_aprueba']."',     
			'".$fin_probatorio."',
			'". $_POST['cedula'] ."',
			MD5('123456'))";

	$res = $db->query($sql);
	if ($res) 
	{

        $empresaPermitido = "SELECT * FROM nomempresa WHERE nom_emp IN ('INGENIERIA Y SOLUCIONES ESPECIALIZADAS S.A.S')";
        $respuestaPermitido = $db->query($empresaPermitido);

        $modificar = false;

        if($respuestaPermitido->num_rows==1){
            $modificar = true;
        }

        if ($modificar) {
            
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

            $ficha = $_POST['ficha'];
            $cedulaEmpleado = $_POST['cedula'];
            $tipoContrato = $_POST['tipemp'];
            $estadoEmpleado = $_POST['estado'];
            $nombres = removeTildes($_POST['nombres']) ." " . removeTildes($_POST['nombres2']);
            $apellidos = removeTildes($_POST['apellidos']) . " ". removeTildes($_POST['apellidos2']);
            $nombreCompleto = removeTildes($_POST['nombres']) ." " . removeTildes($_POST['nombres2']) ." ". removeTildes($_POST['apellidos']) . " ". removeTildes($_POST['apellidos2']);
            $fecingCSV = ($fecing !== false) ? $fecing->format('d/m/Y') : '';
            $direccionEmpleado = removeTildes($_POST['direccion']);
            $departamentoEmpleado = '';
            $ciudadEmpleado = '';
            $emailEmpleado = $_POST['email'];
            $cuentaBancaria = $_POST['cuentacob'];

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
                $colaborador_codigo = 'EM'.str_pad($ficha, 5, '0', STR_PAD_LEFT);
            }
            else {
                $colaborador_codigo = 'EM'.$ficha;
            }

            if ($tipoContrato == 'Fijo') {
                $fecfinCSV = '31/12/2099';
            }
            else {
                $fecfinCSV = ($fecfin !== false) ? $fecfin->format('d/m/Y') : '';
            }

            if ($estadoEmpleado == 'Activo') {
                $status = 'N';
            }
            else {
                $status = 'C';
            }

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
                'EM',
                'TR',
                $cuentaBancaria,
            ];

            // Datos para el archivo Personnel_HR_ADINFO_1.csv
            $personnelHR[] = [
                $colaborador_codigo,
                '1001',
            ];

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
                $ftpUser = 'SFTP-IMP-SES-PREV';
                $ftpPass = 'YTM5Ky0I14OuJVghRXxD';
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

        }


		$f_venc = date('Y')."-12-31 ".date('H:i:s');
		///// Select insert para crear el calendario a la persona al momento de la creación del empleado
		$ano        = date('Y');		
		 $consulta2  = "INSERT INTO nomcalendarios_personal (cod_empresa,ficha,fecha,dia_fiesta,descripcion_dia_fiesta, turno_id)  select 0, '{$_POST['ficha']}', fecha, dia_fiesta, descripcion_dia_fiesta, '{$_POST['turno_id']}' from nomcalendarios_tiposnomina where year(fecha)='{$ano}' group by fecha";
		$resultado2 = $db->query($consulta2) or die(mysqli_error($conexion));
		///// Select insert para crear el calendario a la persona al momento de la creación del empleado
		 $exp_        = "INSERT INTO dias_incapacidad (tipo_justificacion, fecha, tiempo, observacion, cedula,cod_user, fecha_vence, dias, horas, minutos)
		VALUES (5,'".date('Y-m-d')."',144,'REGISTRO INICIAL ".$ano."','{$_POST['cedula']}','{$_POST['ficha']}','$f_venc',18,0,0);";
		//$db->query($exp_);

		// Si selecciona incapacidad, se inserta un registro a la tabla dias_incapacidad
		if ($incapacidad) 
		{
			 $sql_disc    = "INSERT INTO dias_incapacidad (tipo_justificacion, fecha, tiempo, observacion, cedula,cod_user, fecha_vence, dias, horas, minutos)
			VALUES (6,'".date('Y-m-d')."',144,'REGISTRO INICIAL ".$ano."','{$_POST['cedula']}','{$_POST['ficha']}','$f_venc',18,0,0);";
			//$db->query($sql_disc);
		}
		// Si selecciona Familiar con incapacidad, se inserta un registro a la tabla dias_incapacidad
		if($familiar_incapacidad)
		{
			 $sql_famdisc = "INSERT INTO dias_incapacidad (tipo_justificacion, fecha, tiempo, observacion, cedula,cod_user, fecha_vence, dias, horas, minutos)
			VALUES (8,'".date('Y-m-d')."',144,'REGISTRO INICIAL ".$ano."','{$_POST['cedula']}','{$_POST['ficha']}','$f_venc',18,0,0);";
			//$db->query($sql_famdisc);
		}		

		$descripcion = 'Agregar Personal ' . $_POST['ficha'];
		$sql_log     = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
		                  VALUES (NULL, '".$descripcion."', now(), 'Datos-Integrantes', 'ag_integrantes.php', 'insertar','6','".$_SESSION['nombre'] ."', '".$host_ip."')";
		
		$res_log     = $db->query($sql_log);

		$conficha = $_POST['ficha'];
		
		$sql_ficha ="UPDATE nomempresa SET conficha='".$conficha."' ";
		$res_ficha = $db->query($sql_ficha);

        echo "<script>document.location.href = 'ag_integrantes.php';</script>";

	}
    else {

		echo "<script>alert('¡Hay errores en el proceso!');</script>";
    }
}
else {
	if($_POST["usr_pass_ant"] != $_POST["usr_password"] ) {
		$usr_password = md5($_POST["usr_password"]);
	}
    else{
		$usr_password = $_POST["usr_pass_ant"];
	}
        
    if($_POST["nomina_electronica"]!=1) {    
        $sql = "UPDATE nompersonal SET
                                ".$setFoto."
                                ".$setImagenCedula."
                                nacionalidad            = '".$_POST['nacionalidad']."',
                                nomposicion_id          = NULLIF('". (isset($_POST['nomposicion_id']) ? $_POST['nomposicion_id'] : '' )."',''),
                                cedula                  = '".$_POST['cedula']."',
                                apellidos               = '".$_POST['apellidos']."',
                                nombres                 = '".$_POST['nombres']."',
                                apellido_materno        = '".$_POST['apellidos2']."',
                                nombres2                = '".$_POST['nombres2']."',
                                apellido_casada         = '".$_POST['apellido_casada']."',
                                apenom                  = '".$_POST['apellidos']." ".$_POST['apellidos2'].", ".$_POST['nombres']." ".$_POST['nombres2']."',
                                sexo                    = '".$_POST['sexo']."',
                                estado_civil            = '".$_POST['estado_civil']."',
                                fecnac                  = '".$fecnac."',
                                lugarnac                = '".$_POST['lugarnac']."',
                                codpro                  = '".$_POST['codpro']."',
                                direccion               = '".$_POST['direccion']."',
                                telefonos               = '".$_POST['telefonos']."',
                                email                   = NULLIF('".$_POST['email']."',''),
                                estado                  = '".$_POST['estado']."',
                                fecing                  = '".$fecing."',
                                fecharetiro               = '".$fecfin."',
                                ficha                   = '".$_POST['ficha']."',
                                tipopres                = NULLIF('". (isset($_POST['tipopres']) ? $_POST['tipopres'] : '' ) ."',''),
                                forcob                  = '".$_POST['forcob']."',
                                codbancob               = NULLIF('". (isset($_POST['codbancob']) ? $_POST['codbancob'] : '') ."',''),
                                cuentacob               = NULLIF('". (isset($_POST['cuentacob']) ? $_POST['cuentacob'] : '') ."',''),
                                codbanlph               = NULLIF('". (isset($_POST['codbanlph']) ? $_POST['codbanlph'] : '' ) ."',''),
                                cuentalph               = NULLIF('". (isset($_POST['cuentalph']) ? $_POST['cuentalph'] : '' ) ."',''),
                                tipemp                  = '".$_POST['tipemp']."',
                                suesal                  = '".$suesal."',
                                tipnom                  = '".$_POST['tipnom']."',
                                codcat                  = '".$_POST['codcat']."',
                                codcargo                = '".$_POST['codcargo']."',
                                proyecto                = '".$_POST['proyecto']."',
                                codnivel1               = '".$codnivel1."',
                                codnivel2               = '".$codnivel2."',
                                codnivel3               = '".$codnivel3."',
                                codnivel4               = '".$codnivel4."',
                                codnivel5                = '".$codnivel5."',
                                codnivel6               = '".$codnivel6."',
                                codnivel7               = '".$codnivel7."',
                                inicio_periodo          = '".$inicio_periodo."',
                                fin_periodo             = NULLIF('".$fin_periodo."',''), 
                                turno_id                = NULLIF('".$_POST['turno_id']."',''),
                                seguro_social           = NULLIF('".$_POST['seguro_social']."',''),
                                hora_base               = '".$_POST['hora_base']."',
                                segurosocial_sipe       = NULLIF('".$_POST['segurosocial_sipe']."',''),
                                clave_ir                = '".$_POST['clave_ir']."',
                                dv                      = NULLIF('".$_POST['dv']."',''),	           
                                siacap                  = NULLIF('".$_POST['siacap']."',''),
                                puesto_id               = NULLIF('". (isset($_POST['puesto_id']) ? $_POST['puesto_id'] : '')."',''),
                                sueldopro               = '".$suesal."',
                                marca_reloj             = '".$marca_reloj."',
                                tiene_discapacidad      = '".$incapacidad."',
                                tiene_familiar_disca    = '".$familiar_incapacidad."',
                                jefe                    = '".$_POST['aprueba_solicitud']."',
                                IdDepartamento          = '".$_POST['departamento_estructura']."',
                                uid_user_aprueba        = '".$_POST['usuario_aprueba']."',
                                usuario_workflow        = '".$_POST['usuario_workflow']."',
                                usr_password            = '".$usr_password."',
                                fin_probatorio          = NULLIF('".$fin_probatorio."','')
                   WHERE  personal_id = '".$_POST['personal_id']."'";
                   $res = $db->query($sql);
    }
    else {
        $sql2 = "UPDATE nompersonal SET
                                alto_riesgo_pension     = '".$alto_riesgo_pension."',
                                salario_integral        = '".$salario_integral."',
                                dir_trabajo_provincia   = '".$_POST['dir_trabajo_provincia']."',
                                dir_trabajo_distrito    = '".$_POST['dir_trabajo_distrito']."',
                                dir_trabajo_direccion   = '".$_POST['dir_trabajo_direccion']."',
                                tipo_trabajador         = '".$_POST['tipo_trabajador']."',
                                subtipo_trabajador      = '".$_POST['subtipo_trabajador']."',
                                tipo_contrato           = '".$_POST['tipo_contrato']."',
                                tipo_identificacion     = '".$_POST['tipo_identificacion']."'
                   WHERE  personal_id = '".$_POST['personal_id']."'";
        $res2 = $db->query($sql2);
    }

    if($res2) {
        echo "<script>document.location.href = 'ag_integrantes.php?ficha=".$_POST['ficha']."&edit';</script>";
    }

	if($res) {
		if($suesal != $sueldo_original) {
			$descripcion = 'Modificacion de sueldo a ficha ' . $_POST['ficha'] . '. Sueldo anterior '. $sueldo_original;

			$sql = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
		            VALUES (NULL, '".$descripcion."', now(), 'Datos-Integrantes', 'ag_integrantes.php', 'editar','".$suesal."','".$_SESSION['nombre'] ."', '".$host_ip."')";

		    $res = $db->query($sql);
		}

		if($_POST['codcargo'] != $_POST['cargo_original']) {
			$descripcion = 'Modificacion de cargo a ficha '.$_POST['ficha'].'. Cargo anterior '.$_POST["cargo_original"];

			$sql = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
		            VALUES (NULL, '".$descripcion."', now(), 'Datos-Integrantes', 'ag_integrantes.php', 'editar','".$_POST['codcargo']."','".$_SESSION['nombre'] ."', '".$host_ip."')";

		    $res = $db->query($sql);	
		}

                
        if($_POST['cedula'] != $_POST['cedula_actual'])	{
		    $sql_nom_movimientos_nomina = " UPDATE nom_movimientos_nomina 
                                                    SET				
                                                    cedula   = '".$_POST['cedula']."'
                                                    WHERE  cedula = '".$_POST['cedula_actual']."'";

                    $res_nom_movimientos_nomina = $db->query($sql_nom_movimientos_nomina);
                    
                    $sql_salarios_acumulados = " UPDATE salarios_acumulados 
                                                    SET				
                                                    cedula   = '".$_POST['cedula']."'
                                                    WHERE  cedula = '".$_POST['cedula_actual']."'";

                    $res_salarios_acumulados = $db->query($sql_salarios_acumulados);
                    
                    $sql_nom_nomina_netos = " UPDATE nom_nomina_netos 
                                                    SET				
                                                    cedula   = '".$_POST['cedula']."'
                                                    WHERE  cedula = '".$_POST['cedula_actual']."'";

                    $res_nom_nomina_netos = $db->query($sql_nom_nomina_netos);
                    
                    $sql_ach_sipe_detalle = " UPDATE ach_sipe_detalle 
                                                    SET				
                                                    numero_documento   = '".$_POST['cedula']."'
                                                    WHERE  numero_documento = '".$_POST['cedula_actual']."'";

                    $res_ach_sipe_detalle = $db->query($sql_ach_sipe_detalle);
                    
                    $sql_nom_progvacaciones = " UPDATE nom_progvacaciones 
                                                    SET				
                                                    cedula   = '".$_POST['cedula']."'
                                                    WHERE  cedula = '".$_POST['cedula_actual']."'";

                    $res_nom_progvacaciones = $db->query($sql_nom_progvacaciones);
                    
                    $sql_expediente = " UPDATE expediente 
                                                    SET				
                                                    cedula   = '".$_POST['cedula']."'
                                                    WHERE  cedula = '".$_POST['cedula_actual']."'";

                    $res_expediente = $db->query($sql_expediente);
                    
                    $sql_dias_incapacidad = " UPDATE dias_incapacidad 
                                                    SET				
                                                    cedula   = '".$_POST['cedula']."'
                                                    WHERE  cedula = '".$_POST['cedula_actual']."'";

                    $res_dias_incapacidad = $db->query($sql_dias_incapacidad);
                    
                    $sql_nomacumulados_det = " UPDATE nomacumulados_det 
                                                    SET				
                                                    ceduda   = '".$_POST['cedula']."'
                                                    WHERE  ceduda = '".$_POST['cedula_actual']."'";

                    $res_nomacumulados_det = $db->query($sql_nomacumulados_det);
                    
                    
                    $descripcion = 'Modificacion de Cédula a Empleado: '.$_POST['apellidos'].', '.$_POST['nombres'].' / Ficha '.$_POST['ficha'];

		    $sql = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
		            VALUES (NULL, '".$descripcion."', now(), 'Datos-Integrantes', 'ag_integrantes.php', 'editar','".$_POST['cedula']."','".$_SESSION['nombre'] ."', '".$host_ip."')";

		    $res = $db->query($sql);	
		}

		
		echo "<script>document.location.href = 'ag_integrantes.php?ficha=".$_POST['ficha']."&edit';</script>";
	}
	else {
		echo "<script>alert('¡Hay errores en el proceso!');</script>";
    }
}
