<?php
   session_start();
   require_once("../../../libs/php/adodb5/adodb.inc.php");
   require_once("../../../libs/php/configuracion/config.php");
   require_once("../../../libs/php/clases/ConexionComun.php");
   //require_once("../../../libs/php/clases/login.php");
   #include_once("../../../libs/php/clases/compra.php");
   //include_once("../../../libs/php/clases/correlativos.php");
   //require_once "../../../libs/php/clases/numerosALetras.class.php";
   include("../../../../menu_sistemas/lib/common.php");

   $conn = new ConexionComun();
   $response = array('success' => false, 'message' => 'La referencia, está asignada a otro articulo');

   if (isset($_GET["ref"])):
      $referencia = $_GET["ref"];
      $query = "SELECT COUNT(id_item) as cantidad FROM item WHERE referencia ='".$referencia."'";
      $cantidad = $conn->ObtenerFilasBySqlSelect($query);
      if($cantidad[0]["cantidad"] == 0):
         $response = array('success' => true, 'message' =>'');
      endif;
   endif;
   echo json_encode($response);
?>