<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
include "../header.php";
?>
<HTML xmlns="http://www.w3.org/1999/xhtml" xmlns:spry = "http://ns.adobe.com/spry" class="fondo">

<HEAD>
<TITLE>SELECTRA</TITLE>

<LINK href="menu_int_archivos/estilos.css" type="text/css" rel="stylesheet">
<SCRIPT src="menu_int_archivos/common.js" type="text/javascript">
</SCRIPT>




<BODY>
<TABLE width="100%">
  <TBODY>
  <TR>
    <TD class=>
      <TABLE class=tb-tit cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
            <TD><SPAN style="FLOAT: left"><IMG class=icon height=32 src="img_sis/50.png" width=32>M&oacute;dulos</SPAN></TD>
        </TR>
		</TBODY>
		</TABLE>
		</TD>
	</TR>
  <TR>
    <TD>
      <DIV>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD>
            <DIV class=box>
            <TABLE class=boton style="CURSOR: pointer" onClick="javascript:window.open('../../selectraerp/modulos/principal?linked')" height=90 cellSpacing=0 cellPadding=0 width=100 border=0>
              <TBODY>
              <TR>
                <TD vAlign=bottom height=45>
                  <DIV align=center><IMG src="img_sis/icons/7.png" width="36" height="36" class=icon></DIV></TD></TR>
              <TR>
                <TD height=45>
                  <DIV class=boton-text align=center>Administrativo</DIV></TD></TR></TBODY></TABLE></DIV>
				  
				  <DIV class=box>
            <TABLE class=boton style="CURSOR: pointer" onClick="javascript:window.open('../../contabilidad/frame.php')" height=90 cellSpacing=0 cellPadding=0 width=100 border=0>
              <TBODY>
              <TR>
                <TD vAlign=bottom height=45>
                  <DIV align=center><IMG src="img_sis/054.png" width="36" height="36" class=icon></DIV></TD></TR>
              <TR>
                <TD height=45>
                    <DIV class=boton-text align=center>Contabilidad</DIV></TD></TR></TBODY></TABLE></DIV>
				  
			 <!--	  <DIV class=box>
            <TABLE class=boton style="CURSOR: pointer" onClick="javascript:window.open('../../../crm')" height=90 cellSpacing=0 cellPadding=0 width=100 border=0>
              <TBODY>
              <TR>
                <TD vAlign=bottom height=45>
                  <DIV align=center><IMG src="img_sis/12.png" width="36" height="36" class=icon></DIV></TD></TR>
              <TR>
                <TD height=45>
                  <DIV class=boton-text align=center>CRM</DIV></TD></TR></TBODY></TABLE></DIV>-->
		
            </TD>
        </TR>
        <TR>
          <TD>&nbsp;</TD></TR></TBODY></TABLE></DIV></TD></TR></TBODY></TABLE>
</BODY>
</HTML>
