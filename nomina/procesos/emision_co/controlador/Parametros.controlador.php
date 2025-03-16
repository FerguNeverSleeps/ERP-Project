<?php
Class ParametrosControlador
{
    static public function getParametros(){
        return Parametros::getParametros();
    }
    static public function updateContador( $contador ){
        return Parametros::updateContador( $contador );
    }
    static public function updateToken($token){
        return Parametros::updateToken($token);
    }
    static public function insertEmisionCabecera($cabecera){
        return Parametros::insertEmisionCabecera($cabecera);
    }
    static public function insertEmisionDetalle($detalle){
        return Parametros::insertEmisionDetalle($detalle);
    }
    static public function insertEmisionDetalleRequest($detalle){
        return Parametros::insertEmisionDetalleRequest($detalle);
    }
    static public function insertEmisionDetalleResponse($detalle){
        return Parametros::insertEmisionDetalleResponse($detalle);
    }
    static public function ActualizarNominaIndivual($data){
        return Parametros::ActualizarNominaIndivual($data);
    }
    static public function ActualizarEstatusCabecera($id_cabecera){
        return Parametros::ActualizarEstatusCabecera($id_cabecera);
    } 
    static public function GetDatosCabecera($id_cabecera){
        return Parametros::GetDatosCabecera($id_cabecera);
    } 

}