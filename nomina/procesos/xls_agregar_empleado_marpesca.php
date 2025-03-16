<?php
session_start();
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('America/Caracas');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once("../lib/common.php");
//require_once("../paginas/func_bd.php");
require_once("../paginas/phpexcel/Classes/PHPExcel.php");
require_once("../paginas/phpexcel/Classes/PHPExcel/IOFactory.php");
$conexion = new bd($_SESSION['bd']);

// Inicializar variables
$success  = true;
$mensaje  = '';

// Validar posibles errores: 

if($_FILES['archivo']['name'] != '')
{
	$name	  = $_FILES['archivo']['name'];
	$tname 	  = $_FILES['archivo']['tmp_name'];
	$type 	  = $_FILES['archivo']['type'];

	if($type == 'application/vnd.ms-excel')
		$ext = 'xls'; // Extension excel 97
	else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
		$ext = 'xlsx'; // Extension excel 2007 y 2010
	else{

		if (function_exists('finfo_file')) 
		{
			$type  = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tname);

			if($type == 'application/vnd.ms-excel')
				$ext = 'xls'; // Extension excel 97
			else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
				$ext = 'xlsx'; // Extension excel 2007 y 2010		
		}
		// else
		// 	alert("¡Error! No se encuentra disponible la funcion finfo_file. Active la extensión fileinfo en su archivo php.ini");

		if(!isset($ext))
		{
			$mensaje = "¡Error! Extensión de archivo inválida.";
			$success = false;
		}
	}

	if(isset($ext))
	{
		$xls  = 'Excel5';
		$xlsx = 'Excel2007';

		// Creando el lector
		$objReader = PHPExcel_IOFactory::createReader($$ext);

		// Cargamos el archivo
		$objPHPExcel = $objReader->load($tname);

		$objPHPExcel->setActiveSheetIndex(0);

		$i=2;
		while($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue() != '')
		{
			$letra            = "A";
			$ficha            = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Numero de Ficha - Requerido
			$letra++;
			$planilla         = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Planilla
			$letra++;
			$primer_nombre    = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Primer nombre
			$primer_nombre = utf8_decode($primer_nombre);
			$letra++;
			$segundo_nombre   = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Sendo Nombre
			$segundo_nombre = utf8_decode($segundo_nombre);
			$letra++;
			$primer_apellido  = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Primer Apellido
			$primer_apellido = utf8_decode($primer_apellido);
			$letra++;
			$segundo_apellido = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Segundo Apellido
			$segundo_apellido = utf8_decode($segundo_apellido);
			$letra++;
			$apellido_casada  = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // apellido Casada
			$apellido_casada = utf8_decode($apellido_casada);
			$letra++;
			$cedula           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Cedula
			$letra++;
			$estado_civil     = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Estado Civil
			$letra++;
			$telefono         = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Telefono
			$letra++;
			//$fecnac           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue();  // Fecha Nac (dia/mes/año => 21/03/2015)
			
			$fecnac           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getFormattedValue();
			$letra++;
			$lugarnac         = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Lugar
			$letra++;
			$nacionalidad     = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Lugar
			$nacionalidad     = ($nacionalidad == "Panameño") ? 1 : 2 ;
			$letra++;
			$sexo             = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue();  // Fecha de Ingreso
			$letra++;
			$fecing           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getFormattedValue();  // Fecha de Ingreso
			$letra++;
			$suesal           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Salario
			$letra++;
			$hora_base        = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Horas Base
			$letra++;
			$estado           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Status
			$letra++;
			$seguro_social    = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Seguro Social
			$letra++;
			$codcargo         = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Cargo
			$letra++;
			$direccion_hab    = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Dirección
			$letra++;
			$discapacidad     = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Discapacidad
			$discapacidad     = ($discapacidad == "Si") ? 1 : 0 ;

			$letra++;
			$fam_discapacidad = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Familiar con Discapacidad
			$fam_discapacidad = ($fam_discapacidad == "Si") ? 1 : 0 ;

			$letra++;
			$marca_reloj      = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Marca Reloj
			$marca_reloj      = ($marca_reloj == "Si") ? 1 : 0 ;

			$letra++;
			$email            = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Email
			$letra++;

			$categoria        = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Categoria			
			$categoria        = ($categoria == "Empleado") ? 2 : 3;

			$letra++;
			$nivel1           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Nivel
			$letra++;
			$nivel2           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Nivel
			$letra++;
			$nivel3           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Nivel
			$letra++;
			$turnos           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Turnos
			$letra++;
			$foto             = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Foto
			$foto = "fotos/".$ficha."_".$foto;
			$letra++;
			$forma_pago       = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Forma Pago
			$letra++;
			$tipoempleado     = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Tipo Empleado
			
			if ($tipoempleado == "Contrato Indefinido") {
				$tipoempleado = "Fijo";
			}
			$letra++;
			$fecha_inicio     = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Inicio Contrato
			$letra++;
			$fecha_fin        = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Fin Contrato
			$letra++;
			$gastos_representacion  = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Fin Contrato
			$letra++;
			$codbanco         = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Fin Contrato
			$letra++;
			$cuenta_banco     = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Fin Contrato
			$letra++;
			$dv               = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Fin Contrato
			$letra++;
			$clave_ir         = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Fin Contrato
			$letra++;
			$i++;

			//===============================================================================================================================
				//echo "Fecha nacimiento => ". $fecnac . " Fecha Ingreso: ".$fecing;
				$fecnac = str_replace('/', '-', $fecnac);
				$fecnac = DateTime::createFromFormat('m-d-y', $fecnac); // DateTime::createFromFormat('d-m-Y', $fecnac);
				$errorDatetime = DateTime::getLastErrors();
				$fecnac = ($errorDatetime['warning_count'] > 0) ? null :  $fecnac->format('Y-m-d') ;
				//$fecnac = convertir_fecha( $fecnac );
				//echo "Fecha nacimiento => ". $fecnac; exit;

				$fecing = str_replace('/', '-', $fecing);
				$fecing = DateTime::createFromFormat('m-d-y', $fecing); //DateTime::createFromFormat('d-m-Y', $fecing);
				$errorDatetime = DateTime::getLastErrors();
				$fecing = ($errorDatetime['warning_count'] > 0) ? null :  $fecing->format('Y-m-d') ;
				//$fecing = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($fecing)) ;

				$apenom = $primer_apellido.' , '.$primer_nombre;
			//===============================================================================================================================		
			## Registro del empleado
			
			$sql="INSERT INTO nompersonal
					(ficha,
					tipnom,
					nombres,
					nombres2,
					apellidos,
					apellido_materno,
					apellido_casada,
					apenom,
					cedula,
					estado_civil,
					telefonos,
					fecnac,
					lugarnac,
					nacionalidad,
					fecing,
					suesal,
					sueldopro,
					hora_base,
					estado,
					sexo,
					seguro_social,
					clave_ir,
					codcargo,
					direccion,
					tiene_familiar_disca,
					tiene_discapacidad,
					marca_reloj,
					email,
					codcat,
					codnivel1,
					codnivel2,
					codnivel3,
					turno_id,
					foto,
					forcob,
					tipemp,
					inicio_periodo,
					fin_periodo,
					usuario_workflow,
					usr_password,
					gastos_representacion,
					codbancob,
					cuentacob,
					dv
					) 
				VALUES (
				'{$ficha}',
				'{$planilla}',
				'{$primer_nombre}',
				'{$segundo_nombre}',
				'{$primer_apellido}',
				'{$segundo_apellido}',
				'{$apellido_casada}',
				'{$apenom}',
				'{$cedula}',
				'{$estado_civil}',
				'{$telefono}',
				'{$fecnac}',
				'{$lugarnac}',
				'{$nacionalidad}',
				'{$fecing}',
				'{$suesal}',
				'{$suesal}',
				'{$hora_base}',
				'{$estado}',
				'{$sexo}',
				'{$seguro_social}',
				'{$clave_ir}',
				'{$codcargo}',
				'{$direccion_hab}',
				'{$fam_discapacidad}',
				'{$discapacidad}',
				'{$marca_reloj}',
				'{$email}',
				'{$categoria}',
				'{$nivel1}',
				'{$nivel2}',
				'{$nivel3}',
				'{$turnos}',
				'{$foto}',
				'{$forma_pago}',
				'{$tipoempleado}',
				'{$fecha_inicio}',
				'{$fecha_fin}',
				'{$cedula}',
				MD5('12345'),
				'{$gastos_representacion}',
				'{$codbanco}',
				'{$cuenta_banco}',
				'{$dv}');";		
			$res = $conexion->query($sql);
            
            
			if(!$res)
			{
				$success = false;

				$mensaje = "¡Error al registrar los empleados! (Código: ".$conexion->getCodigoError().") ".$conexion->getMensajeError();
				
				if($conexion->getCodigoError() == '1062')
				{
					$mensaje = "¡Error! Existen empleados duplicados (Ficha No: ".$ficha.")";
				}

				break;
			}	
		}

		if($i==2)
			$mensaje = "Error al importar los datos. La celda A$i no contiene datos.";
		else if($success)
			$mensaje = "Datos importados exitosamente";
	}
}
else
{
	$mensaje = "Error al cargar el archivo";
	$success = false;
}

echo "%&&%".($success ? '1' : '0')."&&&&".$mensaje;
?>
