<?php
/*
include_once("nomina_electronica_comprobante.php");
$filePath = curl_file_create("/amaxonia_planilla/nomina/procesos/nomina_electronica/prueba_subir.pdf");
print_r($filePath);

$retorno=nomina_electronica_journals_attachment($access_token,$organizacion_id,"2583548000000103001","prueba_subir_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.pdf");
print "<br>Retorno attachment: ";
print_r($retorno);
*/

print str_replace("\\","/",dirname(__FILE__));


?>