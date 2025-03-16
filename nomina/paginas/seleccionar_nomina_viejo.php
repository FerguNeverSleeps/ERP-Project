<?php
session_start();
ob_start();

//$termino= $_SESSION['termino'];
include("../header.php") ;
include("../lib/common.php") ;
include("func_bd.php") ;

$config = parse_ini_file("../lib/selectra.ini");
/*if ($config['multiempresa']) {
    header("Location: seleccionar_empresa.php?tabla=nomempresa");
} else*/ {
    if ($_SESSION['bd'] == "" || !isset($_POST["seleccion_nomina"])) {
        #echo 'salio';exit;
        $_SESSION['bd'] = $config['bdnombre'];
        $_SESSION['termino'] = $config['termino'];
    }
    require_once "../lib/common.php";
 
    
    $seleccion=$_POST["seleccion_nomina"];
    if ($seleccion==1){
            foreach($_POST['opt'] as $key => $value){
                    $valuetxt=$value;
            }
            $strsql= "select codtip from nomtipos_nomina where descrip = '$valuetxt'";
            $result =sql_ejecutar($strsql);			
            $fila = mysqli_fetch_array($result);
            $_SESSION['codigo_nomina'] = $fila[0];
            $_SESSION['nomina'] = $valuetxt;
            activar_pagina("frame.php");
    }
    
        $bValidPwd = false;
        $sUsername = $_SESSION['usuario'];
        $sPassword = $_SESSION['clave'];
        //echo $_SESSION['bd'];
        $sSql = new bd($_SESSION['bd']);
        $result = $sSql->query("select bd_nomina,bd_contabilidad,nombre nom_emp from nomempresa where bd_nomina = '".$_SESSION['bd_nomina']."'");
        $filaemp = $result->fetch_assoc();
        $_SESSION["bd"] = $filaemp['bd_nomina'];
        $_SESSION["bdc"] = $filaemp['bd_contabilidad'];
        $_SESSION["nombre_empresa_nomina"] = $filaemp['nom_emp'];

        $sSql = new bd($_SESSION['bd']);
        //echo $_SESSION['bd'];
        //echo "select * from nomusuarios where login_usuario='$sUsername' and clave='".hash("sha256",$sPassword)."'";
        
        $result = $sSql->query("select * from nomusuarios where descrip='$sUsername'");// and clave='" . ($sPassword) . "'
        //echo "select * from nomusuarios where login_usuario='$sUsername'";
        //@TODO colocar clave en el query.
        //echo $result->num_rows;
        //exit;
        if ($result->num_rows > 0) {
            //cerrar_conexion($Conn);
            $fila = $result->fetch_assoc();
            $_SESSION['ewSessionStatus'] = "login";            
            $_SESSION['ewSessionUserName'] = $sUsername;
            $_SESSION['nombre'] = $fila["descrip"];
            $_SESSION['coduser'] = $fila["coduser"];
            $_SESSION['id_usuario'] = $fila["coduser"];
            $_SESSION['ewSessionSysAdmin'] = 0; // Non system admin

            $_SESSION['acce_configuracion'] = $fila['acce_configuracion'];
            $_SESSION['acce_usuarios'] = $fila['acce_usuarios'];
            $_SESSION['acce_elegibles'] = $fila['acce_elegibles'];
            $_SESSION['acce_personal'] = $fila['acce_personal'];
            $_SESSION['acce_prestamos'] = $fila['acce_prestamos'];
            $_SESSION['acce_consultas'] = $fila['acce_consultas'];
            $_SESSION['acce_transacciones'] = $fila['acce_transacciones'];
            $_SESSION['acce_procesos'] = $fila['acce_procesos'];
            $_SESSION['acce_reportes'] = $fila['acce_reportes'];
            $_SESSION['acce_enviar_nom'] = $fila['acce_enviar_nom'];
            $_SESSION['acce_autorizar_nom'] = $fila['acce_autorizar_nom'];
            $_SESSION['acce_estuaca'] = $fila['acce_estuaca'];
            $_SESSION['acce_xestuaca'] = $fila['acce_xestuaca'];
            $_SESSION['acce_permisos'] = $fila['acce_permisos'];
            $_SESSION['acce_logros'] = $fila['acce_logros'];
            $_SESSION['acce_penalizacion'] = $fila['acce_penalizacion'];
            $_SESSION['acce_movpe'] = $fila['acce_movpe'];
            $_SESSION['acce_evalde'] = $fila['acce_evalde'];
            $_SESSION['acce_experiencia'] = $fila['acce_experiencia'];
            $_SESSION['acce_antic'] = $fila['acce_antic'];
            $_SESSION['acce_uniforme'] = $fila['acce_uniforme'];
            $_SESSION['acce_generarordennomina'] = $fila['acce_generarordennomina'];
            
            $_SESSION["ewSessionStatus"] = "login";
            $_SESSION['termino'] = $config['termino'];
        } else {
            $_SESSION["ewSessionMessage"] = "no";
        }
}


?>


<script>
function VerificarSeleccion()
{
	seleccion=0
	for(i=0; ele=document.frmseleccionar.elements[i]; i++){  		
		if (ele.name=='opt[]'){			
			if (ele.checked == true){
			seleccion=1}		
		}			
	}	
	
	if (seleccion==0){
	alert("Debe seleccionar un tipo de <?echo $termino?>. Verifique...")}
	else{
	//document.frmseleccionar.action="frame.php";	
	//document.frmseleccionar.submit();
	//alert("SS");
	document.frmseleccionar.seleccion_nomina.value=1;
	document.frmseleccionar.submit();
	}
}
</script>


<font size="3" face="Arial, Helvetica, sans-serif">
<?php





?>
</font>
<form action="" method="post" name="frmseleccionar" id="frmseleccionar">
<input name="seleccion_nomina" type="hidden" value="0">
  <div align="center">
    <p>&nbsp;</p>
    <table  border="0" align="center" cellpadding="0" cellspacing="3">
   <!--   <tr class="row-br">
        <td background="img_sis/bg_logo.png"><div align="center"><img src="img_sis/logo_login.png" width="207" height="130" /></div></td>
      </tr> -->
      <tr class="row-br">
        <td height="22" bgcolor="#A3CCE2"><div align="center"><strong>Seleccione un Tipo de <?echo $termino?> </font></strong></div></td>
      </tr>
      <tr class="row-br">
        <td height="50"><table width="400" border="0" id="lst" cellspacing="0" cellpadding="0">
            <tr class="tb-head2">
              <td width="86%" height="21" class="tb-fila"><div align="left"><STRONG>&nbsp;Nombre de <?echo $termino?></STRONG></font></div></td>
            </tr>
            <?php
	//$sItemRowClass = " class=\"ewTableRow\"";
	//$sListTrJs = " onmouseover='ew_mouseover(this);' onmouseout='ew_mouseout(this);'";
	?>
            <tr>
              <?php 
  	//operaciones para paginaciones
	$strsql= "select * from nomtipos_nomina";
	$result =sql_ejecutar($strsql);		
	
  	$num_fila = 0;
  	$in=1+(($pagina-1)*5);

  	//ciclo para mostrar los datos 
  	while ($fila = mysqli_fetch_array($result))
  	{ 
  	?>
              <td height="27"><div align="left"><span>
                <input name="opt[]" type="radio" class="icon" id="<?php echo $fila[codtip]; ?>" value="<?php echo $fila[descrip]; ?>" />
      <?php
	  echo $fila[descrip];
	  ?>
              </span></div></td>
            </tr>
            <?php   
  	}//fin del ciclo while
  	//operaciones de paginacion
	$num_fila++;
  	$in++;  
  	?>
          </table>
          <div align="center" class="tb-fila">
            <?php btn('ok','VerificarSeleccion();',2) ?>
          </div>
          <div align="center"></div>
        <div align="center"></div></td>
      </tr>
      <?php
	
	if (@$_SESSION[ewSessionMessage] <> "") 	
	{	
	
	?>
      
      <?php 
	$_SESSION[ewSessionMessage] = ""; 
	} 
	?>
      <tr class="row-br" background="img_sis/sup_bg.png">
        <td bgcolor="#A3CCE2"><div align="center" class="Estilo5"><img src="../img_sis/logo.png" width="126" height="46" /></div></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </div>
</form>
</body>
</html>
