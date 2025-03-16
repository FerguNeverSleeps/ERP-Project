<?php
require_once  "../controlador/Nomina.controlador.php";
require_once  "../modelo/Nomina.modelo.php";
Class AjaxNomina{
    public function listarAnioNomina(){
        return NominaControlador::listarAnioNomina();
    }

}

if(isset($_POST['listarAnioNomina']) AND $_POST['listarAnioNomina'] == "yes"){
    $ajax = new AjaxNomina();
    $lista = $ajax->listarAnioNomina();
    
    echo json_encode($lista);
}