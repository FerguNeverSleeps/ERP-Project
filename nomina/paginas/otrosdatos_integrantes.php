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
$host_ip = $_SERVER['REMOTE_ADDR'];

$consultap="SELECT * FROM nompersonal WHERE ficha='$fichasss'";
//echo $consultap;             
$result =sql_ejecutar($consultap);
$fila = fetch_array($result);
$nombre=$fila['apenom'];
$cedula=$fila['cedula'];

//msgbox($registro_id);

if ($opcion==1)
{
	$txtarray_2=$_POST['txtid'];
	foreach($_POST['txtCampo'] as $key => $value)
	{
		$valor_id=$txtarray_2[$key];			
		$strsql1="UPDATE nomcampos_adic_personal "
                        . "SET valor = '$value' "
                        . "WHERE ficha='$registro_id' AND id=$valor_id AND tiponom=$_SESSION[codigo_nomina]";
		$result1=sql_ejecutar($strsql1);
		$strsql1="";
	}
        echo "<SCRIPT type='text/javascript'>
	alert('Campos Adicionales Actualizados Exitosamente')
	</SCRIPT>";
        
	$consulta="INSERT INTO log_transacciones VALUES ('', 'modificacion de campos adicionales', '".date("Y-m-d H:i:s")."', 'Personal-campos adicionales', 'otrosdatos_integrantes.php', 'editar', '$registro_id', '$_SESSION[nombre]', '$host_ip')";
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
              	<?php echo "Campos adicionales / Ficha: ".$registro_id." - ".utf8_encode($nombre); ?>
              	</div>
              	<div class="actions">
                <a class="btn btn-sm blue"  href="javascript:Enviar(1); document.frmPrincipal.submit();">
                  <i class="fa fa-plus"></i>
                      Actualizar
                </a>
                <a class="btn btn-sm blue"  href="javascript:Enviar2(2,<?php echo $fichasss ?>);document.frmPrincipal.submit();">
                  <i class="fa fa-plus"></i>
                      Generar
                </a>
                

              </div>
              </div>
              <div class="portlet-body">
			  		<?php 	
                                            $strsql= "SELECT nomcampos_adic_personal.id,nomcampos_adic_personal.tipo,"
                                                    . "nomcampos_adicionales.descrip,nomcampos_adic_personal.valor "
                                                    . "FROM nomcampos_adic_personal "
                                                    . "INNER JOIN nomcampos_adicionales on nomcampos_adic_personal.id =  nomcampos_adicionales.id "
                                                    . "WHERE nomcampos_adic_personal.ficha = '$registro_id' and nomcampos_adic_personal.tiponom='".$_SESSION['codigo_nomina']."'";
						$result =sql_ejecutar($strsql);		
  						while ($fila = fetch_array($result))
  						{
  					?>
  						<div class="row">
		  						<div class="col-md-3">
		                            <label><?php echo $fila[id]." - ".$fila[descrip].":"; ?></label>
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
