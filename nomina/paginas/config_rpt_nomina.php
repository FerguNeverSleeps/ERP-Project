<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>

<?php
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function VerRecibo()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('rpt_reporte_de_nomina.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}

function VerRecibo_csb()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_reporte_de_nomina_csbpdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function VerRecibo_csb_aumentado()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/recibos_lote_csbpdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function VerRecibo_csb_aumentado3()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/recibos_lote_csb2pdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}

function VerRecibo_conceptos()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_reporte_de_nomina_2pdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function utilidades()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_reporte_de_nomina_utilidadespdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}

function ver_recibos_pago()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('recibos_por_lote.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}

function ver_recibos_pago_csb()
{
	//alert(document.form1.cboTipoNomina.value);
	
	AbrirVentana('../fpdf/recibos_por_lote_csbpdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value+'&dep='+document.form1.seleccion_departamento.value,660,800,0);
}

	var HOST_REPORTES = "http://<?php echo $_SESSION['conf_reportes_host'];?>";
    var PUERTO_REPORTES = "<?php echo $_SESSION['conf_reportes_puerto']; ?>";
    var APP_REPORTES = "<?php echo $_SESSION['conf_reportes_app']; ?>";

function conformidad_csb(op)
{
	//alert(document.form1.cboTipoNomina.value);
	if(op==1){
	AbrirVentana('../fpdf/relacion_conformidad_csbpdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
	}else{
			AbrirVentana(HOST_REPORTES+':'+PUERTO_REPORTES+'/'+APP_REPORTES+'/index.jsp?db_connect=<?echo $_SESSION['bd'];?>&nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value+'&tipo_reporte=conformidad_acree',660,800,0);
	}
}
function resumen_conceptos()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/resumen_conceptospdf.php?nomina='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}

function listado_cargos()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('reporte_cargos.php?nomina_id='+document.form1.cboTipoNomina.value,660,800,0);
}
function listado_habitacional()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('reporte_politica_habitacional.php?nomina='+document.form1.cboTipoNomina.value,660,800,0);
}
function listado_habitacional2()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('reporte_politica_habitacional_ire.php?nomina='+document.form1.cboTipoNomina.value,660,850,0);
}
function aportes_patronales_emp_obr()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('reporte_aportes_pat_emp_obr_ire.php?nomina='+document.form1.cboTipoNomina.value,660,850,0);
}
function listado_basico()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('reporte_basico.php?nomina='+document.form1.cboTipoNomina.value,660,800,0);
}

function deposito_obr_csb()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/reporte_relacion_deposito_csbpdf.php?nomina='+document.form1.cboTipoNomina.value,660,800,0);
}
function banco()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/reporte_relacion_cuentabanco.php?nomina='+document.form1.cboTipoNomina.value,660,800,0);
}function efectivo()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/reporte_relacion_efectivo.php?nomina='+document.form1.cboTipoNomina.value,660,800,0);
}
function analisis_conceptos()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('reporte_analisis_concepto.php?nomina='+document.form1.cboTipoNomina.value+'&concepto='+document.form1.seleccion_concepto.value,660,800,0);
}
function listado_cheque()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/reporte_personal_cheque.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}


function AbrirListPersonal()
{
AbrirVentana('list_personal.php',660,800,0);
}

function analisisConsolidado()
{
	if(( $('select#concepto1').val()==null)||($('select#nom').val()==null)||($('select#frec').val()==null)||( $('#mesano').val()=='undefined'))
	{
		alert("POR FAVOR NO DEJE CAMPOS SIN SELECCIONAR!!!");
		return false;
	}
	$('#concepto').val($('select#concepto1').val());
	$('#nomina').val($('select#nom').val());
	$('#frecuencia').val($('select#frec').val());
	document.form1.action="../fpdf/reporte_analisis_consolidado.php";
	document.form1.submit();
}

function analisisConsolidadoxls()
{
	if(( $('select#concepto1').val()==null)||($('select#nom').val()==null)||($('select#frec').val()==null)||( $('#mesano').val()=='undefined'))
	{
		alert("POR FAVOR NO DEJE CAMPOS SIN SELECCIONAR!!!");
		return false;
	}
	$('#concepto').val($('select#concepto1').val());
	$('#nomina').val($('select#nom').val());
	$('#frecuencia').val($('select#frec').val());
	document.form1.action="reporte_analisis_consolidadoxls.php";
	document.form1.submit();
}

function VerRecibo_adp()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_reporte_de_nomina_recapitulacionpdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function Ver_listado_planilla_sanmiguelito()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_listado_planilla_sanmiguelitopdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function Ver_recapitulacion_planilla_sanmiguelito()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_recapitulacion_planilla_sanmiguelitopdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function Ver_transferencias_planilla_sanmiguelito()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_transferencias_planilla_sanmiguelitopdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function Ver_siacap_aportes_planilla_sanmiguelito()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/rpt_siacap_sanmiguelitopdf.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function listado_alfabetico_planilla_sanmiguelito()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('cartas_trabajo/listado_alfabetico_sanmiguelito.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}
function recapitulacion_adp()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('../fpdf/recapitulacion_adppdf.php?nomina='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}

function prestamos()
{
	//alert(document.form1.cboTipoNomina.value);
	AbrirVentana('reporte_analisis_prestamos.php?nomina='+document.form1.cboTipoNomina.value+'&concepto='+document.form1.seleccion_concepto.value,660,800,0);
}

</script>

<form id="form1" name="form1" method="post" action="">
<table width="807" height="229" border="0" class="row-br">
<tr>
      <td height="31" class="row-br"><table width="789" border="0">
          <tr>
            <td width="762"><div align="left"><font color="#000066"><strong>Parametros del Reporte</strong></font></div></td>
            <td width="17"><div align="center">
              <?php btn('back','submenu_reportes.php?modulo=45?modulo={$modulo}')  ?>
            </div></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td width="489" height="190" class="ewTableAltRow"><table width="520" border="0">
        <tr>
          <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left"><? echo $termino?>:<font size="2" face="Arial, Helvetica, sans-serif">

<? /*$consulta="select codtip from nomtipos_nomina where descrip='".$_SESSION['nomina']."'";
$resultado=sql_ejecutar($consulta);
$fila=mysqli_fetch_array($resultado);*/
?>



            <select name="cboTipoNomina" id="select2" style="width:400px">
<option>Seleccione una <?echo $termino?></option>
              <?php

	 	$query="select codnom,descrip,codtip from nom_nominas_pago where codtip='".$_SESSION['codigo_nomina']."'";
		$result=sql_ejecutar($query);
	 	  //ciclo para mostrar los datos
  		while ($row = fetch_array($result))
  		{ 		
		// Opcion de modificar, se selecciona la situacion del registro a modifica.		
  		   $codt= $row['codtip'];
			?>
              <option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip'];?></option>
		
              <?
		}//fin del ciclo while
		?>		
            </select>
	<input type="hidden" name="codt" id="codt" value="<? echo $codt; ?>" >
          </font></div></td>
          </tr>
	<?
	$valor=$_GET['opcion']; 
	
	switch($valor){
		case "analisis":
	
			$consulta="select * from nomconceptos";
			$resultado=sql_ejecutar($consulta);
			
			
				?>
			
			<tr><td colspan="4">Concepto:&nbsp;&nbsp;<select name="seleccion_concepto" id="seleccion_concepto">
			<option>Seleccione un Concepto</option>
			<?
			while($fila=fetch_array($resultado)){
			
			?>
				<option value="<? echo $fila['codcon']?>"><? echo $fila['descrip']?></option>
			
			<?}?>
			</select></td></tr>
			
			<?
		break;
		case "prestamos":
	
			$consulta="select * from nomconceptos";
			$resultado=sql_ejecutar($consulta);
			
			
				?>
			
			<tr><td colspan="4">Concepto:&nbsp;&nbsp;<select name="seleccion_concepto" id="seleccion_concepto">
			<option>Seleccione un Concepto</option>
			<?
			while($fila=fetch_array($resultado)){
			
			?>
				<option value="<? echo $fila['codcon']?>"><? echo $fila['descrip']?></option>
			
			<?}?>
			</select></td></tr>
			
			<?
		break;

		case "analisisConsolidado":
			$consulta="select * from nomconceptos";
			$resultado=sql_ejecutar($consulta);
			?>
			<tr><td colspan="10">Concepto Prestamo:&nbsp;&nbsp;
			<input name="concepto" type="hidden"  id="concepto"  value="" >
			<input name="nomina" type="hidden"  id="nomina"  value="" >
			<input name="frecuencia" type="hidden"  id="frecuencia"  value="" >
			<select size="13" multiple="multiple" name="concepto1" id="concepto1">
	<!-- 		<option>Seleccione un Concepto</option> -->
			<?php
			while($fila=fetch_array($resultado))
			{
				?>
				<option value="<?php echo $fila['codcon']?>"><?php echo $fila['codcon']. ' -- '.$fila['descrip']?></option>
				<?php
			}
			?>
			</select></td>
			</tr>
			<tr>
			<td colspan="4">Tipo de Nomina:&nbsp;&nbsp;<select size="10" multiple="multiple" name="nom" id="nom">
			<?php
			$consulta="select * from nomtipos_nomina";
			$resultado2=sql_ejecutar($consulta);
			while($fila=fetch_array($resultado2))
			{
				?>
				<option  value="<?php echo $fila['codtip'];?>"><?php echo $fila['descrip'];?></option>
				<?php
			}
			?>
			</select>
			</td>
			
			<td colspan="4">
			Frecuencia: 
			<select size="10" name="frec" multiple="multiple" id="frec">
			<?php
			$consulta="select * from nomfrecuencias";
			$resultado=sql_ejecutar($consulta);
			while($fila=fetch_array($resultado))
			{
				?>
				<option value="<?php echo $fila['codfre']?>"><?php echo $fila['descrip']?></option>
				<?php
			}
			?>
			</select>
			</td>
			
			<td colspan="4">
			Mes y A&ntilde;o: 
			<input name="mesano" type="text"  id="mesano" style="width:100px" value="<?php echo date("m/Y") ?>" maxlength="60" >
			<input name="image2" type="image" id="mesano2" src="../lib/jscalendar/cal.gif" />
			<script type="text/javascript">Calendar.setup({inputField:"mesano",ifFormat:"%m/%Y",button:"mesano2"});</script>
			</td></tr>
			<?
		break;
	
	}
        if($valor=='recibos_csb' OR $valor=='recibos_csb2' OR $valor=='recibos_csb3'){
	?>
	<tr><td colspan="4">Departamento
	<select name="seleccion_departamento" id="seleccion_departamento">
	<option values='Todos' >Todos</option>
	<?
	$consulta_departamento="select * from nomnivel3";
	$query=sql_ejecutar($consulta_departamento);
	while($fila_dep=fetch_array($query)){
	
	?>
		<option value="<? echo $fila_dep['codorg']?>"><? echo $fila_dep['descrip']?></option>
	
	<?}?>
		</select></td></tr>

	<?
	}?>
        
        
        
      </table>
        <p>&nbsp;</p>
        <table width="467" border="0">
          <tr>
            <td width="466"><div align="right">
              <?php 
				

				switch($valor){
					case "resumen_conceptos":
						btn('ok','resumen_conceptos();',2);
						break;
					case "habitacional":
						btn('ok','listado_habitacional();',2);
						break;
					case "habitacional2":
						btn('ok','listado_habitacional2();',2);
						break;
					case "basico":
						btn('ok','listado_basico();',2);
						break;
					case "relacion_deposito_obr_csb":
						btn('ok','deposito_obr_csb();',2);
						break;
					case "banco":
						btn('ok','banco();',2);
						break;
					case "efectivo":
						btn('ok','efectivo();',2);
						break;												
					case "recibos":
						btn('ok','ver_recibos_pago();',2);
						break;
					case "recibos_csb":
						btn('ok','ver_recibos_pago_csb();',2);
						break;
					case "recibos_csb2":
						btn('ok','VerRecibo_csb_aumentado();',2);
						break;
					case "recibos_csb3":
						btn('ok','VerRecibo_csb_aumentado3();',2);
						break;
					case "general":
						btn('ok','VerRecibo();',2);
						break;
					case "general_csb":
						btn('ok','VerRecibo_csb();',2);
						break;
					case "general_conceptos":
						btn('ok','VerRecibo_conceptos();',2);
						break;
					case "cargos":
						btn('ok','listado_cargos();',2);
						break;
					case "analisis":
						btn('ok','analisis_conceptos();',2);
						break;
					case "patronalQ":
						btn('ok','aportes_patronales_emp_obr();',2);
						break;
					case "relacion_conformidad_csb":
						btn('ok','conformidad_csb(1);',2);
						break;
					case "relacion_conformidad_acree":
						btn('ok','conformidad_csb(2);',2);
						break;
					case "reporte_utilidades":
						btn('ok','utilidades();',2);
						break;
					case "cheque":
						btn('ok','listado_cheque();',2);
						break;
					case "analisisConsolidado":
						btn('ok','analisisConsolidado();',2);
						echo "<br>";
						btn("xls",'analisisConsolidadoxls();',2);
						break;
					case "general_adp":
						btn('ok','VerRecibo_adp();',2);
						break;
					case "listado_planilla_sanmiguelito":
						btn('ok','Ver_listado_planilla_sanmiguelito();',2);
						break;
					case "recapitulacion_planilla_sanmiguelito":
						btn('ok','Ver_recapitulacion_planilla_sanmiguelito();',2);
						break;
					case "transferencias_planilla_sanmiguelito":
						btn('ok','Ver_transferencias_planilla_sanmiguelito();',2);
						break;
					case "siacap_aportes_mensuales_planilla_sanmiguelito":
						btn('ok','Ver_siacap_aportes_planilla_sanmiguelito();',2);
						break;
					case "listado_alfabetico_sanmiguelito":
						btn('ok','listado_alfabetico_planilla_sanmiguelito();',2);
						break;
					case "recapitulacion_adp":
						btn('ok','recapitulacion_adp();',2);
						break;
					case "prestamos":
						btn('ok','prestamos();',2);
						break;		
				}  
				?>
            </div></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
</body>
</html>
