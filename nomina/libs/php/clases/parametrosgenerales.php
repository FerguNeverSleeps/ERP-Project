<?php

class ParametrosGenerales extends ConexionComun{
    function __construct(){
        parent::__construct();
    }


    function TriggerActualizarStrintTipoPrecio($NEW_precio,$codTipoPrecio){
/*
 * Verificamos en la tabla tipo_precio; si fue cambiada la descripcion
 * tipo de precio actualizamos el campo descripcion de dicha tabla.
 */

$campo_precio = $this->ObtenerFilasBySqlSelect("select * from
                                                                tipo_precio
                                                                   where cod_tipo_precio = '".$codTipoPrecio."'");
    if($campo_precio!=""){
        $OLD_precio = $campo_precio[0]["descripcion"];
        if($NEW_precio!=$OLD_precio){
            $this->Execute2("update tipo_precio set descripcion = '".$NEW_precio."' where cod_tipo_precio = '".$codTipoPrecio."'");
        }
    }
       
    }

     function isActivePrecioEnLetras() {
         $parametros_generales = $this->ObtenerFilasBySqlSelect("SELECT precio_letra FROM parametros_generales;");
         return $parametros_generales[0]["precio_letra"];
     }
    
    function precio_letras($precio) {

        $campo_precio = $this->ObtenerFilasBySqlSelect("SELECT * FROM precio_letras");         

        if($campo_precio!=""){       
                $numero_array =array();
                $letra_array =array();
                foreach($campo_precio as $clave => $item)
                {
                        $numero_array[] = $item["numero"];
                        $letra_array[] = $item["letra"];
                    
                }
        }

        $letras = str_replace(
            $numero_array,
            $letra_array,
            $precio
        );
        
         return $letras;    
    }
    
     function isActiveTallaYColor() {
         $parametros_generales = $this->ObtenerFilasBySqlSelect("SELECT talla_color FROM parametros_generales;");
         return $parametros_generales[0]["talla_color"];
     }
}


?>