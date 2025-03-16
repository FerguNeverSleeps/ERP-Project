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
                        Campos Adicionales
                    </div>               

                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-responsive" id="table_datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descripcion</th>
                                    <th>Etiqueta</th>
                                    <th>Tipo</th>
                                    <th>Val. Defecto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 	
                                $strsql = "SELECT id, descrip, etiqueta, tipo, valdefecto1 FROM nomcampos_adicionales";
                                $result =sql_ejecutar($strsql);		
                                while ($fila = fetch_array($result))
                                {
                                ?>
                                <tr>
                                    <td><?php echo $fila['id']; ?> </td>
                                    <td><?php echo $fila['descrip']; ?> </td>
                                    <td><?php echo $fila['etiqueta']; ?> </td>
                                    <td><?php echo $fila['tipo']; ?> </td>
                                    <td><?php echo $fila['valdefecto1']; ?> </td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table> 
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
</form>
</body>
</html>