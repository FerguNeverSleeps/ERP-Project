<?php 
session_start();
ob_start();
include("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	
?>
<script src="ckeditor/ckeditor.js"></script>
<script>
function Enviar(){							
	
	if (document.frmConfigurarConstancia.txtnombre.value=='')
	{
		alert("Debe ingresar un nombre para la constancia. Verifique...");
		return false;
	}

	if(document.frmConfigurarConstancia.chkconfiguracion.value=='Template')
	{

    if(document.frmConfigurarConstancia.fileTemplate.value=='' && document.frmConfigurarConstancia.txttemplate.value=='')
    {
      alert("Debe cargar el archivo pdf que sera utilizado como template (de 1 pagina)");
      return false;     
    }

	}		

	document.frmConfigurarConstancia.submit();		
}
</script>
<?php 

  if(isset($_POST['codigo']))
  {
    $codigo_id=$_POST['codigo'];
    $txtnombre= $_POST['txtnombre'];
    $txttitulo= $_POST['txttitulo'];
    //$txtcontenido1= $_POST['txtcontenido1'];
    $txtcontenido2= filtrar_campo($_POST['txtcontenido2']);
    //$txtcontenido3=$_POST['txtcontenido3'];
    //$txtobservaciones=$_POST['txtobservaciones'];
    $chkconfiguracion = $_POST['chkconfiguracion'];
    $txttemplate = $_POST['txttemplate'];
    $txtformula = $_POST['txtformula'];

    $target_dir = "../tcpdf/templates/";
    $target_file = $target_dir . basename($_FILES["fileTemplate"]["name"]);
    $uploadOk = 1;
    $fileType = pathinfo($target_file,PATHINFO_EXTENSION);
    $nombre_fichero = $_FILES["fileTemplate"]["name"];

    $acentos = array('á','é','í','ó','ú',' ','&aacute;','&eacute;','&iacute;','&oacute;','&uacute;');
    $vocales = array('a','e','i','o','u','_','a','e','i','o','u');
    $nombre_fichero = strtolower($nombre_fichero);
    $nombre_fichero = str_replace($acentos, $vocales, $nombre_fichero);

     $target_file = $target_dir . basename($nombre_fichero);
    // Allow certain file formats
    if($fileType != "pdf" && $fileType != "PDF") 
    {
        //echo "Sorry, only PDF";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) 
    {
        if (move_uploaded_file($_FILES["fileTemplate"]["tmp_name"], $target_file)) 
        {
            $txttemplate = $nombre_fichero;
        } 
    }


        // Actualizar datos
        $query="UPDATE nomtipos_constancia SET 
        			         nombre='".$txtnombre."',	
                       contenido2='".$txtcontenido2."',
                       titulo='". $txttitulo."',
                       configuracion='".$chkconfiguracion."',
                       template='".$txttemplate."',
                       formula='".$txtformula."'
                WHERE codigo='".$codigo_id."'";  
                // contenido1='".$txtcontenido1."',  
                // contenido3='".$txtcontenido3."', 
                //  observaciones='".$txtobservaciones."',  
        
        $result=sql_ejecutar_utf8($query);       

        activar_pagina("configurar_modelos_constancias.php");
  }

  // Consultar datos del modelo de constancia

  $modelo_id=$_POST['codigo_id']; // id del modelo de la constancia

  $sql = 'SELECT codigo, nombre, contenido1, contenido2, contenido3, titulo, observaciones, configuracion, template, formula 
          FROM   nomtipos_constancia WHERE codigo='.$modelo_id;

  $result = sql_ejecutar_utf8($sql);

  if($fila=mysqli_fetch_array($result))
  {
    $codigo=$fila['codigo'];
    $nombre_modelo=$fila['nombre'];
    //$contenido1=$fila['contenido1'];
    $contenido2=$fila['contenido2'];
    //$contenido3=$fila['contenido3'];
    $titulo=$fila['titulo'];
    //$observaciones=$fila['observaciones'];
    $configuracion=$fila['configuracion'];
    $template=$fila['template'];
    $formula=$fila['formula'];
  }
?>
<form action="" method="post" name="frmConfigurarConstancia" id="frmConfigurarConstancia"  enctype="multipart/form-data">
   <input name="codigo" type="hidden" id="codigo" value="<?php echo (isset($codigo)) ? $codigo : ''; ?>">
  <table width="900" height="125" border="0" class="row-br">
    <tr>
      <td height="31" class="row-br">
      <font color="#000066">&nbsp;<strong>Configuraci&oacute;n de constancia:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $nombre_modelo; ?></font>
      </td>
    </tr>
    <tr>
      <td  height="86" class="ewTableAltRow">

      <table width="900" border="0" bordercolor="#0066FF">
        <tr bgcolor="#FFFFFF">
          <td width="180" height="30" bgcolor="#FFFFFF" class="ewTableAltRow">&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Nombre del modelo:</font>
          </td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow" >
            <input name="txtnombre" type="text" id="txtnombre"  style="width:95%" maxlength="60" value="<?php  echo (isset($nombre_modelo)) ? $nombre_modelo : '';  ?>">
          </td>
        </tr>
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
          <td height="180" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Contenido 1 (Principal):</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="70" rows="10" name="txtcontenido1" id="txtcontenido1" style="width: 95%"><?php  //echo (isset($contenido1)) ? $contenido1 : '';  ?></textarea>
          </td>
        </tr>
	-->
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="260" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Contenido:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" ><br>
            <textarea cols="70" rows="12" name="txtcontenido2" id="txtcontenido2" style="width: 95%"><?php  echo (isset($contenido2)) ? $contenido2 : '';  ?></textarea><br>
          </td>
            <script>
				CKEDITOR.stylesSet.add( 'default', [
				    // Block Styles
				    { name: 'Espaciado: 195%',       element: 'p',      styles: { 'line-height': '195%' } },
            { name: 'Espaciado: 180%',       element: 'p',      styles: { 'line-height': '180%' } },
            { name: 'Espaciado: 170%',       element: 'p',      styles: { 'line-height': '170%' } },
            { name: 'Espaciado: 160%',       element: 'p',      styles: { 'line-height': '160%' } },
				    { name: 'Espaciado: 150%',       element: 'p',      styles: { 'line-height': '150%' } },
            { name: 'Espaciado: 140%',       element: 'p',      styles: { 'line-height': '140%' } },
            { name: 'Espaciado: 130%',       element: 'p',      styles: { 'line-height': '130%' } },
            { name: 'Espaciado: 120%',       element: 'p',      styles: { 'line-height': '120%' } },
            { name: 'Espaciado: 110%',       element: 'p',      styles: { 'line-height': '110%' } },
				    { name: 'Espaciado: 100%',       element: 'p',      styles: { 'line-height': '100%' } },
				    { name: 'Espaciado: 90%',        element: 'p',      styles: { 'line-height': '90%' } },
				    { name: 'Espaciado: 50%',        element: 'p',      styles: { 'line-height': '50%' } },
            { name: 'Espaciado: 45%',        element: 'p',      styles: { 'line-height': '45%' } },
            { name: 'Espaciado: 40%',        element: 'p',      styles: { 'line-height': '40%' } },
            { name: 'Espaciado: 30%',        element: 'p',      styles: { 'line-height': '30%' } },
            { name: 'Espaciado: 20%',        element: 'p',      styles: { 'line-height': '20%' } },
            { name: 'Espaciado: 10%',        element: 'p',      styles: { 'line-height': '10%' } },
            { name: 'Espaciado: 5%',         element: 'p',      styles: { 'line-height': '5%' } },

				    // Inline Styles
				    { name: 'Resaltar: Amarillo',   element: 'span',    styles: { 'background-color': 'Yellow' } },
				   // { name: 'Espaciado: 195%',    element: 'span',    styles: { 'line-height': '195%' } },
				   // { name: 'Espaciado: 90%',    element: 'span',    styles: { 'line-height': '90%' } },
				] );
             CKEDITOR.replace( 'txtcontenido2',{
                extraPlugins: 'tableresize,tabletools',
             	height: '700px',
             	width: '95%',
             	//resize_enabled: false,
             	resize_dir: 'vertical',
            	toolbar: [['Source'],
            	          ['Bold', 'Italic', 'Underline' ],
            	          ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            	          ['Image', 'Table', 'HorizontalRule' ], ['TextColor', 'BGColor'],
            	          [ 'Styles', 'Format', 'Font', 'FontSize'] ]
            }); 
            </script>
        </tr>
        <!--
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="95" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Contenido 3 (opcional):</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="70" rows="4" name="txtcontenido3" id="txtcontenido3" style="width: 95%"><?php  // echo (isset($contenido3)) ? $contenido3 : '';  ?></textarea>
          </td>
        </tr>

        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="95" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Observaciones:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <textarea cols="70" rows="4" name="txtobservaciones" id="txtobservaciones" style="width: 95%"><?php  //echo (isset($observaciones)) ? $observaciones : '';  ?></textarea>
          </td>
        </tr>
	-->
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="70" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Fondo del PDF:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
                <input type="radio" name="chkconfiguracion" value="Header"   <?php echo (isset($configuracion) && $configuracion=='Header') ? 'checked' : '' ; ?> >Utilizar encabezado y pie de p&aacute;gina de la configuraci&oacute;n general<br>
                <input type="radio" name="chkconfiguracion" value="Template" <?php echo (isset($configuracion) && $configuracion=='Template') ? 'checked' : '' ; ?> >Utilizar Template<br>
                <input type="radio" name="chkconfiguracion" value="Ninguno"  <?php echo ( (isset($configuracion) && $configuracion=='Ninguno') || empty($configuracion) ) ? 'checked' : '' ; ?> >Ninguno
          </td>
        </tr>

        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="50" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Nombre del Template:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <input readonly name="txttemplate" type="text" id="txttemplate"  style="width:95%" value="<?php  echo (isset($template)) ? $template : '';  ?>">
          </td>
        </tr> 
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="50" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">Cargar Template:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" >
            <input type="file" name="fileTemplate" id="fileTemplate" style="border: none">
          </td>
        </tr> 

        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="100" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;
            <font size="2" face="Arial, Helvetica, sans-serif">F&oacute;rmula:</font>
          </td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow">
            <textarea cols="70" rows="12" name="txtformula" id="txtformula" style="width: 95%; resize:vertical;"><?php  echo (isset($formula)) ? $formula : '';  ?></textarea>
          </td>
        </tr> 
        
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
                          <?php btn('ok','Enviar();',2) ?>					
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