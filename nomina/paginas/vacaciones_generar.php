<?php
require_once('../lib/database.php');

date_default_timezone_set('America/Panama');

error_reporting(E_ALL);

$db = new Database($_SESSION['bd']);

$opcion = isset($_GET['opcion']) ? $_GET['opcion'] : '';

if(isset($_POST['anio']))
{
    $anio   = $_POST['anio'];

    $today  = date("Y-m-d");

    $sql = "SELECT fecha, diasantiguedad, diasincremdis, quinquenio, tipodisfrute, diasdisfrute,
                   diasbonvac, diasincrem, antigincremvac, diasmaxincdis, diasmaxinc
            FROM   nomtipos_nomina 
            WHERE  codtip='{$_SESSION['codigo_nomina']}'";
    $tipo_nomina = $db->query($sql)->fetch_assoc();

    if(isset($_POST['cedula']))
    {
        $cedula = $_POST['cedula'];

        $sql = "SELECT fecing, tipemp, antiguedadap, ficha, cedula, suesal , useruid, nomposicion_id
                FROM   nompersonal 
                WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND estado<>'Egresado' AND cedula='{$cedula}'";
         
        $res = $db->query($sql);

        $sql = "DELETE FROM nom_progvacaciones 
                WHERE ceduda='{$cedula}' AND periodo='{$anio}' AND estado='Pendiente' AND tipnom='{$_SESSION['codigo_nomina']}'";
        $db->query($sql);
    }
    else    
    {
        $sql = "SELECT fecing, tipemp, antiguedadap, ficha, cedula, suesal , useruid, nomposicion_id
                FROM   nompersonal 
                WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND estado<>'Egresado'";
        $res = $db->query($sql);

        $sql = "DELETE FROM nom_progvacaciones 
                WHERE periodo='{$anio}' AND estado='Pendiente' AND tipnom='{$_SESSION['codigo_nomina']}'";
        $db->query($sql);
    }
    
    while( $fila = $res->fetch_assoc() )
    {
        $fechaing3 = $fila['fecing'];
        $fec33     = explode("-", $fila['fecing']);
        $fechahoy  = $anio ."-". $fec33[1] ."-". $fec33[2];

        if( $tipo_nomina['fecha'] != '' )
        {
            if( $tipo_nomina['fecha'] >= $fila['fecing'] )
                $fila['fecing'] = $tipo_nomina['fecha'];
        }

        $antiguedad = antiguedad($fila['fecing'], $fechahoy, "D");
        $antmes     = antiguedad($fila['fecing'], $fechahoy, "M");

        if( $antiguedad >= $tipo_nomina['diasantiguedad']  &&  $antiguedad < ($tipo_nomina['diasantiguedad'] * 2) )
        {
            if( $fila['tipemp'] == "Fijo"  ||  $fila['tipemp'] == "Contratado" )
            {
                $diasadic = $tipo_nomina['diasincremdis'];
                
                if( $tipo_nomina['quinquenio'] == 1 )
                {
                    if( $_SESSION['nomina'] == 3 )
                        $a = $antiguedad / 365;
                    else
                        $a = $antiguedad / 360;

                    $antig_anos = (int) $a;
                    $antig_anos = $antig_anos + $fila['antiguedadap'];

                    if( $antig_anos >= 1  &&  $antig_anos <= 5 )
                        $dias_incremento = 0;
                    elseif( $antig_anos >= 6  &&  $antig_anos <= 10 )
                        $dias_incremento = $diasadic;
                    elseif( $antig_anos >= 11  &&  $antig_anos <= 15 )
                        $dias_incremento = $diasadic * 2;
                    elseif( $antig_anos >= 16 )
                        $dias_incremento = ($diasadic * 3) + 1; 
                }
                else
                {
                    $dias_incremento = 0;
                }

                if( $tipo_nomina['tipodisfrute'] == "Co" )
                {
                    $fecha    = explode("-", $fechaing3); 
                    $fecha[0] = $anio;
                    if( $fecha[1] == 2  &&  $fecha[2] == 29 )
                    {
                        $fecha[2] = 28;
                    }
                    $ano = $ano0 = $fecha[0];
                    $mes = $mes0 = $fecha[1];
                    $dia = $dia0 = $diainivac = $fecha[2];
                    $fechainivac = $ano ."-". $mes ."-". $dia;
                    $i = 0;
                    $iniciomes  = $ano ."-". $mes ."-01"; 
                    $diasvac    = $tipo_nomina['diasdisfrute'];
                    $numdiasmes = date("t", strtotime($iniciomes));

                    while( $i < $diasvac )
                    {
                        if( $numdiasmes == $diainivac )
                        {
                            $diainivac = 1;
                            $diainivac = "0". $diainivac;
                            $i = $i + 1;
                            if( $mes == 12 )
                            {
                                $mes = 1;
                                $ano = $ano + 1;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                            else
                            {
                                $mes = $mes + 1;
                                if( $mes <= 9 )
                                    $mes = "0". $mes;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                        }
                        else
                        {
                            $diainivac = $diainivac + 1;
                            if( $diainivac <= 9 )
                                $diainivac = "0". $diainivac;
                            $i = $i + 1;
                        }

                        $fechaconsulta = $ano ."-". $mes ."-". $diainivac;
                        $sql1   = "SELECT dia_fiesta 
                                   FROM   nomcalendarios_tiposnomina 
                                   WHERE  cod_tiponomina='{$_SESSION['codigo_nomina']}' AND fecha='{$fechaconsulta}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();

                        if( $fila1['dia_fiesta'] == 3 )
                            $i = $i - 1;
                    }

                    // LM hasta aqui el calculo de los ultimos 11 meses
                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' 
                             AND    tipooper='DV' AND tipnom='{$_SESSION['codigo_nomina']}'";

                    $res1 = $db->query($sql1);

                    if( $res1->num_rows == 0 )
                    {
	
                        //INSERCION DIAS INCAPACIDAD   
                        $tipo_justificacion=7;     
                        $consulta_incapacidad="INSERT INTO dias_incapacidad
                                ( cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, fecha_vence, dias_restante, horas_restante, minutos_restante,
                                dias, horas, minutos,  cedula, periodo, ficha, tipo_oper)
                                VALUES  
                                ('{$fila["nomposicion_id"]}','{$tipo_justificacion}','','30','30 Dias Vacaciones','','','{$fila["useruid"]}','{$fechainivac}','30','0','0',"
                                . "'{$dias_solic_pdisfrutar}','0','0','{$cedula}')";
	
                                $insert_id="SELECT LAST_INSERT_ID() as id;";
                                
                                $ultimo_id = $db->query($insert_id->fetch_assoc());
                        //echo $consulta_incapacidad;    
                        //$resultado_incapacidad=query($consulta_incapacidad,$conexion);  
                        $db->query($consulta_incapacidad);
                        $sql1 = "INSERT INTO nom_progvacaciones SET 
                                 periodo               = '{$anio}', 
                                 ficha                 = '{$fila['ficha']}', 
                                 ceduda                = '{$fila['cedula']}', 
                                 ddisfrute             = '{$diasvac}', 
                                 dpago                 = '{$tipo_nomina['diasbonvac']}', 
                                 fechareivac           = '', 
                                 monto_vac             = '{$fila['suesal']}', 
                                 saldo_dias            = '{$diasvac}', 
                                 fecha_venc            = '{$fechainivac}', 
                                 fechavac              = '', 
                                 fechaopr              = '{$today}', 
                                 estado                = 'Pendiente', 
                                 tipooper              = 'DV', 
                                 desoper               = 'Dias Vacaciones {$diasvac} {$fechainivac} ', 
                                 marca                 = 0, 
                                 dias_ppagar           = 0, 
                                 saldo_vacaciones      = 30, 
                                 dias_solic_ppagar     = 0, 
                                 dias_vac_disfrute     = 30, 
                                 saldo_dias_pdisfrutar = 30, 
                                 dias_solic_pdisfrutar = 0, 
                                 tipnom                = '{$_SESSION['codigo_nomina']}', 
                                 id_dias_incapacidad   = '{$ultimo_id["id"]}'";
                        $db->query($sql1);
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DA' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        // $sql1="INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', ddisfrute=".$dias_incremento.", fechaopr='".$today."', estado='Pendiente', tipooper='DA', desoper='Dias Vacaciones Adicionales', tipnom=".$_SESSION['codigo_nomina']." ";
                        // $db->query($sql1);
                    }
                    
                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DB' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        $dbono = $tipo_nomina['diasbonvac'];
                        $sql1  = "SELECT SUM(valor) AS valor 
                                  FROM   nomcampos_adic_personal 
                                  WHERE  (id=6 OR id=7) AND ficha='{$fila['ficha']}' AND tiponom='{$_SESSION['codigo_nomina']}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();
                        $suel  = $fila['suesal'] / 30;
                        $total = (($fila1['valor'] / 30) + $suel) * $dbono;
                    }
                    
                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DI' AND tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        //  $sql1 = "INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', dpagob=0, fechaopr='".$today."', estado='Pendiente', tipooper='DI', desoper='Dias Incremento Bono', tipnom=".$_SESSION['codigo_nomina']." ";
                        //  $db->query($sql1);   
                    }
                    
                    $sql1 = "UPDATE nompersonal SET 
                             periodo     = '{$anio}', 
                             fechareivac = '{$fechaconsulta}', 
                             fechavac    = '{$fechainivac}' 
                             WHERE ficha='{$fila['ficha']}' AND tipnom='{$_SESSION['codigo_nomina']}'";
                    $db->query($sql1);
                }
                elseif( $tipo_nomina['tipodisfrute'] == "Ha" )
                {
                    $fecha    = explode("-", $fechaing3);  
                    $fecha[0] = $anio;
                    if( $fecha[1] == 2  &&  $fecha[2] == 29 )
                    {
                        $fecha[2] = 28;
                    }
                    $ano = $ano0 = $fecha[0];
                    $mes = $mes0 = $fecha[1];
                    $dia = $dia0 = $diainivac = $fecha[2];
                    $fechainivac = $ano ."-". $mes ."-". $dia;
                    $i = 0;
                    $iniciomes  = $ano ."-". $mes ."-01"; 
                    $diasvac    = $tipo_nomina['diasdisfrute'];
                    $numdiasmes = date("t", strtotime($iniciomes));

                    while( $i < $diasvac )
                    {
                        if( $numdiasmes == $diainivac )
                        {
                            $diainivac = 1;
                            $diainivac = "0". $diainivac;
                            $i = $i + 1;
                            if( $mes == 12 )
                            {
                                $mes = 1;
                                $ano = $ano + 1;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                            else
                            {
                                $mes = $mes + 1;
                                if( $mes <= 9 )
                                    $mes = "0". $mes;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                        }
                        else
                        {
                            $diainivac = $diainivac + 1;
                            if( $diainivac <= 9 )
                                $diainivac = "0". $diainivac;
                            $i = $i + 1;
                        }

                        $fechaconsulta = $ano ."-". $mes ."-". $diainivac;
                        $sql1  = "SELECT dia_fiesta 
                                  FROM   nomcalendarios_tiposnomina 
                                  WHERE  cod_tiponomina='{$_SESSION['codigo_nomina']}' AND fecha='{$fechaconsulta}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();

                        if( $fila1['dia_fiesta'] == 3  ||  $fila1['dia_fiesta'] == 1 )
                            $i = $i - 1;
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DV' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        $sql1 = "INSERT INTO nom_progvacaciones SET 
                                 periodo               = '{$anio}', 
                                 ficha                 = '{$fila['ficha']}', 
                                 ceduda                = '{$fila['cedula']}', 
                                 ddisfrute             = '{$diasvac}', 
                                 dpago                 = '{$tipo_nomina['diasbonvac']}', 
                                 fechareivac           = '', 
                                 monto_vac             = '{$fila['suesal']}', 
                                 saldo_dias            = '{$diasvac}', 
                                 fechavac              = '', 
                                 fecha_venc            = '{$fechainivac}', 
                                 fechaopr              = '{$today}', 
                                 estado                = 'Pendiente', 
                                 tipooper              = 'DV', 
                                 desoper               = 'Dias Vacaciones', 
                                 marca                 = 0, 
                                 dias_ppagar           = 0, 
                                 saldo_vacaciones      = 30, 
                                 dias_solic_ppagar     = 0, 
                                 dias_vac_disfrute     = 30, 
                                 saldo_dias_pdisfrutar = 30, 
                                 dias_solic_pdisfrutar = 0, 
                                 tipnom                = '{$_SESSION['codigo_nomina']}'";
                        $db->query($sql1);
                    }
                    
                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DA' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        // $sql1="INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', fechaopr='".$today."', ddisfrute=".$dias_incremento.", estado='Pendiente', tipooper='DA', desoper='Dias Vacaciones Adicionales', tipnom=".$_SESSION['codigo_nomina']." ";
                        // $db->query($sql1);   
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DB' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        $dbono = $tipo_nomina['diasbonvac'];
                        $sql1  = "SELECT SUM(valor) AS valor 
                                  FROM   nomcampos_adic_personal 
                                  WHERE  (id=6 OR id=7) AND ficha='{$fila['ficha']}' AND tiponom='{$_SESSION['codigo_nomina']}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();
                        $suel  = $fila['suesal'] / 30;
                        $total = (($fila1['valor'] / 30) + $suel) * $dbono;   
                    }
                    
                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DI' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        // $sql1="INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', dpagob=0, fechaopr='".$today."', estado='Pendiente', tipooper='DI', desoper='Dias Incremento Bono', tipnom=".$_SESSION['codigo_nomina']." ";
                        // $db->query($sql1);   
                    }
                    $sql1 = "UPDATE nompersonal SET 
                             periodo     = '{$anio}', 
                             fechareivac = '{$fechaconsulta}', 
                             fechavac    = '{$fechainivac}' 
                             WHERE ficha='{$fila['ficha']}' AND tipnom='{$_SESSION['codigo_nomina']}'";
                    $db->query($sql1);
                }
            }
        }
        elseif( $antiguedad >= ($tipo_nomina['diasantiguedad'] * 2) )
        {
            $xx = 0;
            if( $antmes >= 1 )
            {
                $xx = 1;
            }

            if( $_SESSION['nomina'] == 3 )
                $a = $antiguedad / 365;
            else
                $a = $antiguedad / 360;

            $antig_anos = (int) $a;

            $diasadic  = $tipo_nomina['diasincremdis'];
            $diasadicb = $tipo_nomina['diasincrem'];
            
            if( $tipo_nomina['quinquenio'] == 1 )
            {
                $antig_anos = $antig_anos + $fila['antiguedadap'];
                if( $antig_anos >= 1  &&  $antig_anos <= 5 )
                    $dias_incremento = 0;
                elseif( $antig_anos >= 6  &&  $antig_anos <= 10 )
                    $dias_incremento = $diasadic;
                elseif( $antig_anos >= 11  &&  $antig_anos <= 15 )
                    $dias_incremento = $diasadic * 2;
                elseif( $antig_anos >= 16 )
                    $dias_incremento = ($diasadic * 3 ) + 1;
            }
            else
            {
                if( $antig_anos >= $tipo_nomina['antigincremvac'] )
                {
                    $dias_incremento = ($antig_anos - $tipo_nomina['antigincremvac']) * $diasadic;
                    if( $dias_incremento >= $tipo_nomina['diasmaxincdis'] )
                        $dias_incremento = $tipo_nomina['diasmaxincdis'];
                }
                else
                    $dias_incremento = 0;
            }

            $dias_incremento_bono = ($antig_anos - 1) * $diasadicb;

            if( $dias_incremento_bono >= $tipo_nomina['diasmaxinc'] )
                $dias_incremento_bono = $tipo_nomina['diasmaxinc'];
            
            if( $fila['tipemp'] == "Fijo"  ||  $fila['tipemp'] == "Contratado" )
            {
                if( $tipo_nomina['tipodisfrute'] == "Co" )
                {
                    $fecha    = explode("-", $fechaing3);
                    $fecha[0] = $anio;
                    if( $fecha[1] == 2  &&  $fecha[2] == 29 )
                    {
                        $fecha[2] = 28;
                    }
                    $ano = $ano0 = $fecha[0];
                    $mes = $mes0 = $fecha[1];
                    $dia = $dia0 = $diainivac = $fecha[2];
                    $fechainivac = $ano ."-". $mes ."-". $dia;
                    $i = 0;

                    $iniciomes  = $ano ."-". $mes ."-01"; 
                    $diasvac    = $tipo_nomina['diasdisfrute'];
                    $diasvac1   = $diasvac + $dias_incremento;
                    $diasbono   = $dias_incremento_bono + $tipo_nomina['diasbonvac'];
                    $numdiasmes = date("t", strtotime($iniciomes));

                    while( $i < $diasvac1 )
                    {
                        if( $numdiasmes == $diainivac )
                        {
                            $diainivac = 1;
                            $diainivac = "0". $diainivac;
                            $i = $i + 1;
                            if( $mes == 12 )
                            {
                                $mes = 1;
                                $ano = $ano + 1;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                            else
                            {
                                $mes = $mes + 1;
                                if( $mes <= 9 )
                                    $mes = "0". $mes;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                        }
                        else
                        {
                            $diainivac = $diainivac + 1;
                            if( $diainivac <= 9 )
                                $diainivac = "0". $diainivac;
                            $i = $i + 1;
                        }
                        $fechaconsulta = $ano ."-". $mes ."-". $diainivac;
                        $sql1  = "SELECT dia_fiesta 
                                  FROM   nomcalendarios_tiposnomina 
                                  WHERE  cod_tiponomina='{$_SESSION['codigo_nomina']}' AND fecha='{$fechaconsulta}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();

                        if( $fila1['dia_fiesta'] == 3 )
                            $i = $i - 1;
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DV' AND tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        $sql1 = "INSERT INTO nom_progvacaciones SET 
                        periodo               = '{$anio}', 
                        ficha                 = '{$fila['ficha']}', 
                        ceduda                = '{$fila['cedula']}', 
                        ddisfrute             = '{$diasvac}', 
                        dpago                 = '{$diasbono}', 
                        
                        monto_vac             = '{$fila['suesal']}', 
                        saldo_dias            = '{$diasvac}', 
                        fechareivac           = '', 
                        fechavac              = '', 
                        fecha_venc            = '{$fechainivac}', 
                        fechaopr              = '{$today}', 
                        estado                = 'Pendiente', 
                        tipooper              = 'DV', 
                        desoper               = 'Dias Vacaciones', 
                        marca                 = 0, 
                        dias_ppagar           = 0, 
                        saldo_vacaciones      = 30, 
                        dias_solic_ppagar     = 0, 
                        dias_vac_disfrute     = 30, 
                        saldo_dias_pdisfrutar = 30, 
                        dias_solic_pdisfrutar = 0, 
                        tipnom                = '{$_SESSION['codigo_nomina']}'";
                        $db->query($sql1);
                    }
        
                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DA' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        // $sql1="INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', ddisfrute=".$dias_incremento.", fechaopr='".$today."', estado='Pendiente', tipooper='DA', desoper='Dias Vacaciones Adicionales', tipnom=".$_SESSION['codigo_nomina']." ";
                        // $db->query($sql1);   
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DB' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        $dbono = $tipo_nomina['diasbonvac'] + $dias_incremento_bono;
                        $sql1  = "SELECT SUM(valor) AS valor 
                                  FROM   nomcampos_adic_personal 
                                  WHERE  (id=6 OR id=7) AND ficha='{$fila['ficha']}' 
                                  AND    tiponom='{$_SESSION['codigo_nomina']}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();
                        $suel  = $fila['suesal'] / 30;
                        $total = (($fila1['valor'] / 30) + $suel) * $dbono;  
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DI' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        // $sql1="INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', dpagob=".$dias_incremento_bono.", fechaopr='".$today."', estado='Pendiente', tipooper='DI', desoper='Dias Incremento Bono', tipnom=".$_SESSION['codigo_nomina']." ";
                        // $db->query($sql1);
                    }
                
                    $sql1 = "UPDATE nompersonal SET 
                             periodo     = '{$anio}', 
                             fechareivac = '{$fechaconsulta}', 
                             fechavac    = '{$fechainivac}' 
                             WHERE ficha='{$fila['ficha']}' AND tipnom='{$_SESSION['codigo_nomina']}'";
                    $db->query($sql1);
                }
                elseif( $tipo_nomina['tipodisfrute'] == "Ha" )
                {
                    $fecha    = explode("-",$fechaing3); 
                    $fecha[0] = $anio;
                    if( $fecha[1] == 2  &&  $fecha[2] == 29 )
                    {
                        $fecha[2] = 28;
                    }
                    $ano = $ano0 = $fecha[0];
                    $mes = $mes0 = $fecha[1];
                    $dia = $dia0 = $diainivac = $fecha[2];
                    $fechainivac = $ano ."-". $mes ."-". $dia;
                    $i = 0;
                    $iniciomes  = $ano ."-". $mes ."-01"; 
                    $diasvac    = $tipo_nomina['diasdisfrute'];
                    $diasvac1   = $diasvac + $dias_incremento;
                    $diasbono   = $dias_incremento_bono + $tipo_nomina['diasbonvac'];
                    $numdiasmes = date("t", strtotime($iniciomes));

                    while( $i < $diasvac1 )
                    {
                        if( $numdiasmes == $diainivac )
                        {
                            $diainivac = 1;
                            $diainivac = "0". $diainivac;
                            $i = $i + 1;
                            if( $mes == 12 )
                            {
                                $mes = 1;
                                $ano = $ano + 1;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                            else
                            {
                                $mes = $mes + 1;
                                if( $mes <= 9 )
                                    $mes = "0". $mes;
                                $iniciomes  = $ano ."-". $mes ."-01"; 
                                $numdiasmes = date("t", strtotime($iniciomes));
                            }
                        }
                        else
                        {
                            $diainivac = $diainivac + 1;
                            if( $diainivac <= 9 )
                                $diainivac = "0". $diainivac;
                            $i = $i + 1;
                        }
                        $fechaconsulta = $ano ."-". $mes ."-". $diainivac;
                        $sql1  = "SELECT dia_fiesta 
                                  FROM   nomcalendarios_tiposnomina 
                                  WHERE  cod_tiponomina='{$_SESSION['codigo_nomina']}' AND fecha='{$fechaconsulta}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();
                        if( $fila1['dia_fiesta'] == 3  ||  $fila1['dia_fiesta'] == 1 )
                            $i = $i - 1;
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DV' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                                 $sql1                 = "INSERT INTO nom_progvacaciones SET 
                                 periodo               = '{$anio}', 
                                 ficha                 = '{$fila['ficha']}', 
                                 ceduda                = '{$fila['cedula']}', 
                                 ddisfrute             = '{$diasvac}', 
                                 dpago                 = '{$diasbono}', 
                                 
                                 monto_vac             = '{$fila['suesal']}', 
                                 saldo_dias            = '{$diasvac}', 
                                 fechareivac           = '', 
                                 fechavac              = '', 
                                 fecha_venc            = '{$fechainivac}', 
                                 fechaopr              = '{$today}', 
                                 estado                = 'Pendiente', 
                                 tipooper              = 'DV', 
                                 desoper               = 'Dias Vacaciones', 
                                 marca                 = 0, 
                                 dias_ppagar           = 0, 
                                 saldo_vacaciones      = 30, 
                                 dias_solic_ppagar     = 0, 
                                 dias_vac_disfrute     = 30, 
                                 saldo_dias_pdisfrutar = 30, 
                                 dias_solic_pdisfrutar = 0, 
                                 tipnom                = '{$_SESSION['codigo_nomina']}'";
                        $db->query($sql1);
                    }
        
                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DA' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        // $sql1 ="INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', ddisfrute=".$dias_incremento.", fechaopr='".$today."', estado='Pendiente', tipooper='DA', desoper='Dias Vacaciones Adicionales', tipnom=".$_SESSION['codigo_nomina']." ";
                        // $db->query($sql1);   
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DB' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        $dbono = $tipo_nomina['diasbonvac'] + $dias_incremento_bono;
                        $sql1  = "SELECT SUM(valor) AS valor 
                                  FROM   nomcampos_adic_personal 
                                  WHERE  (id=6 OR id=7) AND ficha='{$fila['ficha']}' AND tiponom='{$_SESSION['codigo_nomina']}'";
                        $fila1 = $db->query($sql1)->fetch_assoc();
                        $suel  = $fila['suesal'] / 30;
                        $total = (($fila1['valor'] / 30) + $suel) * $dbono;  
                    }

                    $sql1 = "SELECT ceduda 
                             FROM   nom_progvacaciones 
                             WHERE  periodo='{$anio}' AND ficha='{$fila['ficha']}' AND tipooper='DI' 
                             AND    tipnom='{$_SESSION['codigo_nomina']}'";
                    $res1 = $db->query($sql1);
                    if( $res1->num_rows == 0 )
                    {
                        // $sql1 = "INSERT INTO nom_progvacaciones SET periodo=".$anio.", ficha=".$fila['ficha'].", ceduda='".$fila['cedula']."', dpagob=".$dias_incremento_bono.", fechaopr='".$today."', estado='Pendiente', tipooper='DI', desoper='Dias Incremento Bono', tipnom=".$_SESSION['codigo_nomina']." ";
                        // $db->query($sql1);   
                    }
                    $sql1 = "UPDATE nompersonal SET 
                             periodo     = '{$anio}', 
                             fechareivac = '{$fechaconsulta}', 
                             fechavac    = '{$fechainivac}' 
                             WHERE ficha='{$fila['ficha']}' AND tipnom='{$_SESSION['codigo_nomina']}'";
                    $db->query($sql1);
                }
            }   
        }    
    }
    ?>
    <script>
        alert("\u00A1Vacaciones generadas exitosamente!");
        document.location.href = "submenu_vacaciones.php";
    </script>
<?php   
}

function antiguedad($fecha1, $fecha2, $tipo)
{   
    $datetime1 = new DateTime($fecha1);
    $datetime2 = new DateTime($fecha2);

    $datetime1->setTime(0, 0, 0);
    $datetime2->setTime(0, 0, 0);

    if( ($datetime1 > $datetime2)  ||  ($tipo == "") ) 
        return;

    $diff = $datetime1->diff($datetime2);

    if( $tipo == "A" )
        return $diff->y;

    if( $tipo == "M" )
        return $diff->m;

    if( $tipo == "D" )
        return $diff->days;
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Vacaciones</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/css/bootstrap-datepicker3.min.css"/>
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<style>
body {  /* En uso */
  background-color: white !important; 
}

.page-content-wrapper { /* En uso */
  background-color: white !important; 
}

.page-sidebar-closed .page-content { /* En uso */
  margin-left: 0px !important;
}

.portlet > .portlet-title > .caption { /* En uso */
  font-family: helvetica, arial, verdana, sans-serif;
  font-size: 13px;
  font-weight: bold;
  line-height: 21px;
  margin-bottom: 5px;
}

.margin-left-10 {
    margin-left: 10px;
}

.margin-right-10 {
    margin-right: 10px;
}

.padding-right-20 { /* En uso */
    padding-right: 20px !important;
}

label.error { /* En uso */
    color: #b94a48;
}

@media (min-width: 768px)
{
  .form-horizontal .control-label {  /* En uso */
      text-align: left;
      padding-left: 40px;
  }
}

.hide-underline{ /* En uso */
    text-decoration: none !important;
}

.btn-md { /* En uso */
    padding: 5px 12px; 
}
</style>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width">
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-title">Vacaciones</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li><i class="glyphicon glyphicon glyphicon-sort"></i>
                            <a class="hide-underline">Transacciones</a><i class="fa fa-angle-right"></i>
                        </li>
                        <li><a href="submenu_vacaciones.php">Vacaciones</a></li>
                    </ul>
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tab-content">
                                <div class="portlet box blue">
                                    <div class="portlet-title">
                                        <div class="caption"><i class="fa fa-calendar"></i> Generar programa anual de vacaciones</div>
                                        <div class="actions">
                                            <a class="btn btn-sm blue active"  onclick="javascript: window.location='submenu_vacaciones.php'">
                                                <i class="fa fa-arrow-left"></i> Regresar
                                            </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body form" id="blockui_portlet_body">

                                        <?php 
                                            if( $opcion != 1 )
                                            {
                                            ?>
                                                <br>
                                                <div class="alert alert-info margin-left-10 margin-right-10 hide">
                                                    Nota: este proceso genera el programa anual de vacaciones del personal de la planilla actual.
                                                </div>
                                                <div class="note note-info margin-left-10 margin-right-10">
                                                    <p>Nota: este proceso genera el programa anual de vacaciones del personal de la planilla actual.</p>
                                                </div>
                                            <?php
                                            }
                                        ?>                                

                                        <form action="#" id="frmPrincipal" name="frmPrincipal" class="form-horizontal" method="post">

                                            <div class="form-body">

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">A&ntilde;o a procesar</label>
                                                    <div class="col-md-3 padding-right-20">
                                                        <input type="text" name="anio" id="anio" class="form-control" value="<?php echo date("Y"); ?>">
                                                    </div>
                                                </div>
                                                
                                                <?php
                                                    if($opcion==1)
                                                    {
                                                        ?>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">C&eacute;dula del trabajador</label>
                                                                <div class="col-md-3 padding-right-20">
                                                                    <input type="text" name="cedula" id="cedula" class="form-control">
                                                                </div>
                                                            </div>
                                                        <?php
                                                    }
                                                ?>
                                            </div>

                                            <div class="form-actions fluid">
                                                <div class="col-md-12 text-center">
                                                    <button type="button" class="btn blue btn-md" 
                                                    onclick="javascript: enviar();"><i class="fa fa-check"></i> Aceptar</button>
                                                    <button type="button" class="btn default btn-md"
                                                    onclick="javascript: window.location='submenu_vacaciones.php';">Cancelar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../includes/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/locales/bootstrap-datepicker.es.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>
<script>
jQuery(document).ready(function() {
    App.init();
});
</script>
<script>
function showProcesando()
{
    App.blockUI({
        target: '#blockui_portlet_body',
        boxed: true,
        message: 'Procesando'
    });
}

function enviar()
{
    validate = $("#frmPrincipal").valid();

    if(!validate) return false;

    if (confirm("\u00BFEst\u00E1 seguro que desea generar el programa anual de vacaciones?"))
    {
        showProcesando();
    
        document.frmPrincipal.submit();       
    }
}

$(document).ready(function(){
    var opcion = '<?php echo ($opcion==1) ? 1 : 0; ?>';

    opcion = (opcion=='1') ? true : false;

    $("#frmPrincipal").validate({
        rules: {
            anio: {
                required: true,
                digits:   true
            },
            cedula: {
                required: function() {
                    //console.log("Opcion: " + opcion);
                    return opcion;
                }
            }
        }
    });
});
</script>
</body>
</html>