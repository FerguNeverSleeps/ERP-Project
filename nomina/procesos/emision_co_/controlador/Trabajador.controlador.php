<?php

Class TrabajadorControlador
{
    static public function GetDatosTrabajador( $tipnom )
    {
        return TrabajadorModelo::GetDatosTrabajador( $tipnom );
    }
    static public function GetDevengados( $datos )
    {
        return TrabajadorModelo::GetDevengados( $datos );
    }
    static public function GetDatosConceptosDevengos( )
    {
        return TrabajadorModelo::GetDatosConceptosDevengos( );
    }
    static public function GetDatosConceptosDeducciones( )
    {
        return TrabajadorModelo::GetDatosConceptosDeducciones( );
    }
    static public function GetDeducciones( $datos )
    {
        return TrabajadorModelo::GetDeducciones( $datos );
    }
    static public function GetMontoConcepto( $concepto , $tip_con )
    {
        return TrabajadorModelo::GetMontoConcepto( $concepto , $tip_con );
    }
}