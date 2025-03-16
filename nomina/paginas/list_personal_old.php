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
$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : false;
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
?>


<script>
function AbrirConstancia(ficha, nomina, carta_id, _this)
{
  if(carta_id != '')
  {
    var sel      = document.getElementById(_this.id);
    var selected = sel.options[sel.selectedIndex];
    var tipo     = selected.getAttribute('data-id');
    var archivo  = selected.getAttribute('data-arc');  

    if(tipo=='pdf')
    {
      // location.href='../tcpdf/pdf_constancia.php?registro_id='+ficha+'&tipn='+nomina+'&const_id='+carta_id;
      location.href='cartas_trabajo/carta_pdf.php?ficha='+ficha+'&tipnom='+nomina+'&carta_id='+carta_id;
    }
    else if(tipo=='docx')
      location.href='cartas_trabajo/'+archivo+'?ficha='+ficha+'&tipnom='+nomina;
  }
}
function AbrirContrato(tipo_contrato, ficha, planilla){

  console.log("Tipo de contrato: " + tipo_contrato + " Ficha: " + ficha + " Planilla: " + planilla);

  if(tipo_contrato=='Indefinido')
    location.href='cartas_trabajo/contrato_indefinido.php?ficha='+ficha+'&tipnom='+planilla;
  else
  {
    if(tipo_contrato=='Definido')
      location.href='cartas_trabajo/contrato_definido.php?ficha='+ficha+'&tipnom='+planilla;
  }

  //location.href='contrato_doc.php?registro_id='+id+'&tipn='+nom+'&est='+est;
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
	
	$result =sql_ejecutar_utf8($strsql);
	
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
      <td width="3%" class="phpmakerlist">Constancia</td>
      <td width="3%" class="phpmakerlist">Contrato</td>
    </tr>
	<?php
	//$sItemRowClass = " class=\"ewTableRow\"";
	//$sListTrJs = " onmouseover='ew_mouseover(this);' onmouseout='ew_mouseout(this);'";
	?>
    <tr> 
	
      <?php 
  	//operaciones para paginaciones
  	$num_fila = 0;
  	$in=1+(($pagina-1)*5);
    $i=1;

  	//ciclo para mostrar los datos 
  	while ($fila = mysqli_fetch_array($result))
  	{ 
  	?>
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
      <td bgcolor="#DFDFDF" class="ewTableRow">
		<!-- <img src="img_sis/ico_list.gif" alt="Ver Constancia" width="15" height="15" border="0" align="absmiddle"> -->
		<select id="tipo_constancia_<?php echo $i; ?>" name="tipo_constancia_<?php echo $i; ?>" 
            onchange="javascript:AbrirConstancia('<?php echo $fila['ficha']; ?>', '<?php echo $_SESSION['codigo_nomina'];?>', this.value, this);">
			<option value="">Seleccione</option>
		<?php
			$sql = 'SELECT codigo, nombre, tipo_documento, archivo FROM nomtipos_constancia';
			$res = sql_ejecutar($sql);

			while($row = mysqli_fetch_array($res)){?>
				<option value="<?php echo $row['codigo']; ?>" data-id="<?php echo $row['tipo_documento']; ?>" 
                data-arc="<?php echo $row['archivo']; ?>"><?php echo utf8_encode($row['nombre']); ?></option>
			<?php
			}
		?>
		</select>  
      </td>
      <td bgcolor="#DFDFDF" class="ewTableRow">
        <select id="tipo_contrato" name="tipo_contrato" onchange="javascript: AbrirContrato(this.value, '<?php echo $fila['ficha']; ?>', '<?php echo $_SESSION['codigo_nomina'];?>');">
          <option value="">Seleccione</option>
          <option value="Indefinido">Contrato Indefinido</option>
          <option value="Definido">Contrato Definido</option>
        </select>
      <!--
      <a href="javascript:AbrirContrato(this.value, '<?php echo $fila['ficha']; ?>', '<?php echo $_SESSION['codigo_nomina'];?>');" 
          title="Contrato"><img src="img, _sis/ico_list.gif" alt="Ver Contrato" width="15" height="15" border="0" align="absmiddle"></a>
      </td>
      -->
    </tr>
    <?php  
      $i++; 
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
