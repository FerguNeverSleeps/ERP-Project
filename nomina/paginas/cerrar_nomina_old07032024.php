<?php 
require '../lib/database.php';

set_time_limit(0);

$db = new Database($_SESSION['bd']);

// Cerrar la N�mina
$sql_nomina = "SELECT codnom, codtip, periodo_ini, periodo_fin, fechapago, anio, mes, periodo, frecuencia 
	             FROM   nom_nominas_pago
		           WHERE  codnom = '{$_GET['codigo_nomina']}' AND codtip = '{$_SESSION['codigo_nomina']}'"; // tipnom='{$_SESSION['codigo_nomina']}'
$res_nomina = $db->query($sql_nomina);
$fila_nomina = $res_nomina->fetch_assoc();
$codnom = $fila_nomina['codnom'];
$codtip = $fila_nomina['codtip'];
$periodo_ini = $fila_nomina['periodo_ini'];
$periodo_fin = $fila_nomina['periodo_fin'];
$fechapago = $fila_nomina['fechapago'];
$anio = $fila_nomina['anio'];
$mes = $fila_nomina['mes'];
$periodo = $fila_nomina['periodo'];
$frecuencia = $fila_nomina['frecuencia'];
// Convierte la cadena en un objeto DateTime
$fecha = new DateTime($periodo_ini);

$empresaPermitido = "SELECT * FROM nomempresa WHERE nom_emp IN ('INGENIERIA Y SOLUCIONES ESPECIALIZADAS S A S (ISES S A S )')";
$respuestaPermitido = $db->query($empresaPermitido);


if($respuestaPermitido->num_rows==1){

  $nuevaConsulta = "SELECT
                        nmn.cedula,
                        npcc.codigo_proyecto_cc,
                        CASE WHEN npcc.porcentaje IS NULL THEN '100.00' ELSE npcc.porcentaje END AS porcentaje,
                        CASE WHEN  LEFT(pcc.cuenta_contable, 2) IS NULL THEN LEFT(nc.ctacon, 2) ELSE LEFT(pcc.cuenta_contable, 2) END AS mayorCtaCon,
                        CASE WHEN pcc.cuenta_contable IS NULL THEN nc.ctacon ELSE pcc.cuenta_contable END AS cuenta_contable,
                        nmn.monto,
                        nmn.ficha,
                        nmn.codcon,
                        nmn.descrip,
                        nc.ctacon,
                        nc.tipcon,
                        nc.ctacon74,
                        nc.ctacon52,
                        nmp.periodo_ini,
                        nmp.periodo_fin,
                        nc.ccosto
                    FROM nom_movimientos_nomina AS nmn
                      INNER JOIN nom_nominas_pago nmp ON nmp.codnom = nmn.codnom AND nmp.anio = nmn.anio AND nmp.mes = nmn.mes
                      LEFT JOIN nompersonal_pry_cc AS npcc ON npcc.cedula = nmn.cedula AND nmp.periodo_ini BETWEEN npcc.fecha_inicio AND npcc.fecha_fin
                        AND nmp.periodo_fin BETWEEN npcc.fecha_inicio AND npcc.fecha_fin
                      LEFT JOIN proyecto_cc pcc ON npcc.codigo_proyecto_cc = pcc.codigo
                      INNER JOIN nomconceptos nc ON nc.codcon = nmn.codcon
                    WHERE nmn.codnom = '{$codnom}' AND nmp.periodo_ini = '{$periodo_ini}' AND nmp.periodo_fin = '{$periodo_fin}'
                    ORDER BY nmn.cedula, npcc.codigo_proyecto_cc, nmn.codcon ASC;";

  $ejecutar = $db->query($nuevaConsulta);

  $filasInsertadas = [];
  $salariosPorCedula = array();

  while($nuevaFila = $ejecutar->fetch_array()){
      $porcentaje = $nuevaFila['porcentaje'];
      $cedula = $nuevaFila['cedula'];
      $cuentaContableCC = $nuevaFila['cuenta_contable'];
      $codigo_proyecto = $nuevaFila['codigo_proyecto_cc'];
      $monto = $nuevaFila['monto'];
      $ficha = $nuevaFila['ficha'];
      $cuentaContableMayor = $nuevaFila['mayorCtaCon'];
      $cuentaContableReal = $nuevaFila['ctacon'];
      $cuentaContableReal74 = $nuevaFila['ctacon74'];
      $cuentaContableReal52 = $nuevaFila['ctacon52'];
      $codigoconcepto = $nuevaFila['codcon'];
      $descripcion = $nuevaFila['descrip'];
      $tipo_cuenta = $nuevaFila['tipcon'];
      $fecha_inicio = $nuevaFila['periodo_ini'];
      $fecha_fin = $nuevaFila['periodo_fin'];
      $ccosto = $nuevaFila['ccosto'];
      $nuevo_valor = ($monto * $porcentaje) / 100;

      // if ($monto == $nuevo_valor) {
      //   $codigo_proyecto = 'ADMINISTRATIVO';
      // }

      // Agregar estas variables al arreglo de acumulación
      if (!isset($montosAcumuladosPorCedulaPorPorcentaje[$cedula])) {
          $montosAcumuladosPorCedulaPorPorcentaje[$cedula] = array();
      }

      $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentaje][$tipo_cuenta] += $nuevo_valor;
      $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentaje][$codigoconcepto] += $nuevo_valor;
      $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentaje]['codnom'] = $codnom;
      $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentaje]['codigo_proyecto'] = $codigo_proyecto;
      $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentaje]['ficha'] = $ficha;
      $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentaje]['fecha_inicio'] = $fecha_inicio;
      $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentaje]['fecha_fin'] = $fecha_fin;

      if (!isset($salariosPorCedula[$cedula])) {
          $salariosPorCedula[$cedula] = array();
      }

      if (!isset($salariosPorCedula[$cedula][$descripcion])) {
          $salariosPorCedula[$cedula][$descripcion] = array(
              'codnom' => $codnom,
              'codigo_proyecto' => $codigo_proyecto,
              'ficha' => $ficha,
              'monto' => $monto,
              'ccosto' => $ccosto,
              'codigo_concepto' => $codigoconcepto,
              'cuenta_contable_real' => $cuentaContableReal,
              'tipo_cuenta' => $tipo_cuenta,
              'fecha_inicio' => $fecha_inicio,
              'fecha_fin' => $fecha_fin,
              'porcentaje' => 0, // El porcentaje total se actualizará en el bucle foreach final
          );
      }

      $salariosPorCedula[$cedula][$descripcion]['porcentaje'] += $porcentaje;


      if ($cuentaContableMayor == 74 && $descripcion != 'SALARIO' && $tipo_cuenta != 'D') {
        $cuentaContableReal = $cuentaContableReal74;
      }
      else if ($cuentaContableMayor == 52 && $descripcion != 'SALARIO' && $tipo_cuenta != 'D') {
        $cuentaContableReal = $cuentaContableReal52;
      }

      if ($descripcion == 'SALARIO') {
        $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$nuevo_valor', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '$monto', '$cuentaContableCC', '$codigoconcepto', '$descripcion', '$tipo_cuenta', '$ccosto')";
      }
      else {
        if ($tipo_cuenta == 'D') {
          $nuevo_valor = -1 * $nuevo_valor;
        }
        $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$nuevo_valor', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '$monto', '$cuentaContableReal', '$codigoconcepto', '$descripcion', '$tipo_cuenta', '$ccosto')";
      }

  }

  foreach ($salariosPorCedula as $cedula => $descripciones) {
    foreach ($descripciones as $descripcion => $info) {
      $totalPorcentaje = $info['porcentaje'];
      // Agregar estas variables al arreglo de acumulación
      if (!isset($montosAcumuladosPorCedulaPorPorcentaje[$cedula])) {
        $montosAcumuladosPorCedulaPorPorcentaje[$cedula] = array();
      }

      if ($totalPorcentaje < 100) {
          $porcentajeFaltante = 100 - $totalPorcentaje;
          $nuevoValor = ($info["monto"] * $porcentajeFaltante) / 100;

          $codnom = $info["codnom"];
          $codigo_proyecto = "ADMINISTRATIVO";
          $ficha = $info["ficha"];
          $fecha_inicio = $info["fecha_inicio"];
          $fecha_fin = $info["fecha_fin"];
          $monto = $info["monto"];
          $cuenta_contable_real = $info["cuenta_contable_real"];
          $codigo_concepto = $info["codigo_concepto"];
          $tipo_cuenta = $info["tipo_cuenta"];
          $ccosto = $info['ccosto'];

          $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentajeFaltante][$tipo_cuenta] += $nuevoValor;
          $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentajeFaltante][$codigo_concepto] += $nuevoValor;
          $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentajeFaltante]['codnom'] = $codnom;
          $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentajeFaltante]['codigo_proyecto'] = $codigo_proyecto;
          $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentajeFaltante]['ficha'] = $ficha;
          $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentajeFaltante]['fecha_inicio'] = $periodo_ini;
          $montosAcumuladosPorCedulaPorPorcentaje[$cedula][$porcentajeFaltante]['fecha_fin'] = $periodo_fin;

          if ($tipo_cuenta == 'D') {
            $nuevoValor = -1 * $nuevoValor;
          }

          $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentajeFaltante', '$nuevoValor', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '$monto', '$cuenta_contable_real', '$codigo_concepto', '$descripcion', '$tipo_cuenta', '$ccosto')";


      }
    }
  }

  // Resta los montos acumulados de los códigos 200, 201 y 202 al código 100
  foreach ($montosAcumuladosPorCedulaPorPorcentaje as $cedula => $porcentajes) {
      foreach ($porcentajes as $porcentaje => $montos) {
          // Acceder a los valores adicionales
          $codnom = $montos['codnom'];
          $codigo_proyecto = $montos['codigo_proyecto'];
          $ficha = $montos['ficha'];
          $fecha_inicio = $montos['fecha_inicio'];
          $fecha_fin = $montos['fecha_fin'];

          //Valores totales por cada tipo
          $tipoA = isset($montos['A']) ? $montos['A'] : 0;
          $tipoD = isset($montos['D']) ? $montos['D'] : 0;
          $tipoP = isset($montos['P']) ? $montos['P'] : 0;

          //Valores totales por cada concepto
          $monto3000 = isset($montos['3000']) ? $montos['3000'] : 0;
          $monto3001 = isset($montos['3001']) ? $montos['3001'] : 0;
          $monto3002 = isset($montos['3002']) ? $montos['3002'] : 0;
          $monto9005 = isset($montos['9005']) ? $montos['9005'] : 0;
          $monto9006 = isset($montos['9006']) ? $montos['9006'] : 0;
          $monto9007 = isset($montos['9007']) ? $montos['9007'] : 0;
          $monto9008 = isset($montos['9008']) ? $montos['9008'] : 0;
          $monto9009 = isset($montos['9009']) ? $montos['9009'] : 0;

          // Hallamos el valor de Salario por pagar
          $resultado_de_la_resta = $tipoA - $tipoD;
          if ($resultado_de_la_resta > 0) {
            $resultado = -1 * $resultado_de_la_resta;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25050101', '25', 'SALARIOS POR PAGAR', 'D', '0')";
          }

          if ($monto3000 > 0) {
            $resultado = -1 * $monto3000;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '23700501', '26', 'SEGURO SOCIAL PATRONAL POR PAGAR', 'D', '1')";
          }
          if ($monto3001 > 0) {
            $resultado = -1 * $monto3001;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '23701001', '27', 'SEGURO EDUCATIVO PATRONAL POR PAGAR', 'D', '1')";
          }
          if ($monto3002 > 0) {
            $resultado = -1 * $monto3002;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25100101', '28', 'PATRONAL C.S.S XIII POR PAGAR', 'D', '1')";
          }
          if ($monto9005 > 0) {
            $resultado = -1 * $monto9005;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25200101', '29', 'PRIMA DE ANTIGUEDAD PROVISIÓN POR PAGAR', 'D', '0')";
          }
          if ($monto9006 > 0) {
            $resultado = -1 * $monto9006;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25050101', '30', 'INDEMNIZACIÓN PROVISIÓN POR PAGAR', 'D', '0')";
          }
          if ($monto9007 > 0) {
            $resultado = -1 * $monto9007;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25100101', '31', 'XIII MES PROVISIÓN POR PAGAR', 'D', '0')";
          }
          if ($monto9008 > 0) {
            $resultado = -1 * $monto9008;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25250102', '32', 'VACACIONES PROVISIÓN POR PAGAR', 'D', '0')";
          }
          if ($monto9009 > 0) {
            $resultado = -1 * $monto9009;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '23700601', '33', 'RIESGO PROFESIONAL POR PAGAR', 'D', '1')";
          }
      }
  }

  if (!empty($filasInsertadas)) {
      $otraConsulta = "
      INSERT INTO nomina_proyecto_cc
      (cod_nomina, codigo_proyecto, porcentaje, monto, ficha, cedula, fecha_inicio, fecha_fin, quincena, cuenta_contable, codigo_concepto, concepto, tipo_cuenta, acreedor)
      VALUES " . implode(', ', $filasInsertadas);

      $db->query($otraConsulta);
  }
}


// Generar netos
$sql = "SELECT ficha, cedula, apenom, cuentacob, forcob,codbancob, codnivel1, codnivel2, codnivel3, codnivel4, codnivel5, suesal, codcargo, estado  "
        . "FROM nompersonal WHERE tipnom='{$_SESSION['codigo_nomina']}'";
$res = $db->query($sql);

$estatus=1;

$sqlrpt = "select nombre_corto,conceptos from config_reportes_planilla_columnas where id_reporte=4;";
$res_rpt = $db->query($sqlrpt);

while($config=$res_rpt->fetch_array()){
    ${$config['nombre_corto']} = $config['conceptos'];
}
/*
$con_salario_bruto="90,95,100,110,115,169,159,160,161,162,171,105,106,107,108,109,111,112,113,116,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,142,143,144,149,150,151,152,153,154,155,163,164,165,166,167,168,172,174"; 
$con_vac="91,114";
$con_xiii="92,102";
$con_gr="145";
$con_xiii_gr="101";
$con_liquida="93,94,96,97";
$con_bono="189,190";
$con_otros_ing="141,147,157,158";
$con_prima="175";
$con_s_s="200";
$con_s_e="201,206";
$con_isr="202,211,213,214,215";
$con_isr_gr="208";
$con_acreedor="204,216,217,218,219,220,221,222,223,224,225,500,501,502,503,504,505,506,507,508,509,510,511,512,513,514,515,516,517,518,"
            . "519,520,521,522,523,524,525,526,527,528,529,530,531,532,533,534,535,536,537,538,539,540,541,542,543,544,545,546,547,548,"
            . "549,550,551,552,553,554,555,556,557,558,559,560,561,562,563,564,565,566,567,568,569,570,571,572,573,574,575,576,577,578,"
            . "579,580,581,582,583,584,585,586,587,588,589,590,591,592,593,594,595,596,597,598,599";
$con_tardanza="199,203";*/

$sqlNeto = "INSERT INTO nom_nomina_netos 
            (codnom, 
            tipnom, 
            ficha, 
            cedula,
            cta_ban,
            forcob,
            codbancob,
            neto, 
            codnivel1,
            codnivel2,
            codnivel3,
            codnivel4,
            codnivel5,
            suesal,
            cod_cargo) 
            VALUES ";

$sqlNetoValues = array();

$sql_salarios_acumulados = "INSERT INTO salarios_acumulados 
                            (ficha,
                            cedula, 
                            fecha_pago, 
                            cod_planilla, 
                            tipo_planilla,
                            frecuencia_planilla,
                            salario_bruto,
                            vacac,
                            xiii,
                            gtorep,
                            xiii_gtorep,
                            liquida,
                            bono,
                            otros_ing,
                            prima,
                            s_s,
                            s_e,
                            islr,
                            islr_gr,
                            acreedor_suma,
                            viaticos,
                            comisiones,
                            gratificaciones,
                            donaciones,
                            desc_empresa,
                            Neto,
                            estatus) 
                            VALUES ";

$sql_salarios_acumulados_values = array();
$fichas = array();

$fichas['Estado'] = "1";
while($fila = $res->fetch_assoc())
{
    $ficha=$fila['ficha'];
    $cedula=$fila['cedula'];
    $codnivel1=$fila['codnivel1'];
    $codnivel2=$fila['codnivel2'];
    $codnivel3=$fila['codnivel3'];
    $codnivel4=$fila['codnivel4'];
    $codnivel5=$fila['codnivel5'];
    $suesal=$fila['suesal'];
    $codcargo=$fila['codcargo'];
    $estado=$fila['estado'];
    
    $sql2 = "SELECT tipcon, monto, codcon, valor, tipopr, numpre, numcuo, fechaven, salfinal
         FROM   nom_movimientos_nomina 
         WHERE  codnom='$codnom' AND ficha='{$fila['ficha']}'  
         AND    tipnom='{$_SESSION['codigo_nomina']}'";
	
	$res2 = $db->query($sql2);

	$asignaciones = $deducciones = 0;
	
	if($res2->num_rows > 0)
	{
		while($fila2 = $res2->fetch_assoc())
		{
			if($fila2['tipcon']=='A')
				$asignaciones+=$fila2['monto'];
			elseif($fila2['tipcon']=='D')
				$deducciones+=$fila2['monto'];

			if($fila2['codcon']==114)
			{
				$sql3 = "UPDATE nompersonal 
				         SET    estado='Vacaciones' 
				         WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND ficha='{$fila['ficha']}'";
				$res3 = $db->query($sql3);
 
				// $sql3 = "UPDATE nom_progvacaciones SET estado='Pagada' WHERE ficha='{$fila['ficha']}'";
			    $sql3 = "UPDATE nom_progvacaciones 
			             SET    estado='Pagada' 
			             WHERE  ficha='{$fila['ficha']}' "
                                     . "AND (fechavac>='{$periodo_ini}' AND fechareivac<='{$periodo_fin}' AND saldo_vacaciones=0  AND marca ='1')";
				$res3 = $db->query($sql3);			 
			}

			if($fila2['codcon']>=500 && $fila2['codcon']<=599)
			{
                            if($fila2['codcon']==590)
                            {
                                // Actualizar el campo adicional
                                //Concepto Ahorro (590) se debe ir a campos adicionales 22 actualizar el saldo    
                                $sql_adi = "SELECT valor from nomcampos_adic_personal "
                                        . "WHERE ficha ='{$fila['ficha']}' AND id='22' AND tiponom='{$_SESSION['codigo_nomina']}'";
                                $res_adi = $db->query($sql_adi);
                                $fila_adi = $res_adi->fetch_assoc();
                                $valor_adi=$fila_adi['valor'];
                                    $valor=$valor_adi-$fila2['monto'];

                                $sqladi= "UPDATE `nomcampos_adic_personal`
                                          SET valor=$valor
                                          WHERE ficha='{$fila['ficha']}' AND id='22' AND tiponom='{$_SESSION['codigo_nomina']}'";
                                $resadi= $db->query($sqladi);

                            }
                            else
                            {
                                
                                $sql3 = "UPDATE nomprestamos_detalles 
					     SET    estadopre='Cancelada' 
						 WHERE  codnom='{$_SESSION['codigo_nomina']}' AND ficha='{$fila['ficha']}' 
						 AND    fechaven BETWEEN '{$periodo_ini}' AND '{$periodo_fin}'
						 AND    estadopre='Pendiente'";
				$res3 = $db->query($sql3);
				// Para optimizar la consulta crear el �ndice (Esta tabla no tiene primary key ni �ndices):
                                // CREATE INDEX fechaven_idx ON nomprestamos_detalles (fechaven);
                                $sql_prestamos = "SELECT numpre,numcuo,count(*) cuotas from nomprestamos_detalles WHERE fechaven BETWEEN '{$periodo_ini}' AND '{$periodo_fin}' AND ficha='{$fila['ficha']}' GROUP BY numpre";
                                $res_prestamos = $db->query($sql_prestamos);
                                while($fila_pres = $res_prestamos->fetch_assoc())
                                {
                                        $sql_cabecera = "SELECT cuotas from nomprestamos_cabecera WHERE ficha ='{$fila[ficha]}' and numpre = '{$fila_pres[numpre]}' ";
                                        $res_cabecera = $db->query($sql_cabecera);
                                        $fila_cabecra = $res_cabecera->fetch_assoc();
                                        if($fila_cabecra['cuotas'] == $fila_pres['numcuo'] ){
                                                $sql_cab = "UPDATE nomprestamos_cabecera SET estadopre = 'Cancelada' where numpre ='{$fila_pres['numpre']}'";
                                                $db->query($sql_cab);

                                        }
                                }
                                
                                
                                /*
                                $sql_prestamos_cuota = "UPDATE nomprestamos_detalles 
					     SET    estadopre='Cancelada' 
						 WHERE  codnom='{$_SESSION['codigo_nomina']}' AND ficha='{$fila['ficha']}' 
						 AND    numpre='{$fila2['numpre']}' AND numcuo='{$fila2['numcuo']}' AND fechaven='{$fila2['fechaven']}'
						 AND    estadopre='Pendiente'";
				$res_prestamos_cuota = $db->query($sql_prestamos_cuota);
				
                                if($fila2['salfinal']==0.00 || $fila2['salfinal']=="0.00")
                                {
                                        $sql_prestamos = "UPDATE nomprestamos_cabecera SET estadopre = 'Cancelada' "
                                                . "where numpre ='{$fila2['numpre']}' AND ficha='{$fila['ficha']}'";
                                        $res_prestamos=$db->query($sql_prestamos);

                                }*/
                               
                            }
                                
			}
                        
                        

			$sql3 = "SELECT cod_tac, operacion 
					 FROM   nomconceptos_acumulados 
				     WHERE  codcon='{$fila2['codcon']}'";
			$res3 = $db->query($sql3);
			 
			while($fila3 = $res3->fetch_assoc())
			{	
				if($fila3['cod_tac'] == 'LIQ')
				{
					$sql4 = "UPDATE nompersonal SET 
					         estado = 'Egresado', 
					         fecharetiro = sysdate() 
					         WHERE ficha = '{$fila['ficha']}'";
					$res4 = $db->query($sql4);	
				}

				$sql4 = "INSERT INTO nomacumulados_det 
				         (ficha, ceduda, anioa, mesa, fecha, cod_tac, montototal, codcon, codnom, tipnom, operacion, refer)
				         VALUES 
				         ('{$fila['ficha']}', '{$fila['cedula']}', '{$anio}', '{$mes}', 
				          '".date('Y-m-d')."', '{$fila3['cod_tac']}', '{$fila2['monto']}', '{$fila2['codcon']}', 
				          '{$_GET['codigo_nomina']}', '{$_SESSION['codigo_nomina']}', '{$fila3['operacion']}',
				          '{$fila2['valor']}')";
				$res4 = $db->query($sql4) ;
			}
                        if($estado == 'Nuevo')
                        {
                                $sql4 = "UPDATE nompersonal SET 
                                         estado = 'Activo'
                                         WHERE ficha = '{$fila['ficha']}' AND estado = 'Nuevo';";
                                $res4 = $db->query($sql4);      
                        }

        }
        $bd_asig=new Database($_SESSION['bd']);

        $sql_asig3 = "SELECT SUM(nmn.monto) as monto
        FROM nom_movimientos_nomina nmn 
        INNER JOIN nompersonal np ON (np.ficha=nmn.ficha) 
        WHERE nmn.codnom='".$codnom."' AND nmn.tipnom='".$codtip."' AND nmn.ficha='{$ficha}' AND nmn.tipcon='A'
        AND (nmn.codcon >= '300' AND nmn.codcon <='399')
        ORDER BY nmn.ficha,nmn.codcon";
    
        $res_asig3=$bd_asig->query($sql_asig3);
        $monto_asig3 = $res_asig3->fetch_assoc();
    
        /** DEDUCCIONES **/
        $bd_deduc=new Database($_SESSION['bd']);
        $sql_deduc3 = "SELECT SUM(nmn.monto) as monto
        FROM nom_movimientos_nomina nmn 
        INNER JOIN nompersonal np ON (np.ficha=nmn.ficha) 
        WHERE nmn.codnom='".$codnom."' AND nmn.tipnom='".$codtip."' AND nmn.ficha='{$ficha}' AND nmn.tipcon='D'
        AND (nmn.codcon >= '300' AND nmn.codcon <='399')
        ORDER BY nmn.ficha,nmn.codcon";
        
        /** Cálculo del neto**/
        $res_deduc3=$bd_deduc->query($sql_deduc3);
        $monto_deduc3 = $res_deduc3->fetch_assoc();
        $neto3 = $monto_asig3[monto] - $monto_deduc3[monto];
        $neto3_empleado=number_format($neto3,2,'.','');

		$neto=$asignaciones-$deducciones;

        if($neto!=0 AND $neto>0)
        {
                // $sql2 = "INSERT INTO nom_nomina_netos (codnom, tipnom, ficha, cedula, cta_ban, neto) 
                //          VALUES 
                //          ('{$_GET['codigo_nomina']}', '{$_SESSION['codigo_nomina']}', '{$fila['ficha']}',
                //           '{$fila['cedula']}', '{$fila['cuentacob']}', '{$neto}')";
                // $res2 = $db->query($sql2);

                $sqlNetoValues[] = "('{$_GET['codigo_nomina']}',"
                . " '{$_SESSION['codigo_nomina']}', "
                . "'{$fila['ficha']}', ".
                "'{$fila['cedula']}', "
                . "'{$fila['cuentacob']}',"
                . " '{$fila['forcob']}',"
                . "'{$fila['codbancob']}',"
                . "'{$neto}',"
                . "'{$codnivel1}',"
                . "'{$codnivel2}',"
                . "'{$codnivel3}',"
                . "'{$codnivel4}',"
                . "'{$codnivel5}',"
                . "'{$suesal}',"
                . "'{$codcargo}')";

                //-----------------------------------------------------------------------------------------------------------
                // Actualizar campos adicionales (97, 98 y 99) al cerrar la Planilla de Vacaciones
                if($frecuencia==8)
                {
                        $adicionales = array('97'=>$periodo_ini, '98'=>$periodo_ini, '99'=>$neto);

                        foreach ($adicionales as $id => $valor) 
                        {
                        // Consultar si el campo adicional ya existe para la ficha actual
                        $sql2 = "SELECT valor 
                                FROM   nomcampos_adic_personal
                                WHERE  ficha='{$fila['ficha']}' AND id='{$id}' AND tiponom='{$_SESSION['codigo_nomina']}'";
                        $res2 = $db->query($sql2);

                        if($res2->num_rows == 0)
                        {
                                // Registrar el campo adicional
                                $sql3 = "INSERT INTO `nomcampos_adic_personal` 
                                                (`ficha`, `id`, `valor`, `tipo`, `tiponom`) 
                                                VALUES 
                                                ('{$fila['ficha']}', '{$id}', '{$valor}', NULL, '{$_SESSION['codigo_nomina']}')";
                                $res3 = $db->query($sql3);
                        }
                        else
                        {
                                // Actualizar el campo adicional
                                $sql3 = "UPDATE `nomcampos_adic_personal` SET
                                                valor='{$valor}'
                                                WHERE ficha='{$fila['ficha']}' AND id='{$id}' AND tiponom='{$_SESSION['codigo_nomina']}'";
                                $res3 = $db->query($sql3);
                        }
                        }
                }	
                //-----------------------------------------------------------------------------------------------------------
            
                $tardanza=0.00;
                if(isset($con_tardanza) && $con_tardanza!=''){     
                        $consulta_tardanza="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_tardanza}) "
                                . "AND cedula='$fila[cedula]'";               
                        $resultado_tardanza = $db->query($consulta_tardanza);
                        $fila_tardanza = $resultado_tardanza->fetch_assoc();
                        $tardanza=$fila_tardanza['monto']; 
                }

                $salario_bruto=0.00; 
                if(isset($con_salario_bruto) && $con_salario_bruto!=''){ 
                        $consulta_salario_bruto="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_salario_bruto}) "
                                . "AND cedula='$fila[cedula]'";      
                        $resultado_salario_bruto = $db->query($consulta_salario_bruto);
                        $fila_salario_bruto = $resultado_salario_bruto->fetch_assoc();
                        $salario_bruto=$fila_salario_bruto['monto'];
                }

                $salario_resta=0.00; 
                if(isset($con_salario_resta) && $con_salario_resta!=''){ 
                        $consulta_salario_resta="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_salario_resta}) "
                                . "AND cedula='$fila[cedula]'";               
                        $resultado_salario_resta = $db->query($consulta_salario_resta);
                        $fila_salario_resta = $resultado_salario_resta->fetch_assoc();
                        $salario_resta=$fila_salario_resta['monto']; 
                }

                $vacac=0.00; 
                if(isset($con_vac) && $con_vac!=''){ 
                        $consulta_vac="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_vac}) "
                                . "AND cedula='$fila[cedula]'";             
                        $resultado_vac = $db->query($consulta_vac);
                        $fila_vac = $resultado_vac->fetch_assoc();
                        $vacac=$fila_vac['monto'];
                }

                $xiii=0.00; 
                if(isset($con_xiii) && $con_xiii!=''){ 

                        $consulta_xiii="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_xiii}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_xiii = $db->query($consulta_xiii);
                        $fila_xiii = $resultado_xiii->fetch_assoc();
                        $xiii=$fila_xiii['monto'];
                }

                $gr=0.00; 
                if(isset($con_gr) && $con_gr!=''){ 

                        $consulta_gr="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_gr}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_gr = $db->query($consulta_gr);
                        $fila_gr = $resultado_gr->fetch_assoc();
                        $gr=$fila_gr['monto'];
                }

                $xiii_gr=0.00; 
                if(isset($con_xiii_gr) && $con_xiii_gr!=''){ 

                        $consulta_xiii_gr="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_xiii_gr}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_xiii_gr = $db->query($consulta_xiii_gr);
                        $fila_xiii_gr = $resultado_xiii_gr->fetch_assoc();
                        $xiii_gr=$fila_xiii_gr['monto'];
                }

                $liquida=0.00; 
                if(isset($con_liquida) && $con_liquida!=''){ 

                        $consulta_liquida="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_liquida}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_liquida = $db->query($consulta_liquida);
                        $fila_liquida = $resultado_liquida->fetch_assoc();
                        $liquida=$fila_liquida['monto'];
                }

                $bono=0.00; 
                if(isset($con_bono) && $con_bono!=''){ 

                        $consulta_bono="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_bono}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_bono = $db->query($consulta_bono);
                        $fila_bono = $resultado_bono->fetch_assoc();
                        $bono=$fila_bono['monto'];
                }

                $otros_ing=0.00; 
                if(isset($con_otros_ing) && $con_otros_ing!=''){ 

                        $consulta_otros_ing="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_otros_ing}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_otros_ing = $db->query($consulta_otros_ing);
                        $fila_otros_ing = $resultado_otros_ing->fetch_assoc();
                        $otros_ing=$fila_otros_ing['monto'];
                }

                $prima=0.00; 
                if(isset($con_prima) && $con_prima!=''){ 

                        $consulta_prima="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_prima}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_prima = $db->query($consulta_prima);
                        $fila_prima = $resultado_prima->fetch_assoc();
                        $prima=$fila_prima['monto'];
                }

                $s_s=0.00; 
                if(isset($con_s_s) && $con_s_s!=''){ 

                        $consulta_s_s="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_s_s}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_s_s = $db->query($consulta_s_s);
                        $fila_s_s = $resultado_s_s->fetch_assoc();
                        $s_s=$fila_s_s['monto'];
                }

                $s_e=0.00; 
                if(isset($con_s_e) && $con_s_e!=''){ 

                        $consulta_s_e="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_s_e}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_s_e = $db->query($consulta_s_e);
                        $fila_s_e = $resultado_s_e->fetch_assoc();
                        $s_e=$fila_s_e['monto'];
                }

                $isr=0.00; 
                if(isset($con_isr) && $con_isr!=''){ 

                        $consulta_isr="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_isr}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_isr = $db->query($consulta_isr);
                        $fila_isr = $resultado_isr->fetch_assoc();
                        $isr=$fila_isr['monto'];
                }

                $isr_gr=0.00; 
                if(isset($con_isr_gr) && $con_isr_gr!=''){ 

                        $consulta_isr_gr="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_isr_gr}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_isr_gr = $db->query($consulta_isr_gr);
                        $fila_isr_gr = $resultado_isr_gr->fetch_assoc();
                        $isr_gr=$fila_isr_gr['monto'];
                }

                $acreedor_suma=0.00; 
                if(isset($con_acreedor) && $con_acreedor!=''){ 

                        $consulta_acreedor="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$con_acreedor}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_acreedor = $db->query($consulta_acreedor);
                        $fila_acreedor = $resultado_acreedor->fetch_assoc();
                        $acreedor_suma=$fila_acreedor['monto'];
                }
                //NUEVOS CAMPOS AGREGADOS
                $val_viaticos=0.00; 
                if(isset($viaticos) && $viaticos!=''){ 

                        $consulta_acreedor="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$viaticos}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_acreedor = $db->query($consulta_acreedor);
                        $fila_acreedor = $resultado_acreedor->fetch_assoc();
                        $val_viaticos=$fila_acreedor['monto'];
                }
                
                $val_comisiones=0.00; 
                if(isset($Comisiones) && $Comisiones!=''){ 

                        $consulta_acreedor="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$Comisiones}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_acreedor = $db->query($consulta_acreedor);
                        $fila_acreedor = $resultado_acreedor->fetch_assoc();
                        $val_comisiones=$fila_acreedor['monto'];
                }
                
                $val_gratificaciones=0.00; 
                if(isset($gratificaciones) && $gratificaciones!=''){ 

                        $consulta_acreedor="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$gratificaciones}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_acreedor = $db->query($consulta_acreedor);
                        $fila_acreedor = $resultado_acreedor->fetch_assoc();
                        $val_gratificaciones=$fila_acreedor['monto'];
                }
                
                $val_donaciones=0.00; 
                if(isset($donaciones) && $donaciones!=''){ 

                        $consulta_acreedor="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$donaciones}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_acreedor = $db->query($consulta_acreedor);
                        $fila_acreedor = $resultado_acreedor->fetch_assoc();
                        $val_donaciones=$fila_acreedor['monto'];
                }
                
                $val_desc_empresa=0.00; 
                if(isset($desc_empresa) && $desc_empresa!=''){ 

                        $consulta_acreedor="SELECT IFNULL(sum(monto),0) as monto  "
                                . "FROM nom_movimientos_nomina  "
                                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                                . "AND codcon IN ({$desc_empresa}) "
                                . "AND cedula='$fila[cedula]'";
                        $resultado_acreedor = $db->query($consulta_acreedor);
                        $fila_acreedor = $resultado_acreedor->fetch_assoc();
                        $val_desc_empresa=$fila_acreedor['monto'];
                }
            
                //restar conceptos al salario bruto
                if(isset($salario_resta))
                        $salario_bruto = $salario_bruto-$salario_resta;

                $sql_salarios_acumulados_values[] = "("
                        . "'{$ficha}', "
                        . "'{$cedula}', "
                        . "'{$periodo_fin}', "
                        ."'{$codnom}', "
                        . "'{$codtip}', "
                        . "'{$frecuencia}',"
                        . "'{$salario_bruto}',"
                        . "'{$vacac}',"
                        . "'{$xiii}',"
                        . "'{$gr}',"
                        . "'{$xiii_gr}',"
                        . "'{$liquida}',"
                        . "'{$bono}',"
                        . "'{$otros_ing}',"
                        . "'{$prima}',"
                        . "'{$s_s}',"
                        . "'{$s_e}',"
                        . "'{$isr}',"
                        . "'{$isr_gr}',"
                        . "'{$acreedor_suma}',"
                        . "'{$val_viaticos}',"
                        . "'{$val_comisiones}',"
                        . "'{$val_gratificaciones}',"
                        . "'{$val_donaciones}',"
                        . "'{$val_desc_empresa}',"
                        . "'{$neto}',"
                        . "'{$estatus}')";
                                            
        }//fin condicion del neto mayo que 0
        if($neto3_empleado<0)
        {
            $fichas['Estado'] = "0";
            $fichas['data'][] =  array("ficha" => ( is_null($fila['ficha']) ? "-" : $fila['ficha'] ),
            "apenom" =>( is_null($fila['apenom']) ? "-" : $fila['apenom'] ),
            "neto" =>( is_null($neto) ? "-" : $neto ),
            "neto3" =>( is_null($neto3_empleado) ? "-" : $neto3_empleado ));
        }
    }//FIN IF ROWS >0
        
}//FIN WHILE

$countNeto = count($sqlNetoValues);
if($fichas['Estado'] != "0")
{

        $fichas['Estado'] = "1";
    
    if($countNeto > 0)
    {
            $sqlNeto .= implode(",", $sqlNetoValues);
            $res = $db->query($sqlNeto);
    }

    $sql_salarios_acumulados .= implode(",", $sql_salarios_acumulados_values);

    $res = $db->query($sql_salarios_acumulados);
    //echo $sql_salarios_acumulados;
    //exit;   
    $sql = "UPDATE nomperiodos 
            SET    status='Cerrado' 
            WHERE  codfre='{$frecuencia}' AND anio='{$anio}' AND nper='{$periodo}'";
    $res = $db->query($sql);

    $sql = "UPDATE nom_nominas_pago SET 
            status='C', 
            usuario_aprobacion = '".$_SESSION['usuario']."',
            fecha_aprobacion = NOW(),
            libre=0 
            WHERE codnom='{$_GET['codigo_nomina']}' AND codtip='{$_SESSION['codigo_nomina']}'"; // tipnom='{$_SESSION['codigo_nomina']}'
    $res = $db->query($sql);
    echo json_encode($fichas);

}
else{
    echo json_encode($fichas);
}
?>
