<?php
include("../lib/common.php");
include ("../header.php");
include ("func_bd.php");
include ("funciones_nomina.php");

ini_set("memory_limit", "-1");
set_time_limit(0);

$registro_id   = isset($_POST['registro_id'])   ? $_POST['registro_id']   : '';		
$codigo_nomina = isset($_POST['codigo_nomina']) ? $_POST['codigo_nomina'] : '';	

// Ajustes a la base de datos, para optimizar el tiempo de ejecución de los insert:
sql_ejecutar("SET FOREIGN_KEY_CHECKS = 0");
sql_ejecutar("SET UNIQUE_CHECKS = 0");
//sql_ejecutar("SET AUTOCOMMIT = 0;");

// Cambiar el tamaño máximo de un paquete
// sql_ejecutar("SET GLOBAL max_allowed_packet=1073741824");
// $fila = fetch_array(sql_ejecutar("SELECT @@global.max_allowed_packet as max_allowed_packet"));
// echo "GLOBAL max_allowed_packet = ".$fila['max_allowed_packet'];
		
// BORRA LOS MOVIMIENTO DE LA NOMINA A PROCESAR
$query="DELETE FROM nom_movimientos_nomina WHERE tipnom='{$codigo_nomina}' AND codnom='{$registro_id}' AND contractual='1'";			
$result3=sql_ejecutar($query);
// PARA ACTIVAR EL BONO POR RAZON DE SERVICIO EN EL CSB
unset($result3);

$query_historico="DELETE FROM nom_movimientos_historico WHERE tipnom='{$codigo_nomina}' AND codnom='{$registro_id}'";
$result_historico=sql_ejecutar($query_historico);

$consulta="SELECT frecuencia, periodo_ini, periodo_fin, mes, anio 
           FROM   nom_nominas_pago 
           WHERE  codnom='{$registro_id}' AND tipnom='{$codigo_nomina}'";
$resultado_nom=sql_ejecutar($consulta);
$fila_nom=fetch_array($resultado_nom);

$consulta="SELECT monsalmin FROM nomempresa";
$resultado_salmin=sql_ejecutar($consulta);
$fila_salmin=fetch_array($resultado_salmin);
		
$query="SELECT c.codcon, c.descrip, c.tipcon, c.formula, c.contractual, c.proratea, c.montocero, c.unidad,
			   pe.apenom, pe.cedula, pe.ficha, pe.suesal, pe.sexo, pe.hora_base, pe.fecnac, pe.tipnom, 
			   pe.fecing, pe.inicio_periodo, pe.fin_periodo, pe.codpro, pe.codcat, pe.codcargo, pe.estado, pe.sueldopro, pe.contrato,
			   pe.forcob, pe.codnivel1, pe.codnivel2, pe.codnivel3, pe.codnivel4, pe.codnivel5, pe.codnivel6, pe.codnivel7, 
			   pe.fechaplica, pe.tipopres, pe.fechasus, pe.fechareisus, pe.fecharetiro, pe.fechavac, pe.fechareivac, 
			   pe.antiguedadap, pe.clave_ir, pe.motivo_liq, pe.cod_tli, pe.preaviso, pe.marca_reloj 
		FROM nomconceptos as c
		INNER JOIN nomconceptos_tiponomina  as ct ON c.codcon = ct.codcon
		INNER JOIN nomconceptos_frecuencias as cf ON c.codcon = cf.codcon
		INNER JOIN nomconceptos_situaciones as cs ON c.codcon = cs.codcon 
		INNER JOIN nompersonal as pe ON cs.estado = pe.estado
		WHERE cf.codfre='{$fila_nom['frecuencia']}' AND pe.tipnom = '{$codigo_nomina}' 
		AND   ct.codtip = '{$codigo_nomina}'
		AND   c.tipcon='A' AND c.contractual='1' 
		ORDER BY c.codcon";
		// pe.nominstruccion_id, 
		// group by pe.apenom,pe.ficha,c.formula,c.codcon,cs.estado 
		// echo $query;
		// return false;
$result2     =sql_ejecutar($query);
$end         = num_rows($result2);	
$cont        =0;	

// pertenece a los campos pero es el mismo valor para todos
$FECHAHOY    =date("d/m/Y");

$CODNOM      =$registro_id;
$FECHANOMINA =$fila_nom['periodo_ini'];
$FECHAFINNOM =$fila_nom['periodo_fin'];
$LUNES       =lunes($FECHANOMINA);	
$LUNESPER    =lunes_per($FECHANOMINA,$FECHAFINNOM);
$FRECUENCIA  =$fila_nom['frecuencia'];

$SALARIOMIN  =$fila_salmin['monsalmin'];
$ICEDULA     =0;
$insert      = "INSERT INTO nom_movimientos_historico VALUES ";
// $query = "INSERT INTO nom_movimientos_nomina 
// 				(codnom,codcon,ficha,mes,anio,monto,cedula,tipcon,unidad,valor,descrip,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual,saldopre,refcheque) VALUES ";
$aux = 0 ;
while ($fila = fetch_array($result2))
{
	// Prepara las variables con los valores
	$aux               = 1 ; 
	$FECHALIQUIDACION  = $fila['fecharetiro'];
	$MOTIVOLIQUIDACION = $fila['motivo_liq'];
	$TIPOLIQUIDACION   = $fila['cod_tli'];
	$PREAVISO          =$fila['preaviso'];
	$NOMBRE            =$fila['apenom'];
	
	$CEDULA            = $fila['cedula'];
	$FICHA             = $fila['ficha'];
	$SUELDO            =$fila['suesal'];
	$SEXO              =".".$fila['sexo']."'";
	$HORABASE          = $fila['hora_base'];
	$FECHANACIMIENTO   =$fila['fecnac'];
	$EDAD              =date("Y")-date("Y",$fila['fecnac']);
	$TIPONOMINA        =$fila['tipnom'];
	$FECHAINGRESO      =$fila['fecing'];
	$FECHAINICON       =$fila['inicio_periodo'];
	$FECHAFINCON       =$fila['fin_periodo'];
	$CODPROFESION      =$fila['codpro'];
	$CODCATEGORIA      =$fila['codcat'];
	$CODCARGO          =$fila['codcargo'];
	$SITUACIONPER      =$SITUACION=$fila['estado'];
	$SITUACIONPER      =$fila['estado'];
	$SUELDOPROPUESTO   =$fila['sueldopro'];
	$TIPOCONTRATO      =$fila['contrato'];
	$FORMACOBRO        =$fila['forcob'];
	$NIVEL1            =$fila['codnivel1'];
	$NIVEL2            =$fila['codnivel2'];
	$NIVEL3            =$fila['codnivel3'];
	$NIVEL4            =$fila['codnivel4'];
	$NIVEL5            =$fila['codnivel5'];
	$NIVEL6            =$fila['codnivel6'];
	$NIVEL7            =$fila['codnivel7'];
	$FECHAAPLICACION   =$fila['fechaplica'];
	$TIPOPRESENTACION  =$fila['tipopres'];
	$FECHAFINSUS       =$fila['fechasus'];
	$FECHAINISUS       =$fila['fechareisus'];
	$FECHAFINCONTRATO  =$fila['fecharetiro'];
	$REF               =0;
	$CONTRACTUAL       =$fila['contractual'];
	$FECHAVAC          =$fila['fechavac'];
	$FECHAREIVAC       =$fila['fechareivac'];
	$PRT               =$fila['proratea'];
	$SALDOPRE          =0;
	// $CODINSTRUCCION =$fila['nominstruccion_id'];
	$ANTIGUEDADAA      =$fila['antiguedadap'];
	$FECHAX            ="1997-06-19";
	$CLAVE_IR          =$fila['clave_ir'];
	$MARCA_RELOJ       =$fila['marca_reloj'];
	$cheque            =0;
	if($FORMACOBRO=='Cheque')
		$cheque=1;
		
	if(strtotime($FECHAINGRESO)<=strtotime($FECHAX))
		$FECHAPRESTACION=$FECHAX;
	else
		$FECHAPRESTACION=$FECHAINGRESO;
		       
	if($CEDULA!=$ICEDULA)
	{
		$ICEDULA=$CEDULA;
		if($HISTORICO=='' || $HISTORICO!=$CEDULA)
		{
			$HISTORICO=$CEDULA;
			$insert .=" ('','{$registro_id}','{$codigo_nomina}','{$NIVEL1}','{$NIVEL2}','{$NIVEL3}','{$NIVEL4}','{$NIVEL5}','{$NIVEL6}','{$NIVEL7}','{$FICHA}','{$SUELDO}','{$CODCARGO}','{$SITUACIONPER}','{$CEDULA}'),";				
		}
	}

	$cont=$cont+1;

	if ($fila['formula']!='')
	{
		$formula=$fila['formula'];

		if ($fila['contractual']==1){
			eval($formula);

			$entrar=1;
			if($MONTO<=0 && $fila['montocero']==1){
				$entrar=0;
			}

			if ($entrar==1)
			{
				$sql = "INSERT INTO nom_movimientos_nomina 
						(codnom, codcon, ficha, mes, anio, monto, cedula, tipcon, unidad, valor, descrip, codnivel1, codnivel2, 
						 codnivel3, codnivel4, codnivel5, codnivel6, codnivel7, tipnom, contractual, saldopre, refcheque, suesal, cod_cargo) 
						VALUES 
						('{$registro_id}', '{$fila['codcon']}', '{$fila['ficha']}', '{$fila_nom['mes']}', '{$fila_nom['anio']}', 
						 '{$MONTO}', '{$CEDULA}', '{$fila['tipcon']}', '{$fila['unidad']}', '{$REF}', '{$fila['descrip']}', 
						 '{$fila['codnivel1']}', '{$fila['codnivel2']}', '{$fila['codnivel3']}', '{$fila['codnivel4']}',
						 '{$fila['codnivel5']}', '{$fila['codnivel6']}', '{$fila['codnivel7']}', '{$codigo_nomina}', 
						 '{$fila['contractual']}', '{$SALDOPRE}', '{$cheque}', '{$fila['suesal']}','{$fila['codcargo']}')";
//                                if($fila['ficha']==25)
//                                {
//                                    echo "SQL1: ".$sql; 
//                                    echo "<br>";
////                                    exit;
//                                }
				$res = sql_ejecutar($sql);					
			}
		}
	}
        
        //ACTUALIZACION CAPATAZ RELOJ DETALLE
        $select_campo_adicional   = "SELECT id, valor from nomcampos_adic_personal "
                                  . "WHERE id = '2' AND ficha = '".$FICHA."'";
        $res_campo_adicional = sql_ejecutar($select_campo_adicional);
        $fila_campo_adicional = fetch_array($res_campo_adicional);
        $valor=$fila_campo_adicional[valor];
    
        $capataz=0;
        if($valor=="Si")
        {
//            echo $valor;
//            exit;            
            $capataz=1;
        }
        

        $actualizar_capataz = "UPDATE reloj_detalle
                                SET capataz='{$capataz}'
                                WHERE (ficha = '".$FICHA."' AND `fecha` >=  '".$FECHANOMINA."' AND `fecha` <=  '".$FECHAFINNOM."')";
        
//        if($valor=="Si")
//        {
//            echo $actualizar_capataz;
//            exit;            
//           
//        }                       
        $res_capataz = sql_ejecutar($actualizar_capataz);
        
	unset($MONTO);
	unset($T01);
	unset($T02);
	unset($T03);
	unset($T04);
	unset($T05);
	unset($T06);
	unset($T07);	
	unset($FICHA);
	unset($SUELDO);
	unset($SEXO);
	unset($FECHANACIMIENTO);
	unset($EDAD);
	unset($TIPONOMINA);
	unset($FECHAINGRESO);
	unset($CODPROFESION);
	unset($CODCATEGORIA);
	unset($CODCARGO);
	unset($SITUACION);
	unset($FORMACOBRO);
        
        
}
/*
if($aux == 1)
{ 
	$insert .= '****';
	$insert = str_replace(',****', ';', $insert);
	$result_insert=sql_ejecutar($insert);

	// $query .= '****';
	// $query = str_replace(',****', ';', $query);
	// $result=sql_ejecutar($query);
}*/

//=======================================================================================================================
// Consultar deducciones (Actualizado para optimizar el tiempo de ejecución)

$sql = "SELECT  pe.apenom, pe.cedula, pe.ficha, pe.suesal, pe.sexo, pe.hora_base, pe.fecnac, pe.tipnom, 
				pe.fecing, pe.inicio_periodo, pe.fin_periodo, pe.codpro, pe.codcat, pe.codcargo, pe.estado, pe.sueldopro, pe.contrato, 
				pe.forcob, pe.codnivel1, pe.codnivel2, pe.codnivel3, pe.codnivel4, pe.codnivel5, pe.codnivel6, pe.codnivel7, 
				pe.fechaplica, pe.tipopres, pe.fechasus, pe.fechareisus, pe.fecharetiro, pe.fechavac, pe.fechareivac, 
				pe.antiguedadap, pe.clave_ir , pe.motivo_liq, pe.cod_tli, pe.preaviso,pe.marca_reloj  
		FROM  nompersonal as pe
		WHERE pe.tipnom = '{$codigo_nomina}'";
$res = sql_ejecutar($sql);

while($fila = fetch_array($res))
{
	$ficha = $fila['ficha'];
	$personal[$ficha] = $fila; // Arreglo que contiene todos los colaboradores de la nomina actual
	// Nombre = $personal[1]['apenom'];
}

$sql = "SELECT c.codcon, c.descrip, c.tipcon, c.formula, c.contractual, c.proratea, c.montocero, c.unidad
		FROM   nomconceptos as c
		INNER JOIN nomconceptos_tiponomina  as ct ON c.codcon = ct.codcon
		INNER JOIN nomconceptos_frecuencias as cf ON c.codcon = cf.codcon
		WHERE cf.codfre='{$fila_nom['frecuencia']}'
		AND   ct.codtip = '{$codigo_nomina}' 
		AND   c.tipcon='D' AND c.contractual='1'";
		// ORDER BY c.codcon

$res = sql_ejecutar($sql);

$cont = 0;	

// pertenece a los campos pero es el mismo valor para todos
$FECHAHOY    = date("d/m/Y");
		
$CODNOM      = $registro_id;
$FECHANOMINA = $fila_nom['periodo_ini'];
$FECHAFINNOM = $fila_nom['periodo_fin'];
$LUNES       = lunes($FECHANOMINA);	
$LUNESPER    = lunes_per($FECHANOMINA,$FECHAFINNOM);

$SALARIOMIN  = $fila_salmin['monsalmin'];
$ICEDULA     = 0;
$HISTORICO   = '';
$insert      = "INSERT INTO nom_movimientos_historico VALUES ";
// $query = "INSERT INTO nom_movimientos_nomina 
// 				(codnom,codcon,ficha,mes,anio,monto,cedula,tipcon,unidad,valor,descrip,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual,saldopre,refcheque) VALUES ";

$aux = 0 ;				
while ($fila = fetch_array($res))
{
	$codcon = $fila['codcon'];

	$sql1 = "SELECT pe.ficha 
			 FROM   nomconceptos_situaciones as cs
			 INNER JOIN nompersonal as pe ON cs.estado = pe.estado 
			 WHERE cs.codcon={$codcon} AND pe.tipnom='{$codigo_nomina}'";

	$res1 = sql_ejecutar($sql1);

	while($fila1 = fetch_array($res1))
	{
		$ficha             = $fila1['ficha'];
		
		// Prepara las variables con los valores
		$aux               = 1 ;
		$NOMBRE            = $personal[$ficha]['apenom'];
		$FECHALIQUIDACION  = $personal[$ficha]['fecharetiro'];
		$MOTIVOLIQUIDACION = $personal[$ficha]['motivo_liq'];
		$TIPOLIQUIDACION   = $personal[$ficha]['cod_tli'];
		$PREAVISO          = $personal[$ficha]['preaviso'];
		
		$CEDULA            = $personal[$ficha]['cedula'];
		$FICHA             = $ficha; // $personal[$ficha]['ficha'];
		$SUELDO            = $personal[$ficha]['suesal'];
		$SEXO              = "." . $personal[$ficha]['sexo']."'";
		$FECHANACIMIENTO   = $personal[$ficha]['fecnac'];
		$EDAD              = date("Y") - date("Y", $personal[$ficha]['fecnac']);
		$TIPONOMINA        = $personal[$ficha]['tipnom'];
		$FECHAINGRESO      = $personal[$ficha]['fecing'];
		$FECHAINICON       = $personal[$ficha]['inicio_periodo'];
		$FECHAFINCON       = $personal[$ficha]['fin_periodo'];
		$CODPROFESION      = $personal[$ficha]['codpro'];
		$CODCATEGORIA      = $personal[$ficha]['codcat'];
		$CODCARGO          = $personal[$ficha]['codcargo'];
		$SITUACIONPER      = $SITUACION = $personal[$ficha]['estado'];
		$SITUACIONPER      = $personal[$ficha]['estado'];
		$SUELDOPROPUESTO   = $personal[$ficha]['sueldopro'];
		$TIPOCONTRATO      = $personal[$ficha]['contrato'];
		$FORMACOBRO        = $personal[$ficha]['forcob'];
		$NIVEL1            = $personal[$ficha]['codnivel1'];
		$NIVEL2            = $personal[$ficha]['codnivel2'];
		$NIVEL3            = $personal[$ficha]['codnivel3'];
		$NIVEL4            = $personal[$ficha]['codnivel4'];
		$NIVEL5            = $personal[$ficha]['codnivel5'];
		$NIVEL6            = $personal[$ficha]['codnivel6'];
		$NIVEL7            = $personal[$ficha]['codnivel7'];
		$FECHAAPLICACION   = $personal[$ficha]['fechaplica'];
		$TIPOPRESENTACION  = $personal[$ficha]['tipopres'];
		$FECHAFINSUS       = $personal[$ficha]['fechasus'];
		$FECHAINISUS       = $personal[$ficha]['fechareisus'];
		$FECHAFINCONTRATO  = $personal[$ficha]['fecharetiro'];
		$REF               = 0;
		$CONTRACTUAL       = $fila['contractual'];
		$FECHAVAC          = $personal[$ficha]['fechavac'];
		$FECHAREIVAC       = $personal[$ficha]['fechareivac'];
		$PRT               = $fila['proratea'];
		$SALDOPRE          = 0;
		// $CODINSTRUCCION =$fila['nominstruccion_id'];
		$ANTIGUEDADAA      = $personal[$ficha]['antiguedadap'];
		$FECHAX            = "1997-06-19";
		$CLAVE_IR          =$personal[$ficha]['clave_ir'];
        $MARCA_RELOJ       =$personal[$ficha]['marca_reloj'];
	    $cheque=0;
		if($FORMACOBRO=='Cheque')
			$cheque=1;
		
		if(strtotime($FECHAINGRESO)<=strtotime($FECHAX))
			$FECHAPRESTACION=$FECHAX;
		else
			$FECHAPRESTACION=$FECHAINGRESO;
		//-----------------------------------
			       
		if($CEDULA!=$ICEDULA)
		{
			$ICEDULA = $CEDULA;
			if($HISTORICO=='' || $HISTORICO!=$CEDULA)
			{
				$HISTORICO=$CEDULA;

				$insert .= "('','{$registro_id}','{$codigo_nomina}','{$NIVEL1}','{$NIVEL2}','{$NIVEL3}','{$NIVEL4}','{$NIVEL5}',
				             '{$NIVEL6}','{$NIVEL7}','{$FICHA}','{$SUELDO}','{$CODCARGO}','{$SITUACIONPER}','{$CEDULA}'), ";

				if( ($cont+1) % 50000 == 0 )
				{
					$insert .= '****';
					$insert  = str_replace(', ****', ';', $insert);
					//sql_ejecutar($insert);	

					$insert = "INSERT INTO nom_movimientos_historico VALUES ";		
				}
			}
		}

		$cont=$cont+1;

		if ($fila['formula']!='')
		{
			$formula=$fila['formula'];

			if ($fila['contractual']==1)
			{
				eval($formula);

				$entrar=1;
				if($MONTO<=0 && $fila['montocero']==1){
					$entrar=0;
				}
				if(!isset($GASTOADMON) || empty($GASTOADMON))
					$GASTOADMON = 0.00;

				if ($entrar==1)
				{
					// $query .= "('{$registro_id}','{$fila['codcon']}','{$fila['ficha']}','{$fila_nom['mes']}','{$fila_nom['anio']}','{$MONTO}','{$CEDULA}','{$fila['tipcon']}','{$fila['unidad']}','{$REF}','{$fila['descrip']}','{$fila['codnivel1']}','{$fila['codnivel2']}','{$fila['codnivel3']}','{$fila['codnivel4']}','{$fila['codnivel5']}','{$fila['codnivel6']}','{$fila['codnivel7']}','{$codigo_nomina}','{$fila['contractual']}','{$SALDOPRE}','{$cheque}'),";
			                                        
                                        if(gettype ( $MONTO )=='array')
                                        {
                                            for($i=0;$i<count($MONTO);$i++)
                                            {
                                                $monto=$MONTO[$i]["montocuo"];
                                                $codigopr=$MONTO[$i]["codigopr"];
                                                $tipopr=$MONTO[$i]["id_tipoprestamo"];
                                                $numpre=$MONTO[$i]["numpre"];
                                                $tipocuo=$MONTO[$i]["tipocuo"];
                                                $numcuo=$MONTO[$i]["numcuo"];
                                                $fechaven=$MONTO[$i]["fechaven"];
                                                $salinicial=$MONTO[$i]["salinicial"];
                                                $salfinal=$MONTO[$i]["salfinal"];
                                                $sql = "INSERT INTO nom_movimientos_nomina 
							(codnom, 
                                                        codcon, 
                                                        ficha, 
                                                        mes, 
                                                        anio, 
                                                        monto, 
                                                        cedula, 
                                                        tipcon, 
                                                        unidad, 
                                                        valor, 
                                                        descrip, 
                                                        codnivel1, 
                                                        codnivel2, 
							 codnivel3, 
                                                         codnivel4, 
                                                         codnivel5, 
                                                         codnivel6, 
                                                         codnivel7, 
                                                         tipnom, 
                                                         contractual, 
                                                         saldopre, 
                                                         refcheque, 
                                                         suesal, 
                                                         cod_cargo,
                                                         gastos_admon, 
                                                         tipopr,
                                                        numpre,
                                                        numcuo,
                                                        fechaven,
                                                        tipocuo,
                                                        montocuo,
                                                        salinicial,
                                                        salfinal) 
							VALUES 
							('{$registro_id}',"
                                                        . "'{$codcon}', "
                                                        . "'{$ficha}', "
                                                        . "'{$fila_nom['mes']}', "
                                                        . "'{$fila_nom['anio']}', 
                                                        '{$monto}', "
                                                        . "'{$CEDULA}', "
                                                        . "'{$fila['tipcon']}', "
                                                        . "'{$fila['unidad']}', "
                                                        . "'{$REF}', "
                                                        . "'{$fila['descrip']}', 
                                                        '{$personal[$ficha]['codnivel1']}', "
                                                        . "'{$personal[$ficha]['codnivel2']}', "
                                                        . "'{$personal[$ficha]['codnivel3']}', "
                                                        . "'{$personal[$ficha]['codnivel4']}',
                                                        '{$personal[$ficha]['codnivel5']}', "
                                                        . "'{$personal[$ficha]['codnivel6']}', "
                                                        . "'{$personal[$ficha]['codnivel7']}', "
                                                        . "'{$codigo_nomina}', 
                                                        '{$fila['contractual']}', "
                                                        . "'{$SALDOPRE}', "
                                                        . "'{$cheque}', "
                                                        . "'{$personal[$ficha]['suesal']}', "
                                                        . "'{$personal[$ficha]['codcargo']}', "
                                                        . "'{$GASTOADMON}' , "
                                                        . "'$tipopr',"
                                                        . "'$numpre',"
                                                        . "'$numcuo',"
                                                        . "'$fechaven',"
                                                        . "'$tipocuo',"
                                                        . "'$monto',"
                                                        . "'$salinicial',"
                                                        . "'$salfinal')";
    //                                            
                                                sql_ejecutar($sql);	
                                            }
                                        }    
                                        else
                                        {
                                            $sql = "INSERT INTO nom_movimientos_nomina 
							(codnom, codcon, ficha, mes, anio, monto, cedula, tipcon, unidad, valor, descrip, codnivel1, codnivel2, 
							 codnivel3, codnivel4, codnivel5, codnivel6, codnivel7, tipnom, contractual, saldopre, refcheque, suesal, cod_cargo,gastos_admon) 
							VALUES 
							('{$registro_id}', '{$codcon}', '{$ficha}', '{$fila_nom['mes']}', '{$fila_nom['anio']}', 
							 '{$MONTO}', '{$CEDULA}', '{$fila['tipcon']}', '{$fila['unidad']}', '{$REF}', '{$fila['descrip']}', 
							 '{$personal[$ficha]['codnivel1']}', '{$personal[$ficha]['codnivel2']}', '{$personal[$ficha]['codnivel3']}', '{$personal[$ficha]['codnivel4']}',
							 '{$personal[$ficha]['codnivel5']}', '{$personal[$ficha]['codnivel6']}', '{$personal[$ficha]['codnivel7']}', '{$codigo_nomina}', 
							 '{$fila['contractual']}', '{$SALDOPRE}', '{$cheque}', '{$personal[$ficha]['suesal']}', '{$personal[$ficha]['codcargo']}', '{$GASTOADMON}' )";
    //                                        if($ficha==25)
    //                                        {
    //                                            echo "SQL2: ".$sql;  
    //                                            echo "<br>";
    ////                                            exit;
    //                                        }              
                                            sql_ejecutar($sql);	
                                        }
				}
			}
		}
		unset($MONTO);
		unset($T01);
		unset($T02);
		unset($T03);
		unset($T04);
		unset($T05);
		unset($T06);
		unset($T07);
		unset($GASTOADMON);	
		unset($FICHA);
		unset($SUELDO);
		unset($SEXO);
		unset($FECHANACIMIENTO);
		unset($EDAD);
		unset($TIPONOMINA);
		unset($FECHAINGRESO);
		unset($CODPROFESION);
		unset($CODCATEGORIA);
		unset($CODCARGO);
		unset($SITUACION);
		unset($FORMACOBRO);
	}	
}
		
if($cont > 0 && $cont < 50000)
{ 
	$insert .= '****';
	$insert = str_replace(', ****', ';', $insert)  ;
	//sql_ejecutar($insert);

	// $query .= '****';
	// $query = str_replace(',****', ';', $query)  ;
	// sql_ejecutar($query);
}

//=======================================================================================================================
 
$query="SELECT c.codcon, c.descrip, c.tipcon, c.formula, c.contractual, c.proratea, c.montocero, c.unidad, 
			   pe.apenom, pe.cedula, pe.ficha, pe.suesal, pe.sexo, pe.fecnac, pe.tipnom, pe.fecing, pe.inicio_periodo, pe.fin_periodo, 
			   pe.codpro, pe.codcat, pe.codcargo, pe.estado, pe.sueldopro, pe.contrato, pe.forcob, pe.codnivel1, pe.codnivel2, 
			   pe.codnivel3, pe.codnivel4, pe.codnivel5, pe.codnivel6, pe.codnivel7, pe.fechaplica, pe.tipopres, 
			   pe.fechasus, pe.fechareisus, pe.fecharetiro, pe.fechavac, pe.fechareivac, pe.antiguedadap, pe.clave_ir,
			   pe.motivo_liq, pe.cod_tli, pe.preaviso  
		FROM nomconceptos as c
		INNER JOIN nomconceptos_tiponomina  as ct ON c.codcon = ct.codcon
		INNER JOIN nomconceptos_frecuencias as cf ON c.codcon = cf.codcon
		INNER JOIN nomconceptos_situaciones as cs ON c.codcon = cs.codcon 
		INNER JOIN nompersonal as pe ON cs.estado = pe.estado
		WHERE cf.codfre='{$fila_nom['frecuencia']}' AND pe.tipnom = '{$codigo_nomina}' 
		AND   ct.codtip='{$codigo_nomina}'
		AND   c.tipcon='P' AND c.contractual='1' 
		ORDER BY c.codcon";
		// group by pe.cedula,pe.ficha,c.formula,c.codcon,cs.estado 

$result2=sql_ejecutar($query);
$end = num_rows($result2);	
$cont=0;	

// pertenece a los campos pero es el mismo valor para todos
$FECHAHOY=date("d/m/Y");
		
$CODNOM=$registro_id;
$FECHANOMINA=$fila_nom['periodo_ini'];
$FECHAFINNOM=$fila_nom['periodo_fin'];
$LUNES=lunes($FECHANOMINA);	
$LUNESPER=lunes_per($FECHANOMINA,$FECHAFINNOM);

$SALARIOMIN=$fila_salmin['monsalmin'];
$ICEDULA=0;
$aux = 0 ;
$insert= "INSERT INTO nom_movimientos_historico VALUES ";
// $query = "INSERT INTO nom_movimientos_nomina 
// 				(codnom,codcon,ficha,mes,anio,monto,cedula,tipcon,unidad,valor,descrip,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual,saldopre,refcheque) VALUES ";

while ($fila = fetch_array($result2))
{
	// Prepara las variables con los valores
	$aux               = 1 ;
	$NOMBRE            =$fila['apenom'];
	$FECHALIQUIDACION  = $fila['fecharetiro'];
	$MOTIVOLIQUIDACION = $fila['motivo_liq'];
	$TIPOLIQUIDACION   = $fila['cod_tli'];
	$PREAVISO          =$fila['preaviso'];
	
	$CEDULA            = $fila['cedula'];
	$FICHA             = $fila['ficha'];
	$SUELDO            =$fila['suesal'];
	$SEXO              =".".$fila['sexo']."'";
	$FECHANACIMIENTO   =$fila['fecnac'];
	$EDAD              =date("Y")-date("Y",$fila['fecnac']);
	$TIPONOMINA        =$fila['tipnom'];
	$FECHAINGRESO      =$fila['fecing'];
    $FECHAINICON       =$fila['inicio_periodo'];
	$FECHAFINCON       =$fila['fin_periodo'];
	$CODPROFESION      =$fila['codpro'];
	$CODCATEGORIA      =$fila['codcat'];
	$CODCARGO          =$fila['codcargo'];
	$SITUACIONPER      =$SITUACION=$fila['estado'];
	$SITUACIONPER      =$fila['estado'];
	$SUELDOPROPUESTO   =$fila['sueldopro'];
	$TIPOCONTRATO      =$fila['contrato'];
	$FORMACOBRO        =$fila['forcob'];
	$NIVEL1            =$fila['codnivel1'];
	$NIVEL2            =$fila['codnivel2'];
	$NIVEL3            =$fila['codnivel3'];
	$NIVEL4            =$fila['codnivel4'];
	$NIVEL5            =$fila['codnivel5'];
	$NIVEL6            =$fila['codnivel6'];
	$NIVEL7            =$fila['codnivel7'];
	$FECHAAPLICACION   =$fila['fechaplica'];
	$TIPOPRESENTACION  =$fila['tipopres'];
	$FECHAFINSUS       =$fila['fechasus'];
	$FECHAINISUS       =$fila['fechareisus'];
	$FECHAFINCONTRATO  =$fila['fecharetiro'];
	$REF               =0;
	$CONTRACTUAL       =$fila['contractual'];
	$FECHAVAC          =$fila['fechavac'];
	$FECHAREIVAC       =$fila['fechareivac'];
	$PRT               =$fila['proratea'];
	$SALDOPRE          =0;
	// $CODINSTRUCCION =$fila['nominstruccion_id'];
	$ANTIGUEDADAA      =$fila['antiguedadap'];
	$FECHAX            ="1997-06-19";
	$CLAVE_IR          =$fila['clave_ir'];
	$MARCA_RELOJ       =$fila['marca_reloj'];     
	
	$cheque            =0;
	if($FORMACOBRO=='Cheque')
		$cheque=1;		
		
	if(strtotime($FECHAINGRESO)<=strtotime($FECHAX))
		$FECHAPRESTACION=$FECHAX;
	else
		$FECHAPRESTACION=$FECHAINGRESO;
	//-----------------------------------
		       
	if($ICEDULA!=$CEDULA)
	{
		$ICEDULA=$CEDULA;
		if($HISTORICO=='' || $HISTORICO!=$CEDULA)
		{
			$HISTORICO=$CEDULA;
			$insert .= "('','{$registro_id}','{$codigo_nomina}','{$NIVEL1}','{$NIVEL2}','{$NIVEL3}','{$NIVEL4}','{$NIVEL5}','{$NIVEL6}','{$NIVEL7}','{$FICHA}','{$SUELDO}','{$CODCARGO}','{$SITUACIONPER}','{$CEDULA}'),";
		}
	}

	$cont=$cont+1;

	if ($fila['formula']!='')
	{
		$formula=$fila['formula'];

		if ($fila['contractual']==1){
			eval($formula);

			$entrar=1;
			if($MONTO<=0 && $fila['montocero']==1){
				$entrar=0;
			}

			if ($entrar==1)
			{
				// $query .="('{$registro_id}','{$fila['codcon']}','{$fila['ficha']}','{$fila_nom['mes']}','{$fila_nom['anio']}','{$MONTO}','{$CEDULA}','{$fila['tipcon']}','{$fila['unidad']}','{$REF}','{$fila['descrip']}','{$fila['codnivel1']}','{$fila['codnivel2']}','{$fila['codnivel3']}','{$fila['codnivel4']}','{$fila['codnivel5']}','{$fila['codnivel6']}','{$fila['codnivel7']}','{$codigo_nomina}','{$fila['contractual']}','{$SALDOPRE}','{$cheque}'),";
				$sql = "INSERT INTO nom_movimientos_nomina 
						(codnom, codcon, ficha, mes, anio, monto, cedula, tipcon, unidad, valor, descrip, codnivel1, codnivel2, 
						 codnivel3, codnivel4, codnivel5, codnivel6, codnivel7, tipnom, contractual, saldopre, refcheque, suesal, cod_cargo) 
						VALUES 
						('{$registro_id}', '{$fila['codcon']}', '{$fila['ficha']}', '{$fila_nom['mes']}', '{$fila_nom['anio']}', 
						 '{$MONTO}', '{$CEDULA}', '{$fila['tipcon']}', '{$fila['unidad']}', '{$REF}', '{$fila['descrip']}', 
						 '{$fila['codnivel1']}', '{$fila['codnivel2']}', '{$fila['codnivel3']}', '{$fila['codnivel4']}',
						 '{$fila['codnivel5']}', '{$fila['codnivel6']}', '{$fila['codnivel7']}', '{$codigo_nomina}', 
						 '{$fila['contractual']}', '{$SALDOPRE}', '{$cheque}', '{$fila['suesal']}', '{$fila['codcargo']}')";
//                                if($fila['ficha']==25)
//                                {
//                                    echo "SQL3: ".$sql; 
//                                    echo "<br>";
//                                    //exit;
//                                }           
				$res = sql_ejecutar($sql);		
			}
		}
	}
	unset($MONTO);
	unset($T01);
	unset($T02);
	unset($T03);
	unset($T04);
	unset($T05);
	unset($T06);
	unset($T07);	
	unset($FICHA);
	unset($SUELDO);
	unset($SEXO);
	unset($FECHANACIMIENTO);
	unset($EDAD);
	unset($TIPONOMINA);
	unset($FECHAINGRESO);
	unset($CODPROFESION);
	unset($CODCATEGORIA);
	unset($CODCARGO);
	unset($SITUACION);
	unset($FORMACOBRO);
}

if($aux == 1)
{ 
	$insert .= '****';
	$insert = str_replace(',****', ';', $insert)  ;
	//$result_insert=sql_ejecutar($insert);

	// $query .= '****';
	// $query = str_replace(',****', ';', $query)  ;
	// $result=sql_ejecutar($query);
}
 
$codigo_nuevo  = AgregarCodigo("nom_nominas_pago","codnom", "WHERE codtip='".$codigo_nomina."'");
$codigo_nuevo -= 1;
$consulta = "UPDATE nomtipos_nomina SET codnom='{$codigo_nuevo}' WHERE codtip='{$codigo_nomina}'";
sql_ejecutar($consulta);

// Ajustes a la base de datos, para optimizar el tiempo de ejecución de los insert:
// sql_ejecutar("COMMIT");
sql_ejecutar("SET UNIQUE_CHECKS = 1");
sql_ejecutar("SET FOREIGN_KEY_CHECKS = 1");
		
echo "FINALIZACION";
?>
