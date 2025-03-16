<?php
	
	require_once '../lib/common.php';
	include ("../paginas/func_bd.php");
	$conexion=conexion();
	$consulta   = "SELECT * FROM  nomprestamos ";
	$resultado2 = query($consulta, $conexion);
	$i=501;        
	while($array = fetch_array($resultado2))
	{
		print_r($array);echo "<br>";
        $formula       = '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,'.$array['codigopr'].'); $MONTO=$T01;';
        //Agrego los registro a conceptos
        $sql1 = "insert into nomconceptos (codcon, descrip, tipcon, unidad, ctacon, contractual, impdet, proratea, usaalter, descalter, formula, modifdef, markar, tercero, ccosto, codccosto, debcre, bonificable, htiempo, valdefecto, con_cu_cc, con_mcun_cc, con_mcuc_cc, con_cu_mccn, con_cu_mccc, con_mcun_mccn, con_mcuc_mccc, con_mcun_mccc, con_mcuc_mccn, nivelescuenta, nivelesccosto, semodifica, verref, vermonto, particular, montocero, ee, fmodif, aplicaexcel, descripexcel, ctacon1, carga_masiva, orden_reporte_contable)
    		VALUES ({$i}, '{$array['descrip']}', 'D', 'M', '640000000', '1', 'S', '0', '0', '', '{$formula}', '0', '0', '0', '{$array['codigopr']}', '{$array['tipos_prestamos']}', '0', '0', '0', '0.00', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '1', '0', '1', '0', '0', '0', 'DEDUC', '142.0.1.010.01.01.001', 'N', '0')";
        query($sql1,$conexion);
        //agrego registro a acumulados conceptos
        $sql2 = "INSERT INTO nomconceptos_acumulados (codcon ,cod_tac ,operacion ,ee) VALUES ({$i}, 'CON', 'S', '0')";
        query($sql2,$conexion);
        $sql2 = "INSERT INTO nomconceptos_acumulados (codcon ,cod_tac ,operacion ,ee) VALUES ({$i}, 'OD', 'S', '0')";
        query($sql2,$conexion);
        $sql2 = "INSERT INTO nomconceptos_acumulados (codcon ,cod_tac ,operacion ,ee) VALUES ({$i}, 'PRES', 'S', '0')";
        query($sql2,$conexion);

        //agrego registro a frecuencia conceptos
        $sql3 = "INSERT INTO nomconceptos_frecuencias (codcon , codfre , ee) VALUES ({$i}, '2', '0')";
        query($sql3,$conexion);
        $sql4 = "INSERT INTO nomconceptos_frecuencias (codcon , codfre , ee) VALUES ({$i}, '3', '0')";
        query($sql4,$conexion);

        //agrego registro a situaciones conceptos
        $sql5 = "INSERT INTO nomconceptos_situaciones (codcon , estado , ee) VALUES ({$i}, 'REGULAR', '0')";
        query($sql5,$conexion);

        $consulta_codtip = "SELECT codtip from nomtipos_nomina group by codtip";
        $resultado_codtip=query($consulta_codtip,$conexion);
        while ($fetch_codtip=fetch_array($resultado_codtip)) 
        {
            //agrego registro a tiponomina conceptos
            $sql6 = "insert INTO nomconceptos_tiponomina (codcon , codtip , ee) VALUES ('{$i}', '{$fetch_codtip['codtip']}', '0')";
            query($sql6,$conexion);
        
        }
        
        $i++;
		
	}

		
	
?>