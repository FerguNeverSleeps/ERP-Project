<?php 
session_start();
ob_start();
//$termino=$_SESSION['termino'];
	//include ("../header.php");
	include("../../lib/common.php");
	include("../paginas/func_bd.php");
        $conexion2=conexion();
        //require_once('../lib/database.php');

//$db = new Database($_SESSION['bd']);

?>

<?php
	if($_GET['codigo']!='')
	{
		$consulta="SELECT * FROM expediente WHERE cedula='$_GET[cedula]' AND cod_expediente_det=$_GET[codigo]";
		$resultado555=sql_ejecutar($consulta);
		$fetch33=fetch_array($resultado555);
	}
	if($_GET['cedula']!='')
	{
		$consulta="SELECT * FROM nompersonal WHERE cedula='$_GET[cedula]'";
		$resultado66=sql_ejecutar($consulta);
		$fetch66=fetch_array($resultado66);
                $nombre=$fetch66['apenom'];
                $cedula=$_GET[cedula];
                $cod_car=$fetch66['codcargo'];
                $nomposicion_id=$fetch66['nomposicion_id'];
                $seguro_social=$fetch66['seguro_social'];
                $tipnom=$fetch66['tipnom'];
                $idDepartamento=$fetch66['IdDepartamento'];      
                $cuenta_contable=$fetch66['ctacontab'];
                $tipo_funcionario=$fetch66['tipo_funcionario'];    
                $nomfuncion_id=$fetch66['nomfuncion_id'];   
                $salario=$fetch66['suesal'];   
                $gastos_representacion=$fetch66['gastos_representacion'];
                $fecha_inicio=$fetch66['fecing'];
                $codnivel1=$fetch66['codnivel1'];
                
                //Consulta Tipo Empleado
                $consulta_empleado="SELECT * FROM tipoempleado WHERE IdTipoEmpleado='$tipo_funcionario'";
                $resultado_empleado=sql_ejecutar($consulta_empleado);
                $fetch_empleado=fetch_array($resultado_empleado);
                $tipo_empleado=$fetch_empleado['Descripcion'];
                
                 //Consulta Funcion
                $consulta_funcion="SELECT * FROM nomfuncion WHERE nomfuncion_id='$nomfuncion_id'";
                $resultado_funcion=sql_ejecutar($consulta_funcion);
                $fetch_funcion=fetch_array($resultado_funcion);
                $funcion=$fetch_funcion['descripcion_funcion'];
                
                //Consulta Cargo
                $consulta_cargo="SELECT des_car FROM nomcargos WHERE cod_car='$cod_car'";
                $resultado_cargo=sql_ejecutar($consulta_cargo);
                $fetch_cargo=fetch_array($resultado_cargo);
                $cargo=$fetch_cargo['des_car'];
                
                //Consulta Posicion
                $consulta_posicion="SELECT * FROM nomposicion WHERE nomposicion_id='$nomposicion_id'";
                $resultado_posicion=sql_ejecutar($consulta_posicion);
                $fetch_posicion=fetch_array($resultado_posicion);
                $posicion=$fetch_posicion['nomposicion_id'];
                $partida=$fetch_posicion['partida'];
                
                
                 //Consulta Planilla
                
                $consulta_planilla="SELECT descrip FROM nomtipos_nomina WHERE codtip='$tipnom'";               
                $resultado_planilla=sql_ejecutar($consulta_planilla);
                $fetch_planilla=fetch_array($resultado_planilla);
                $pplanilla=$fetch_planilla['descrip'];
                
                //Consulta Departamento
                
                $consulta_departamento="SELECT Descripcion FROM departamento WHERE IdDepartamento='$idDepartamento'";               
                $resultado_departamento=sql_ejecutar($consulta_departamento);
                $fetch_departamento=fetch_array($resultado_departamento);
                $departamento=$fetch_departamento['Descripcion'];
                
               
                
                
	}
	$cedula=$_GET['cedula'];
        $codigo=$_GET['codigo'];
	$opcion=$fetch33['tipo'];
        
        //echo $opcion;
	switch ($opcion)
	{	
               
                case '21':                     
		   
                    include 'pdf/analisis_cambio_categoria_pdf.php'; 
                        
		break;
                case '22':
                       
                    include 'pdf/analisis_cambio_etapa_pdf.php';
                        
		break;
                
                case '23':
                       
                    include 'pdf/vigencias_expiradas_pdf.php';
                        
		break;
                
	}
        
        
?>


