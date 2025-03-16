<?php 
session_start();
ob_start();
include("../../lib/common.php");
include("../paginas/func_bd.php");
$conexion2=conexion();
?>

<?php
sql_ejecutar("SET names utf8;");
	if($_GET['codigo']!='')
	{
		$consulta="SELECT * FROM expediente WHERE cedula='$_GET[cedula]' AND cod_expediente_det=$_GET[codigo]";
		$resultado555=sql_ejecutar($consulta);
		$fetch33=fetch_array($resultado555);
        $codigo=$_GET['codigo'];
	}
	if($_GET['cedula']!='')
	{
		$consulta="SELECT * FROM nompersonal WHERE cedula='$_GET[cedula]'";
		$resultado66=sql_ejecutar($consulta);
		$fetch66=fetch_array($resultado66);
                $nombre=$fetch66['apenom'];
                $cedula=$_GET['cedula'];
                $ficha=$fetch66['ficha'];
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
                $fecha_permanencia = $fetch66['fecha_permanencia'];
                $codnivel1=intval($fetch66['codnivel1']);
                $codnivel2=intval($fetch66['codnivel2']);
                $codnivel3=intval($fetch66['codnivel3']);
                $numero_decreto_ingreso=intval($fetch66['num_decreto']);
                $fecha_decreto_ingreso=$fetch66['fecha_decreto'];
                $numero_resuelto_baja=intval($fetch66['num_resolucion_baja']);
                $fecha_resuelto_baja=$fetch66['fecha_resolucion_baja'];
                
                //Consulta Gerencia
                $consulta_gerencia="SELECT codorg, descrip FROM nomnivel1 WHERE codorg='$codnivel1'";                
                $resultado_gerencia=sql_ejecutar($consulta_gerencia);
                $fetch_gerencia=fetch_array($resultado_gerencia);
                $nivel1=$fetch_gerencia['descrip'];
                
                $consulta_departamento="SELECT codorg, descrip FROM nomnivel2 WHERE codorg='$codnivel2'";                
                $resultado_departamento=sql_ejecutar($consulta_departamento);
                $fetch_departamento=fetch_array($resultado_departamento);
                $nivel2=$fetch_departamento['descrip'];
                
                //Consulta Departamento
                
                $consulta_departamento="SELECT Descripcion FROM departamento WHERE IdDepartamento='$idDepartamento'";               
                $resultado_departamento=sql_ejecutar($consulta_departamento);
                $fetch_departamento=fetch_array($resultado_departamento);
                $departamento=$fetch_departamento['Descripcion'];
                
                $consulta_seccion="SELECT codorg, descrip FROM nomnivel3 WHERE codorg='$codnivel3'";                
                $resultado_seccion=sql_ejecutar($consulta_seccion);
                $fetch_seccion=fetch_array($resultado_seccion);
                $nivel3=$fetch_seccion['descrip'];
                
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
                
                
                
               
                
                
	}
	$periodo = $_GET['periodo'];
	$frecuencia = $_GET['frecuencia'];
	$opcion=$_GET['opcion'];
        
    $consulta="SELECT * FROM expediente_subtipo where id_expediente_tipo='$opcion' ORDER BY id_expediente_subtipo ASC";
        $resultado=query($consulta,$conexion2);
	switch ($opcion)
	{	
                case '1':   
                    
                    include 'estudio_academico.php';  
                    
		break;
		
		case '2':
                        
                    include 'capacitacion.php';
                    
		break;

		case '4':
			
		    include 'permiso.php';
                    
		break;
                
                case '5':
                    
                    include 'amonestacion.php';
			
		break;
                
                case '6':
                    
                    include 'suspension.php';
			
		break;
                
                case '7':
                    
                    include 'renuncia.php';
			
		break;
                
                case '8':                       
                    
                     include 'destitucion.php';
                    
		break;
			
		case '9':
                     include 'movimiento.php';
		break;

		case '10':
                    
                    include 'evaluacion.php';                    
                    
		break;
            
                case '11':
                    
                    include 'vacacion.php';
                    
		break;
		case '12':
		
                    include 'tiempo_compensatorio.php';
                    
		break;
		case '13':
                    
                    include 'documento.php';
                    
		break;
		case '14':
                     
                    include 'experiencia.php';
                        
		break;
                case '15':
		case '16':
                case '17':    
                    
                    include 'licencia.php';
                    
		break;
                case '18':                       
                    
                    include 'observacion.php';
                    
		break;
                case '19':
                       
                    include 'baja.php';
                    
		break;
                case '20':
                       
                    include 'suspension_pago.php';
                    
		break;
                case '21':
                       
		    include 'analisis_cambio_categoria.php';
                        
		break;
                case '22':
                       
                    include 'analisis_cambio_etapa.php';
                        
		break;
                
                case '23':
                       
                    include 'vigencias_expiradas.php';
                        
		break;
                
                case '24':
                case '25':
                case '26':                       
                    include 'apoyo_reasignacion_rotacion.php';
                        
		break;
            
                case '27':
                       
                    include 'ajuste_tiempo.php';
                
                break;
                
                case '28':

                    include 'mision_oficial.php';
                        
		break;
                
                case '29':

                    include 'certificacion_trabajo.php';
                        
		break;
                
                case '30':

                    include 'ascenso.php';
                        
		break;
            
                case '31':

                    include 'aumento_ajuste_salarial.php';
                        
		break;
                
                case '32':

                    include 'revocatoria.php';
                        
		break;                
                
                case '33':

                    include 'modificacion_decreto.php';
                        
		break;       
            
                case '34':
                       
                    include 'excedente_planilla.php';
                        
		break;
                
                case '35':
                       
                    include 'jubilacion.php';
                        
		break;
                
                case '36':
                       
                    include 'prorroga_continuacion.php';
                        
		break;
            
                case '37':
                       
                    include 'inicio_labores.php';
                        
		break;
            
                case '38':
                       
                    include 'cambio_nombre_apellido.php';
                        
		break;
                
                case '39':
                       
                    include 'defuncion.php';
                        
		break;
            
                case '40':
                       
                    include 'reincorporacion.php';
                        
		break;
                
                case '41':
                       
                    include 'reclasificacion.php';
                        
		break;
                
                case '42':
                       
                    include 'aumento_horas.php';
                        
		break;
                
                case '43':
                       
                    include 'libre_nombramiento_remocion.php';
                        
		break;
            
                case '44':
                       
                    include 'cambio_categoria.php';
                        
		break;
                
                case '45':
                       
                    include 'retorno_titular.php';
                        
		break;
                
                case '46':
                       
                    include 'terminacion_contrato.php';
                        
		break;
                
                case '47':
                       
                    include 'aplicacion_reglamento.php';
                        
		break;
            
                case '48':
                       
                    include 'pensionado.php';
                        
		break;
            
                case '49':
                       
                    include 'terminacion_nombramiento_licencia.php';
                        
		break;
            
                case '50':
                       
                    include 'terminacion_periodo.php';
                        
		break;
            
                case '51':
                       
                    include 'terminacion_nombramiento_transitorio.php';
                        
		break;
            
                case '52':
                       
                    include 'cese_labores.php';
                        
		break;
            
                case '53':
                       
                    include 'interinos.php';
                        
		break;
            
                case '54':
                       
                    include 'abandono_cargo.php';
                        
		break;
                case '56':
                       
                    include 'aumentese_asciendase_trasladese.php';
                        
		break;
                case '57':
                       
                    include 'baja_oficial.php';
                        
		break;
                case '58':
                       
                    include 'reintegro.php';
                        
		break;
                case '59':
                       
                    include 'carrera_migratoria.php';
                        
		break;
                case '61':
                       
                    include 'investigacion.php';
                        
		break;
            
                case '62':
                       
                    include 'extemporanea.php';
                break; 
            
                case '63':                       
                    include 'diagnostico_enfermedad.php';
                        
		break;
            
                case '64':                       
                    include 'evaluacion_antecedente.php';
                        
		break;
            
                case '65':                       
                    include 'trabajo_social.php';
                        
		break;
                
                case '66':                       
                    include 'renovacion_contrato.php';
                        
		break;
            
                case '67':                       
                    include 'renovacion_cargo.php';
                        
		break;
            
        case '68':                       
            include 'registro_contrado.php';
        break;
            
        case '69':                       
            include 'curriculo.php';
        break;
            
        case '70':                       
            include 'curriculo.php';
        break;
            
        case '71':                       
            include 'curriculo.php';
        break;
            
        case '72':                       
            include 'curriculo.php';
        break;
            
        case '73':                       
            include 'curriculo.php';
        break;
            
        case '74':                       
            include 'curriculo.php';
        break;
            
        case '75':                       
            include 'curriculo.php';
        break;
            
        case '76':                       
            include 'curriculo.php';
        break;
            
        case '77':                       
            include 'curriculo.php';
        break;
            
        case '78':                       
            include 'curriculo.php';
        break;
            
        case '79':                       
            include 'curriculo.php';
        break;

        case '80':
            include 'entrega_kit.php';
        break;

        case '81':
            include 'reporte_incidente.php';
        break;

        case '82':
            include 'solicitud_empleo.php';
        break;
        
        case '83':
            include 'form_compras_rhexpress.php';
        break;

        case '84':
            include 'form_actualizacion_anual_rhexpress.php';
        break;
                
                
	}
        
        
?>


