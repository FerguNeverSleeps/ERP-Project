<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?


//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
//include ("../header.php");

include ("../header4.php");
$conexion=conexion();

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
                AND (np.contrato=2 OR np.contrato=3)
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

include ("../header4.php");
//?>

<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<div class="page-container">
  <div class="page-content-wrapper">
    <div class="page-content">
      <div class="row">
        <div class="col-md-12">
           <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                GESTIÓN SIPE
              </div>
              <div class="actions">
                <a class="btn btn-sm blue" onclick="enviar(<?php echo(4); ?>,<?php echo $id;?>,'','','','');">
                  <i class="fa fa-pencil"></i>
                      Generar Excel
                  </a>
              </div>
            </div>
             <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                    <tr>
                      
                      <th>Tipo Documento</th>
                      <th>Nº Documento</th>
                      <th>Nº Seguro Social</th>
                      <th>Nombre</th>
                      <th>Apellido</th>
                      <th>Sueldo</th>  
                      <th>Horas Extras</th>  
                      <th>Impuesto Renta</th>  
                      <th>XIII Mes</th>
                      <th>Vacaciones</th>
                      <th>Comisiones</th>
                      <th>Bonificaciones</th>
                      <th>Combustible</th>
                      <th>Dieta</th>
                      <th>Salario Especies</th>
                      <th>Viaticos</th>
                      <th>GR</th>
                      <th>Impuesto Renta GR</th>
                      <th>XIII Mes GR</th>
                      <th>Prima Producción</th>
                      <th>Dividendos</th>
                      <th>Beneficio Ingreso</th>
                      <th>Aguinaldo</th>
                      <th>Preaviso</th>
                      <th>Indemnizacion</th>
<!--                      <th></th>
                      <th></th>-->
                    </tr>
                  </thead>
                  <tbody style="font-size: 12px; ">
                      <?php
                          while ($fila_tabla=fetch_array($resultado_tabla,$conexion))
                          { 
                              $total_excep=$fila_tabla['total_excep'];
                              $total_salario=$fila_tabla['total_salario'];
                              $total_isr=$fila_tabla['total_isr'];
                              $total_xiii=$fila_tabla['$total_xiii'];
                              $total_x=$fila_tabla['total_x'];
                              $total_enfermedad=$total_enfermedad+$fila_tabla['dias_enfermedad'];
                           
                          ?>
                            <tr id="<?php echo $fila_tabla['id_detalle'];?>">                              
                              <td><?php echo $fila_tabla['tipo_documento']; ?></td>
                              <td><?php echo $fila_tabla['numero_documento']; ?></td>
                              <td><?php echo $fila_tabla['seguro_social']; ?></td>
                              <td><?php echo $fila_tabla['nombre']; ?></td>
                              <td><?php echo $fila_tabla['apellido']; ?></td>
                              <td><?php echo $fila_tabla['sueldo']; ?></td>
                              <td><?php echo $fila_tabla['horas_extras']; ?></td>
                              <td><?php echo $fila_tabla['impuesto_renta']; ?></td>
                              <td><?php echo $fila_tabla['decimo_tercer_mes']; ?></td>
                              <td><?php echo $fila_tabla['vacaciones']; ?></td>
                              <td><?php echo $fila_tabla['comisiones']; ?></td>
                              <td><?php echo $fila_tabla['bonificaciones']; ?></td>
                              <td><?php echo $fila_tabla['combustible']; ?></td>
                              <td><?php echo $fila_tabla['dieta']; ?></td>
                              <td><?php echo $fila_tabla['salario_especies']; ?></td>
                              <td><?php echo $fila_tabla['viaticos']; ?></td>
                              <td><?php echo $fila_tabla['gasto_representacion']; ?></td>
                              <td><?php echo $fila_tabla['impuesto_renta_gr']; ?></td>
                              <td><?php echo $fila_tabla['decimo_tercer_mes_gr']; ?></td>
                              <td><?php echo $fila_tabla['prima_produccion']; ?></td>
                              <td><?php echo $fila_tabla['dividendo']; ?></td>
                              <td><?php echo $fila_tabla['beneficio_ingreso']; ?></td>
                              <td><?php echo $fila_tabla['gratificacion_aguinaldo']; ?></td>
                              <td><?php echo $fila_tabla['preaviso']; ?></td>
                              <td><?php echo $fila_tabla['indmenizacion']; ?></td>
<!--                              <td>
                                <div align="center">
                                    <font size="2" face="Arial, Helvetica, sans-serif">
                                        <a onclick="enviar(<?php echo(3); ?>,<?php echo $fila_tabla['id_detalle'];?>,'<?php echo $fila_tabla['sec_empleado'];?>','<?php echo $fila_tabla['apellido'];?>','<?php echo $fila_tabla['dias_enfermedad'];?>','<?php echo $fila_tabla['observaciones'];?>');" data-toggle="modal" href="#editar"><img src="../img_sis/ico_edit.gif" alt="Modificar el Registro Actual" width="16" height="16" border="0" align="absmiddle">
                                        </a>
                                    </font>
                                </div>
                              </td>
                              <td>
                                <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila_tabla['id']); ?>);"><img src="../imagenes/delete.gif" alt="Eliminar el Registro Actual" width="16" height="16" border="0" align="absmiddle" ></a></font></div>
                              </td>-->
                            </tr>
                            <?php
                            }
                            ?>
                  </tbody>
<!--                  <tfoot>
                        <tr>                            
                            <th></th>
                            <th colspan="2">TOTALES</th>
                            <th id="total_excepciones"><?php print $total_excep;?></th>
                            <th id="total_salario"><?php print $total_salario;?></th>
                            <th id="total_isr"><?php print $total_isr;?></th>
                            <th id="total_xiii"><?php print $total_xiii;?></th>
                            <th id="total_otros"><?php print $total_otros;?></th>
                            <th id="total_x"><?php print $total_x;?></th>
                            <th id="total_enfermedad"><?php print $total_enfermedad;?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>-->
                </table>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
    <input name="txtficha" id="txtficha" type="hidden" value="<?php echo $ficha; ?>">    
    <input name="cedula" id="cedula" type="hidden" value="<?php echo $cedula; ?>">
    <input name="registro_id" id="registro_id" type="hidden" value="">  
    <input name="op" type="hidden" value="">  
  </form>
  <?php include("../footer4.php"); ?>
<script type="text/javascript">
   $(document).ready(function() { 
    //$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 25,
              "bStateSave" : true,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended", 
              //"sPaginationType": "full_numbers",
                "oLanguage": {
                  "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    /*"oPaginate": {
                        "sPrevious": "Página Anterior",
                        "sNext": "Página Siguiente"
                    }*/
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                /*
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                */
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [11, 12] },
                    { "bSearchable": false, "aTargets": [11, 12 ] },
                    { "sWidth": "5%", "aTargets": [11, 12] },
                    { "sWidth": "20%", "aTargets": [1,10] }
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
                /*
              "aoColumns": [
            null,
            null,
            { "sWidth": "10px" },
            { "sWidth": "10px" },
        ]*/
              /*
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false, "sType": "text" },
                  null,
                  { "bSortable": false },
                  { "bSortable": false }
                ],*/
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });



function enviar(op,id){

  
  if (op==4){   // Opcion de Eliminar
    if (confirm("¿Desea Generar EXCEL SIPE?"))
    {         
//      document.frmPrincipal.id.value=id;
//      alert(id);
//      document.frmPrincipal.action="excel_sipe_css.php";
//      document.frmPrincipal.submit(); 
      location.href ="excel_sipe_css.php?id="+id;
    }   
  }
}

</script>


</body>
</html>