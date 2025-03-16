<?php
Class EmisionCol
{
    static public function listarNominasEmision(){

        $stmt = Conexion::conectar();
        $sql  ="SELECT * 
        FROM emision_col_configuracion 
        ORDER by tipo ASC, nombre ASC";
        $stm->prepare("$sql");        

        $stmt -> execute();

        return $stmt ->fetchAll(PDO::FETCH_ASSOC);
        
    }
    static public function listarNominasEmisionCabecera(){

        $stmt = Conexion::conectar();
        $sql_procesadas = "SELECT *
            FROM   nomina_electronica_cabecera
            WHERE  estatus='1' AND tipo='1'
        ORDER BY fecha_creacion DESC";
        $stm->prepare("$sql");        

        $stmt -> execute();

        return $stmt ->fetchAll(PDO::FETCH_ASSOC);
        
    }
    static public function listarNominasEmisionAnuladas(){

        $stmt = Conexion::conectar();
        $sql_procesadas = "SELECT *
            FROM   nomina_electronica_cabecera
            WHERE  estatus='2' AND tipo='1'
        ORDER BY fecha_creacion DESC";
        $stm->prepare("$sql");        

        $stmt -> execute();

        return $stmt ->fetchAll(PDO::FETCH_ASSOC);
        
    }
}