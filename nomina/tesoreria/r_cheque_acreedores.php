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

$consulta3="select * from nomcheques where codnom='".$nomina."' and tipo='2'";
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

	?>
	<TABLE width="700" align="center" border=0>
	<TR>
	<TD align="right" colspan="3"><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format($monto,2,',','.'); ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	</TR>
	
	<TR>
	<TD align="left" width="400" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><?php echo $nombreContribuyente;?></strong></TD>
	</tr>
	<tr>
	<?php $calculo=strlen($montoLetras)/70;
	if($calculo>=0 && $calculo<=1){ ?>
	<TD align="left"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo strtoupper($montoLetras);?></strong></TD>
	<?php }else{ ?>
	<TD align="left"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo strtoupper($montoLetras);?></strong></TD>
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