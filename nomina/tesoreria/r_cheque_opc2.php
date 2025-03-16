<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include('../header.php');
include('../lib/numerosALetras.class.php');


$nomina = @$_GET['nomina'];


?>
<STYLE TYPE="text/css">
     P.breakhere {page-break-before: always}
</STYLE>

<div align="right"><INPUT type="button" name="imprimir" value="Imprimir" onclick="javascript:imprimir('impresion')"></div>
<div id="impresion">

<?php

$conexion = conexion();

$consulta3="select nch.*, np.ficha, np.suesal as sueldo, np.seguro_social from nomcheques nch join nompersonal np on np.personal_id=nch.personal_id where codnom='".$nomina."' and tipo='1'";
$resultado3 = query($consulta3, $conexion);
while($fila3=fetch_array($resultado3))
{
	$nombreContribuyente = $fila3['beneficiario'];
	$usuario=$fila3['log_usr'];
	$fecha=$fila3['fecha'];
	$temp=explode('-',$fecha);
	$dia=$temp[2];
	$mes=$temp[1];
	$ano=$temp[0];
	$monto=$fila3['monto'];
	$concepto = $fila3['concepto'];
	$cedula = $fila3['cedula_rif'];
	$n = new numerosALetras();
	$montoLetras=$n->convertir($monto);
	$ficha = $fila3[ficha];
	$sueldo = str_replace('.', '', $fila3[sueldo]);
	$seguro_social = $fila3[seguro_social];

	$consulta4="select * from nom_movimientos_nomina where codnom='".$nomina."' and ficha='".$ficha."' and codcon='200'";
	$resultado4 = query($consulta4, $conexion);
	$fila4=fetch_array($resultado4);
	$ss=str_replace('.', '', $fila4[monto]);


	$consulta5="select ifnull(monto,0) as monto from nom_movimientos_nomina where codnom='".$nomina."' and ficha='".$ficha."' and codcon='143'";
	$resultado5 = query($consulta5, $conexion);
	$fila5=fetch_array($resultado5);
	$islr=str_replace('.', '', $fila5[monto]);

	$consulta6="select ifnull(monto,0) as monto from nom_movimientos_nomina where codnom='".$nomina."' and ficha='".$ficha."' and codcon='201'";
	$resultado6 = query($consulta6, $conexion);
	$fila6=fetch_array($resultado6);
	$se=str_replace('.', '', $fila6[monto]);

	$consulta7="select sum(monto) as monto from nom_movimientos_nomina where codnom='".$nomina."' and ficha='".$ficha."' and tipcon='D'";
	$resultado7 = query($consulta7, $conexion);
	$fila7=fetch_array($resultado7);
	$totald=str_replace('.', '', $fila7[monto]);

	?>
	<TABLE width="800" align="center" border=0>
	<TR>
	<TD align="right" width='500px'><strong> <?php echo $dia.$mes.$ano; ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD align="left" > <?php echo $dia.$mes.$ano; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha.'&nbsp;'.$sueldo.'&nbsp;'.$ss.'&nbsp;&nbsp;&nbsp;&nbsp;'.$islr.'&nbsp;'.$se; ?></TD>
	</TR>
	<TR>
	<TD align="left" width='500px'><strong> <?php echo $cedula; ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD align="left" > <?php echo $nombreContribuyente; ?>&nbsp;&nbsp;&nbsp; <?php echo $seguro_social?>&nbsp;&nbsp;&nbsp;<?php echo $cedula; ?></TD>
	</TR>

	<TR>
	<TD align="left" style="padding: 10px 0px 5px 70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><?php echo $nombreContribuyente;?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format($monto,2,',','.'); ?></TD>

	<TD align="right" >  &nbsp;&nbsp;&nbsp; <?php echo $monto?></TD>
	</TR>

	<TR>
	<?php $calculo=strlen($montoLetras)/70;
	if($calculo>=0 && $calculo<=1){ ?>
	<TD align="left"  style="padding: 10px 0px 20px 70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo strtoupper($montoLetras);?></strong></TD>
	<?php }else{ ?>
	<TD align="left" style="padding: 10px 0px 10px 70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo strtoupper($montoLetras);?></strong></TD>
	<?php }?>
	</TR>
	
	<!--<TR>
	<TD colspan="3" align="right" style="padding: 0px 90px 0px 0px;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*** NO ENDOSABLE ***</strong></TD>
	</TR>
	<TR>
	<TD colspan="3" align="right" style="padding: 0px 90px 0px 0px;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CADUCA A LOS 90 DIAS</strong></TD></TR>-->
	<tr>
	<td>
	<P CLASS="breakhere">
	</td>
	</tr>
	</TABLE>



	<!--<BR>
	<BR>
	<BR>
	<BR>
	<BR>
	<BR>
	<BR>-->
	<?php
	/*$conexion = conexion();
	$idbanco=$fila3['banco'];
	$consulta1 = "SELECT * FROM bancos WHERE codigo='".$idbanco."'";
	$resultado1 = query($consulta1, $conexion);
	$fila1 = fetch_array($resultado1);
	$nombreBanco=$fila1['descripcion'];
	$cuenta=$fila1['cuenta'];
	$numero=$fila3['cheque'];
	$fechaemi=$fila3['fecha'];
	$concepto=$fila3['concepto'];
	list($anio,$mes,$dia)=explode("-",$odpfecha);
   	$fecha= $dia."/".$mes."/".$anio; 
	list($anio,$mes,$dia)=explode("-",$fechaemi);
   	$fecha2= $dia."/".$mes."/".$anio; */
	?>
	<!--<TABLE width="700" align="center" border=0>
	<TR>
	<TD align="right" colspan="3" style="padding: 120px 0px 0px 0px;"> &nbsp;</TD>
	</TR>
	<TR>
	<TD align="left" colspan="3" style="padding: 0px 0px 0px 25px;">  <?php echo $nombreContribuyente;?></TD>
	</TR>
	<TR>
	<TD align="center" colspan="3" style="padding: 0px 0px 0px 0px;">  <?php echo $fila3['cedula'];?></TD>
	</TR>
	<TR>
	<TD align="left" colspan="3" style="padding: 0px 0px 0px 25px;">  <?php echo strtoupper($fila3['concepto']);?></TD>
		
	</TR>
	<TR>
	<TD align="left"  style="padding: 0px 0px 0px 25px;">  <?php if ($odpfecha!= NULL ||$odpfecha!='') {echo $fecha;}else{echo 'NPOP';};?></TD>
	<TD align="left"  style="padding: 0px 0px 0px 25px;">  <?php echo $odp;?></TD>
	<TD align="left"  style="padding: 0px 0px 0px 25px;">  <?php echo $fecha2;?></TD>
		
	</TR>
	<TR>
	<TD align="left"  style="padding: 0px 0px 0px 25px;">  <?php echo $numero;?></TD>
	<TD align="left"  style="padding: 0px 0px 0px 25px;">  <?php echo $nombreBanco;?></TD>
	<TD align="left"  style="padding: 0px 0px 0px 25px;">  <?php echo $cuenta;?></TD>
	</TR>
	</TABLE>-->

<?php
}
?>
</div>
</BODY>
</HTML>