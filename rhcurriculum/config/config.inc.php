<?php

/*-------------------------------------
 *         VARIABLES GLOBALES         -
 *                                    -
 *----------------------------------- */

//RUTA DE ACCESO
//DEFINE('PATH', "C:/AppServ/www");// RUTA PARA SISTEMAS OP WIN AppServ
DEFINE('PATH', $_SERVER['DOCUMENT_ROOT']);// RUTA PARA SISTEMAS OP LINUX
//DEFINE('PATH', "C:/xampp/htdocs/");// RUTA PARA SISTEMAS OP WIN XAMPP

//SISTEMA
DEFINE('SYS', "/ingrese_curriculum/");
//DEFINE('SYSADMIN', "/selectra_administrativo");


//DIRECCION
//DEFINE('DIR', "http://192.168.1.100/brentwood_movil");

//NOMBRE DEL PROYECTO
//DEFINE('NOMBRE_SYS', "BRENTWOOD MOVIL");

//NOMBRE DEL PROYECTO
//DEFINE('INICIALES_SYS', "");


//UBICACIÓN DE LAS IMAGENES
//DEFINE('IMG', "/img/");
//DEFINE('IMG_ADMIN', "/selectraerp/imagenes/");

//UBICACION DE LOS SCRIPT
//DEFINE('JS', "/js/");


/*-------------------------------------
 * CONFIGURACIÓN DE LA BASE DE DATO   -
 *                                    -
 *----------------------------------- */
//database server
DEFINE('DB_SERVER', "192.168.1.200");

//database login name
DEFINE('DB_USER', "root");

//database login password
DEFINE('DB_PASS', "haribol");

//database name
DEFINE('DB_DATABASE', 'asamblea_rrhh');

?>
