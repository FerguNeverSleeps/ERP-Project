<?php 
session_start();
ob_start();
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	
?>
<!-- <script src="//cdn.ckeditor.com/4.4.3/full/ckeditor.js"></script>-->
<script src="ckeditor/ckeditor.js"></script>
<script>
function Enviar(){
	/*
    var limite = document.frmConfigurarConstancia.txtcantidad.value;
    if(!/^([0-9])*$/.test(limite)){
        alert("Limite mensual invalido");
        return false;
    } */

    document.frmConfigurarConstancia.submit();
}
</script>
<?php 
  # Primero consultamos los datos de la tabla nomconf_constancia

  $sql = 'SELECT codigo, encabezado, pie_pagina, titulo, slogan, cargo_gerente, abreviatura, observaciones,
                 cantidad_mensual, tipo_validacion
          FROM   nomconf_constancia';
  $result = sql_ejecutar_utf8($sql);

  if($fila=mysqli_fetch_array($result)){
    $codigo=$fila['codigo'];
    $encabezado=$fila['encabezado'];
    $pie_pagina=$fila['pie_pagina'];
    $titulo= $fila['titulo'];
   // $slogan= $fila['slogan']; 
   // $cargo_gerente=$fila['cargo_gerente'];
   // $abreviatura=$fila['abreviatura'];
   // $observaciones=$fila['observaciones'];
   // $cantidad_mensual=$fila['cantidad_mensual'];
   // $tipo_validacion=$fila['tipo_validacion'];
  }

  if(isset($_POST['codigo_id'])){
    $codigo_id=$_POST['codigo_id'];
    $txtencabezado= filtrar_campo($_POST['txtencabezado']);
    $txtpiepagina= filtrar_campo($_POST['txtpiepagina']);
    $txttitulo=$_POST['txttitulo'];
    //$txtslogan=$_POST['txtslogan'];
    //$txtcargo_gerente=$_POST['txtcargo_gerente'];
    //$txtabreviatura=$_POST['txtabreviatura'];
    //$txtobservaciones=$_POST['txtobservaciones'];
    //$txtcantidad= (int)$_POST['txtcantidad'];
    //$radiotipo_val= $_POST['radio_tipovalidacion'];


    //echo "Formulario enviado";
    if(empty($codigo_id))
    {
      // Insertar datos
    /*
    $query="INSERT INTO nomconf_constancia
            (encabezado, pie_pagina, titulo, slogan, cargo_gerente, abreviatura, observaciones, cantidad_mensual, tipo_validacion)
            VALUES
            ('".$txtencabezado."', '".$txtpiepagina."', '".$txttitulo."', '".$txtslogan."',
             '".$txtcargo_gerente."','".$txtabreviatura."','".$txtobservaciones."', '".$txtcantidad."', '".$radiotipo_val."')";
    */
    $query="INSERT INTO nomconf_constancia
            (encabezado, pie_pagina, titulo)
            VALUES
            ('".$txtencabezado."', '".$txtpiepagina."', '".$txttitulo."')";
         
    $result=sql_ejecutar_utf8($query); 

    }
    else{
        // Actualizar datos
        /*
        $query="UPDATE nomconf_constancia SET 
                    encabezado='".$txtencabezado."',
                    pie_pagina='".$txtpiepagina."',
                    titulo='".$txttitulo."',
                    slogan='". $txtslogan."',
                    cargo_gerente='".$txtcargo_gerente."',
                    abreviatura='".$txtabreviatura."',
                    observaciones='".$txtobservaciones."',
                    cantidad_mensual='".$txtcantidad."',
                    tipo_validacion='".$radiotipo_val."'
                WHERE codigo='".$codigo_id."'";  
                */     
         $query="UPDATE nomconf_constancia SET 
                    encabezado='".$txtencabezado."',
                    pie_pagina='".$txtpiepagina."',
                    titulo='".$txttitulo."'
                WHERE codigo='".$codigo_id."'";         
        $result=sql_ejecutar_utf8($query);       
    }

    activar_pagina("configurar_constancias.php");
  }
?>
<form action="" method="post" name="frmConfigurarConstancia" id="frmConfigurarConstancia">
   <input name="codigo_id" type="hidden" id="codigo_id" value="<?php echo (isset($codigo)) ? $codigo : ''; ?>">
  <table width="780" height="125" border="0" class="row-br">
    <tr>
      <td height="31" class="row-br">
      <font color="#000066">&nbsp;<strong>Configuraci&oacute;n general de constancias</strong></font>
      </td>
    </tr>
    <tr>
      <td width="489" height="86" class="ewTableAltRow">

      <table width="790" border="0" bordercolor="#0066FF">
        <tr bgcolor="#FFFFFF">
          <td width="180" height="24" bgcolor="#FFFFFF" class="ewTableAltRow">&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">T&iacute;tulo:</font>
          </td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow" >
            <input name="txttitulo" type="text" id="txttitulo"  style="width:95%" maxlength="70" value="<?php  echo (isset($titulo)) ? $titulo : '';  ?>">
          </td>
        </tr>
        <!--
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Cargo del gerente:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="70" rows="2" name="txtcargo_gerente" id="txtcargo_gerente" maxlength="255" style="width: 95%"><?php  echo (isset($cargo_gerente)) ? $cargo_gerente : '';  ?></textarea>
          </td>
        </tr>

        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Abreviatura profesi&oacute;n:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <input name="txtabreviatura" type="text" id="txtabreviatura" style="width:100px" value="<?php  echo (isset($abreviatura)) ? $abreviatura : '';  ?>" maxlength="40">
          </td>
        </tr>
		-->
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="260" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Encabezado:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="30" rows="5" name="txtencabezado" id="txtencabezado" style="width: 95%"><?php  echo (isset($encabezado)) ? $encabezado : '';  ?></textarea>
            <script>
             CKEDITOR.replace( 'txtencabezado',{
              extraPlugins: 'tableresize',
             	height: '160px',
             	width: '95%',
             	//resize_enabled: false,
             	resize_dir: 'vertical',
            	toolbar: [['Source'],['Bold', 'Italic', 'Underline' ],['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],['Image', 'Table', 'HorizontalRule' ]]
            }); 
            </script>
          </td>
        </tr>

        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="190" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Pie de p&aacute;gina:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="70" rows="5" name="txtpiepagina" id="txtpiepagina" style="width: 95%"><?php  echo (isset($pie_pagina)) ? $pie_pagina : '';  ?></textarea>
            <script>
             CKEDITOR.replace( 'txtpiepagina',{
              extraPlugins: 'tableresize',
             	height: '100px',
             	width: '95%',
             	//resize_enabled: false,
             	resize_dir: 'vertical',
            	toolbar: [['Source'],['Bold', 'Italic', 'Underline' ],['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],['Image', 'Table', 'HorizontalRule' ]]
            });  
            </script>
          </td>
        </tr>
        <!--
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="95" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Eslogan:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="70" rows="5" name="txtslogan" id="txtslogan" style="width: 95%"><?php  echo (isset($slogan)) ? $slogan : '';  ?></textarea>
          </td>
        </tr>

        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="95" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Observaciones:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="70" rows="5" name="txtobservaciones" id="txtobservaciones" style="width: 95%"><?php  echo (isset($observaciones)) ? $observaciones : '';  ?></textarea>
          </td>
        </tr>

          <tr valign="middle" bgcolor="#FFFFFF">
              <td height="35" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
                  <font size="2" face="Arial, Helvetica, sans-serif">Límite mensual de constancias a validar:</font>
              </td>
              <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
                  <input name="txtcantidad" type="text" id="txtcantidad" size="2" style="width:100px" value="<?php  echo (isset($cantidad_mensual)) ? $cantidad_mensual : '';  ?>" maxlength="40">
              </td>
          </tr>

          <tr valign="middle" bgcolor="#FFFFFF">
              <td height="35" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
                  <font size="2" face="Arial, Helvetica, sans-serif">Tipo de validación:</font>
              </td>
              <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
                  <label><input type="radio" name="radio_tipovalidacion" id="radio_tipovalidacion" value="General" <?php if(empty($tipo_validacion) || $tipo_validacion=='General'){?> checked="checked" <?php }?>>General</label>
                  <label><input type="radio" name="radio_tipovalidacion" id="radio_tipovalidacion" value="Modelo" <?php if($tipo_validacion=='Modelo'){?> checked="checked" <?php } ?>>Por Modelo</label>
              </td>
          </tr>
        -->
        
        <tr bgcolor="#FFFFFF">
          <td height="30" bgcolor="#FFFFFF" class="ewTableAltRow">&nbsp;</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow">
            <div align="left">
                  <table width="85" border="0">
                    <tr>
                      <td width="39"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                          <?php btn('cancel','history.back();',2) ?>
                      </font></div></td>
                      <td width="36"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                          <?php btn('ok','return Enviar();',2) ?>
                      </font></div>
                      </td>
                    </tr>
                  </table>
            </div>
          </td>
        </tr>

      </table>

      </td>
    </tr>
  </table> 
</form>
</body>
</html>