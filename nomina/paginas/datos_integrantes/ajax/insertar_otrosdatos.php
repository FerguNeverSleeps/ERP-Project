<?php 
require_once('../../../lib/database.php');

$sqlinsertar='INSERT INTO `posicionempleado`(`solicitud`,  `personalexterno`, `fecha_permanencia`, `telefono_celular`, `tipo_sangre`, `nivel_educativo`, `hijos`, `contacto_emergencia`, `telefono_contacto`, `discapacidad`, `fam_discapacidad`, `enfermedades_alergias`) VALUES ('.$_POST['solicitud'].','.$_POST['personalexterno'].''.$_POST['fecha_permanencia'].','.$_POST['telefono_celular'].','.$_POST['tipo_sangre'].','.$_POST['nivel_educativo'].','.$_POST['hijos'].','.$_POST['contacto_emergencia'].','.$_POST['telefono_contacto'].','.$_POST['discapacidad'].'),'.$_POST['fam_discapacidad'].','.$_POST['enfermedades_alergias'].')';

$db     = new Database($_SESSION['bd']);


$res    = $db->query($sql);
$insert = $res->fetch_object();
 ?>