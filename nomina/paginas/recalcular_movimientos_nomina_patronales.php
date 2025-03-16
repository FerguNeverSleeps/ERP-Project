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
$query="DELETE FROM nom_movimientos_nomina "
        . "WHERE tipnom='{$codigo_nomina}' AND codnom='{$registro_id}' AND tipcon='P'";			
$result3=sql_ejecutar($query);
// PARA ACTIVAR EL BONO POR RAZON DE SERVICIO EN EL CSB
unset($result3);

//$query_historico="DELETE FROM nom_movimientos_historico "
//        . "WHERE tipnom='{$codigo_nomina}' AND codnom='{$registro_id}'";
//$result_historico=sql_ejecutar($query_historico);

$consulta="SELECT frecuencia, periodo_ini, periodo_fin, mes, anio 
           FROM   nom_nominas_pago 
           WHERE  codnom='{$registro_id}' AND tipnom='{$codigo_nomina}'";
$resultado_nom=sql_ejecutar($consulta);
$fila_nom=fetch_array($resultado_nom);

$consulta="SELECT monsalmin FROM nomempresa";
$resultado_salmin=sql_ejecutar($consulta);
$fila_salmin=fetch_array($resultado_salmin);
		

//=======================================================================================================================
 
//$query="SELECT c.codcon, c.descrip, c.tipcon, c.formula, c.contractual, c.proratea, c.montocero, c.unidad, 
//			   pe.apenom, pe.cedula, pe.ficha, pe.suesal, pe.sexo, pe.fecnac, pe.tipnom, pe.fecing, pe.inicio_periodo, pe.fin_periodo, 
//			   pe.codpro, pe.codcat, pe.codcargo, pe.estado, pe.sueldopro, pe.contrato, pe.forcob, pe.codnivel1, pe.codnivel2, 
//			   pe.codnivel3, pe.codnivel4, pe.codnivel5, pe.codnivel6, pe.codnivel7, pe.fechaplica, pe.tipopres, 
//			   pe.fechasus, pe.fechareisus, pe.fecharetiro, pe.fechavac, pe.fechareivac, pe.antiguedadap, pe.clave_ir,
//			   pe.motivo_liq, pe.cod_tli, pe.preaviso  
//		FROM nomconceptos as c
//		INNER JOIN nomconceptos_tiponomina  as ct ON c.codcon = ct.codcon
//		INNER JOIN nomconceptos_frecuencias as cf ON c.codcon = cf.codcon
//		INNER JOIN nomconceptos_situaciones as cs ON c.codcon = cs.codcon 
//		INNER JOIN nompersonal as pe ON cs.estado = pe.estado
//		WHERE cf.codfre='{$fila_nom['frecuencia']}' AND pe.tipnom = '{$codigo_nomina}' 
//		AND   ct.codtip='{$codigo_nomina}'
//		AND   c.tipcon='P' AND c.contractual='1' 
//		ORDER BY c.codcon";
                
$query="SELECT c.codcon, c.descrip, c.tipcon, c.formula, c.contractual, c.proratea, c.montocero, c.unidad, c.impdet,
                pe.apenom, pe.cedula, pe.ficha, pe.suesal, pe.sexo, pe.fecnac, pe.tipnom, pe.fecing, pe.inicio_periodo, pe.fin_periodo, 
                pe.codpro, pe.codcat, pe.codcargo, pe.estado, pe.sueldopro, pe.contrato, pe.forcob, pe.codnivel1, pe.codnivel2, 
                pe.codnivel3, pe.codnivel4, pe.codnivel5, pe.codnivel6, pe.codnivel7, pe.fechaplica, pe.tipopres, 
                pe.fechasus, pe.fechareisus, pe.fecharetiro, pe.fechavac, pe.fechareivac, pe.antiguedadap, pe.clave_ir,
                pe.motivo_liq, pe.cod_tli, pe.preaviso  
        FROM nomconceptos as c
        INNER JOIN nomconceptos_tiponomina  as ct ON c.codcon = ct.codcon
        INNER JOIN nomconceptos_frecuencias as cf ON c.codcon = cf.codcon
        INNER JOIN nompersonal as pe ON ct.codtip = pe.tipnom
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
        
        $IMPDET       =$fila['impdet'];     
        
        $query_nivel="SELECT nm.codnivel1 
		FROM nom_movimientos_nomina as nm
		WHERE nm.tipnom = '{$codigo_nomina}' AND   nm.codnom='{$registro_id}' AND nm.ficha='{$FICHA}'
		AND   nm.codcon>=100 AND nm.codcon<=199";

        $result_nivel=sql_ejecutar($query_nivel);
	$fila_nivel = fetch_array($result_nivel);
        $NIVEL1            =$fila_nivel['codnivel1'];
        
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
						(codnom, codcon, ficha, impdet, mes, anio, monto, cedula, tipcon, unidad, valor, descrip, codnivel1, codnivel2, 
						 codnivel3, codnivel4, codnivel5, codnivel6, codnivel7, tipnom, contractual, saldopre, refcheque, suesal, cod_cargo) 
						VALUES 
						('{$registro_id}', '{$fila['codcon']}', '{$fila['ficha']}', '{$fila['impdet']}', '{$fila_nom['mes']}', '{$fila_nom['anio']}', 
						 '{$MONTO}', '{$CEDULA}', '{$fila['tipcon']}', '{$fila['unidad']}', '{$REF}', '{$fila['descrip']}', 
						 '{$NIVEL1}', '{$fila['codnivel2']}', '{$fila['codnivel3']}', '{$fila['codnivel4']}',
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
