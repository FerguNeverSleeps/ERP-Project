<?php 
session_start();
ob_start();
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<?php
?>
<?php
//codigo para pagina;
$TAMANO_PAGINA = 20;
$pagina = $_GET["pagina"];
if (!$pagina) {
    $inicio = 1;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * $TAMANO_PAGINA+1;
} 
$limit=$inicio-1;

include("../lib/common.php");
include("../lib/pdfcommon.php");
include ("../header.php");
include ("func_bd.php") ;

$opt1=$_POST['opt1'];
$opt2=$_POST['opt2'];
$opt3=$_POST['opt3'];
$identificador=$_POST['identificacion'];
?>



<script>
function AbrirConstancia(id,nom,est,opt,opt2)
{
	if(opt=='1'){
		location.href='../fpdf/rpt_constancia_personalpdf.php?registro_id='+id+'&tipN='+nom+'&est='+est
	}else{
		location.href='../fpdf/rpt_constancia_personalpdf.php?registro_id='+id+'&tipN='+nom+'&est='+est+'&opt='+opt+'&iden='+opt2
	}
/*AbrirVentana('rpt_constancia_personalpdf.php?registro_id='+id,660,800,0);*/

}

function AbrirConstanciaOtra(id,nom,est,opt,opt2,tipo)
{
	if(tipo=='1'){
		location.href='../fpdf/rpt_constancia_panamapdf.php?registro_id='+id+'&tipN='+nom+'&est='+est
	}else if (tipo=='2'){
		location.href='../fpdf/rpt_constancia_cert_panamapdf.php?registro_id='+id+'&tipN='+nom+'&est='+est+'&opt='+opt+'&iden='+opt2
	}else{
		location.href='../fpdf/rpt_constancia_trab_panamapdf.php?registro_id='+id+'&tipN='+nom+'&est='+est+'&opt='+opt+'&iden='+opt2	
	}


}

function AbrirAntecedente(id,nom)
{
location.href='../fpdf/rpt_antecedente_personalpdf.php?registro_id='+id+'&tipN='+nom

}
function Abrirpension(id,nom)
{
location.href='../fpdf/rpt_pension_personalpdf.php?registro_id='+id+'&tipN='+nom

}
function Abrirjubilado(id,nom)
{
location.href='../fpdf/rpt_jubilado_personalpdf.php?registro_id='+id+'&tipN='+nom

}
function enviar(op,id){

	if (op==1){		// Opcion de Agregar
		//document.frmAgregar.registro_id.value=id;
		document.frmAgregar.op.value=op;
		document.frmAgregar.action="rpt_constancia.php";
		document.frmAgregar.submit();	
	}
	if (op==2){	 	// Opcion de Modificar
		//alert($op);		
		document.frmIntegrantes.registro_id.value=id;		
		document.frmIntegrantes.op.value=op;
		document.frmIntegrantes.action="ag_maestro_integrantes.php";
		document.frmIntegrantes.submit();		
	}
	if (op==3){		// Opcion de Eliminar
		if (confirm("Esta seguro que desea eliminar el registro ?"))
		{					
			document.frmIntegrantes.registro_id.value=id;
			document.frmIntegrantes.op.value=op;
  			document.frmIntegrantes.submit();
		}		
	}
}
</script>
  <?php 

?>
<p>
  <font size="2" face="Arial, Helvetica, sans-serif">
  <?php 

$criterio=$_POST['optOpcion'];
$cadena=$_POST['textfield'];

$registro_id=$_POST['registro_id'];	
$op=$_POST['op'];


if ($op==3) //Se presiono el boton de Eliminar
	{	
	
	$query="delete from nompersonal where cedula=$registro_id";	
		
	$result=sql_ejecutar($query);
	activar_pagina("maestro_personal.php");		 
	}     
elseif ($cadena <> "") 		// Condicion para filtrado
	{   
		// para obtener la cantidad de registros
		$strsql="select COUNT(*) from nomvis_integrantes";		
		$strsql=filtrado($criterio,$cadena,$strsql,"cedula","nombres","apellidos","ficha","descrip",
		'','','','','',"'".$_SESSION[nomina]."'",'descrip');		
		
		//msgbox($strsql);
		
		$result =sql_ejecutar($strsql);			
		$fila = mysqli_fetch_array($result);	
		$num_total_registros = $fila[0];
		$strsql="select * from nomvis_integrantes ";
		$strsql=filtrado($criterio,$cadena,$strsql,"cedula","nombres","apellidos","ficha","descrip",
		'','','','','',"'".$_SESSION[nomina]."'",'descrip');

		$strsql= "$strsql LIMIT $TAMANO_PAGINA OFFSET $limit";			
		$result =sql_ejecutar($strsql);			
		 // para paginacion		 
    	$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
	
     }
else					// No se filtra y se muestran todos los datos
{
	
	
	$strsql= "select COUNT(*) from nomvis_integrantes where descrip='".$_SESSION['nomina']."'";
	$result =sql_ejecutar($strsql);	
	$fila = mysqli_fetch_array($result);	
	$num_total_registros = $fila[0];
	
	$strsql= "select * from nomvis_integrantes where descrip='".$_SESSION['nomina']."' order by apellidos,nombres LIMIT $TAMANO_PAGINA  OFFSET $limit";
	
	$result =sql_ejecutar($strsql);		
	
    $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
		
}
?>
</font></p>
<form name="frmIntegrantes" method="post" action="">
  <table width="100%" class="tb-tit">
    <tr>
      <td class="row-br"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="96%">
			<strong>
			<font color="#000066">
			&nbsp;Personal			</font>			</strong>			</td>
            <td width="2%" align="right"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td>
				    <div align="right">
				      <?php btn('cancel','window.close();',2) ?>
				    </div></td>
                </tr>
            </table></td>
          </tr>
      </table></td>
    </tr>
</table>
  <table width="100%" border="0" class="ewTableHeader">
    <tr class="">
      <td width="162"><font size="2" face="Arial, Helvetica, sans-serif" class="ewTableHeader">
        <input name="textfield" type="text" class="boton-text" style="width:150px" size="20"
		value="<?php if (isset($_POST[textfield])){echo $_POST[textfield];}?>">
      </font></td>
      <td width="17"><font size="2" face="Arial, Helvetica, sans-serif" class="ewTableHeader">
        <?php btn('search','frmIntegrantes',1) ?>
      </font></td>
      <td width="17"><font size="2" face="Arial, Helvetica, sans-serif" class="ewTableHeader">
        <?php btn('show_all','list_personal.php?cmd=reset') ?>
      </font></td>
      <td width="579"><font size="2" face="Arial, Helvetica, sans-serif" class="ewBasicSearch">
        <label>        </label>
      </font><font size="2" face="Arial, Helvetica, sans-serif">
<label>
            <input name="optOpcion" type="radio" id="Sea Igual a"  value="Sea Igual a"
			<?php if ($criterio=='Sea Igual a'){?> checked="checked"<?php }?>>
            Frase exacta&nbsp;</label>
<label>
<input name="optOpcion" type="radio" id="Contenga"  value="Contenga"   checked="true"
			 <?php if ($criterio=='Contenga'){?> checked="checked"<?php }?>>
        Cualquier palabra</label>
      </font></td>
    </tr>
</table>
  <table width="100%" border="0" bordercolor="#0066FF" bgcolor="#FFFFFF" class="ewTable" id="lst"  cellspacing="0" cellpadding="0">
    
	
    <tr bgcolor="#CCCCCC" class="ewTableHeader"> 
    	<td width="9%" height="21" align="right" class="phpmakerlist"> 
			<div align="left" class="tb-head">
	  <font size="2" face="Arial, Helvetica, sans-serif"> Ced&uacute;la  </font></div>	  </td>
		
      <td width="11%" class="phpmakerlist">
	  	<div align="left">
	  		<font size="2" face="Arial, Helvetica, sans-serif">
	  Ficha	  			  </font></div>	  </td>
	  
      <td width="40%" class="phpmakerlist">
	  	<div align="left">
	  		<font size="2" face="Arial, Helvetica, sans-serif">
	  Nombres y Apellidos	  			  </font></div>	  </td>
	  
      <td width="10%" class="phpmakerlist">
	  	<div align="left">
	  <font size="2" face="Arial, Helvetica, sans-serif"> Situaci&oacute;n  </font></div>	  </td>
	  
      <td width="24%" class="phpmakerlist"><div align="center">
        <div align="left" class="phpmakerlist"><font size="2" face="Arial, Helvetica, sans-serif">Tipo de Nomina </font></div>
      </div></td>
      <td width="3%" class="phpmakerlist">&nbsp;</td>
	<?php if ( ($_SESSION['codigo_nomina']!=5)&&($_SESSION['codigo_nomina']!=6)){ ?>
	  <td width="3%" class="phpmakerlist">SI</td>
      <td width="3%" class="phpmakerlist">CT</td>
	  <td width="3%" class="phpmakerlist">RE</td>
	  <td width="3%" class="phpmakerlist">GE</td>
	  <td width="3%" class="phpmakerlist">CARTA TRABAJO</td>
	<?php } ?>
    </tr>
	<?php
	//$sItemRowClass = " class=\"ewTableRow\"";
	//$sListTrJs = " onmouseover='ew_mouseover(this);' onmouseout='ew_mouseout(this);'";
	?>
    
	
      <?php 
  	//operaciones para paginaciones
  	$num_fila = 0;
  	$in=1+(($pagina-1)*5);

  	//ciclo para mostrar los datos 
  	while ($fila = mysqli_fetch_array($result))
  	{ 
  	?>
		<tr> 
      <td height="20" bgcolor="#DFDFDF" class="ewTableRow"> <div align="left" class="row-even"><font size="2" face="Arial, Helvetica, sans-serif">	
          <?php 
		  echo $fila[cedula]; 	// cedula de identidad
		  ?>
      </font></div></td>
      <td bgcolor="#DFDFDF"  class="ewTableRow"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">
        <?php 
		  echo $fila[ficha]; 	// ficha del integrante
		  ?>
      </font></div></td>
      <td bgcolor="#DFDFDF" class="ewTableRow"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
          
          <?php 
	  		echo $fila[apellidos].', '.$fila[nombres];  	// apellidos y nombres
	  	?>
      </font></div></td>
      <td bgcolor="#DFDFDF" class="ewTableRow"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">  
          <?php 
	  	echo $fila[estado];   	// 
	  	?>
      </font></div></td>
      <td bgcolor="#DFDFDF" class="ewTableRow"><div align="left" class="row-even"><font size="2" face="Arial, Helvetica, sans-serif">
        <?php 
	  	echo $fila[descrip];   	
	  	?>
      </font></div></td>
      <td bgcolor="#DFDFDF" class="ewTableRow"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
	
	<a href="
	<?php if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']<=4)){ ?>
		javascript:AbrirConstancia('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>','NO','<?php echo $opt2; ?>','<?php echo $identificador;?>');
	 <?php } if ($fila[estado]=='Egresado') { ?> 
		javascript:AbrirAntecedente('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>'); 
	<?php }if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']==6||($fila[estado]=='Pensionado'))){?>
		javascript:Abrirpension('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>'); 
	<?php }	if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']==5||($fila[estado]=='Jubilado'))){?>
		javascript:Abrirjubilado('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>'); 
	<?php } ?>
	"><img src="img_sis/ico_list.gif" alt="Ver Constancia" width="15" height="15" border="0" align="absmiddle"></a>
        <label></label>
      </font></div></td>
	  <td bgcolor="#DFDFDF" class="ewTableRow"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
	  <a href="
		<?php if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']<=4)){ ?>
		javascript:AbrirConstancia('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>','IN','<?php echo $opt2; ?>','<?php echo $identificador;?>');
		<?php } ?>
		"><img src="img_sis/ico_list.gif" alt="Ver Constancia" width="15" height="15" border="0" align="absmiddle"></a>
        <label></label>
	  </font></div></td>
	<?php if (($_SESSION['codigo_nomina']!=5) &&($_SESSION['codigo_nomina']!=6) && ($fila[estado]!='Jubilado') && ($fila[estado]!='Pensionado')){ ?>
		<td bgcolor="#DFDFDF" class="ewTableRow"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
		<?php if ($fila[estado]!='Egresado'){?>	
		<a href="
		<?php if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']<=4)){ ?>
			javascript:AbrirConstancia('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>','SI','<?php echo $opt2; ?>','<?php echo $identificador;?>');
		<?php }  ?>
		"><img src="img_sis/ico_list.gif" alt="Ver Constancia" width="15" height="15" border="0" align="absmiddle"></a> <?php } ?>
		<label></label>
	</font></div></td>
	<?php }  ?>
	<?php if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']<=4)){ ?>
	<td bgcolor="#DFDFDF" class="ewTableRow"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
	  <a href="
		
		javascript:AbrirConstanciaOtra('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>','IN','<?php echo $opt2; ?>','<?php echo $identificador;?>',1);
		
		"><img src="img_sis/ico_list.gif" alt="Ver Constancia" width="15" height="15" border="0" align="absmiddle"></a>
        <label></label>
	  </font></div></td>
	  <?php }else{echo "<td></td>";} ?>
	  
		<?php if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']<=4)){ ?>
		<td bgcolor="#DFDFDF" class="ewTableRow"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
	  <a href="
		
		javascript:AbrirConstanciaOtra('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>','IN','<?php echo $opt2; ?>','<?php echo $identificador;?>',2);
		
		"><img src="img_sis/ico_list.gif" alt="Ver Constancia" width="15" height="15" border="0" align="absmiddle"></a>
			
        <label></label>
	  </font></div></td>
	  <?php }else{echo "<td></td>";} ?>
	  <?php if (($fila[estado]!='Egresado') && ($_SESSION['codigo_nomina']<=4)){ ?>
		<td bgcolor="#DFDFDF" class="ewTableRow"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
	  <a href="
		
		javascript:AbrirConstanciaOtra('<?php echo $fila[ficha]; ?>','<?php echo $_SESSION['codigo_nomina'];?>','IN','<?php echo $opt2; ?>','<?php echo $identificador;?>',3);
		
		"><img src="img_sis/ico_list.gif" alt="Ver Constancia" width="15" height="15" border="0" align="absmiddle"></a>
			
        <label></label>
	  </font></div></td>
	  <?php }else{echo "<td></td>";} ?>
    </tr>
    <?php   
  	}//fin del ciclo while
  	//operaciones de paginacion
	$num_fila++;
  	$in++;  
  	?>
    <input name="registro_id" type="hidden" value="">
    <input name="op" type="hidden" value="">	
</table>
  <table width="100%" height="31" class="tb-footer">
    <tr>
      <td width="61%"><font size="2" face="Arial, Helvetica, sans-serif"><span class="Estilo1">
        <?php
	if ($num_total_registros > 0) 
	{
	$rsEof = ($num_total_registros < ($inicio + $TAMANO_PAGINA));
	$PrevStart = $inicio - $TAMANO_PAGINA;	
	if ($PrevStart < 1) 
	{ 
	$PrevStart = 1; 
	}
	$NextStart = $inicio + $TAMANO_PAGINA;
	
	if ($NextStart > $num_total_registros) 
	{$NextStart = $inicio ; }	
	$LastStart = intval(($num_total_registros-1)/$TAMANO_PAGINA+1);
	?>
      </span></font>
        <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><span class="phpmaker">P&aacute;gina&nbsp;</span></td>
          <!--first page button-->
          <?php if ($inicio == 1) { ?>
          <td><img src="images/firstdisab.gif" alt="Primera" width="16" height="16" border="0"></td>
          <?php } else { ?>
          <td><a href="list_personal.php?pagina=1&criterio=<?php $criterio;?>"><img src="images/first.gif" alt="Primera" width="16" height="16" border="0"></a></td>
          <?php } ?>
          <!--previous page button-->
          <?php if ($PrevStart == $inicio) { ?>
          <td><img src="images/prevdisab.gif" alt="Anterior" width="16" height="16" border="0"></td>
          <?php } else { ?>
          <td><a href="list_personal.php?pagina=<?php echo $pagina-1; ?>&criterio=<?php $criterio;?>"><img src="images/prev.gif" alt="Anterior" width="16" height="16" border="0"></a></td>
          <?php } ?>
          <!--current page number-->
          <td><input type="text" name="pageno" value="<?php echo intval(($inicio-1)/$TAMANO_PAGINA+1); ?>" size="4"></td>
          <!--next page button-->
          <?php if ($NextStart == $inicio) { ?>
          <td><img src="images/nextdisab.gif" alt="Siguiente" width="16" height="16" border="0"></td>
          <?php } else { ?>
          <td><a href="list_personal.php?pagina=<?php echo $pagina+1; ?>&criterio=<?php $criterio;?>"><img src="images/next.gif" alt="Siguiente" width="16" height="16" border="0"></a></td>
          <?php  } ?>
          <!--last page button-->
          <?php if ($NextStart == $inicio) { ?>
          <td><img src="images/lastdisab.gif" alt="Ultima" width="16" height="16" border="0"></td>
          <?php } else {?>
          <td><a href="list_personal.php?pagina=<?php  echo $LastStart; ?>&criterio=<?php $criterio;?>
		  "><img src="images/last.gif" alt="Ultima" width="16" height="16" border="0"></a></td>
          <?php } ?>
          <td><span class="phpmaker">&nbsp;de <?php echo intval(($num_total_registros-1)/$TAMANO_PAGINA+1);?></span></td>
        </tr>
      </table>
      </td>
      <td width="39%"><?php if ($inicio > $num_total_registros) { $inicio = $num_total_registros; }
	$nStopRec = $inicio + $TAMANO_PAGINA - 1;
	$nRecCount = $num_total_registros - 1;
	if ($rsEof) { $nRecCount = $num_total_registros; }
	if ($nStopRec > $nRecCount) { $nStopRec = $nRecCount; } ?>
Registro <?php echo $inicio; ?> a <?php echo $nStopRec; ?> de <?php echo $num_total_registros; ?> </td>
    </tr>
  </table>
  	  <?php
	  }
	 ?> 

  <p align="center">&nbsp;</p>

  <p align="left"><font size="2" face="Arial, Helvetica, sans-serif"></a></font></p>
  
  <font size="2" face="Arial, Helvetica, sans-serif">
  
  </font>
</form>
<p>&nbsp;</p>
</body>
</html>
