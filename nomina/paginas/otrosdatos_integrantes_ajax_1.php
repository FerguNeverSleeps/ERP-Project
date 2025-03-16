<?php 
session_start();
ob_start();
?>


<script language="JavaScript" type="text/javascript">
	function CerrarVentana(){
		javascript:window.close();
	}

	function Enviar(op)
	{
	document.frmPrincipal.opcion.value=op;
	document.frmPrincipal.submit();
	}
	function Enviar2(op,ficha)
	{
	document.frmPrincipal.opcion.value=op;
	document.frmPrincipal.txtficha.value=ficha;
	document.frmPrincipal.submit();
	}

</script>

<?php
include("../lib/common.php");
include("func_bd.php");
$opcion=$_POST[opcion];
$fichasss=$_GET['txtficha'];
$registro_id=$_GET['txtficha'];
$fichass=$_POST['txtficha'];
//msgbox($registro_id);
$host_ip = $_SERVER['REMOTE_ADDR'];

if ($opcion==1)
{
	$txtarray_2=$_POST['txtid'];
	foreach($_POST['txtCampo'] as $key => $value)
	{
		$valor_id=$txtarray_2[$key];			
		$strsql1="update nomcampos_adic_personal set valor = '$value' where ficha='$registro_id' and id=$valor_id and tiponom=$_SESSION[codigo_nomina]";
		$result1=sql_ejecutar($strsql1);
		$strsql1="";
	}
	$consulta="INSERT INTO log_transacciones VALUES ('', 'modificacion de campos adicionales', '".date("Y-m-d H:i:s")."', 'Personal-campos adicionales', 'otrosdatos_integrantes_ajax.php', 'editar', '$registro_id', '$_SESSION[nombre]', '".$host_ip."')";
	$result2=sql_ejecutar($consulta);
	
	?>
	<script>
	CerrarVentana();
	</script>
	<?PHP
}
elseif ($opcion==2)
{
	
	$url="DEMONIO_CAMPOS_ADICIONALES";
	echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	document.location.href=\"".$url.".php?ficha=".$fichass."\"
	</SCRIPT>";
	
}
include ("../header4.php");
?>
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<p>
   	<input type="hidden" name="txtficha" id="txtficha" value="">
	<input type="hidden" name="opcion" id="opcion" value="">
</p>
<div class="page-container">
        <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
        <div class="row">
          <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box blue">
              <div class="portlet-title">
              	<div class="caption">
              	<?php echo "Campos adicionales Ficha Nº".$registro_id; ?>
              	</div>
              	<div class="actions">
                <a class="btn btn-sm blue"  href="javascript:Enviar2(2,<?php echo $fichasss ?>);document.frmPrincipal.submit();">
                  <i class="fa fa-plus"></i>
                      Generar
                </a>
                <a class="btn btn-sm blue"  href="javascript:Enviar(1); document.frmPrincipal.submit();">
                  <i class="fa fa-plus"></i>
                      Actualizar
                </a>

              </div>
              </div>
              <div class="portlet-body">
			  		<?php 	
	                    $strsql= "select nomcampos_adic_personal.id,nomcampos_adic_personal.tipo,nomcampos_adicionales.descrip,nomcampos_adic_personal.valor from nomcampos_adic_personal inner join nomcampos_adicionales on nomcampos_adic_personal.id =  nomcampos_adicionales.id where nomcampos_adic_personal.ficha = '$registro_id' and nomcampos_adic_personal.tiponom='".$_SESSION['codigo_nomina']."'";
						$result =sql_ejecutar($strsql);		
  						while ($fila = fetch_array($result))
  						{
  					?>
  						<div class="row">
		  						<div class="col-md-3">
		                            <label><?php echo $fila[descrip].":"; ?></label>
		                        </div> 
		                        <div class="col-md-3"> 
		                            <input name="txtid[]" type="hidden" id="" style="width:100px" value="<?php echo $fila[id];?>">
		                            <input name="txtCampo[]" type="text" id="txt<?php echo $fila[id]; ?>" class="form-control" value="<?php echo $fila[valor];?>" maxlength="60" <?php if ($fila[tipo]=='N') { ?>  <?php } if ($fila[tipo]=='F') { ?> readonly="true" <?php }?>/>
		                             <?php if ($fila[tipo]=='F'){?>
	   <input name="image3" type="image" id="dis_<?php echo $fila[id]; ?>" src="lib/jscalendar/cal.gif" />
	   	 	<script type="text/javascript"> 
		Calendar.setup({inputField:"txt<?php echo $fila[id]; ?>",ifFormat:"%d/%m/%Y",button:"dis_<?php echo $fila[id]; ?>"}); 
		</script>

	   <?php } ?>
		                            <br>
		                        </div> 
  						</div>
  					<?php
  					}
  					?> 
              	
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
</form>
</body>
</html>
