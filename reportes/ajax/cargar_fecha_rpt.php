<?php
$datos = array(
    'fecini' => date('d-m-Y',strtotime('+1 day',strtotime(date('d-m-Y',strtotime('-1 month',strtotime($_POST['fecha']) ) ) ) ) )
);
//------------------------------------------------------------
echo json_encode($datos);
?>