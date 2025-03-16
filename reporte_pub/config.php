<?php
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}
include("../generalp.config.inc.php");

$connection = array(
    'dbname' => $_SESSION[bd_nomina],
    'user' => DB_USUARIO,
    'password' => DB_CLAVE,
    'host' => DB_HOST,
    'driver' => 'pdo_mysql',
);

$quincena = array(
    '1' => '1ra',
    '2' => '2da',
);

$meses = array(
    '1' => 'Enero',
    '2' => 'Febrero',
    '3' => 'Marzo',
    '4' => 'Abril',
    '5' => 'Mayo',
    '6' => 'Junio',
    '7' => 'Julio',
    '8' => 'Agosto',
    '9' => 'Septiembre',
    '10' => 'Octubre',
    '11' => 'Noviembre',
    '12' => 'Diciembre'
);

function fecha_sql($fecha = ''){
    return $fecha == '' ? '' : substr($fecha,6,4).'-'.substr($fecha,3,2).'-'.substr($fecha,0,2);
}
function fecha($fecha = ''){
    return $fecha == '' ? '' : substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4);
}

function dropdown($name = '', $options = array(), $selected = '', $extra = '') {
    $form = '<select  id="'.$name.'" name="'.$name.'"'.$extra.'>';
    foreach ($options as $key => $val) {
        $form .= '<option value="'.$key.'"'.( ($key == $selected) ? ' selected="selected"' : '').'>'.$val."</option>\n";
    }
    return $form."</select>\n";
}
