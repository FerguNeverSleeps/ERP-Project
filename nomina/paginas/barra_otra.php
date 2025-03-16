<?php
		include("../lib/common.php");
include ("../header.php");
include ("func_bd.php");
include ("funciones_nomina.php");

	$registro_id=$_GET['registro_id'];		
	$codigo_nomina=$_GET['codigo_nomina'];	
	$consulta="select * from nom_nominas_pago where codnom='".$registro_id."' and tipnom='".$codigo_nomina."'";
	$resultado_nom=sql_ejecutar($consulta);
	$fila_nom=fetch_array($resultado_nom);
	//$op=$_POST['op'];
	//mysqli_free_result($resultado_nom);

	$consulta="select monsalmin from nomempresa";
	$resultado_salmin=sql_ejecutar($consulta);
	$fila_salmin=fetch_array($resultado_salmin);
	
		
		$query="delete from nom_movimientos_nomina where tipnom='".$codigo_nomina."' and codnom='$registro_id' and contractual='1'";			
		$result3=sql_ejecutar($query);
		// PARA ACTIVAR EL BONO POR RAZON DE SERVICIO EN EL CSB
		unset($result3);
		
		$query_historico="delete from nom_movimientos_historico where tipnom='".$codigo_nomina."' and codnom='$registro_id'";
		$result_historico=sql_ejecutar($query_historico);
// 		$bandera="NO";
// 		if($codigo_nomina==3)
// 		{
// 			
// 			$consulta_per="SELECT semfin FROM nomperiodos WHERE nper=$fila_nom[periodo] AND anio=$fila_nom[anio] and codfre=1";
// 			$resultado_per=sql_ejecutar($consulta_per);
// 			$fetch_per=fetch_array($resultado_per);
// 			if($fetch_per['semfin']==1)
// 				$bandera='SI';
// 		}
// 		unset($resultado_per);
		// FILTRALOS EMPLEADOS SEGUN LA NOMINA ACTUAL
		//$query="select * from nompersonal where tipnom = $codigo_nomina";
		

		$query="select * from nomconceptos as c
		inner join
		nomconceptos_tiponomina as ct on c.codcon = ct.codcon
		inner join
		nomconceptos_frecuencias as cf on c.codcon = cf.codcon
		inner join
		nomconceptos_situaciones as cs on c.codcon = cs.codcon inner join
		nompersonal as pe on cs.estado = pe.estado
		where cf.codfre='".$fila_nom['frecuencia']."' and pe.tipnom = '".$codigo_nomina."' and ct.codtip = '".$codigo_nomina."' and cs.estado = pe.estado and c.contractual='1' and c.tipcon='A'
		group by pe.cedula,pe.ficha,c.formula,c.codcon,cs.estado order by c.codcon";
		//exit;
		$result2=sql_ejecutar($query);
		$end = num_rows($result2);	
		$cont=0;	
		
		// pertenece a los campos pero es el mismo valor para todos
		$FECHAHOY=date("d/m/Y");
		
		?>
	<!--  <script>
		var loopObject = {start:0, end:<?php echo $end; ?>, current:0, interval:null};
		</script>-->
	  <?php

		$CODNOM=$registro_id;
		$FECHANOMINA=$fila_nom['periodo_ini'];
		$FECHAFINNOM=$fila_nom['periodo_fin'];
		$LUNES=lunes($FECHANOMINA);	
		$LUNESPER=lunes_per($FECHANOMINA,$FECHAFINNOM);
		
// 		$PRS=$bandera;
		$SALARIOMIN=$fila_salmin['monsalmin'];
		$ICEDULA=0;
		while ($fila = fetch_array($result2))
		{
			// prepara las variables con los valores
			
			$NOMBRE=$fila[apenom];
			//msgbox($fila[apenom]);
			
			?>
			<!--<script>
			document.frmPrincipal.empleado.value ='Empleado: <?php echo $NOMBRE; ?>';
			document.frmPrincipal.concepto.value ='Concepto: <?php echo $fila[descrip]; ?>';			
			</script>-->
			<?php
			$CEDULA = $fila[cedula];
			$FICHA = $fila[ficha];
			$SUELDO=$fila[suesal];//LISTO
			$SEXO=".".$fila[sexo]."'";
			//$FECHANACIMIENTO=date("d/m/Y",strtotime($fila[$fecnac]));
			$FECHANACIMIENTO=$fila[fecnac];
			$EDAD=date("Y")-date("Y",$fila[fecnac]);
			$TIPONOMINA=$fila[tipnom];//LISTO
			$FECHAINGRESO=$fila[fecing];//LISTO
			$CODPROFESION=$fila[codpro];
			$CODCATEGORIA=$fila[codcat];
			$CODCARGO=$fila[codcargo];
			$SITUACIONPER=$SITUACION=$fila[estado];
			$SITUACIONPER=$fila[estado];
			$SUELDOPROPUESTO=$fila[sueldopro];
			$TIPOCONTRATO=$fila[contrato];
			$FORMACOBRO=$fila[forcob];
			$NIVEL1=$fila[codnivel1];
			$NIVEL2=$fila[codnivel2];
			$NIVEL3=$fila[codnivel3];
			$NIVEL4=$fila[codnivel4];
			$NIVEL5=$fila[codnivel5];
			$NIVEL6=$fila[codnivel6];
			$NIVEL7=$fila[codnivel7];
			$FECHAAPLICACION=$fila[fechaplica];
			$TIPOPRESENTACION=$fila[tipopres];
			$FECHAFINSUS=$fila[fechasus];
			$FECHAINISUS=$fila[fechareisus];
			$FECHAFINCONTRATO=$fila[fecharetiro];
			$REF=0;
			$CONTRACTUAL=$fila[contractual];
			$FECHAVAC=$fila[fechavac];
			$FECHAREIVAC=$fila[fechareivac];
			$PRT=$fila[proratea];
                        $SALDOPRE=0;
                        $CODINSTRUCCION=$fila[nominstruccion_id];
                        $ANTIGUEDADAA=$fila[antiguedadap];
                        $FECHAX="1997-06-19";
			if($FORMACOBRO=='Cheque')
				$cheque=1;
			else
				$cheque=0;
				
			if(strtotime($FECHAINGRESO)<=strtotime($FECHAX))
				$FECHAPRESTACION=$FECHAX;
			else
				$FECHAPRESTACION=$FECHAINGRESO;
			//-----------------------------------
			
			//$query="select cedula from nom_movimientos_historico where tipnom='".$codigo_nomina."' and codnom='$registro_id' and cedula='$CEDULA'";
			//$resulthhis=sql_ejecutar($query);
			//$fetchhhis=fetch_array($resulthhis);		       
			if($ICEDULA!=$CEDULA)
			{
				$ICEDULA=$CEDULA;
				if($HISTORICO=='' || $HISTORICO!=$CEDULA)
				{
					$HISTORICO=$CEDULA;
					$insert="insert into nom_movimientos_historico values ('','$registro_id','".$codigo_nomina."','$NIVEL1','$NIVEL2','$NIVEL3','$NIVEL4','$NIVEL5','$NIVEL6','$NIVEL7','$FICHA','$SUELDO','$CODCARGO','$SITUACIONPER','$CEDULA')";
					$result_insert=sql_ejecutar($insert);
				}
			}
			//mysqli_free_result($resulthhis);
			//mysqli_free_result($result_insert);

			$cont=$cont+1;

			if ($fila['formula']!='')
			{
				//$formula=strtoupper($fila[formula]);
				//$cadena_eval="\$MONTO=$formula";	
				$formula=$fila[formula];
				//eval($cadena_eval);

				if ($fila[contractual]==1){
					eval($formula);
					if($MONTO<=0 && $fila[montocero]==1){
						$entrar=0;
					}else{
						$entrar=1;
					}
					if ($entrar==1)
					{
						$query="insert into nom_movimientos_nomina 
						(codnom,codcon,ficha,mes,anio,monto,cedula,tipcon,unidad,valor,descrip,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual,saldopre,refcheque) values ('$registro_id','".$fila[codcon]."','".$fila[ficha]."','".$fila_nom[mes]."','".$fila_nom[anio]."','$MONTO','$CEDULA','".$fila[tipcon]."','".$fila[unidad]."','".$REF."','".$fila['descrip']."','$fila[codnivel1]','$fila[codnivel2]','$fila[codnivel3]','$fila[codnivel4]','$fila[codnivel5]','$fila[codnivel6]','$fila[codnivel7]','".$codigo_nomina."','".$fila['contractual']."','$SALDOPRE','$cheque')";
						$result=sql_ejecutar($query);
						/*if(($codigo_nomina==2)||($codigo_nomina==4))
							mysqli_free_result($result);
						unset($result);*/
						//mysqli_free_result($result);
					}
				}
			}

			?>
		<!--<script>
		loopObject.current =<?php echo $cont; ?>;
		runit();
		</script>	-->	
	  <?php	
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
		
		$query="select * from nomconceptos as c
		inner join
		nomconceptos_tiponomina as ct on c.codcon = ct.codcon
		inner join
		nomconceptos_frecuencias as cf on c.codcon = cf.codcon
		inner join
		nomconceptos_situaciones as cs on c.codcon = cs.codcon inner join
		nompersonal as pe on cs.estado = pe.estado
		where cf.codfre='".$fila_nom['frecuencia']."' and pe.tipnom = '".$codigo_nomina."' and ct.codtip = '".$codigo_nomina."' and cs.estado = pe.estado and c.contractual='1' and c.tipcon='D'
		group by pe.apenom,pe.ficha,c.formula,c.codcon,cs.estado order by c.codcon";
		//exit;
		$result2=sql_ejecutar($query);
		$end = num_rows($result2);	
		$cont=0;	
		
		// pertenece a los campos pero es el mismo valor para todos
		$FECHAHOY=date("d/m/Y");
		
		?>
	<!--  <script>
		var loopObject = {start:0, end:<?php echo $end; ?>, current:0, interval:null};
		</script>-->
	  <?php

		$CODNOM=$registro_id;
		$FECHANOMINA=$fila_nom['periodo_ini'];
		$FECHAFINNOM=$fila_nom['periodo_fin'];
		$LUNES=lunes($FECHANOMINA);	
		$LUNESPER=lunes_per($FECHANOMINA,$FECHAFINNOM);
		
// 		$PRS=$bandera;
		$SALARIOMIN=$fila_salmin['monsalmin'];
		$ICEDULA=0;
		while ($fila = fetch_array($result2))
		{
			// prepara las variables con los valores
			
			$NOMBRE=$fila[apenom];
			
			//msgbox($fila[apenom]);
			
			?>
		<!--	<script>
			document.frmPrincipal.empleado.value ='Empleado: <?php echo $NOMBRE; ?>';
			document.frmPrincipal.concepto.value ='Concepto: <?php echo $fila[descrip]; ?>';			
			</script>-->
			<?php
			$CEDULA = $fila[cedula];
			$FICHA = $fila[ficha];
			$SUELDO=$fila[suesal];//LISTO
			$SEXO=".".$fila[sexo]."'";
			//$FECHANACIMIENTO=date("d/m/Y",strtotime($fila[$fecnac]));
			$FECHANACIMIENTO=$fila[fecnac];
			$EDAD=date("Y")-date("Y",$fila[fecnac]);
			$TIPONOMINA=$fila[tipnom];//LISTO
			$FECHAINGRESO=$fila[fecing];//LISTO
			$CODPROFESION=$fila[codpro];
			$CODCATEGORIA=$fila[codcat];
			$CODCARGO=$fila[codcargo];
			$SITUACIONPER=$SITUACION=$fila[estado];
			$SITUACIONPER=$fila[estado];
			$SUELDOPROPUESTO=$fila[sueldopro];
			$TIPOCONTRATO=$fila[contrato];
			$FORMACOBRO=$fila[forcob];
			$NIVEL1=$fila[codnivel1];
			$NIVEL2=$fila[codnivel2];
			$NIVEL3=$fila[codnivel3];
			$NIVEL4=$fila[codnivel4];
			$NIVEL5=$fila[codnivel5];
			$NIVEL6=$fila[codnivel6];
			$NIVEL7=$fila[codnivel7];
			$FECHAAPLICACION=$fila[fechaplica];
			$TIPOPRESENTACION=$fila[tipopres];
			$FECHAFINSUS=$fila[fechasus];
			$FECHAINISUS=$fila[fechareisus];
			$FECHAFINCONTRATO=$fila[fecharetiro];
			$REF=0;
			$CONTRACTUAL=$fila[contractual];
			$FECHAVAC=$fila[fechavac];
			$FECHAREIVAC=$fila[fechareivac];
			$PRT=$fila[proratea];
                        $SALDOPRE=0;
                        $CODINSTRUCCION=$fila[nominstruccion_id];
                        $ANTIGUEDADAA=$fila[antiguedadap];
                        $FECHAX="1997-06-19";
			if($FORMACOBRO=='Cheque')
				$cheque=1;
			else
				$cheque=0;
				
			if(strtotime($FECHAINGRESO)<=strtotime($FECHAX))
				$FECHAPRESTACION=$FECHAX;
			else
				$FECHAPRESTACION=$FECHAINGRESO;
			//-----------------------------------
			
			//$query="select cedula from nom_movimientos_historico where tipnom='".$codigo_nomina."' and codnom='$registro_id' and cedula='$CEDULA'";
			//$resulthhis=sql_ejecutar($query);
			//$fetchhhis=fetch_array($resulthhis);		       
			if($ICEDULA!=$CEDULA)
			{
				if($HISTORICO=='' || $HISTORICO!=$CEDULA)
				{
					$HISTORICO=$CEDULA;
					$insert="insert into nom_movimientos_historico values ('','$registro_id','".$codigo_nomina."','$NIVEL1','$NIVEL2','$NIVEL3','$NIVEL4','$NIVEL5','$NIVEL6','$NIVEL7','$FICHA','$SUELDO','$CODCARGO','$SITUACIONPER','$CEDULA')";
					$result_insert=sql_ejecutar($insert);
				}
			}
			//mysqli_free_result($resulthhis);
			//mysqli_free_result($result_insert);

			$cont=$cont+1;

			if ($fila['formula']!='')
			{
				//$formula=strtoupper($fila[formula]);
				//$cadena_eval="\$MONTO=$formula";	
				$formula=$fila[formula];
				//eval($cadena_eval);

				if ($fila[contractual]==1){
					eval($formula);
					//echo $formula; GLEON (Para ver las fórmulas que aplica) 30/07/2013
					if($MONTO<=0 && $fila[montocero]==1){
						$entrar=0;
					}else{
						$entrar=1;
					}
					if ($entrar==1)
					{
						$query="insert into nom_movimientos_nomina 
						(codnom,codcon,ficha,mes,anio,monto,cedula,tipcon,unidad,valor,descrip,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual,saldopre,refcheque) values ('$registro_id','".$fila[codcon]."','".$fila[ficha]."','".$fila_nom[mes]."','".$fila_nom[anio]."','$MONTO','$CEDULA','".$fila[tipcon]."','".$fila[unidad]."','".$REF."','".$fila['descrip']."','$fila[codnivel1]','$fila[codnivel2]','$fila[codnivel3]','$fila[codnivel4]','$fila[codnivel5]','$fila[codnivel6]','$fila[codnivel7]','".$codigo_nomina."','".$fila['contractual']."','$SALDOPRE','$cheque')";
						$result=sql_ejecutar($query);
						/*if(($codigo_nomina==2)||($codigo_nomina==4))
							mysqli_free_result($result);
						unset($result);*/
						//mysqli_free_result($result);
					}
				}
			}

			?>
	<!--	<script>
		loopObject.current =<?php echo $cont; ?>;
		runit();
		</script>	-->	
	  <?php	
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
		
		$query="select * from nomconceptos as c
		inner join
		nomconceptos_tiponomina as ct on c.codcon = ct.codcon
		inner join
		nomconceptos_frecuencias as cf on c.codcon = cf.codcon
		inner join
		nomconceptos_situaciones as cs on c.codcon = cs.codcon inner join
		nompersonal as pe on cs.estado = pe.estado
		where cf.codfre='".$fila_nom['frecuencia']."' and pe.tipnom = '".$codigo_nomina."' and ct.codtip = '".$codigo_nomina."' and cs.estado = pe.estado and c.contractual='1' and c.tipcon='P'
		group by pe.apenom,pe.ficha,c.formula,c.codcon,cs.estado order by c.codcon";
		//exit;
		$result2=sql_ejecutar($query);
		$end = num_rows($result2);	
		$cont=0;	
		
		// pertenece a los campos pero es el mismo valor para todos
		$FECHAHOY=date("d/m/Y");
		
		?>
	 <!-- <script>
		var loopObject = {start:0, end:<?php echo $end; ?>, current:0, interval:null};
		</script>-->
	  <?php

		$CODNOM=$registro_id;
		$FECHANOMINA=$fila_nom['periodo_ini'];
		$FECHAFINNOM=$fila_nom['periodo_fin'];
		$LUNES=lunes($FECHANOMINA);	
		$LUNESPER=lunes_per($FECHANOMINA,$FECHAFINNOM);
		
// 		$PRS=$bandera;
		$SALARIOMIN=$fila_salmin['monsalmin'];
		$ICEDULA=0;
		while ($fila = fetch_array($result2))
		{
			// prepara las variables con los valores
			
			$NOMBRE=$fila[apenom];
			
			//msgbox($fila[apenom]);
			
			?>
		<!--	<script>
			document.frmPrincipal.empleado.value ='Empleado: <?php echo $NOMBRE; ?>';
			document.frmPrincipal.concepto.value ='Concepto: <?php echo $fila[descrip]; ?>';			
			</script>-->
			<?php
			$CEDULA = $fila[cedula];
			$FICHA = $fila[ficha];
			$SUELDO=$fila[suesal];//LISTO
			$SEXO=".".$fila[sexo]."'";
			//$FECHANACIMIENTO=date("d/m/Y",strtotime($fila[$fecnac]));
			$FECHANACIMIENTO=$fila[fecnac];
			$EDAD=date("Y")-date("Y",$fila[fecnac]);
			$TIPONOMINA=$fila[tipnom];//LISTO
			$FECHAINGRESO=$fila[fecing];//LISTO
			$CODPROFESION=$fila[codpro];
			$CODCATEGORIA=$fila[codcat];
			$CODCARGO=$fila[codcargo];
			$SITUACIONPER=$SITUACION=$fila[estado];
			$SITUACIONPER=$fila[estado];
			$SUELDOPROPUESTO=$fila[sueldopro];
			$TIPOCONTRATO=$fila[contrato];
			$FORMACOBRO=$fila[forcob];
			$NIVEL1=$fila[codnivel1];
			$NIVEL2=$fila[codnivel2];
			$NIVEL3=$fila[codnivel3];
			$NIVEL4=$fila[codnivel4];
			$NIVEL5=$fila[codnivel5];
			$NIVEL6=$fila[codnivel6];
			$NIVEL7=$fila[codnivel7];
			$FECHAAPLICACION=$fila[fechaplica];
			$TIPOPRESENTACION=$fila[tipopres];
			$FECHAFINSUS=$fila[fechasus];
			$FECHAINISUS=$fila[fechareisus];
			$FECHAFINCONTRATO=$fila[fecharetiro];
			$REF=0;
			$CONTRACTUAL=$fila[contractual];
			$FECHAVAC=$fila[fechavac];
			$FECHAREIVAC=$fila[fechareivac];
			$PRT=$fila[proratea];
                        $SALDOPRE=0;
                        $CODINSTRUCCION=$fila[nominstruccion_id];
                        $ANTIGUEDADAA=$fila[antiguedadap];
                        $FECHAX="1997-06-19";
			if($FORMACOBRO=='Cheque')
				$cheque=1;
			else
				$cheque=0;
				
			if(strtotime($FECHAINGRESO)<=strtotime($FECHAX))
				$FECHAPRESTACION=$FECHAX;
			else
				$FECHAPRESTACION=$FECHAINGRESO;
			//-----------------------------------
			
			//$query="select cedula from nom_movimientos_historico where tipnom='".$codigo_nomina."' and codnom='$registro_id' and cedula='$CEDULA'";
			//$resulthhis=sql_ejecutar($query);
			//$fetchhhis=fetch_array($resulthhis);		       
			if($ICEDULA!=$CEDULA)
			{
				if($HISTORICO=='' || $HISTORICO!=$CEDULA)
				{
					$HISTORICO=$CEDULA;
					$insert="insert into nom_movimientos_historico values ('','$registro_id','".$codigo_nomina."','$NIVEL1','$NIVEL2','$NIVEL3','$NIVEL4','$NIVEL5','$NIVEL6','$NIVEL7','$FICHA','$SUELDO','$CODCARGO','$SITUACIONPER','$CEDULA')";
					$result_insert=sql_ejecutar($insert);
				}
			}
			//mysqli_free_result($resulthhis);
			//mysqli_free_result($result_insert);

			$cont=$cont+1;

			if ($fila['formula']!='')
			{
				//$formula=strtoupper($fila[formula]);
				//$cadena_eval="\$MONTO=$formula";	
				$formula=$fila[formula];
				//eval($cadena_eval);

				if ($fila[contractual]==1){
					eval($formula);
					if($MONTO<=0 && $fila[montocero]==1){
						$entrar=0;
					}else{
						$entrar=1;
					}
					if ($entrar==1)
					{
						$query="insert into nom_movimientos_nomina 
						(codnom,codcon,ficha,mes,anio,monto,cedula,tipcon,unidad,valor,descrip,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual,saldopre,refcheque) values ('$registro_id','".$fila[codcon]."','".$fila[ficha]."','".$fila_nom[mes]."','".$fila_nom[anio]."','$MONTO','$CEDULA','".$fila[tipcon]."','".$fila[unidad]."','".$REF."','".$fila['descrip']."','$fila[codnivel1]','$fila[codnivel2]','$fila[codnivel3]','$fila[codnivel4]','$fila[codnivel5]','$fila[codnivel6]','$fila[codnivel7]','".$codigo_nomina."','".$fila['contractual']."','$SALDOPRE','$cheque')";
						$result=sql_ejecutar($query);
						/*if(($codigo_nomina==2)||($codigo_nomina==4))
							mysqli_free_result($result);
						unset($result);*/
						//mysqli_free_result($result);
					}
				}
			}

			?>
	<!--	<script>
		loopObject.current =<?php echo $cont; ?>;
		runit();
		</script>	-->	
	  <?php	
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
		
		$codigo_nuevo=AgregarCodigo("nom_nominas_pago","codnom", "where codtip='".$codigo_nomina."'");
		$codigo_nuevo-=1;
		$consulta="update nomtipos_nomina set codnom='".$codigo_nuevo."' where codtip='".$codigo_nomina."'";
		sql_ejecutar($consulta);
		//echo memory_get_usage()/1024;
		//ECHO 'GENERACION LISTA!!';
?>