<?php
   session_start();
   require_once("../../../libs/php/adodb5/adodb.inc.php");
   require_once("../../../libs/php/configuracion/config.php");
   require_once("../../../libs/php/clases/ConexionComun.php");
   include_once("../../../libs/php/clases/correlativos.php");
   require_once "../../../libs/php/clases/numerosALetras.class.php";
   include("../../../../menu_sistemas/lib/common.php");

   $conn = new ConexionComun();
   $response = array('success' => false, 'message' => 'El codigo ya esta en uso');

   if (isset($_GET["cod"])):
      $codigo = $_GET["cod"];
      $query = "SELECT COUNT(id_cliente) as id FROM clientes WHERE cod_cliente = %s";
      $query = sprintf($query, $codigo);

      if(isset($_GET["id"]) && $_GET["id"] != null):
         $id = $_GET["id"];
         $query = "SELECT COUNT(id_cliente) as id FROM clientes WHERE cod_cliente = %s AND id_cliente <> %d";
         $query = sprintf($query, $codigo, $id);
         $response = array('success' => true, 'message' =>$query);
      endif;

      $cantidad = $conn->ObtenerFilasBySqlSelect($query);

      if($cantidad[0]["id"] == 0):
         $response = array('success' => true, 'message' =>'El Código es valido');
      endif;
   endif;

   echo json_encode($response);
?>