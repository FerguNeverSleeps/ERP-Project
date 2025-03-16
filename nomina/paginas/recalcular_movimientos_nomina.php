<?php 
include("../lib/common.php");
include ("func_bd.php");
include ("funciones_nomina.php");

$host_ip = $_SERVER['REMOTE_ADDR'];
$modulo = "Movimientos Planilla - Generar";
$url = "movimientos_nomina_persona_recalcular.php";
$ficha=$_GET['ficha'];
$registro_id      =$_GET['nomina'];		
$cod_nomina       =$_GET['codigo_nomina'];

$consulta         ="select * from nom_nominas_pago where codnom='".$registro_id."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado_nom    =sql_ejecutar($consulta);
$fila_nom         =fetch_array($resultado_nom);
$op               =1;
$pagina2          =$_GET['pagina2'];
//mysqli_free_result($resultado_nom);

$consulta         ="select monsalmin from nomempresa";
$resultado_salmin =sql_ejecutar($consulta);
$fila_salmin      =fetch_array($resultado_salmin);

$consulta_personal="SELECT p.apenom, p.cedula "
        . "FROM nompersonal as p "
        . "WHERE p.ficha='".$_GET['ficha']."'";
//echo $consulta;
$resultado_personal=sql_ejecutar($consulta_personal);
$fila_personal = fetch_array($resultado_personal);
$nombre=       $fila_personal[apenom];
$cedula=       $fila_personal[cedula];
$nomina=$_GET['nomina'];
$tipo_nomina=$_SESSION['codigo_nomina'];
$accion="generar";

$descripcion = "Generar Movimientos - Colaborador ".$ficha." - Nombre ".$nombre." - Cedula: ".$cedula;

if ($op==1)
{
	
	// BORRA LOS MOVIMIENTO DE LA NOMINA A PROCESAR
	// BORRA LOS MOVIMIENTO DE LA NOMINA A PROCESAR
	$query   ="DELETE FROM "
                . "nom_movimientos_nomina "
                . "WHERE tipnom='".$_SESSION['codigo_nomina']."' "
                . "AND codnom='$registro_id' "
                . "AND ficha='".$_GET['ficha']."' "
                . "AND contractual=1 "
                . "AND ((codcon>=200 AND codcon<500) OR (codcon>599))";
	$result3 =sql_ejecutar($query);	
	// FILTRALOS EMPLEADOS SEGUN LA NOMINA ACTUAL
	//$query="select * from nompersonal where tipnom = $cod_nomina";



	/* MODIFICAMOS EL VALOR DE LOS ATRIBUTOS continua_laborando Y ultimo_ingreso DE LA TABLA nom_movimiento_nomina*/
	$query   ="UPDATE nom_movimientos_nomina 
	           SET continua_laborando='".$_GET['continua_laborando']."', 
	               ultimo_ingreso='".$_GET['ultimo_ingreso']."'
	 			WHERE tipnom='".$_SESSION['codigo_nomina']."' AND 
	 			      codnom='".$registro_id."' AND 
	 			      ficha='".$_GET['ficha']."' AND 
	 			      codcon=114";
	$result =sql_ejecutar($query);	
	/**/

	$query="
	SELECT 
		*,
		c.unidad concepto_unidad, c.impdet
	FROM nomconceptos as c
		inner join nomconceptos_tiponomina as ct on c.codcon = ct.codcon
		inner join nomconceptos_frecuencias as cf on c.codcon = cf.codcon
		inner join nomconceptos_situaciones as cs on c.codcon = cs.codcon
		inner join nompersonal as pe on cs.estado = pe.estado 
	WHERE 
		cf.codfre='".$fila_nom['frecuencia']."' and 
		pe.tipnom = '".$_SESSION['codigo_nomina']."' and 
		ct.codtip = '".$_SESSION['codigo_nomina']."' and 
		pe.ficha='$_GET[ficha]' and 
		cs.estado = pe.estado and 
		c.contractual='1' and
		((c.codcon>=200 AND c.codcon<500) OR (c.codcon>599))
	GROUP BY 
		pe.apenom,
		pe.ficha,
		c.formula,
		c.codcon,
		cs.estado 
	ORDER BY
		c.tipcon, 
		c.codcon";
     
	//print $query;
    //exit;
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
	$PRS         =$bandera;	
	$SALARIOMIN  =$fila_salmin['monsalmin'];
	$FRECUENCIA  =$fila_nom['frecuencia'];

	while ($fila = fetch_array($result2))
	{
		// prepara las variables con los valores
		
		$NOMBRE           =$fila[apenom];
		$FECHARETIRO      =$fila[fecharetiro];
		//msgbox($fila[apenom]);
		
		
		$CEDULA           = $fila[cedula];
		$FICHA            = $fila[ficha];
                $FECHALIQUIDACION  = $fila[fecharetiro_tmp];
                $MOTIVOLIQUIDACION = $fila[motivo_liq_tmp];
                $TIPOLIQUIDACION   = $fila[cod_tli_tmp];
                $PREAVISO          =$fila[preaviso_tmp];
                $ARTICULO_122      =$fila[articulo_122_tmp];
		$SUELDO           =$fila[suesal];//LISTO
		$SEXO             =".".$fila[sexo]."'";
		$HORABASE         = $fila[hora_base];
		$FECHANACIMIENTO  =date("d/m/Y",strtotime($fila[$fecnac]));
		$EDAD             =date("Y")-date("Y",$fila[$fecnac]);
		$TIPONOMINA       =$fila[tipnom];//LISTO
		$FECHAINGRESO     =$fila[fecing];//LISTO
		$FECHAINICON      =$fila[inicio_periodo];
	    $FECHAFINCON      =$fila[fin_periodo];
		$CODPROFESION     =$fila[codpro];
		$CODCATEGORIA     =$fila[codcat];
		$CODCARGO         =$fila[codcargo];
		$SITUACIONPER     =$SITUACION=$fila[estado];
		$SITUACIONPER     =$fila[estado];
		$SUELDOPROPUESTO  =$fila[sueldopro];
		$TIPOCONTRATO     =$fila[contrato];
		$FORMACOBRO       =$fila[forcob];
		$NIVEL1           =$fila[codnivel1];
		$NIVEL2           =$fila[codnivel2];
		$NIVEL3           =$fila[codnivel3];
		$NIVEL4           =$fila[codnivel4];
		$NIVEL5           =$fila[codnivel5];
		$NIVEL6           =$fila[codnivel6];
		$NIVEL7           =$fila[codnivel7];
		$FECHAAPLICACION  =$fila[fechaplica];
		$TIPOPRESENTACION =$fila[tipopres];
		$FECHAFINSUS      =$fila[fechasus];
		$FECHAINISUS      =$fila[fechareisus];
		$FECHAFINCONTRATO =$fila[fecharetiro];
		$REF              =0;
		$CONTRACTUAL      =$fila[contractual];
		if($fila[fechavac])
		$FECHAVAC         =date("Y-m-d", strtotime("$fila[fechavac] -1 day"));
		else
		$FECHAVAC         ='0000-00-00';
		$FECHAREIVAC      =$fila[fechareivac];
		$PRT              =$fila[proratea];
		$MARCA_RELOJ      =$fila[marca_reloj];
		
                $IMPDET       =$fila[impdet];   
		//-----------------------------------
		
		
		
		$cont        =$cont+1;

		if ($fila['formula']!='')
		{
			//$formula=strtoupper($fila[formula]);
			//$cadena_eval="\$MONTO=$formula";	
			$formula=$fila[formula];
			//eval($cadena_eval);

			if ($fila[contractual]==1)
                        {
				eval($formula);
                                if($fila[codcon]==217)
                                {
//                                    echo $formula; 
//                                    echo "<br>";
//                                    echo "MONTO: ".$MONTO;
                                }
				if($MONTO<=0 && $fila[montocero]==1)
                                {
					$entrar=0;
				}else
                                {
					$entrar=1;
				}
				if ($entrar==1)
				{
					$query="INSERT INTO nom_movimientos_nomina 
                                                (codnom,
                                                codcon,
                                                ficha,
                                                impdet,
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
                                                suesal,
                                                cod_cargo) 
                                                VALUES 
                                                ('$registro_id',"
                                                . "'".$fila[codcon]."',"
                                                . "'".$fila[ficha]."',"
                                                . "'".$fila[impdet]."',"
                                                . "'".$fila_nom[mes]."',"
                                                . "'".$fila_nom[anio]."',"
                                                . "'$MONTO',"
                                                . "'$CEDULA',"
                                                . "'".$fila[tipcon]."',"
                                                . "'".$fila[concepto_unidad]."',"
                                                . "'".$REF."',"
                                                . "'".$fila['descrip']."',"
                                                . "'$fila[codnivel1]',"
                                                . "'$fila[codnivel2]',"
                                                . "'$fila[codnivel3]',"
                                                . "'$fila[codnivel4]',"
                                                . "'$fila[codnivel5]',"
                                                . "'$fila[codnivel6]',"
                                                . "'$fila[codnivel7]',"
                                                . "'".$_SESSION['codigo_nomina']."',"
                                                . "'".$fila['contractual']."',"
                                                . "'$SUELDO',"
                                                . "'$CODCARGO')";
					$result=sql_ejecutar($query);
					//echo $query,"<br> ";

					unset($result);
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
	//unset($formula);
	//unset($resultado2);
	//mysqli_free_result($resultado2);
	}
	$codigo_nuevo =AgregarCodigo("nom_nominas_pago","codnom", "where codtip='".$_SESSION['codigo_nomina']."'");
	$codigo_nuevo -=1;
	$consulta     ="update nomtipos_nomina set codnom='".$codigo_nuevo."' where codtip='".$_SESSION['codigo_nomina']."'";
	sql_ejecutar($consulta);
        
        $sql_log = "INSERT INTO log_transacciones 
            (cod_log, 
            descripcion, 
            fecha_hora, 
            modulo, 
            url, 
            accion, 
            valor, 
            usuario,
            host) 
            VALUES 
            (NULL, 
            '".$descripcion."', "
            . "now(), "
        . "'".$modulo."', "
        . "'".$url."', "
        . "'".$accion."',"
        . "'".$cod."',"
        . "'".$_SESSION['usuario'] ."',"
        . "'".$host_ip."')";

        $res_log = sql_ejecutar($sql_log);

}

?>
