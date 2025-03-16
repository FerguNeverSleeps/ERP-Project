<?php
require_once "conexion.php";
Class NominaModelo{
    static public function listarAnioNomina()
    {      
 		 
		$conex = Conexion::conectar();
        $select =  $conex->prepare("SELECT DISTINCT(YEAR(fechapago)) as anio FROM nom_nominas_pago WHERE  `fechapago` !=  '0000-00-00';");
        $select -> execute();
        return $select -> fetchAll( PDO::FETCH_ASSOC );
    }
    static public function consultaConceptos($datos){
        
        $consulta_conceptos="SELECT
            SUM( nm.monto ) AS suma,
            nm.codcon,
            nm.descrip,
            nm.tipcon 
        FROM
            nom_movimientos_nomina AS nm
            LEFT JOIN nomconceptos AS c ON ( nm.codcon = c.codcon ) 
        WHERE
            nm.anio=:anio AND 
            nm.mes=:mes AND 
            nm.tipnom=:codtip AND 
            nm.ficha=:ficha AND 
            nm.cedula=:cedula AND 
            nm.monto>0 
        GROUP BY 
            nm.codcon 
        ORDER BY 
            nm.codcon;";
                
 		$stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_conceptos );
 		$select -> bindParam(":anio" , $datos["anio"], PDO::PARAM_STR);
 		$select -> bindValue(":mes" , $datos["mes"], PDO::PARAM_INT);
 		$select -> bindValue(":codtip" , $datos["codtip"], PDO::PARAM_INT);
 		$select -> bindValue(":ficha" , $datos["ficha"], PDO::PARAM_INT);
 		$select -> bindParam(":cedula" , $datos["cedula"], PDO::PARAM_STR);
         

 		return $select ->fetchAll(PDO::FETCH_ASSOC);
    }
    static public function GetNominaMesAnio($datos){
        $consulta_nomina="SELECT
            fecha,
            fechapago,
            periodo_ini,
            periodo_fin, 
            mes, 
            anio,
            codnom,
            tipnom

        FROM
            nom_nominas_pago
        WHERE
           periodo_ini >= :fecha_ini AND periodo_fin <= :fecha_fin;";
 		$stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_nomina );
 		$select -> bindParam(":fecha_ini" , $datos->fecha_inicio, PDO::PARAM_STR);
 		$select -> bindValue(":fecha_fin" , $datos->fecha_fin, PDO::PARAM_STR);
        $select -> execute();
        $retur = $select ->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
 		return $retur;
    }
}