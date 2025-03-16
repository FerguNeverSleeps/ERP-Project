<?php

//session_start();
//error_reporting(0);
//ini_set('display_errors', FALSE);
//ini_set('display_startup_errors', FALSE);
//date_default_timezone_set('America/Panama');


//include('../../lib/common.php');

function formato_fecha($fecha,$formato){
    if (empty($fecha))
    {
    	$fecha = date('Y-m-d');
    }
    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $separa = explode("-",$fecha);
    $dia = $separa[2];
    $mes = $separa[1];
    $anio = $separa[0];
    switch ($formato)
    {
        case 1:
            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
            break;
        case 2:
            $f = $dia." del mes ".$meses[$mes-1]." de ".$anio;
            break;
        default:
                case 3:
            $f = $dia."/".$mes."/".$anio;
            break;
        default:
            break;
    }
    return $f;
}

function nomina_electronica_proceso_nomina_general_periodos_global_procesar($anio,$mes,$fecha_ini,$fecha_fin,$codtip,$descripcion)
{  
    $conexion= new bd($_SESSION['bd']);

    $sql = "SELECT UPPER(t.descrip) as descrip 
                    FROM   nomtipos_nomina t
                    WHERE  t.codtip=".$codtip;
    $res=$conexion->query($sql);

    if($fila=$res->fetch_array())
    {
            $NOMINA = $fila['descrip']; 
    }	

    $sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
                            e.edo_emp, e.imagen_izq as logo
                    FROM   nomempresa e";
    $res=$conexion->query($sql);
    $fila=$res->fetch_array();
    $logo=$fila['logo'];

    $sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.frecuencia, np.fechapago as fechapago, np.mes, np.anio,
                    DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin, np.descrip, F.descrip frecuencia_descripcion
            FROM nom_nominas_pago np 
            LEFT JOIN  nomfrecuencias F on F.codfre=np.frecuencia
            WHERE  np.anio=".$anio." AND nP.mes=".$mes." AND np.tipnom=".$codtip;

    $res2=$conexion->query($sql2);
    $fila2=$res2->fetch_array();
    if(!isset($fila2["anio"])){
        print "Busqueda sin resultado.";
        exit;
    }

    $meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

    $desde=$fila2['desde'];
    $hasta=$fila2['hasta'];

    $desde1=formato_fecha($fila2['desde'],1);
    $hasta1=formato_fecha($fila2['hasta'],1);
    $fechapago=formato_fecha($fila2['fechapago'],3);
    $dia_ini = $fila2['dia_ini'];
    $frecuencia = $fila2['frecuencia'];
    $frecuencia_descripcion=$fila2["frecuencia_descripcion"];
    $dia_fin = $fila2['dia_fin']; 
    $mes_numero = $fila2['mes'];
    $mes_letras = $meses[$mes_numero - 1];
    $anio = $fila2['anio'];

    $empresa = $fila['empresa'];

    $RETORNO["empresa"]=strtoupper(utf8_encode($empresa) );
    $RETORNO["descripcion"]=strtoupper("(AMX A)-{$anio}/{$mes_letras}-{$codnom}-".$fila2['descrip']);
    $RETORNO["rango_fecha"]='DEL '. $desde1 .' AL '. $hasta1;
    $RETORNO["fecha_pago"]=$fila2['fechapago'];
    $RETORNO["ffecha_pago"]=$fechapago;
    $RETORNO["detalle"]=[];
	
    
    $fechaInicio = new DateTime($fecha_ini);
    $fechaFin = new DateTime($fecha_fin);

    $fechaI = $fechaInicio->format('Y-m-d');
    $fechaF = $fechaFin->format('Y-m-d');

    $insert_nomina_electronica_cabecera = "INSERT INTO nomina_electronica_cabecera 
               (id_cabecera,
               fecha_inicio, 
               fecha_fin,
               mes,
               anio,
               descripcion,
               estatus, 
               usuario_creacion, 
               fecha_creacion)
               VALUES
               ('',"
                . "'{$fechaInicio->format('Y-m-d')}',"
                . "'{$fechaFin->format('Y-m-d')}',"
                . "'{$mes}',"
                . "'{$anio}',"
                . "'{$descripcion}',"
                . "1,"
                . "'".$_SESSION['usuario']."',"
                . "'".date("Y-m-d H:i:s")."')";
    $res_insert_nomina_electronica_cabecera=$conexion->query($insert_nomina_electronica_cabecera);

    $select_id_cabecera = "SELECT MAX(id_cabecera) as id FROM nomina_electronica_cabecera";
    $res_id_cabecera=$conexion->query($select_id_cabecera);
    $fila_id_cabecera=$res_id_cabecera->fetch_array();
    $id_cabecera=$fila_id_cabecera['id'];
    
    $RETORNO["id_cabecera"]=$id_cabecera;

    $sql_empleados = "SELECT DISTINCT np.ficha, np.*
    FROM   nompersonal np
    INNER JOIN nom_movimientos_nomina nm ON (nm.ficha=np.ficha AND nm.cedula=np.cedula)    
    WHERE  nm.anio=".$anio." AND nm.mes=".$mes." AND nm.tipnom=".$codtip."
    ORDER BY np.ficha";

    $res1=$conexion->query($sql_empleados);
    
    while($row1=$res1->fetch_array())
    {	
        $totalA=$totalD=0;
        $asignaciones=$deducciones=0;
        $codnivel1=$row1['codnivel1'];
        $departamento=$row1['departamento'];
        $markar=$row1['markar'];
       
        $insert_nomina_electronica_detalle_trabajador = "INSERT INTO nomina_electronica_detalle_trabajador 
                                                        (id_detalle_trabajador,
                                                        id_cabecera,
                                                        ficha, 
                                                        cedula,
                                                        campo,
                                                        valor)
                                                        VALUES
                                                        ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'altoRiesgoPension',"
                                                         . "'0'),
                                                        ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'codigoTrabajador',"
                                                         . "'{$row1['ficha']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'email',"
                                                         . "'{$row1['email']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'extrasNom',"
                                                         . "'NULL'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'lugarTrabajoDepartamentoEstado',"
                                                         . "'11'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'lugarTrabajoDireccion',"
                                                         . "'Direccion de prueba'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'lugarTrabajoMunicipioCiudad',"
                                                         . "'Direccion de prueba'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'lugarTrabajoPais',"
                                                         . "'CO'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'numeroDocumento',"
                                                         . "'{$row1['cedula']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'otrosNombres',"
                                                         . "'NULL'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'primerApellido',"
                                                         . "'{$row1['apellidos']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'primerNombre',"
                                                         . "'{$row1['nombres']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'salarioIntegral',"
                                                         . "'{$row1['suesal']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'segundoApellido',"
                                                         . "'{$row1['apellidos']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'subTipoTrabajador',"
                                                         . "'00'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'sueldo',"
                                                         . "'{$row1['suesal']}'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'tipoContrato',"
                                                         . "'1'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'tipoIdentificacion',"
                                                         . "'31'),
                                                         ('',"
                                                         . "'{$id_cabecera}',"
                                                         . "'{$row1['ficha']}',"
                                                         . "'{$row1['cedula']}',"
                                                         . "'tipoTrabajador',"
                                                         . "'01')
                                                         ";
        $res_insert_nomina_electronica_detalle_trabajador=$conexion->query($insert_nomina_electronica_detalle_trabajador);
    
        $consulta_conceptos="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon  "
                . "FROM nom_movimientos_nomina AS nm "
                . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                . "WHERE nm.anio='".$anio."' AND nm.mes='".$mes."' AND nm.tipnom='".$codtip."' "
                . "AND nm.ficha='".$row1['ficha']."' AND nm.cedula='".$row1['cedula']."' AND nm.monto>0 "
                . "GROUP BY nm.codcon "
                . "ORDER BY nm.codcon";
		//echo $consulta_conceptos;
        //        exit;
        $res_conceptos=$conexion->query($consulta_conceptos);
        while($row2=$res_conceptos->fetch_array())
        {	
                $enc=true;
                $codcon = $row2['codcon'];
                $descrip = utf8_decode($row2['descrip']);
                $tipcon = $row2['tipcon'];
                $suma = $row2['suma'];

                $insert_nomina_electronica_detalle_conceptos = "INSERT INTO nomina_electronica_detalle_conceptos 
                                                                (id_detalle_conceptos,
                                                                id_cabecera,
                                                                ficha, 
                                                                cedula,
                                                                codcon,
                                                                monto)
                                                                VALUES
                                                                ('',"
                                                                 . "'{$id_cabecera}',"
                                                                 . "'{$row1['ficha']}',"
                                                                 . "'{$row1['cedula']}',"
                                                                 . "'{$row2['codcon']}',"
                                                                 . "'{$row2['suma']}')
                                                                 ";
            //print $insert_nomina_electronica_detalle_conceptos;exit;
            $res_insert_nomina_electronica_detalle_conceptos=$conexion->query($insert_nomina_electronica_detalle_conceptos);
            
        }
                
    }     


    return $RETORNO;
}



?>