<?php 
session_start();
ob_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

?>
<?php
include("../header4.php") ;
include("../lib/common.php") ;
include("../paginas/func_bd.php") ;

/*$cod = (empty($_REQUEST['cod'])) ? '' : $_REQUEST['cod'];


$sSql = "SELECT * FROM nom_modulos WHERE cod_modulo = 1";  
$result = sql_ejecutar($sSql);

$row_rs = mysqli_fetch_array($result);

if ($row_rs['archivo'] <> '') {
	//header ('location: '.$row_rs['archivo']);
	activar_pagina($row_rs['archivo']);
	
}*/

?>
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width"  marginheight="0">

<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <h4>NETSUITE ORACLE</h4>
            </div>
            <div class="portlet-body">
              <div class="row">
                <?php

$sSql = "SELECT * FROM nom_modulos WHERE cod_modulo_padre IN(410,412,424) AND `activo` !=  '0' "
        . "ORDER BY orden";
//$rss = $Conn->query("SELECT * FROM modulos WHERE cod_modulo_padre = $cod ORDER BY orden");

$result = sql_ejecutar($sSql);


while ($row_rss = mysqli_fetch_array($result)) 
{
 //boton2($row_rss[cod_modulo],$row_rss[nom_menu],$row_rss[archivo],$row_rss[img]);
 ?>
                      <div class="col-md-2 text-center">
                        <a href="<?php echo $row_rss['archivo']; ?>">
                          <div class="tile bg-blue">
                            <div class="tile-body">
                              <img width="100%" height="75%" src="<?= $row_rss['img']; ?>" class="icon"/>
                              <span><?php echo $row_rss['nom_menu']; ?></span>                            
                            </div>
                          </div>
                        </a>
                      </div>
                    <?php
                  }
                ?>
              </div>

            <!-- END PORTLET BODY-->
            </div>
        <!-- END EXAMPLE TABLE PORTLET-->
          </div>
        </div>
      </div>
    <!-- END PAGE CONTENT-->
    </div>
  </div>
    <!-- END CONTENT -->
</div>
</body>
<?php include("../header4.php") ;
 ?>
</html>
<? // $rs->close();?>
<? // $Conn->close();?>
