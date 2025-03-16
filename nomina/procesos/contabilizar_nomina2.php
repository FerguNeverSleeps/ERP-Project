<?php 
session_start();
ob_start();
?>
<?php
require_once "../lib/common.php";
include("../header.php");
$url="contabilizar_nomina";
$conexion=conexion();
$conexc=conexion_contabilidad();
$nomina=$_GET['codigo_nomina'];

$cuenta=$_GET['cod_banco'];
$query333="SELECT Descrip FROM {$_SESSION['bdc']}.cwconcue WHERE Cuenta='$cuenta'";
$resultado333=query($query333,$conexc);
$fetch_ctacon=fetch_array($resultado333);

$query2="SELECT * FROM nomempresa";
$resultado2=query($query2,$conexion);
$fetchemp=fetch_array($resultado2);


function numcom($fecha,$par)
{
	$conexc=conexion_contabilidad();
	
	$consult = "SELECT * FROM {$_SESSION['bdc']}.cwconemp";
	$result = query($consult,$conexc);
	$fil=fetch_array($result);
	
	$mes=explode('-',$fecha);
	$i=$mes[1];
	if($i=='01')
	{
		$mesl='Enero';	
		$bd='Comene';
	}	
	elseif($i=='02')
	{
		$mesl='Febrero';
		$bd='Comfeb';
	}	
	elseif($i=='03')
	{
		$mesl='Marzo';
		$bd='Commar';
	}	
	elseif($i=='04')
	{
		$mesl='Abril';
		$bd='Comabr';
	}	
	elseif($i=='05')
	{
		$mesl='Mayo';
		$bd='Commay';
	}	
	elseif($i=='06')
	{
		$mesl='Junio';
		$bd='Comjun';
	}		
	elseif($i=='07')
	{
		$mesl='Julio';
		$bd='Comjul';
	}	
	elseif($i=='08')
	{
		$mesl='Agosto';
		$bd='Comago';
	}	
	elseif($i=='09')
	{
		$mesl='Septiembre';
		$bd='Comsep';
	}	
	elseif($i==10)
	{
		$mesl='Octubre';
		$bd='Comoct';
	}	
	elseif($i==11)
	{
		$mesl='Noviembre';
		$bd='Comnov';
	}	
	elseif($i==12)
	{
		$mesl='Diciembre';
		$bd='Comdic';
	}

	$numcom = $fil[$bd];
	$ultimo_comprobante2=$fil['Numcom'];

	if($par==1)
		return $numcom;
	elseif($par==2)
		return $ultimo_comprobante2;
	elseif($par==3)
		return $bd;

}


//$result_upt=query("UPDATE nom_nominas_pago SET contabilizada=1 WHERE codnom=$nomina AND tipnom=$_SESSION[codigo_nomina]",$conexion);
$conexion=conexion();

// $consulta_nomina="select * from nom_nominas_pago where codnom=$nomina and tipnom=$_SESSION[codigo_nomina]";
// $resultado_nomina=query($consulta_nomina,$conexion);
// $fila_nomina=fetch_array($resultado_nomina);

$i=0;
$j=0;
$cadena="";
while($i<$_POST[cant])
{
	$k="nomina".$i;
	if($_POST[$k]!='')
	{
		$codigo=explode("-",$_POST[$k]);
		if($cadena=="")
			$cadena.="(nom_movimientos_nomina.codnom='$codigo[0]' and nom_movimientos_nomina.tipnom='$codigo[1]')";
		else
			$cadena.=" or (nom_movimientos_nomina.codnom='$codigo[0]' and nom_movimientos_nomina.tipnom='$codigo[1]')";
		$codigos[$j][0]=$codigo[0];
		$codigos[$j][1]=$codigo[1];
		$j++;
	}
	$i++;
}



$k=$j;
$i=1;
$j=0;
while($j<$k)
{
	$total_deduc=$total_asig=0;
	$conexion=conexion();
	//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO ASIGNACION QUE EXISTEN EN ESTA NOMINA
	$consulta="SELECT tipnom, codnom, descrip, periodo_ini, periodo_fin, fechapago "
                . " FROM nom_nominas_pago WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."'";	
	$resultnomina=query($consulta,$conexion);
	$fetchnom=fetch_array($resultnomina);
	$fecham=$fetchnom[periodo_fin];
        $codnom=$fetchnom[codnom];
	$tipnom=$fetchnom[tipnom];
        $descrip=$fetchnom[descrip];
        $periodo_ini=$fetchnom[periodo_ini];
        $fecha_pago=$fetchnom[fecha_pago];

	$conexion=conexion_contabilidad();

	$fetchnom[periodo_fin];
	$numCom=numcom($fecham,1);
	$ultimo_comprobante=numcom($fecham,2);
	$campo=numcom($fecham,3);
	$numCom++;
	$ultimo_comprobante++;
	
	$resulatdo=mysqli_query($conexion,"update {$_SESSION['bdc']}.cwconemp set Numcom='".$ultimo_comprobante."',$campo='".$numCom."'");
	
	$consulta="insert into {$_SESSION['bdc']}.cwconhco (Numcom,Codtipo,Fecha,Descrip,Estado) "
                . "values "
                . "('".$numCom."',"
                . "'3',"
                . "'".$fecham."',"
                . "'Contabilizacion de Planilla/Nomina: ".$codnom." - ".$descrip."',"
                . "'1')";
	$resultado=query($consulta,$conexion);

	$conexion=conexion();
	//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO ASIGNACION QUE EXISTEN EN ESTA NOMINA
	$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."' AND tipcon='A' AND (codcon<>198  or codcon<> 199) ORDER BY codcon";	
	$result10=query($consulta,$conexion);

	while($fetch_con=fetch_array($result10))
	{
		$conexion=conexion();
		$consulta="SELECT SUM(monto) as suma FROM nom_movimientos_nomina join nompersonal on nom_movimientos_nomina.cedula=nompersonal.cedula and nom_movimientos_nomina.ficha=nompersonal.ficha  WHERE nom_movimientos_nomina.codnom='".$codigos[$j][0]."' AND nom_movimientos_nomina.tipnom='".$codigos[$j][1]."' AND nom_movimientos_nomina.codcon=".$fetch_con['codcon']."";
		$result_suma=query($consulta,$conexion);
		$fetch_suma=fetch_array($result_suma);

		//$consultax="SELECT SUM(monto) as suma FROM nom_movimientos_nomina join nompersonal on nom_movimientos_nomina.cedula=nompersonal.cedula and nom_movimientos_nomina.ficha=nompersonal.ficha nom_movimientos_nomina.ficha=nompersonal.ficha  WHERE nom_movimientos_nomina.codnom='".$codigos[$j][0]."' AND nom_movimientos_nomina.tipnom='".$codigos[$j][1]."' AND nom_movimientos_nomina.codcon=".$fetch_con['codcon']." and (codcon=198 or codcon=199)";
		//$result_sumax=query($consultax,$conexion);
		//$fetch_sumax=fetch_array($result_sumax);
		
		$consulta="SELECT ctacon FROM nomconceptos WHERE codcon=".$fetch_con['codcon']."";
		$result_cta=query($consulta,$conexion);
		$fetch_cta=fetch_array($result_cta);
			
		$consulta="SELECT descrip FROM nomconceptos WHERE codcon=$fetch_con[codcon]";
		$result_descon=query($consulta,$conexion);
		$fetch_descon=fetch_array($result_descon);


		//$descripcion=$descripcion." ".$fila_cheque['concepto'];
		$conexion=conexion_contabilidad();
		$consulta_cwcondco="insert into {$_SESSION['bdc']}.cwcondco (Numcom,Fecha,Cuenta,Referen,Tiporef,Descrip,Debito,Credito,Numlim,FechaD) values ('".$numCom."','".$fecham."','".$fetch_cta['ctacon']."','','NO','".$fetch_descon['descrip']."','".$fetch_suma['suma']."','','".$i."','".date("Y-m-d")."')";
		$resultado_cwcondco=query($consulta_cwcondco,$conexion);
		$i++;
		$total_asig+=$fetch_suma['suma'];

		$conexion=conexion();
	}
	$conexion=conexion();
	//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO DEDUCCION QUE EXISTEN EN ESTA NOMINA
	$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."' AND tipcon='D' ORDER BY codcon";
	$result_cond=query($consulta,$conexion);
	
	$total_deduc;
	while($fetch_cond=fetch_array($result_cond))
	{	
		$conexion=conexion();
		$consulta="SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."' AND codcon=".$fetch_cond['codcon']." ";
		$result_suma2=query($consulta,$conexion);
		$fetch_suma2=fetch_array($result_suma2);
			
		$consulta="SELECT ctacon FROM nomconceptos WHERE codcon='$fetch_cond[codcon]'";
		$result_cta2=query($consulta,$conexion);
		$fetch_cta2=fetch_array($result_cta2);
				
		$consulta="SELECT descrip FROM nomconceptos WHERE codcon='$fetch_cond[codcon]'";
		$result_descon2=query($consulta,$conexion);
		$fetch_descon2=fetch_array($result_descon2);
			//$descripcion=$descripcion." ".$fila_cheque['concepto'];
		$conexion=conexion_contabilidad();
		$consulta_cwcondco="insert into {$_SESSION['bdc']}.cwcondco (Numcom,Fecha,Cuenta,Referen,Tiporef,Descrip,Debito,Credito,Numlim,FechaD) values ('".$numCom."','".$fecham."','".$fetch_cta2['ctacon']."','','NO','".$fetch_descon2['descrip']."','','".$fetch_suma2['suma']."','".$i."','".date("Y-m-d")."')";
		$resultado_cwcondco=query($consulta_cwcondco,$conexion);
		$i++;
		$total_deduc+=$fetch_suma2['suma'];
		$conexion=conexion();
	}
	$conexion=conexion();
	//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO PATRONAL QUE EXISTEN EN ESTA NOMINA
	$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."' AND tipcon='P' ORDER BY codcon";	
	$result31=query($consulta,$conexion);

	while($fetch_con=fetch_array($result31))
	{
		$conexion=conexion();
		$consulta="SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."' AND codcon=".$fetch_con['codcon']."";
		$result_suma=query($consulta,$conexion);
		$fetch_suma=fetch_array($result_suma);
		
		$consulta="SELECT ctacon FROM nomconceptos WHERE codcon=".$fetch_con['codcon']."";
		$result_cta=query($consulta,$conexion);
		$fetch_cta=fetch_array($result_cta);
			
		$consulta="SELECT descrip FROM nomconceptos WHERE codcon=$fetch_con[codcon]";
		$result_descon=query($consulta,$conexion);
		$fetch_descon=fetch_array($result_descon);
		//$descripcion=$descripcion." ".$fila_cheque['concepto'];
		$conexion=conexion_contabilidad();
		$consulta_cwcondco="insert into {$_SESSION['bdc']}.cwcondco (Numcom,Fecha,Cuenta,Referen,Tiporef,Descrip,Debito,Credito,Numlim,FechaD) values ('".$numCom."','".$fecham."','".$fetch_cta['ctacon']."','','NO','".$fetch_descon['descrip']."','".$fetch_suma['suma']."','','".$i."','".date("Y-m-d")."')";
		$resultado_cwcondco=query($consulta_cwcondco,$conexion);
		$i++;
		$conexion=conexion();

	}
	$conexion=conexion();
	//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO PATRONAL QUE EXISTEN EN ESTA NOMINA
	$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."' AND tipcon='P' ORDER BY codcon";	
	$result32=query($consulta,$conexion);

	while($fetch_con=fetch_array($result32))
	{
		$consulta="SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$codigos[$j][0]."' AND tipnom='".$codigos[$j][1]."' AND codcon=".$fetch_con['codcon']."";
		$result_suma=query($consulta,$conexion);
		$fetch_suma=fetch_array($result_suma);
		
		$consulta="SELECT ctacon,ctacon1 FROM nomconceptos WHERE codcon=".$fetch_con['codcon']."";
		$result_cta=query($consulta,$conexion);
		$fetch_cta=fetch_array($result_cta);
			
		$consulta="SELECT descrip FROM nomconceptos WHERE codcon=$fetch_con[codcon]";
		$result_descon=query($consulta,$conexion);
		$fetch_descon=fetch_array($result_descon);
		//$descripcion=$descripcion." ".$fila_cheque['concepto'];
		$conexion=conexion_contabilidad();
		$consulta_cwcondco="insert into {$_SESSION['bdc']}.cwcondco (Numcom,Fecha,Cuenta,Referen,Tiporef,Descrip,Debito,Credito,Numlim,FechaD) values ('".$numCom."','".$fecham."','".$fetch_cta['ctacon1']."','','NO','".$fetch_descon['descrip']."','','".$fetch_suma['suma']."','".$i."','".date("Y-m-d")."')";
		$resultado_cwcondco=query($consulta_cwcondco,$conexion);
		$i++;
		$conexion=conexion();
	}
	$conexion=conexion_contabilidad();

	$cuenta=$_POST['banco'];
	$query333="SELECT Descrip FROM {$_SESSION['bdc']}.cwconcue WHERE Cuenta='$cuenta'";
	$resultado333=query($query333,$conexion);
	$fetch_ctacon=fetch_array($resultado333);
	
	if(($fetchemp[ctacheque]!='')&&($fetchemp[ctacheque]!=0))
	{
		$queryy="SELECT Descrip FROM {$_SESSION['bdc']}.cwconcue WHERE Cuenta='$fetchemp[ctacheque]'";
		$resultadoy=query($queryy,$conexion);
		$fetch_ctacony=fetch_array($resultadoy);

		if(($fetch_sumax['suma']!='')&&($fetch_sumax['suma']!=0))
		{
			$consulta_cwcondco="insert into {$_SESSION['bdc']}.cwcondco (Numcom,Fecha,Cuenta,Referen,Tiporef,Descrip,Debito,Credito,Numlim,FechaD) values ('".$numCom."','".$fecham."','$fetchemp[ctacheque]','','NO','$fetch_ctacony[Descrip]','".$fetch_sumax['suma']."','','".$i."','".date("Y-m-d")."')";
			$resultado_cwcondco=query($consulta_cwcondco,$conexion);
			$i++;
		}
	}
	$consulta_cwcondco="insert into {$_SESSION['bdc']}.cwcondco (Numcom,Fecha,Cuenta,Referen,Tiporef,Descrip,Debito,Credito,Numlim,FechaD) values ('".$numCom."','".$fecham."','2.06.01.00001.','','NO','Total Salarios Por Pagar','','".(($fetch_sumax['suma']+$total_asig)-$total_deduc)."','".$i."','".date("Y-m-d")."')";
	$resultado_cwcondco33=query($consulta_cwcondco,$conexion);
	$j++;
        
	$conexion=conexion();
        
        $update_nomina="UPDATE nom_nominas_pago SET contabilizada=1 WHERE codnom='$codnom' AND tipnom='$tipnom'";
        $result_update=query($update_nomina,$conexion);
}

cerrar_conexion($conexion);
echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
alert(\"NOMINA CONTABILIZADA\")
parent.cont.location.href=\"../paginas/menu_procesos.php\"
</SCRIPT>";
