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
}