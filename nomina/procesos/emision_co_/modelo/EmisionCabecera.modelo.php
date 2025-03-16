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
}