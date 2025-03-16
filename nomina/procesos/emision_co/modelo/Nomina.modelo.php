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
        fechapago >= :fecha_ini AND fechapago <= :fecha_fin AND status = 'C' AND status_dian = '0';";
 		$stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_nomina );
 		$select -> bindParam(":fecha_ini" , $datos->fecha_inicio, PDO::PARAM_STR);
 		$select -> bindValue(":fecha_fin" , $datos->fecha_fin, PDO::PARAM_STR);
        $select -> execute();
        $retur = $select ->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
 		return $retur;
    }
    static public function GetNominasTrabajadorMesAnio($datos){
        $consulta_nomina="SELECT
            nmn.ficha, 
            GROUP_CONCAT(DISTINCT nmn.codnom) nominas

        FROM
            nom_nominas_pago np
        INNER JOIN 
            nom_movimientos_nomina nmn ON np.codnom = nmn.codnom and np.tipnom = nmn.tipnom
        WHERE
            np.fechapago >= :fecha_ini AND np.fechapago <= :fecha_fin AND np.status = 'C' AND np.status_dian = '0' AND nmn.ficha = :ficha;";
 		$stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_nomina );
 		$select -> bindParam(":fecha_ini" , $datos->fecha_inicio, PDO::PARAM_STR);
 		$select -> bindValue(":fecha_fin" , $datos->fecha_fin, PDO::PARAM_STR);
 		$select -> bindValue(":ficha" , $datos->ficha, PDO::PARAM_STR);
        $select -> execute();
        $retur = $select ->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
 		return $retur[0];
    }
    static public function GetNominasReenviarTrabajadorMesAnio($datos){
        $consulta_nomina="SELECT
            nmn.ficha, 
            GROUP_CONCAT(DISTINCT nmn.codnom) nominas

        FROM
            nom_nominas_pago np
        INNER JOIN 
            nom_movimientos_nomina nmn ON np.codnom = nmn.codnom and np.tipnom = nmn.tipnom
        WHERE
            np.fechapago >= :fecha_ini AND np.fechapago <= :fecha_fin AND np.status = 'C' AND np.status_dian = '0' AND nmn.ficha = :ficha;";
        $stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_nomina );
        $select -> bindParam(":fecha_ini" , $datos["fecha_ini"], PDO::PARAM_STR);
        $select -> bindValue(":fecha_fin" , $datos["fecha_final"], PDO::PARAM_STR);
        $select -> bindValue(":ficha" , $datos["ficha"], PDO::PARAM_STR);
        $select -> execute();
        $retur = $select ->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        return $retur[0];
    }
    static public function GetNominasPeriodo($datos){
        $consulta_nomina="SELECT
            GROUP_CONCAT(DISTINCT nmn.codnom) nominas

        FROM
            nom_nominas_pago np
        INNER JOIN 
            nom_movimientos_nomina nmn ON np.codnom = nmn.codnom and np.tipnom = nmn.tipnom
        WHERE
            np.fechapago >= :fecha_ini AND np.fechapago <= :fecha_fin AND np.status = 'C' AND np.status_dian = '0';";
 		$stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_nomina );
 		$select -> bindParam(":fecha_ini" , $datos->fecha_inicio, PDO::PARAM_STR);
 		$select -> bindValue(":fecha_fin" , $datos->fecha_fin, PDO::PARAM_STR);
        $select -> execute();
        $retur = $select ->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
 		return $retur[0];
    }
    static public function GetNominasReenviarPeriodo($datos){
        $consulta_nomina="SELECT
            GROUP_CONCAT(DISTINCT nmn.codnom) nominas

        FROM
            nom_nominas_pago np
        INNER JOIN 
            nom_movimientos_nomina nmn ON np.codnom = nmn.codnom and np.tipnom = nmn.tipnom
        WHERE
            np.fechapago >= :fecha_ini AND np.fechapago <= :fecha_fin AND np.status = 'C' ;";
        $stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_nomina );
        $select -> bindParam(":fecha_ini" , $datos["fecha_ini"], PDO::PARAM_STR);
        $select -> bindValue(":fecha_fin" , $datos["fecha_final"], PDO::PARAM_STR);
        $select -> execute();
        $retur = $select ->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        return $retur[0];
    }
    static public function  ActualizarStatusEnvioDian( $datos,$tipnom, $status )
    {
        $status = ($status)? 1:0;
         $consulta_nomina="UPDATE nom_nominas_pago
        SET status_dian = :status
        WHERE codnom IN (".$datos.") AND tipnom = :tipnom;";
        file_put_contents("1sql.log", $consulta_nomina);
 		$stmt = Conexion::conectar();
        $select = $stmt ->prepare( $consulta_nomina );
 		$select -> bindValue(":tipnom" , $tipnom , PDO::PARAM_STR);
 		$select -> bindValue(":status" , $status , PDO::PARAM_STR);
        $select -> execute();
        $retur = $select ->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
 		return $retur;

    }
}