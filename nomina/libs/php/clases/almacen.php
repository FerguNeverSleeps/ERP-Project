<?php

class Almacen extends ConexionComun{

function __construct(){
        parent::__construct();
}
    
    function existComprobanteEntrada ($anio,$comprobante) { 
         $kardex_almacen = $this->ObtenerFilasBySqlSelect("SELECT id_transaccion FROM kardex_almacen "
                 . " WHERE  tipo_movimiento_almacen = 3 and anio =  $anio  and trim(comprobante) = trim(' $comprobante ');");

         return $kardex_almacen[0]["id_transaccion"];
     }
}

?>
