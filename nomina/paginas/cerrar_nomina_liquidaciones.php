<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
ob_start();

//cerrar la nomina
require '../lib/database.php';

$db = new Database($_SESSION['bd']);

$estatus=1;
$con_salario_bruto="90,95,100,110,115,169,159,160,161,162,171,105,106,107,108,109,111,112,113,116,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,142,143,144,149,150,151,152,153,154,155,163,164,165,166,167,168,172,174";
    
$con_vac="91,114";
$con_xiii="92,102";
$con_gr="145";
$con_xiii_gr="101";
$con_liquida="93,94,96,97";
$con_bono="189,190";
$con_otros_ing="141,147,157,158";
$con_s_s="200";
$con_s_e="201,206";
$con_isr="202,211,213,214,215";
$con_isr_gr="208";
$con_acreedor="204,216,217,218,219,220,221,222,223,224,225,500,501,502,503,504,505,506,507,508,509,510,511,512,513,514,515,516,517,518,"
            . "519,520,521,522,523,524,525,526,527,528,529,530,531,532,533,534,535,536,537,538,539,540,541,542,543,544,545,546,547,548,"
            . "549,550,551,552,553,554,555,556,557,558,559,560,561,562,563,564,565,566,567,568,569,570,571,572,573,574,575,576,577,578,"
            . "579,580,581,582,583,584,585,586,587,588,589,590,591,592,593,594,595,596,597,598,599";
$con_tardanza="199,203";




$sql_nomina = "SELECT codnom, codtip, periodo_ini, periodo_fin, fechapago, anio, mes, periodo, frecuencia 
	       FROM nom_nominas_pago 
	       WHERE codnom='{$_GET['codigo_nomina']}'"; // tipnom='{$_SESSION['codigo_nomina']}'


// return var_dump($sql_nomina);
               // Where Viejo, el activo esta por David Sandoval
               // WHERE codnom='{$_GET['codigo_nomina']}' AND codtip='{$_SESSION['codigo_nomina']}'"; // tipnom='{$_SESSION['codigo_nomina']}'
$res_nomina = $db->query($sql_nomina);
$fila_nomina = $res_nomina->fetch_assoc();
$codnom=$fila_nomina['codnom'];
$codtip=$fila_nomina['codtip'];
$periodo_ini=$fila_nomina['periodo_ini'];
$periodo_fin=$fila_nomina['periodo_fin'];
$fechapago=$fila_nomina['fechapago'];
$anio=$fila_nomina['anio'];
$mes=$fila_nomina['mes'];
$periodo=$fila_nomina['periodo'];
$frecuencia=$fila_nomina['frecuencia'];


$empresaPermitido = "SELECT * FROM nomempresa WHERE nom_emp IN ('INGENIERIA Y SOLUCIONES ESPECIALIZADAS S.A.S')";
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
      $nuevo_valor = round($nuevo_valor, 2);

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
          $nuevoValor = round($nuevoValor, 2);

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
          $resultado_de_la_resta = round($resultado_de_la_resta, 2);

          if ($resultado_de_la_resta > 0) {
            $resultado = -1 * $resultado_de_la_resta;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25050101', '25', 'SALARIOS POR PAGAR', 'D', '0')";
          }


          $monto3000 = round($monto3000, 2);
          if ($monto3000 > 0) {
            $resultado = -1 * $monto3000;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '23700501', '26', 'SEGURO SOCIAL PATRONAL POR PAGAR', 'D', '1')";
          }

          $monto3001 = round($monto3001, 2);
          if ($monto3001 > 0) {
            $resultado = -1 * $monto3001;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '23701001', '27', 'SEGURO EDUCATIVO PATRONAL POR PAGAR', 'D', '1')";
          }

          $monto3002 = round($monto3002, 2);
          if ($monto3002 > 0) {
            $resultado = -1 * $monto3002;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25100102', '28', 'PATRONAL C.S.S XIII POR PAGAR', 'D', '1')";
          }

          $monto9005 = round($monto9005, 2);
          if ($monto9005 > 0) {
            $resultado = -1 * $monto9005;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25100102', '29', 'PRIMA DE ANTIGUEDAD PROVISIÓN POR PAGAR', 'D', '0')";
          }

          $monto9006 = round($monto9006, 2);
          if ($monto9006 > 0) {
            $resultado = -1 * $monto9006;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25050101', '30', 'INDEMNIZACIÓN PROVISIÓN POR PAGAR', 'D', '0')";
          }

          $monto9007 = round($monto9007, 2);
          if ($monto9007 > 0) {
            $resultado = -1 * $monto9007;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25200102', '31', 'XIII MES PROVISIÓN POR PAGAR', 'D', '0')";
          }

          $monto9008 = round($monto9008, 2);
          if ($monto9008 > 0) {
            $resultado = -1 * $monto9008;
            $filasInsertadas[] = "('$codnom', '$codigo_proyecto', '$porcentaje', '$resultado', '$ficha', '$cedula', '$periodo_ini', '$periodo_fin', '0', '25250102', '32', 'VACACIONES PROVISIÓN POR PAGAR', 'D', '0')";
          }

          $monto9009 = round($monto9009, 2);
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


//generar netos
// $consulta="select * from nompersonal where ficha in (select distinct(ficha) from nom_movimientos_nomina where tipnom='".$_SESSION['codigo_nomina']."' and codnom = '".$_GET['codigo_nomina']."')";

//generar netos por David Sandoval
$consulta="select * from nompersonal where ficha in (select distinct(ficha) from nom_movimientos_nomina where codnom='".$_SESSION['codigo_nomina']."')";

$resultado=$db->query($consulta);

$sql_salarios_acumulados = "INSERT INTO salarios_acumulados 
                            (id,
                            ficha,
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
                            s_s,
                            s_e,
                            islr,
                            islr_gr,
                            acreedor_suma,
                            Neto,
                            estatus) 
                            VALUES ";

$sql_salarios_acumulados_values = array();

while($fila=$resultado->fetch_assoc())
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
    
        $consulta="select * from nom_movimientos_nomina where ficha='".$fila['ficha']."' and codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";
	$resultado_mov=$db->query($consulta);
	
	$consulta_inac = "UPDATE nompersonal SET estado = 'Egresado', cod_tli=cod_tli_tmp, fecharetiro=fecharetiro_tmp,"
                        . " motivo_liq=motivo_liq_tmp, preaviso=preaviso_tmp "
                        . " WHERE ficha = '".$fila['ficha']."' AND tipnom='".$_SESSION['codigo_nomina']."'";
	$resultado_inac=$db->query($consulta_inac);
        
        $consulta_limpiar = "UPDATE nompersonal SET cod_tli_tmp=NULL, fecharetiro_tmp=NULL,"
                        . " motivo_liq_tmp=NULL, preaviso_tmp=NULL "
                        . " WHERE ficha = '".$fila['ficha']."' AND tipnom='".$_SESSION['codigo_nomina']."'";
	$resultado_limpiar=$db->query($consulta_limpiar);
	$asignaciones=0;
	$deducciones=0;
	
	if($resultado_mov->num_rows!=0){
		while($fila_mov=$resultado_mov->fetch_assoc()){
			if($fila_mov['tipcon']=="A"){
				$asignaciones+=$fila_mov['monto'];
			}elseif($fila_mov['tipcon']=="D"){
				$deducciones+=$fila_mov['monto'];
			}	
		}	
		$neto=$asignaciones-$deducciones;
		
		if($neto!=0){
			$sentencia="insert into nom_nomina_netos (codnom,tipnom,ficha,cedula,cta_ban,neto) values ('".$_GET['codigo_nomina']."','".$_SESSION['codigo_nomina']."','".$fila['ficha']."','".$fila['cedula']."','".$fila['cuentacob']."','".$neto."')";
			
			$insercion=$db->query($sentencia);
		}
                
                $consulta_tardanza="SELECT IFNULL(sum(monto),0) as monto  "
                . "FROM nom_movimientos_nomina  "
                . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                . "AND codcon IN ({$con_tardanza}) "
                . "AND cedula='$fila[cedula]'";
        //        echo $consulta_tardanza;
        //        exit;	               
                $resultado_tardanza = $db->query($consulta_tardanza);
                $fila_tardanza = $resultado_tardanza->fetch_assoc();
                $tardanza=$fila_tardanza['monto']; 


                $consulta_salario_bruto="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  "
                    . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                    . "AND codcon IN ({$con_salario_bruto}) "
                    . "AND cedula='$fila[cedula]'";
        //        echo $consulta_salario_bruto;
        //        exit;	               
                $resultado_salario_bruto = $db->query($consulta_salario_bruto);
                $fila_salario_bruto = $resultado_salario_bruto->fetch_assoc();
                $salario_bruto=$fila_salario_bruto['monto']-$tardanza;   


                $consulta_vac="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  "
                    . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                    . "AND codcon IN ({$con_vac}) "
                    . "AND cedula='$fila[cedula]'";
        //        echo $consulta_salario_bruto;
        //        exit;	               
                $resultado_vac = $db->query($consulta_vac);
                $fila_vac = $resultado_vac->fetch_assoc();
                $vacac=$fila_vac['monto'];

                $consulta_xiii="SELECT IFNULL(sum(monto),0) as monto  "
                    . "FROM nom_movimientos_nomina  "
                    . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                    . "AND codcon IN ({$con_xiii}) "
                    . "AND cedula='$fila[cedula]'";
    //        echo $consulta;
    //        exit;

                $resultado_xiii = $db->query($consulta_xiii);
                $fila_xiii = $resultado_xiii->fetch_assoc();
                $xiii=$fila_xiii['monto'];

                $consulta_gr="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_gr}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_gr = $db->query($consulta_gr);
                $fila_gr = $resultado_gr->fetch_assoc();
                $gr=$fila_gr['monto'];

                $consulta_xiii_gr="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_xiii_gr}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_xiii_gr = $db->query($consulta_xiii_gr);
                $fila_xiii_gr = $resultado_xiii_gr->fetch_assoc();
                $xiii_gr=$fila_xiii_gr['monto'];

                $consulta_liquida="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_liquida}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_liquida = $db->query($consulta_liquida);
                $fila_liquida = $resultado_liquida->fetch_assoc();
                $liquida=$fila_liquida['monto'];

                $consulta_bono="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_bono}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_bono = $db->query($consulta_bono);
                $fila_bono = $resultado_bono->fetch_assoc();
                $bono=$fila_bono['monto'];

                $consulta_otros_ing="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_otros_ing}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_otros_ing = $db->query($consulta_otros_ing);
                $fila_otros_ing = $resultado_otros_ing->fetch_assoc();
                $otros_ing=$fila_otros_ing['monto'];

                $consulta_s_s="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_s_s}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_s_s = $db->query($consulta_s_s);
                $fila_s_s = $resultado_s_s->fetch_assoc();
                $s_s=$fila_s_s['monto'];

                $consulta_s_e="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_s_e}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_s_e = $db->query($consulta_s_e);
                $fila_s_e = $resultado_s_e->fetch_assoc();
                $s_e=$fila_s_e['monto'];

                $consulta_isr="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_isr}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_isr = $db->query($consulta_isr);
                $fila_isr = $resultado_isr->fetch_assoc();
                $isr=$fila_isr['monto'];

                $consulta_isr_gr="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_isr_gr}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_isr_gr = $db->query($consulta_isr_gr);
                $fila_isr_gr = $resultado_isr_gr->fetch_assoc();
                $isr_gr=$fila_isr_gr['monto'];

                $consulta_acreedor="SELECT IFNULL(sum(monto),0) as monto  "
                        . "FROM nom_movimientos_nomina  "
                        . "WHERE (codnom IN ({$codnom}) AND tipnom IN ({$codtip})) "
                        . "AND codcon IN ({$con_acreedor}) "
                        . "AND cedula='$fila[cedula]'";
        //        echo $consulta;
        //        exit;

                $resultado_acreedor = $db->query($consulta_acreedor);
                $fila_acreedor = $resultado_acreedor->fetch_assoc();
                $acreedor_suma=$fila_acreedor['monto'];
                
                $asignaciones = $salario_bruto + $vacac + $xiii + $gr + $xiii_gr + $liquida + $bono + $otros_ing;
                $deducciones = $s_s + $s_e + $isr + $isr_gr + $acreedor_suma;
                
                $neto = $asignaciones - $deducciones;
                        
                $sql_salarios_acumulados_values[] = 
                                                "('', "
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
                                                . "'{$s_s}',"
                                                . "'{$s_e}',"
                                                . "'{$isr}',"
                                                . "'{$isr_gr}',"
                                                . "'{$acreedor_suma}',"
                                                . "'{$neto}',"
                                                . "'{$estatus}')";
	}
}

$sql_salarios_acumulados .= implode(",", $sql_salarios_acumulados_values);

$res = $db->query($sql_salarios_acumulados);

//Update anterior
// $consulta="update nom_nominas_pago set status='C' where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";

//Update modificado por David Sandoval
$consulta="update nom_nominas_pago set status='C' where codnom='".$_GET['codigo_nomina']."'";
$resultado=$db->query($consulta);
?>