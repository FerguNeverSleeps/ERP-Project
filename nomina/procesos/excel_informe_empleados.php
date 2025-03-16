<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
//include ("../paginas/funciones_nomina.php");

//include('../lib/common_excel.php');
//include('../paginas/lib/php_excel.php');
//require_once '../paginas/func_bd.php';
//include ("../paginas/funciones_nomina.php");

$conexion=conexion();

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("excel_informe_empresas.xlsx");
//------------------------------------------------------------
$anio = $_REQUEST['anio'];
$mes    = $_REQUEST['mes'];
$opcion    = $_REQUEST['opcion'];

$consulta_sipe           = "SELECT * FROM configuracion_sipe_css "
                                . "WHERE id_configuracion=1";
$resultado_sipe         =query($consulta_sipe,$conexion);
$fila_sipe              =fetch_array($resultado_sipe,$conexion);
$codigo                         = trim($fila_sipe["id_configuracion"]);
$con_sueldo                     = trim($fila_sipe["con_sueldo"]);
$con_horas_extras               = trim($fila_sipe["con_horas_extras"]);
$con_impuesto_renta             = trim($fila_sipe["con_impuesto_renta"]);
$con_decimo_tercer_mes          = trim($fila_sipe["con_decimo_tercer_mes"]);
$con_vacaciones                 = trim($fila_sipe["con_vacaciones"]);
$con_comisiones                 = trim($fila_sipe["con_comisiones"]);
$con_bonificaciones             = trim($fila_sipe["con_bonificaciones"]);
$con_combustible                = trim($fila_sipe["con_combustible"]);
$con_dieta                      = trim($fila_sipe["con_dieta"]);
$con_salario_especies           = trim($fila_sipe["con_salario_especies"]);
$con_viaticos                   = trim($fila_sipe["con_viaticos"]);
$con_gasto_representacion       = trim($fila_sipe["con_gasto_representacion"]);
$con_impuesto_renta_gr          = trim($fila_sipe["con_impuesto_renta_gr"]);
$con_decimo_tercer_mes_gr       = trim($fila_sipe["con_decimo_tercer_mes_gr"]);
$con_prima_produccion           = trim($fila_sipe["con_prima_produccion"]);
$con_dividendo                  = trim($fila_sipe["con_dividendo"]);
$con_beneficio_ingreso          = trim($fila_sipe["con_beneficio_ingreso"]);
$con_gratificacion_aguinaldo    = trim($fila_sipe["con_gratificacion_aguinaldo"]);
$con_preaviso                   = trim($fila_sipe["con_preaviso"]);
$con_indemnizacion              = trim($fila_sipe["con_indemnizacion"]);
$con_tardanza                   = trim($fila_sipe["con_tardanza"]);
$con_ausencia                   = trim($fila_sipe["con_ausencia"]);

//echo $con_horas_extras;
//exit;
//$con_sueldo="90,100,115,140,169,170";
//$con_horas_extras="105,106,107,108,109,110,111,112,113,116,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,"
//        . "138,139,142,143,144,147,149,150,151,152,153,154,155,158,159,160,161,162,163,164,165,166,167,168,171,172";
//$con_impuesto_renta="202,211,213,214,215";
//$con_decimo_tercer_mes="92,102";
//$con_vacaciones="91,114,117";
//$con_comisiones="141,157";
//$con_bonificaciones="189,190";
//$con_combustible="156";
//$con_dieta="148";
//$con_salario_especies="180";
//$con_viaticos="181";
//$con_gasto_representacion="145";
//$con_impuesto_renta_gr="208";
//$con_decimo_tercer_mes_gr="101";
//$con_prima_produccion="182";
//$con_dividendo="183";
//$con_beneficio_ingreso="184";
//$con_gratificacion_aguinaldo="185";
//$con_preaviso="95";
//$con_indemnizacion="94,96,97";
//$con_tardanza="199";
//$con_ausencia="198";


if($opcion==1)
{
    $consulta_tabla= "SELECT a.id "
          . " FROM ach_sipe as a"
          . " WHERE a.mes='$mes' AND a.anio='$anio' ";
    $resultado_tabla=query($consulta_tabla,$conexion);
    $fila_tabla=fetch_array($resultado_tabla,$conexion);
    $id=$fila_tabla['id'];
    
//    echo $id;
//    exit;
    
    if($id!=0 && $id!=NULL && $id!='')
    {
        $consulta_delete1= "DELETE FROM ach_sipe "
          . " WHERE id='$id'";
        $resultado_delete1=query($consulta_delete1,$conexion);
        
        $consulta_delete2= "DELETE FROM ach_sipe_detalle "
          . " WHERE id='$id'";
        $resultado_delete2=query($consulta_delete2,$conexion);
    }
    
    //    $sql_nominas = "SELECT codnom,tipnom 
//                    FROM nom_nominas_pago 
//                    WHERE YEAR(periodo_fin) = '{$anio}' AND MONTH(periodo_fin) = '{$mes}'";
                    
    $sql_nominas = "SELECT codnom,tipnom 
                    FROM nom_nominas_pago 
                    WHERE anio_sipe = '{$anio}' AND mes_sipe = '{$mes}'";
    $res_nominas=query($sql_nominas,$conexion);
    //        echo $sql_nominas;
    //        exit;
    $nominas = array();
    $tipos = array();
    $codnom=$codtip="";

    while ( $fila_nominas=fetch_array($res_nominas,$conexion)) 
    {
            array_push($nominas,$fila_nominas[codnom]);
            array_push($tipos,$fila_nominas[tipnom]);
    }

    for ($jxx=0; $jxx < count($nominas); $jxx++) 
    { 
            if($jxx==(count($nominas)-1))
            {
                    $codnom .= $nominas[$jxx];
                    $codtip .= $tipos[$jxx];
            }
            else
            {
                    $codnom .= $nominas[$jxx].",";
                    $codtip .= $tipos[$jxx].",";
            }
    }
 
    
    $fecha_creacion = date("Y-m-d");
    $fecha = date("Y-m-d");
    $usuario = $_SESSION['usuario'];

    $consulta_sipe="INSERT INTO ach_sipe "
                . "(id, "             
                . "anio, "
                . "mes, "
                . "fecha, "
                . "usuario_creacion, "
                . "fecha_creacion, "
                . "usuario_edicion, "
                . "fecha_edicion) "
                . "VALUES "
                . "('', "               
                . "'".$anio."',"
                . "'".$mes."',"
                . "'".$fecha."',"
                . "'".$usuario."',"
                . "'".$fecha_creacion."',"
                . "'".$usuario."',"
                . "'".$fecha_creacion."')";
    $resultado_sipe=query($consulta_sipe,$conexion);
    $ultimo_id = mysqli_insert_id($conexion);
    //echo $ultimo_id;
    //exit;
    
    $consulta=" SELECT np.apenom, np.telefonos, np.nombres, np.apellidos, np.sexo, np.nacionalidad,
                np.seguro_social, np.cedula, np.ficha, np.clave_ir 
                FROM nompersonal np 
                WHERE np.ficha IN
                (
                    SELECT DISTINCT ficha 
                    FROM nom_movimientos_nomina as mn
                    LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom)
                    WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}')
                )
                ORDER by np.cedula DESC";

    $resultado2=query($consulta,$conexion);

    while($fila=fetch_array($resultado2,$conexion))
    {
            $cod=$cod2="";
            //SUELDO
            $consulta_sueldo="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_sueldo}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_sueldo=query($consulta_sueldo,$conexion);
            $fila_sueldo=fetch_array($resultado_sueldo,$conexion);
            $sueldo=$fila_sueldo['monto'];
            
            //HORAS EXTRAS
            $consulta_horas_extras="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_horas_extras}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_horas_extras=query($consulta_horas_extras,$conexion);
            $fila_horas_extras=fetch_array($resultado_horas_extras,$conexion);
            $horas_extras=$fila_horas_extras['monto'];
            
            //IMPUESTO RENTA
            $consulta_impuesto_renta="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_impuesto_renta}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_impuesto_renta=query($consulta_impuesto_renta,$conexion);
            $fila_impuesto_renta=fetch_array($resultado_impuesto_renta,$conexion);
            $impuesto_renta=$fila_impuesto_renta['monto'];
            
            //DECIMO TERCER MES
            $consulta_decimo_tercer_mes="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_decimo_tercer_mes}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_decimo_tercer_mes=query($consulta_decimo_tercer_mes,$conexion);
            $fila_decimo_tercer_mes=fetch_array($resultado_decimo_tercer_mes,$conexion);
            $decimo_tercer_mes=$fila_decimo_tercer_mes['monto'];
            
            //VACACIONES
            $consulta_vacaciones="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_vacaciones}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_vacaciones=query($consulta_vacaciones,$conexion);
            $fila_vacaciones=fetch_array($resultado_vacaciones,$conexion);
            $vacaciones=$fila_vacaciones['monto'];
            
            //COMISIONES
            $consulta_comisiones="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_comisiones}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_comisiones=query($consulta_comisiones,$conexion);
            $fila_comisiones=fetch_array($resultado_comisiones,$conexion);
            $comisiones=$fila_comisiones['monto'];
            
            //BONIFICACIONES
            $consulta_bonificaciones="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_bonificaciones}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_bonificaciones=query($consulta_bonificaciones,$conexion);
            $fila_bonificaciones=fetch_array($resultado_bonificaciones,$conexion);
            $bonificaciones=$fila_bonificaciones['monto'];
            
            //COMBUSTIBLE
            $consulta_combustible="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_combustible}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_combustible=query($consulta_combustible,$conexion);
            $fila_combustible=fetch_array($resultado_combustible,$conexion);
            $combustible=$fila_combustible['monto'];
            
            //DIETA
            $consulta_dieta="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_dieta}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_dieta=query($consulta_dieta,$conexion);
            $fila_dieta=fetch_array($resultado_dieta,$conexion);
            $dieta=$fila_dieta['monto'];
            
            //SALARIO ESPECIES
            $consulta_salario_especies="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_salario_especies}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_salario_especies=query($consulta_salario_especies,$conexion);
            $fila_salario_especies=fetch_array($resultado_salario_especies,$conexion);
            $salario_especies=$fila_salario_especies['monto'];
            
            //VIATICOS
            $consulta_viaticos="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_viaticos}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_viaticos=query($consulta_viaticos,$conexion);
            $fila_viaticos=fetch_array($resultado_viaticos,$conexion);
            $viaticos=$fila_viaticos['monto'];
            
            //GASTOS REPRESENTACION
            $consulta_gasto_representacion="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_gasto_representacion}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_gasto_representacion=query($consulta_gasto_representacion,$conexion);
            $fila_gasto_representacion=fetch_array($resultado_gasto_representacion,$conexion);
            $gasto_representacion=$fila_gasto_representacion['monto'];
            
            //IMPUESTO RENTA GASTOS REPRESENTACION
            $consulta_impuesto_renta_gr="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_impuesto_renta_gr}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_impuesto_renta_gr=query($consulta_impuesto_renta_gr,$conexion);
            $fila_impuesto_renta_gr=fetch_array($resultado_impuesto_renta_gr,$conexion);
            $impuesto_renta_gr=$fila_impuesto_renta_gr['monto'];
            
            //DECIMO TERCER MES GASTOS REPRESENTACION
            $consulta_decimo_tercer_mes_gr="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_decimo_tercer_mes_gr}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_decimo_tercer_mes_gr=query($consulta_decimo_tercer_mes_gr,$conexion);
            $fila_decimo_tercer_mes_gr=fetch_array($resultado_decimo_tercer_mes_gr,$conexion);
            $decimo_tercer_mes_gr=$fila_decimo_tercer_mes_gr['monto'];
            
            //PRIMA PRODUCCION
            $consulta_prima_produccion="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_prima_produccion}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_prima_produccion=query($consulta_prima_produccion,$conexion);
            $fila_prima_produccion=fetch_array($resultado_prima_produccion,$conexion);
            $prima_produccion=$fila_prima_produccion['monto'];
            
            //DIVIDENDO
            $consulta_dividendo="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_dividendo}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_dividendo=query($consulta_dividendo,$conexion);
            $fila_dividendo=fetch_array($resultado_dividendo,$conexion);
            $dividendo=$fila_dividendo['monto'];
            
            //BENEFICIO INGRESO
            $consulta_beneficio_ingreso="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_beneficio_ingreso}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_beneficio_ingreso=query($consulta_beneficio_ingreso,$conexion);
            $fila_beneficio_ingreso=fetch_array($resultado_beneficio_ingreso,$conexion);
            $beneficio_ingreso=$fila_beneficio_ingreso['monto'];
            
             //GRATIFICACION AGUINALDO
            $consulta_gratificacion_aguinaldo="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_gratificacion_aguinaldo}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_gratificacion_aguinaldo=query($consulta_gratificacion_aguinaldo,$conexion);
            $fila_gratificacion_aguinaldo=fetch_array($resultado_gratificacion_aguinaldo,$conexion);
            $gratificacion_aguinaldo=$fila_gratificacion_aguinaldo['monto'];
            
             //PREAVISO
            $consulta_preaviso="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_preaviso}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_preaviso=query($consulta_preaviso,$conexion);
            $fila_preaviso=fetch_array($resultado_preaviso,$conexion);
            $preaviso=$fila_preaviso['monto'];
            
            //INDEMNIZACION
            $consulta_indemnizacion="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_indemnizacion}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_indemnizacion=query($consulta_indemnizacion,$conexion);
            $fila_indemnizacion=fetch_array($resultado_indemnizacion,$conexion);
            $indemnizacion=$fila_indemnizacion['monto'];

            $consulta_tardanza="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_tardanza}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_tardanza=query($consulta_tardanza,$conexion);
            $fila_tardanza=fetch_array($resultado_tardanza,$conexion);
            $tardanza=$fila_tardanza['monto'];
            
            $consulta_ausencia="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_ausencia}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta_salario_bruto;
    //        exit;	
            $resultado_ausencia=query($consulta_ausencia,$conexion);
            $fila_ausencia=fetch_array($resultado_ausencia,$conexion);
            $ausencia=$fila_ausencia['monto'];
            
            $sueldo = $sueldo - ($tardanza + $ausencia);
            
           

            $posicion = $fila[ficha];
            
            $nacionalidad = $fila[nacionalidad];
            
            $seguro_social = $fila[seguro_social];
            
            
            $numero_documento = $fila[cedula];
            $tipo_documento="Cédula";
//            $cadena_buscada='-';
//            $posicion = strpos($numero_documento, $cadena_buscada);
            if ($nacionalidad ==2) 
            {
                $tipo_documento="Pasaporte";
            }
           
            $apellido=str_replace(",","",$fila['apellidos']);
            $apellido=str_replace("  "," ",$apellido);
            $apellido=str_replace("Ñ"," ",$apellido);

            if(strlen($apellido)<14)
            {	
                    $i=strlen($apellido);
                    while($i<14)
                    {
                            $apellido.=" ";
                            $i+=1;
                    }
            }
            elseif(strlen($apellido)>14)
            {
                    $apellido=substr($apellido,0,14);
            }


            $nombre=str_replace(",","",$fila['nombres']);
            $nombre=trim($nombre);

            if(strlen($nombre)<14)
            {	
                    $i=strlen($nombre);
                    while($i<14)
                    {
                            $nombre.=" ";
                            $i+=1;
                    }
            }
            elseif(strlen($nombre)>14)
            {
                    $nombre=substr($nombre,0,14);
            }
            
            $consulta_sipe_detalle="INSERT INTO ach_sipe_detalle "
                . "(id, "
                . "id_detalle, "
                . "posicion, "
                . "tipo_documento, "
                . "numero_documento, "
                . "seguro_social, "
                . "nombre, "
                . "apellido, "
                . "sueldo, "
                . "horas_extras, "
                . "impuesto_renta, "
                . "decimo_tercer_mes, "
                . "vacaciones, "
                . "comisiones, "
                . "bonificaciones, "
                . "combustible, "
                . "dieta, "
                . "salario_especies, "
                . "viaticos, "
                . "gasto_representacion, "
                . "impuesto_renta_gr, "
                . "decimo_tercer_mes_gr, "
                . "prima_produccion, "                
                . "dividendo, "
                . "beneficio_ingreso, "
                . "gratificacion_aguinaldo, "
                . "preaviso, "
                . "indmenizacion) "
                . "VALUES "
                . "('".$ultimo_id."', "
                . "'',"
                . "'".$posicion."',"
                . "'".$tipo_documento."',"
                . "'".$numero_documento."',"
                . "'".$seguro_social."',"
                . "'".$nombre."',"
                . "'".$apellido."',"
                . "'".$sueldo."',"
                . "'".$horas_extras."',"
                . "'".$impuesto_renta."',"
                . "'".$decimo_tercer_mes."',"
                . "'".$vacaciones."',"
                . "'".$comisiones."',"
                . "'".$bonificaciones."',"
                . "'".$combustible."',"
                . "'".$dieta."',"
                . "'".$salario_especies."',"
                . "'".$viaticos."',"
                . "'".$gasto_representacion."',"
                . "'".$impuesto_renta_gr."',"
                . "'".$decimo_tercer_mes_gr."',"
                . "'".$prima_produccion."',"
                . "'".$dividendo."',"
                . "'".$beneficio_ingreso."',"
                . "'".$gratificacion_aguinaldo."',"
                . "'".$preaviso."',"
                . "'".$indmenizacion."')";
            $resultado_sipe_detalle=query($consulta_sipe_detalle,$conexion);


    }


}

$consulta_id= "SELECT id "
          . " FROM ach_sipe "
          . " WHERE mes='$mes' AND "
        . "anio='$anio' ";
$resultado_id=query($consulta_id,$conexion);
$fila_id=fetch_array($resultado_id,$conexion);
$id=$fila_id['id'];

$consulta_tabla= "SELECT a.*, b.* "
          . " FROM ach_sipe as a"
          . " LEFT JOIN ach_sipe_detalle as b ON (a.id=b.id)"
          . " WHERE a.mes='$mes' AND a.anio='$anio' "
          . " ORDER BY b.posicion ASC ";
$resultado_tabla=query($consulta_tabla,$conexion);



$meses1      = array('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
$mes_letras  = $meses1[$mes - 1];
//--------------------------------------------------------------
//titulo de la tabla
$borders = array(
    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    )
    ),
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Calibri'
    )
);
//$objPHPExcel->getActiveSheet()->setCellValue('B1', 'SIPE MES DE '. $mes_letras .' '. $anio );
//$objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
//$objPHPExcel->getActiveSheet()->SetCellValue('G1', "Usuario: ".$_SESSION['nombre']);
//$objPHPExcel->getActiveSheet()->SetCellValue('I1', "Fecha: ".date('d-m-Y'));
//$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($borders);
//$objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($borders);
//$objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($borders);
//$objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($borders);

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "NOMBRE, DENOMINACION O RAZON SOCIAL DEL PATRONO");
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "NACIONAL O EXTRANJERO");
$objPHPExcel->getActiveSheet()->SetCellValue('C1', "NUMERO PATRONAL IGSS");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "NIT");
$objPHPExcel->getActiveSheet()->SetCellValue('E1', "NOMBRE EMPRESA");
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "ACTIVIDAD ECONOMICA");
$objPHPExcel->getActiveSheet()->SetCellValue('G1', "TELEFONO");
$objPHPExcel->getActiveSheet()->SetCellValue('H1', "EMAIL");
$objPHPExcel->getActiveSheet()->SetCellValue('I1', "SITIO WEB");
$objPHPExcel->getActiveSheet()->SetCellValue('J1', "UBICACION GEOGRAFICA");
$objPHPExcel->getActiveSheet()->SetCellValue('K1', "ZONA");
$objPHPExcel->getActiveSheet()->SetCellValue('L1', "BARRIO O COLONIA");
$objPHPExcel->getActiveSheet()->SetCellValue('M1', "AVENIDA");
$objPHPExcel->getActiveSheet()->SetCellValue('N1', "CALLE");
$objPHPExcel->getActiveSheet()->SetCellValue('O1', "NOMENCLATURA");
$objPHPExcel->getActiveSheet()->SetCellValue('P1', "PERSONA RESPONSABLE DE ELABORAR EL INFORME");
$objPHPExcel->getActiveSheet()->SetCellValue('Q1', "DOCUMENTO IDENTIFICACIÓN RESPONSABLE");
$objPHPExcel->getActiveSheet()->SetCellValue('R1', "EMAIL DE LA EMPRESA DEL RESPONSABLE");
$objPHPExcel->getActiveSheet()->SetCellValue('S1', "TELÉFONO DEL RESPONSABLE");
$objPHPExcel->getActiveSheet()->SetCellValue('T1', "PAIS ORIGEN RESPONSABLE");
$objPHPExcel->getActiveSheet()->SetCellValue('U1', "EXISTE SINDICATO");
$objPHPExcel->getActiveSheet()->SetCellValue('V1', "CANTIDAD TOTAL EMPLEADOS INICIO AÑO");
$objPHPExcel->getActiveSheet()->SetCellValue('W1', "CANTIDAD TOTAL EMPLEADOS FINAL AÑO");
$objPHPExcel->getActiveSheet()->SetCellValue('X1', "TIENE PLANIFICADO CONTRATAR NUEVO PERSONAL");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "MEDIR RANGO DE INGRESOS ANUAL");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "CONTABILIDAD COMPLETA DE LA EMPRESA");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "NOMBRE REPRESENTANTE LEGAL");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "DOCUMENTO IDENTIFICACION");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "NACIONALIDAD DEL REPRESENTANTE LEGAL");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "NOMBRE JEFE DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "AÑO INFORME");
/*$objPHPExcel->getActiveSheet()->SetCellValue('L2', "Deducc.");*/
//Fin encabezado tabla
$rowCount = 2;
$count    = 1;
/* Se buscan los campos que tuvieron movimientos en planilla */


while ($fila=fetch_array($resultado,$conexion))
{ 
    
  
    //----------------------------------------
    $fonts = array(
        'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('argb' => 'FF000000')
            )
        ),
        'font'  => array(
            'bold'   => false,
            'color'  => array('rgb' => '000000'),
            'size'   => 10,
            'name'   => 'Arial',
            'center' => true
        )
    );
  
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':I'.$rowCount)->applyFromArray($fonts);/*
    $objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");*/
    /* Se muestran los datos del funcionario/colaborador además de los conceptos devengados que están almacenados en planilla */
  
//    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $fila['tipo_documento']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rowCount", $fila['tipo_documento'], PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $fila['numero_documento']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rowCount", $fila['numero_documento'], PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $fila['seguro_social']);
//$objPHPExcel->getActiveSheet()->setCellValueExplicit("C$rowCount", $fila['seguro_social'], PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, utf8_encode($fila['nombre']));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$rowCount", utf8_encode($fila['nombre']), PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, utf8_encode($fila['apellido']));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E$rowCount", utf8_encode($fila['apellido']), PHPExcel_Cell_DataType::TYPE_STRING);
    //7$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, number_format($salario1, 2, '.',','));
    //$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, number_format($salario2, 2, '.',','));
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $fila['sueldo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $fila['horas_extras']);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $fila['impuesto_renta']);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $fila['decimo_tercer_mes']);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $fila['vacaciones']);
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $fila['comisiones']);
    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $fila['bonificaciones']);
    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $fila['combustible']);
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $fila['dieta']);
    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $fila['salario_especies']);
    $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $fila['viaticos']);
    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $fila['gasto_representacion']);
    $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $fila['impuesto_renta_gr']);
    $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $fila['decimo_tercer_mes_gr']);
    $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $fila['prima_produccion']);
    $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $fila['dividendo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $fila['beneficio_ingreso']);
    $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $fila['gratificacion_aguinaldo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $fila['preaviso']);
    $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $fila['indmenizacion']);
//    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, number_format($total, 2, '.',','));
    $borders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            )
        ),
    );
    $desde = "B".$rowCount;
    $hasta = "Y".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;

}

$final = $rowCount-1;
/* Se muestra la sumatoria de los conceptos que está almacenados en movimientos de planilla */
/*$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,'Totales:');
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, '=SUM(E2:E'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, '=SUM(F2:F'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=SUM(G2:G'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, '=SUM(H2:H'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, '=SUM(I2:I'.$final.')');*/
$objPHPExcel->getActiveSheet()->setTitle('Informe Patrono');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$nombre_archivo = "empresas_".$empresa."_".$anio.".xlsx";
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
header('Cache-Control: max-age=0');
ob_clean();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>