<?php
session_start(); set_time_limit(0);
$registro_id=$argv[1];
$codigo_nomina=$argv[2];
$frecuencia=$argv[3];
$periodo_ini=$argv[4];
$periodo_fin=$argv[5];
$mes=$argv[6];
$anio=$argv[7];
$_SESSION['bd']=$argv[8];
$SALARIOMIN=$argv[9];
$_SESSION['codigo_nomina']=$codigo_nomina;

include("../lib/common.php");
include ("func_bd.php");
include ("funciones_nomina.php");

$query="select pe.apenom, c.descrip, pe.cedula, pe.ficha, pe.suesal, pe.sexo, pe.fecnac, pe.tipnom,
     		   pe.fecing, pe.codpro, pe.codcat, pe.codcargo, pe.estado, pe.sueldopro, pe.contrato, pe.forcob,
     		   pe.codnivel1, pe.codnivel2, pe.codnivel3, pe.codnivel4, pe.codnivel5, pe.codnivel6, pe.codnivel7,
     		   pe.fechaplica, pe.tipopres, pe.fechasus, pe.fechareisus, pe.fecharetiro, c.contractual,
     		   pe.fechavac, pe.fechareivac, c.proratea, c.formula, c.montocero, c.codcon, c.tipcon, c.unidad, 
     		   pe.antiguedadap, pe.nominstruccion_id  
		from nomconceptos as c
		inner join nomconceptos_tiponomina as ct on c.codcon = ct.codcon
		inner join nomconceptos_frecuencias as cf on c.codcon = cf.codcon
		inner join nomconceptos_situaciones as cs on c.codcon = cs.codcon 
		inner join nompersonal as pe on cs.estado = pe.estado
		where cf.codfre='".$frecuencia."' and pe.tipnom = '".$codigo_nomina."' 
		and ct.codtip = '".$codigo_nomina."' and c.tipcon='P' 
		and cs.estado = pe.estado and c.contractual='1'";

$result2=sql_ejecutar($query); 
$end = num_rows($result2);
$cont=0;

$FECHAHOY=date("d/m/Y");
$CODNOM=$registro_id;
$FECHANOMINA=$periodo_ini;
$FECHAFINNOM=$periodo_fin;
$LUNES=lunes($FECHANOMINA);
$LUNESPER=lunes_per($FECHANOMINA,$FECHAFINNOM);
while ($fila = fetch_array($result2))
{
	$NOMBRE=$fila['apenom'];
	$CEDULA = $fila['cedula'];
	$FICHA = $fila['ficha'];
	$SUELDO=$fila['suesal'];
	$SEXO=".".$fila['sexo']."'";
	//$FECHANACIMIENTO=date("d/m/Y",strtotime($fila[$fecnac]));
	$FECHANACIMIENTO=$fila['fecnac'];
	$EDAD=date("Y")-date("Y",$fila['fecnac']);
	$TIPONOMINA=$fila['tipnom'];
	$FECHAINGRESO=$fila['fecing'];
	$CODPROFESION=$fila['codpro'];
	$CODCATEGORIA=$fila['codcat'];
	$CODCARGO=$fila['codcargo'];
	$SITUACIONPER=$SITUACION=$fila['estado'];
	$SITUACIONPER=$fila['estado'];
	$SUELDOPROPUESTO=$fila['sueldopro'];
	$TIPOCONTRATO=$fila['contrato'];
	$FORMACOBRO=$fila['forcob'];
	$NIVEL1=$fila['codnivel1'];
	$NIVEL2=$fila['codnivel2'];
	$NIVEL3=$fila['codnivel3'];
	$NIVEL4=$fila['codnivel4'];
	$NIVEL5=$fila['codnivel5'];
	$NIVEL6=$fila['codnivel6'];
	$NIVEL7=$fila['codnivel7'];
	$FECHAAPLICACION=$fila['fechaplica'];
	$TIPOPRESENTACION=$fila['tipopres'];
	$FECHAFINSUS=$fila['fechasus'];
	$FECHAINISUS=$fila['fechareisus'];
	$FECHAFINCONTRATO=$fila['fecharetiro'];
	$REF=0;
	$CONTRACTUAL=$fila['contractual'];
	$FECHAVAC=$fila['fechavac'];
	$FECHAREIVAC=$fila['fechareivac'];
	$PRT=$fila['proratea'];
    $SALDOPRE=0;
    $CODINSTRUCCION=$fila['nominstruccion_id'];
    $ANTIGUEDADAA=$fila['antiguedadap'];
    $FECHAX="1997-06-19";
	if($FORMACOBRO=='Cheque')
		$cheque=1;
	else
		$cheque=0;

	if(strtotime($FECHAINGRESO)<=strtotime($FECHAX))
		$FECHAPRESTACION=$FECHAX;
	else
		$FECHAPRESTACION=$FECHAINGRESO;

	$cont=$cont+1;

	if ($fila['formula']!='')
	{
		$formula=$fila['formula'];
		if ($fila['contractual']==1){
			eval($formula);
			if($MONTO<=0 && $fila['montocero']==1){
				$entrar=0;
			}else{
				$entrar=1;
			}
			if ($entrar==1)
			{
				$query="insert into nom_movimientos_nomina 
						(codnom,codcon,ficha,mes,anio,monto,cedula,tipcon,unidad,valor,descrip,codnivel1,codnivel2,codnivel3,
						 codnivel4,codnivel5,codnivel6,codnivel7,tipnom,contractual,saldopre,refcheque) 
						values ('".$registro_id."','".$fila['codcon']."','".$fila['ficha']."','".$mes."','".$anio."','".$MONTO."',
							    '".$CEDULA."','".$fila['tipcon']."','".$fila['unidad']."','".$REF."','".$fila['descrip']."',
							    '".$NIVEL1."','".$NIVEL2."','".$NIVEL3."','".$NIVEL4."','".$NIVEL5."','".$NIVEL6."','".$NIVEL7."',
							    '".$codigo_nomina."','".$CONTRACTUAL."','".$SALDOPRE."','".$cheque."')";
				$result=sql_ejecutar($query);
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
//=========================================================================================================================================
//$codigo_nuevo=AgregarCodigo("nom_nominas_pago","codnom", "where codtip='".$codigo_nomina."'");
//$codigo_nuevo-=1;
//$consulta="update nomtipos_nomina set codnom='".$codigo_nuevo."' where codtip='".$codigo_nomina."'";
//sql_ejecutar($consulta);
//=========================================================================================================================================
?>