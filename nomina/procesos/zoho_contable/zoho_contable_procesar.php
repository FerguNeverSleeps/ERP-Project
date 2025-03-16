<?php
/*
include_once("zoho_contable_comprobante.php");
$filePath = curl_file_create("/amaxonia_planilla/nomina/procesos/zoho_contable/prueba_subir.pdf");
print_r($filePath);

$retorno=zoho_journals_attachment($access_token,$organizacion_id,"2583548000000103001","prueba_subir_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.pdf");
print "<br>Retorno attachment: ";
print_r($retorno);
*/

print str_replace("\\","/",dirname(__FILE__));


?>