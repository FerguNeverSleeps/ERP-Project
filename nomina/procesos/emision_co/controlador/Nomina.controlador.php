<?php

Class NominaControlador
{
    static public function listarAnioNomina()
    {
        return NominaModelo::listarAnioNomina();
    }
    static public function consultaConceptos( $datos )
    {
        return NominaModelo::consultaConceptos( $datos );
    }
    static public function GetNominaMesAnio( $datos )
    {
        return NominaModelo::GetNominaMesAnio( $datos );
    }
    static public function GetNominasTrabajadorMesAnio( $datos )
    {
        return NominaModelo::GetNominasTrabajadorMesAnio( $datos );
    }
    static public function GetNominasReenviarTrabajadorMesAnio( $datos )
    {
        return NominaModelo::GetNominasReenviarTrabajadorMesAnio( $datos );
    }
    static public function GetNominasPeriodo( $datos )
    {
        return NominaModelo::GetNominasPeriodo( $datos );
    }
    static public function GetNominasReenviarPeriodo( $datos )
    {
        return NominaModelo::GetNominasReenviarPeriodo( $datos );
    }
    static public function ActualizarStatusEnvioDian( $datos,$tipnom,  $status )
    {
        return NominaModelo::ActualizarStatusEnvioDian( $datos, $tipnom,  $status );
    }
}