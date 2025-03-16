<?php
Class EmisionColControlador
{
    static public function listarNominasEmision(){
        return EmisionCol::listarNominasEmision();
    }
    static public function listarNominasEmisionCabecera(){
        return EmisionCol::listarNominasEmisionCabecera();
    }
    static public function listarNominasEmisionCabeceraAnuladas(){
        return EmisionCol::listarNominasEmisionCabeceraAnuladas();
    }
}