<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>


<?php
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>/*
function comprobante_contable(){
    //alert(document.form1.cboTipoNomina.value);
    AbrirVentana('../fpdf/comprobante_contable_pdf.php?nomina='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}*/
function redireccion(variable,codt)
{
    location.href='../fpdf/comprobante_contable_pdf.php?codt='+codt+variable; 

}
function comprobante_contable(){
    
   // var mes = document.form1.cboMes.value; // PK nom_nominas_pago
    var planillasmes = [];
    var anio = [];
    var mes = [];
    var codnom = [];
    var variable = "";
    var planillasmes = document.getElementsByName('planilla[]');
    var anio = document.getElementsByName('anio[]');
    var codnom = document.getElementsByName('codnom[]');
    var ln = planillasmes.length;
    codt = document.form1.codt.value;
    //alert(ln);
    var p=0;
        //Si solo hay una opción 
        
            //Se buscan las opciones seleccionadas
            for (i = 0; i < ln; i++)
            {
                if(planillasmes[i].checked)
                {
                    variable=variable+'&planillasmes[]='+planillasmes[i].value;
                    variable=variable+'&anio[]='+anio[i].value;
                    variable=variable+'&codnom[]='+codnom[i].value;

                    mes[p] = planillasmes[i].value;
                    p++;

                }
            }
            if(p==0)
            {
                alert("Debe seleccionar al menos una planilla");
            }
            if(p==1)
            {
                //alert(variable);
                //location.href='comprobante_contable_pdf1.php?codt='+codt+variable;
                redireccion(variable,codt);
            }
            if(p==2)
            {
                if(mes[0] != mes[1])
                {
                    alert("Debe seleccionar dos opciones del mismo mes");

                }
                else
                {
                    //alert(variable);
                    //location.href='comprobante_contable_pdf1.php?codt='+codt+variable;
                    redireccion(variable,codt); 

                }                
            }
            if(p>2)
            {
                alert("Debe seleccionar dos opciones del mismo mes");
                //location.href='comprobante_contable_pdf1.php?codt='+codt+variable; 
            }
        
    
    //AbrirVentana('../fpdf/comprobante_contable_pdf.php?mes='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);    
    
    
}

</script>

<form id="form1" name="form1" method="post" action="">
<table width="100%" height="229" border="0" class="row-br">
<tr>
<td height="31" class="row-br">
<table width="99%" border="0">
<tr>
<td width="97%"><div align="left"><font color="#000066"><strong>Parámetros del Reporte</strong></font></div></td>
<td width="3%"><div align="center">
<?php btn('back','submenu_reportes.php?modulo=45')  ?>
</div>
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td width="100%" height="190" class="ewTableAltRow">
<br>
<table width="100%" border="0">
	<tr>
		<td width="90%" height="40"  valign="middle" align="left"  style="padding-left: 40px">Seleccione planilla:
<!--			<select name="cboMes" id="cboMes" style="width:400px">
                            <option value="">Seleccione Mes</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="1">Mayo</option>
                            <option value="2">Junio</option>
                            <option value="3">Julio</option>
                            <option value="4">Agosto</option>
                            <option value="1">Septiembre</option>
                            <option value="2">Octubre</option>
                            <option value="3">Noviembre</option>
                            <option value="4">Diciembre</option>
			</select>-->
                    
                        <table width="100%" border="0" align="center">
                        <tr>
                        <br>
                        <?
                        $consulta="select * from nom_nominas_pago where status='C'";
                        $resultado=sql_ejecutar($consulta);
                        ?>

                        <?
                        $i=0;
                        while($fila=fetch_array($resultado))
                        {
/*/                        <input type="checkbox" name="planilla[]" id="planilla[]" value="<?php echo $fila[codnom];?>-<?php echo $fila[codtip];?>">*/
                            
                        ?>
                        <tr><td colspan="4" height="40">
                        <input type="checkbox" name="planilla[]" id="planilla[]" value="<?php echo $fila[mes];?>">
                        <input type="hidden" name="anio[]" id="anio[]" value="<?php echo  $fila[anio];?>">
                        <input type="hidden" name="codnom[]" id="codnom[]" value="<?php echo  $fila[codnom];?>">
                        &nbsp;&nbsp;&nbsp; <?php echo $fila[descrip]?>
                        </td></tr>
                        <?
                        $i++;		
                        }?>
                        </table>
                        <?php
				$query="SELECT codnom, descrip, codtip 
				        FROM   nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
						
				$result=sql_ejecutar($query);
				while ($row = fetch_array($result))
				{
					$codtip = $row['codtip']; // Tabla nomtipos_nomina
					// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
				
				}	
			?>
			<input type="hidden" name="codt" id="codt" value="<?php echo $codtip; ?>" >
		</td>
	</tr>
	

</table>
<p>&nbsp;</p>
<table width="467" border="0">
<tr>
<td width="466" align="right">
<?php
$valor=$_GET['opcion'];
switch($valor)
{
	default:
		btn('xls','comprobante_contable();',2);
		break;
                
                
}
?>
</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
</body>
</html>
