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

$conexion=conexion();

$anio = $_REQUEST['anio'];
$mes    = $_REQUEST['mes'];
$opcion    = $_REQUEST['opcion'];



if($opcion==1)
{
    $consulta_tabla= "SELECT a.id "
          . " FROM ach_sipe_sysmeca as a"
          . " WHERE a.mes='$mes' AND a.anio='$anio' ";
    $resultado_tabla=query($consulta_tabla,$conexion);
    $fila_tabla=fetch_array($resultado_tabla,$conexion);
    $id=$fila_tabla['id'];
    
//    echo $id;
//    exit;
    
    if($id!=0 && $id!=NULL && $id!='')
    {
        $consulta_delete1= "DELETE FROM ach_sipe_sysmeca "
          . " WHERE id='$id'";
        $resultado_delete1=query($consulta_delete1,$conexion);
        
        $consulta_delete2= "DELETE FROM ach_sipe_detalle_sysmeca "
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
    //echo $codnom;echo "<br>";
    //echo $codtip;echo "<br>";
    //exit; 

    $correlativo = '78223'; 
    $numero_patronal=873700322;
    $total_salario_bruto=$total_xiii=$total_gr=$total_bono=$total_otros_ing=$total_s_s=$total_s_e=$total_isr=$total_isr_gr=0.00;
    $total_excep=$total_x=0;

    $fecha_creacion = date("Y-m-d");
    $fecha = date("Y-m-d");
    $usuario = $_SESSION['usuario'];

    $consulta_sipe="INSERT INTO ach_sipe_sysmeca "
                . "(id, "
                . "correlativo, "
                . "total_salario, "
                . "total_isr, "
                . "total_excep, "
                . "total_xiii, "
                . "total_x, "
                . "numero_patronal, "
                . "anio, "
                . "mes, "
                . "fecha, "
                . "usuario_creacion, "
                . "fecha_creacion, "
                . "usuario_edicion, "
                . "fecha_edicion) "
                . "VALUES "
                . "('', "
                . "'".$correlativo."',"
                . "'".$total_salario_bruto."',"
                . "'".$total_isr."',"
                . "'".$total_excep."',"
                . "'".$total_xiii."',"
                . "'".$total_x."',"
                . "'".$numero_patronal."',"
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

    $con_salario_bruto="90,91,100,105,106,107,108,109,110,111,112,113,114,115,116,120,121,123,124,125,126,127,128,129,130,"
            . "131,132,133,134,135,136,137,138,139,140,142,143,144,149,150,151,152,153,159,160,161,162,163,164,165,166,167,168,169,"
            . "171,172,174";
    $con_tardanza="199";
    $con_ausencia="203";
    $con_xiii="92,101,102";
    $con_gr="145,117";
    $con_bono="189,190";
    $con_otros_ing="93,94,95,96,97,141,147,157";
    $con_prima_produccion="158,182";
    $con_s_s="200";
    $con_s_e="201,206";
    $con_isr="202,215";
    $con_isr_gr="208";



    $consulta=" SELECT np.apenom, np.telefonos, np.nombres, np.apellidos, np.sexo,
                np.seguro_social, np.cedula, np.ficha, np.clave_ir 
                FROM nompersonal np 
                WHERE np.ficha IN
                (
                    SELECT DISTINCT ficha 
                    FROM nom_movimientos_nomina as mn
                    LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom)
                    WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}')
                )
                ORDER by np.cedula ASC";

    $resultado2=query($consulta,$conexion);
//    echo $consulta;
//    exit;

    while($fila=fetch_array($resultado2,$conexion))
    {
            $cod=$cod2="";

            $consulta_salario_bruto="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_salario_bruto}) "
                    . "AND mn.cedula='$fila[cedula]')";
//            if($fila[cedula]=="8-222-02778")
//            {
//                echo $consulta_salario_bruto;
//                exit;	
//            }
            $resultado_salario_bruto=query($consulta_salario_bruto,$conexion);
            $fila_salario_bruto=fetch_array($resultado_salario_bruto,$conexion);
            $salario_bruto=$fila_salario_bruto['monto'];
            $total_salario_bruto=$total_salario_bruto+$salario_bruto;
            
            
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
            $total_tardanza=$total_tardanza+$tardanza;
            
            
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
            $total_ausencia=$total_ausencia+$ausencia;
            
            $consulta_xiii="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_xiii}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_xiii=query($consulta_xiii,$conexion);
            $fila_xiii=fetch_array($resultado_xiii,$conexion);
            $xiii=$fila_xiii['monto'];
            $total_xiii=$total_xiii+$xiii;

            
            $consulta_gr="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_gr}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_gr=query($consulta_gr,$conexion);
            $fila_gr=fetch_array($resultado_gr,$conexion);
            $gr=$fila_gr['monto'];
            $total_gr=$total_gr+$gr;

            
            $consulta_bono="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_bono}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_bono=query($consulta_bono,$conexion);
            $fila_bono=fetch_array($resultado_bono,$conexion);
            $bono=$fila_bono['monto'];
            $total_bono=$total_bono+$bono;

            
            $consulta_otros_ing="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_otros_ing}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_otros_ing=query($consulta_otros_ing,$conexion);
            $fila_otros_ing=fetch_array($resultado_otros_ing,$conexion);
            $otros_ing=$fila_otros_ing['monto'];
            $total_otros_ing=$total_otros_ing+$otros_ing;

            $salario = $salario_bruto - ($tardanza + $ausencia);
            
            $consulta_prima_produccion="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_prima_produccion}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_prima_produccion=query($consulta_prima_produccion,$conexion);
            $fila_prima_produccion=fetch_array($resultado_prima_produccion,$conexion);
            $prima_produccion=$fila_prima_produccion['monto'];
            $total_prima_produccion=$total_prima_produccion+$prima_produccion;
            
            $consulta_s_s="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_s_s}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_s_s=query($consulta_s_s,$conexion);
            $fila_s_s=fetch_array($resultado_s_s,$conexion);
            $s_s=$fila_s_s['monto'];
            $total_s_s=$total_s_s+$s_s;

            
            $consulta_s_e="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_s_e}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_s_e=query($consulta_s_e,$conexion);
            $fila_s_e=fetch_array($resultado_s_e,$conexion);
            $s_e=$fila_s_e['monto'];
            $total_s_e=$total_s_e+$s_e;

            
            $consulta_isr="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_isr}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_isr=query($consulta_isr,$conexion);
            $fila_isr=fetch_array($resultado_isr,$conexion);
            $isr_salario=$fila_isr['monto'];
            $total_isr_salario=$total_isr_salario+$isr_salario;

            
            $consulta_isr_gr="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  as mn "
                    . "LEFT JOIN nom_nominas_pago as np ON (mn.codnom=np.codnom AND mn.tipnom=np.tipnom) "
                    . "WHERE (np.anio_sipe = '{$anio}' AND np.mes_sipe = '{$mes}' "
                    . "AND mn.codcon IN ({$con_isr_gr}) "
                    . "AND mn.cedula='$fila[cedula]')";
    //        echo $consulta;
    //        exit;
            $resultado_isr_gr=query($consulta_isr_gr,$conexion);
            $fila_isr_gr=fetch_array($resultado_isr_gr,$conexion);
            $isr_gr=$fila_isr_gr['monto'];
            $total_isr_gr=$total_isr_gr+$isr_gr;

            $cod_adicion = '2';

            $ficha = str_pad($fila[ficha], 5, "0", STR_PAD_LEFT);

            $seguro_social = str_replace("-","",$fila[seguro_social]);
            if($seguro_social==NULL || $seguro_social==0 || $seguro_social=='' || strlen($seguro_social)>7)
                $seguro_social='9999999';
           $seguro_social=str_pad($seguro_social, 7, "0", STR_PAD_LEFT);

            $cedula_transformada = $fila[cedula];
            
            $buscar_guion = strpos($fila[cedula], "-");
            if($buscar_guion!=false)
            {
                $cedula = explode('-', $fila[cedula]);

                $cedula1 = $cedula[0];
                $cedula1 = str_pad($cedula1, 2, "0", STR_PAD_LEFT);
                if($cedula[0]=="E" || $cedula[0]=="PE")
                    $cedula1 = $cedula[0];

                $cedula2 = $cedula[1];
                $cedula2 = str_pad($cedula2, 5, "0", STR_PAD_LEFT);

                $cedula3 = $cedula[2];
                $cedula3 = str_pad($cedula3, 6, "0", STR_PAD_LEFT);
                
                $cedula_txt=$cedula1."  ".$cedula2."".$cedula3;                
            
                if($cedula[0]=="E")
                {
    //                echo "Entro"; exit;

                    $cedula_txt=$cedula1." ".$cedula2."".$cedula3;
//                    if($fila[cedula]=="P1079338")
//                    {
//                        echo $cedula_txt;
//        //                exit;
//                    }
                }            


                if($cedula[0]=="PE")
                {
    //                echo "Entro"; exit;
                    $cedula_txt=$cedula1."".$cedula2."".$cedula3;
                }
            }
            else
            {
//                echo "Entro"; echo $fila[cedula];
                $buscar_pasaporte = strpos($fila[cedula], "P"); 
                
                if($buscar_pasaporte>=0)
                {
//                    echo $buscar_pasaporte;exit;
                    $cedula1 = "P";
                    $cedula1 = str_pad($cedula1, 2, "0", STR_PAD_LEFT);
                    

                    $cedula2 = "0";
                    $cedula2 = str_pad($cedula2, 5, "0", STR_PAD_LEFT);
                    
                    
                    $cedula3 = substr($fila[cedula],-5);                    
                    $cedula3 = str_pad($cedula3, 6, "0", STR_PAD_LEFT);
//                    echo $cedula3;exit;
                    $cedula_txt=$cedula1."  ".$cedula2."".$cedula3;  
                }
                
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


            $sexo=substr($fila[sexo],0,1);

            

            $clave_ir = $fila[clave_ir];
            if($clave_ir==NULL || $clave_ir==0 || $clave_ir=='' || strlen($clave_ir)>2)
                $clave_ir='A0';

            $x=0;
            if($salario<250)
            {
                $x=1;
                $total_x++;
            }

            $dias_enfermedad=0;

            $observacion="";

            
            
            if($salario>0)
            {
                $excep=3;
                $total_excep=$total_excep+$excep;
                $consulta_sipe_detalle="INSERT INTO ach_sipe_detalle_sysmeca "
                    . "(id, "
                    . "id_detalle, "
                    . "cod_adicion, "
                    . "correlativo, "
                    . "sec_empleado, "
                    . "seguro_social, "
                    . "cedula, "
                    . "apellido, "
                    . "nombre, "
                    . "sexo, "
                    . "cod_excepcion, "
                    . "departamento, "
                    . "dias_enfermedad, "
                    . "salario, "
                    . "isr, "
                    . "clave_ir, "
                    . "observaciones, "
                    . "error, "
                    . "siacap, "
                    . "nueva_cedula, "
                    . "cod_pasaporte, "
                    . "cedula_transformada, "                
                    . "x) "
                    . "VALUES "
                    . "('".$ultimo_id."', "
                    . "'',"
                    . "'".$cod_adicion."',"
                    . "'".$correlativo."',"
                    . "'".$ficha."',"
                    . "'".$seguro_social."',"
                    . "'".$cedula_txt."',"
                    . "'".$apellido."',"
                    . "'".$nombre."',"
                    . "'".$sexo."',"
                    . "'".$excep."',"
                    . "'".$departamento."',"
                    . "'".$dias_enfermedad."',"
                    . "'".$salario."',"
                    . "'".$isr_salario."',"
                    . "'".$clave_ir."',"
                    . "'".$observaciones."',"
                    . "'".$error."',"
                    . "'".$siacap."',"
                    . "'".$nueva_cedula."',"
                    . "'".$pasaporte."',"
                    . "'".$cedula_transformada."',"
                    . "'".$x."')";
                $resultado_sipe_detalle=query($consulta_sipe_detalle,$conexion);
            }
            
            if($gr>0)
            {
                $excep=73;
                $total_excep=$total_excep+$excep;
                $consulta_sipe_detalle="INSERT INTO ach_sipe_detalle_sysmeca "
                    . "(id, "
                    . "id_detalle, "
                    . "cod_adicion, "
                    . "correlativo, "
                    . "sec_empleado, "
                    . "seguro_social, "
                    . "cedula, "
                    . "apellido, "
                    . "nombre, "
                    . "sexo, "
                    . "cod_excepcion, "
                    . "departamento, "
                    . "dias_enfermedad, "
                    . "salario, "
                    . "isr, "
                    . "clave_ir, "
                    . "observaciones, "
                    . "error, "
                    . "siacap, "
                    . "nueva_cedula, "
                    . "cod_pasaporte, "
                    . "cedula_transformada, "                
                    . "x) "
                    . "VALUES "
                    . "('".$ultimo_id."', "
                    . "'',"
                    . "'".$cod_adicion."',"
                    . "'".$correlativo."',"
                    . "'".$ficha."',"
                    . "'".$seguro_social."',"
                    . "'".$cedula_txt."',"
                    . "'".$apellido."',"
                    . "'".$nombre."',"
                    . "'".$sexo."',"
                    . "'".$excep."',"
                    . "'".$departamento."',"
                    . "'".$dias_enfermedad."',"
                    . "'".$gr."',"
                    . "'".$isr_gr."',"
                    . "'".$clave_ir."',"
                    . "'".$observaciones."',"
                    . "'".$error."',"
                    . "'".$siacap."',"
                    . "'".$nueva_cedula."',"
                    . "'".$pasaporte."',"
                    . "'".$cedula_transformada."',"
                    . "'".$x."')";
                $resultado_sipe_detalle=query($consulta_sipe_detalle,$conexion);
            }
            
            if($bono>0)
            {
                $excep=75;
                $total_excep=$total_excep+$excep;
                $consulta_sipe_detalle="INSERT INTO ach_sipe_detalle_sysmeca "
                    . "(id, "
                    . "id_detalle, "
                    . "cod_adicion, "
                    . "correlativo, "
                    . "sec_empleado, "
                    . "seguro_social, "
                    . "cedula, "
                    . "apellido, "
                    . "nombre, "
                    . "sexo, "
                    . "cod_excepcion, "
                    . "departamento, "
                    . "dias_enfermedad, "
                    . "salario, "
                    . "isr, "
                    . "clave_ir, "
                    . "observaciones, "
                    . "error, "
                    . "siacap, "
                    . "nueva_cedula, "
                    . "cod_pasaporte, "
                    . "cedula_transformada, "                
                    . "x) "
                    . "VALUES "
                    . "('".$ultimo_id."', "
                    . "'',"
                    . "'".$cod_adicion."',"
                    . "'".$correlativo."',"
                    . "'".$ficha."',"
                    . "'".$seguro_social."',"
                    . "'".$cedula_txt."',"
                    . "'".$apellido."',"
                    . "'".$nombre."',"
                    . "'".$sexo."',"
                    . "'".$excep."',"
                    . "'".$departamento."',"
                    . "'".$dias_enfermedad."',"
                    . "'".$bono."',"
                    . "'0',"
                    . "'".$clave_ir."',"
                    . "'".$observaciones."',"
                    . "'".$error."',"
                    . "'".$siacap."',"
                    . "'".$nueva_cedula."',"
                    . "'".$pasaporte."',"
                    . "'".$cedula_transformada."',"
                    . "'".$x."')";
                $resultado_sipe_detalle=query($consulta_sipe_detalle,$conexion);
            }
            if($prima_produccion>0)
            {
                $excep=81;
                $total_excep=$total_excep+$excep;
                $consulta_sipe_detalle="INSERT INTO ach_sipe_detalle_sysmeca "
                    . "(id, "
                    . "id_detalle, "
                    . "cod_adicion, "
                    . "correlativo, "
                    . "sec_empleado, "
                    . "seguro_social, "
                    . "cedula, "
                    . "apellido, "
                    . "nombre, "
                    . "sexo, "
                    . "cod_excepcion, "
                    . "departamento, "
                    . "dias_enfermedad, "
                    . "salario, "
                    . "isr, "
                    . "clave_ir, "
                    . "observaciones, "
                    . "error, "
                    . "siacap, "
                    . "nueva_cedula, "
                    . "cod_pasaporte, "
                    . "cedula_transformada, "                
                    . "x) "
                    . "VALUES "
                    . "('".$ultimo_id."', "
                    . "'',"
                    . "'".$cod_adicion."',"
                    . "'".$correlativo."',"
                    . "'".$ficha."',"
                    . "'".$seguro_social."',"
                    . "'".$cedula_txt."',"
                    . "'".$apellido."',"
                    . "'".$nombre."',"
                    . "'".$sexo."',"
                    . "'".$excep."',"
                    . "'".$departamento."',"
                    . "'".$dias_enfermedad."',"
                    . "'".$prima_produccion."',"
                    . "'0',"
                    . "'".$clave_ir."',"
                    . "'".$observaciones."',"
                    . "'".$error."',"
                    . "'".$siacap."',"
                    . "'".$nueva_cedula."',"
                    . "'".$pasaporte."',"
                    . "'".$cedula_transformada."',"
                    . "'".$x."')";
                $resultado_sipe_detalle=query($consulta_sipe_detalle,$conexion);
            }
            
            

    }

    $total_salario = $total_salario_bruto + $total_bono +  $total_gr + $total_prima_produccion - ($total_tardanza + $total_ausencia);
    
    $total_isr = $total_isr_salario + $total_isr_gr;
            
    $consulta_sipe = "UPDATE ach_sipe_sysmeca SET "
                    . "total_salario = '{$total_salario}',"
                    . "total_isr = '{$total_isr}',"
                    . "total_excep = '{$total_excep}',"
                    . "total_xiii = '{$total_xiii}',"
                    . "total_x = '{$total_x}'"
                    . " WHERE id='{$ultimo_id}'";
    $resultado_sipe=query($consulta_sipe,$conexion);
}

if($opcion==3){
    $id_detalle=$_REQUEST["id_detalle"];
    $dias_enfermedad=$_REQUEST["dias_enfermedad"];
    $observaciones=$_REQUEST["observaciones"];
    $consulta_sipe = "UPDATE ach_sipe_detalle_sysmeca SET "
                   . "dias_enfermedad = '{$dias_enfermedad}',"
                   . "observaciones = '{$observaciones}'"
                   . " WHERE id_detalle='{$id_detalle}'";

    $resultado_sipe=query($consulta_sipe,$conexion);
}



$consulta_tabla= "SELECT a.*, b.* "
          . " FROM ach_sipe_sysmeca as a"
          . " LEFT JOIN ach_sipe_detalle_sysmeca as b ON (a.id=b.id)"
          . " WHERE a.mes='$mes' AND a.anio='$anio' "
          . " ORDER BY b.cedula ASC ";
$resultado_tabla=query($consulta_tabla,$conexion);

$consulta_id= "SELECT id "
          . " FROM ach_sipe_sysmeca "
          . " WHERE mes='$mes' AND anio='$anio' ";
$resultado_id=query($consulta_id,$conexion);
$fila_id=fetch_array($resultado_id,$conexion);
$id=$fila_id['id'];

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
                      Generar TXT
                  </a>
              </div>
            </div>
             <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                    <tr>
                      <th>Ficha</th>
                      <th>Seguro Social</th>
                      <th>Cedula</th>
                      <th>Nombre y Apellidos</th>
                      <th>Sexo</th>
                      <th>Excepcion</th>
                      <th>Salario</th>
                      <th>ISR</th>
                      <th>XIII</th>
                      <th>Otros</th>
                      <th>X</th>  
                      <th>Dias Enf.</th>  
                      <th>Observación</th>  
                      <th></th>
                      <th></th>
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
                              <td><?php echo $fila_tabla['sec_empleado']; ?></td>
                              <td><?php echo $fila_tabla['seguro_social']; ?></td>
                              <td><?php echo $fila_tabla['cedula']; ?></td>
                              <td><?php echo $fila_tabla['apellido'].",".$fila_tabla['nombre']; ?></td>
                              <td><?php echo $fila_tabla['sexo']; ?></td>
                              <td><?php echo $fila_tabla['cod_excepcion']; ?></td>
                              <td><?php echo $fila_tabla['salario']; ?></td>
                              <td><?php echo $fila_tabla['isr']; ?></td>
                              <td><?php echo $fila_tabla['xiii']; ?></td>
                              <td><?php echo $fila_tabla['otros']; ?></td>
                              <td><?php echo $fila_tabla['x']; ?></td>
                              <td><?php echo $fila_tabla['dias_enfermedad']; ?></td>
                              <td><?php echo $fila_tabla['observaciones']; ?></td>
                              <td>
                                <div align="center">
                                    <font size="2" face="Arial, Helvetica, sans-serif">
                                        <a onclick="enviar(<?php echo(3); ?>,<?php echo $fila_tabla['id_detalle'];?>,'<?php echo $fila_tabla['sec_empleado'];?>','<?php echo $fila_tabla['apellido'];?>','<?php echo $fila_tabla['dias_enfermedad'];?>','<?php echo $fila_tabla['observaciones'];?>');" data-toggle="modal" href="#editar"><img src="../img_sis/ico_edit.gif" alt="Modificar el Registro Actual" width="16" height="16" border="0" align="absmiddle">
                                        </a>
                                    </font>
                                </div>
                              </td>
                              <td>
                                <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila_tabla['id']); ?>);"><img src="../imagenes/delete.gif" alt="Eliminar el Registro Actual" width="16" height="16" border="0" align="absmiddle" ></a></font></div>
                              </td>
                            </tr>
                            <?php
                            }
                            ?>
                  </tbody>
                  <tfoot>
                        <tr>                            
                            <th></th>
                            <th colspan="4">TOTALES</th>
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
                    </tfoot>
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
              "aaSorting": [[ 2, "asc" ]],
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



function enviar(op,id,sec_empleado,apellido,dias_enfermedad,observaciones){
  if(op==3){

    $("#modal-id-detalle").val(id);
    $("#modal-ficha").val(sec_empleado+" "+apellido);
    $("#modal-dias-enfermedad").val(dias_enfermedad);
    $("#modal-observaciones").val(observaciones);

    return;
  }
  
  if (op==4){   // Opcion de Eliminar
    if (confirm("¿Desea Generar TXT SIPE?"))
    {         
      
      location.href ="txt_sipe_sysmeca.php?id="+id;
    }   
  }
}

function modal_aceptar(){
    window.location.href="?anio=<?php print $_REQUEST['anio']?>&mes=<?php print $_REQUEST['mes']?>&opcion=3&id_detalle="+$("#modal-id-detalle").val()+"&dias_enfermedad="+$("#modal-dias-enfermedad").val()+"&observaciones="+$("#modal-observaciones").val();
}
</script>



    <!--MODAL-->
    <div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Editar</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal-id-detalle" value="">
                    <div class="row">
                        <div class="col-md-12"><b>Ficha:</b></div>
                        <div class="col-md-12"><input type="text" id="modal-ficha" class="form-control" disabled=""></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12"><b>Dias Enfermedad:</b></div>
                        <div class="col-md-12">
                            <input type="number" class="form-control" id="modal-dias-enfermedad" min="0" value="0">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12"><b>Observación:</b></div>
                        <div class="col-md-12">
                            <textarea class="form-control" id="modal-observaciones"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn green" onclick="modal_aceptar()">Aceptar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


</body>
</html>