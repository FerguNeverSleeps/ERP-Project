<?php
require_once 'conexion.php';

Class Parametros{
    public $grant_type;
    public $client_id;
    public $client_secret;
    public $username;
    public $password;
    function _construct($grant_type = "", $client_id = "",  $client_secret = "",  $username = "",  $password = ""){
        $this->grant_type = $grant_type;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->username = $username;
        $this->password = $password;

    }
    static public function getParametros(){
        $conexion = Conexion::conectar();
        $sql = "SELECT * FROM emision_col_parametros; ";
        $select = $conexion->prepare($sql);
        $select -> execute();
        $parametros = $select ->fetchAll(PDO::FETCH_ASSOC);
        return $parametros[0];

    }
    static public function updateContador($contador){
        $conexion = Conexion::conectar();
        $sql = "UPDATE emision_col_parametros SET correlativo_contador = :contador ; ";
        $select = $conexion->prepare($sql);
        $select -> bindParam(":contador" ,  $contador, PDO::PARAM_INT);
        $select -> execute();
        return $select;

    }
    static public function updateToken( $bearer_token ){
        $conexion = Conexion::conectar();
        $sql = "UPDATE emision_col_parametros SET bearer_token = :bearer_token; ";
        $select = $conexion->prepare($sql);
        $select -> bindParam(":bearer_token" ,  $bearer_token , PDO::PARAM_STR);
        $select -> execute();
        return $select;

    }
    static public function insertEmisionCabecera( $datos ){
        $conexion = Conexion::conectar();
        $conexion->beginTransaction();
        $sql_insert = "INSERT INTO emision_col_cabecera( 
        fecha_inicio,
        fecha_fin,
        mes,
        anio,
        descripcion,
        estatus,
        fecha_creacion,
        usuario_creacion )
        VALUES
            ( :fecha_inicio,
             :fecha_fin,
             :mes,
             :anio,
             :descripcion,
             :estatus,
             now( ),
             :usuario 
        
        );";
        $select = $conexion->prepare($sql_insert);
        $select -> bindParam(":fecha_inicio" ,  $datos["fecha_inicio"], PDO::PARAM_STR);
        $select -> bindParam(":fecha_fin" ,  $datos["fecha_fin"], PDO::PARAM_STR);
        $select -> bindParam(":mes" ,  $datos["mes"], PDO::PARAM_STR);
        $select -> bindParam(":anio" ,  $datos["anio"], PDO::PARAM_STR);
        $select -> bindParam(":descripcion" ,  $datos["descripcion"], PDO::PARAM_STR);
        $select -> bindParam(":estatus" ,  $datos["estatus"], PDO::PARAM_STR);
        $select -> bindParam(":usuario" ,  $datos["usuario"], PDO::PARAM_STR);

        $select -> execute();
        $id = $conexion->lastInsertId();

        $conexion->commit();
        return $id;
    }
    static public function insertEmisionDetalleRequest( $datos ){
        $conexion = Conexion::conectar();
        $conexion->beginTransaction();
        $sql_insert = "INSERT INTO emision_col_detalle_request( 
            id_cabecera,
            consecutivoDocumentoNom,
            deducciones,
            devengados,
            documentosReferenciadosNom,
            extrasNom,
            fechaEmisionNom,
            novedad ,
            lugarGeneracionXML ,
            pagos ,
            periodoNomina ,
            periodos ,
            rangoNumeracionNom ,
            redondeo ,
            tipoDocumentoNom ,
            tipoMonedaNom ,
            tipoNota ,
            totalComprobante ,
            totalDeducciones ,
            totalDevengados ,
            trm ,
            trabajador ,
            ficha ,
            cedula
        )
        VALUES
            ( 
             :id_cabecera,
             :consecutivoDocumentoNom,
             :deducciones,
             :devengados,
             :documentosReferenciadosNom,
             :extrasNom,
             :fechaEmisionNom,
             :novedad ,
             :lugarGeneracionXML ,
             :pagos ,
             :periodoNomina ,
             :periodos ,
             :rangoNumeracionNom ,
             :redondeo ,
             :tipoDocumentoNom ,
             :tipoMonedaNom ,
             :tipoNota ,
             :totalComprobante ,
             :totalDeducciones ,
             :totalDevengados ,
             :trm ,
             :trabajador ,
             :ficha ,
             :cedula
        
        );";
        $select = $conexion->prepare($sql_insert);
        $select -> bindParam(":id_cabecera" ,  $datos["id_cabecera"], PDO::PARAM_STR);
        $select -> bindParam(":consecutivoDocumentoNom" ,  $datos["consecutivoDocumentoNom"], PDO::PARAM_STR);
        $select -> bindParam(":deducciones" ,  $datos["deducciones"], PDO::PARAM_STR);
        $select -> bindParam(":devengados" ,  $datos["devengados"], PDO::PARAM_STR);
        $select -> bindParam(":documentosReferenciadosNom" ,  $datos["documentosReferenciadosNom"], PDO::PARAM_STR);
        $select -> bindParam(":extrasNom" ,  $datos["extrasNom"], PDO::PARAM_STR);
        $select -> bindParam(":fechaEmisionNom" ,  $datos["fechaEmisionNom"], PDO::PARAM_STR);
        $select -> bindParam(":novedad" ,  $datos["novedad"], PDO::PARAM_STR);
        $select -> bindParam(":lugarGeneracionXML" ,  $datos["lugarGeneracionXML"], PDO::PARAM_STR);
        $select -> bindParam(":pagos" ,  $datos["pagos"], PDO::PARAM_STR);
        $select -> bindParam(":periodoNomina" ,  $datos["periodoNomina"], PDO::PARAM_STR);
        $select -> bindParam(":periodos" ,  $datos["periodos"], PDO::PARAM_STR);
        $select -> bindParam(":rangoNumeracionNom" ,  $datos["rangoNumeracionNom"], PDO::PARAM_STR);
        $select -> bindParam(":redondeo" ,  $datos["redondeo"], PDO::PARAM_STR);
        $select -> bindParam(":tipoDocumentoNom" ,  $datos["tipoDocumentoNom"], PDO::PARAM_STR);
        $select -> bindParam(":tipoMonedaNom" ,  $datos["tipoMonedaNom"], PDO::PARAM_STR);
        $select -> bindParam(":tipoNota" ,  $datos["tipoNota"], PDO::PARAM_STR);
        $select -> bindParam(":totalComprobante" ,  $datos["totalComprobante"], PDO::PARAM_STR);
        $select -> bindParam(":totalDeducciones" ,  $datos["totalDeducciones"], PDO::PARAM_STR);
        $select -> bindParam(":totalDevengados" ,  $datos["totalDevengados"], PDO::PARAM_STR);
        $select -> bindParam(":trm" ,  $datos["trm"], PDO::PARAM_STR);
        $select -> bindParam(":trabajador" ,  $datos["trabajador"], PDO::PARAM_STR);
        $select -> bindParam(":ficha" ,  $datos["ficha"], PDO::PARAM_STR);
        $select -> bindParam(":cedula" ,  $datos["cedula"], PDO::PARAM_STR);

        $select -> execute();
        $id = $conexion->lastInsertId();

        $conexion->commit();
        return $id;

    }
    static public function insertEmisionDetalleResponse( $datos ){
        $conexion = Conexion::conectar();
        $conexion->beginTransaction();
        $sql_insert = "INSERT INTO emision_col_detalle_response( 
            id_cabecera,
            id_detalle_request,
            codigo,
            mensaje,
            resultado,
            consecutivoDocumento,
            cune,
            trackId ,
            reglasNotificacionesEmision ,
            reglasNotificacionesDIAN ,
            reglasRechazoEmision ,
            reglasRechazoDIAN ,
            nitEmpleador ,
            nitEmpleado ,
            idSoftware ,
            qr ,
            esvalidoDIAN ,
            xml ,
            ficha ,
            cedula
        )
        VALUES
            ( 
             :id_cabecera,
             :id_detalle_request,
             :codigo,
             :mensaje,
             :resultado,
             :consecutivoDocumento,
             :cune,
             :trackId ,
             :reglasNotificacionesEmision ,
             :reglasNotificacionesDIAN ,
             :reglasRechazoEmision ,
             :reglasRechazoDIAN ,
             :nitEmpleador ,
             :nitEmpleado ,
             :idSoftware ,
             :qr ,
             :esvalidoDIAN ,
             :xml ,
             :ficha ,
             :cedula
        
        );";
        $select = $conexion->prepare($sql_insert);
        $select -> bindParam(":id_cabecera" ,  $datos["id_cabecera"], PDO::PARAM_STR);
        $select -> bindParam(":id_detalle_request" ,  $datos["id_detalle_request"], PDO::PARAM_STR);
        $select -> bindParam(":codigo" ,  $datos["codigo"], PDO::PARAM_STR);
        $select -> bindParam(":mensaje" ,  $datos["mensaje"], PDO::PARAM_STR);
        $select -> bindParam(":resultado" ,  $datos["resultado"], PDO::PARAM_STR);
        $select -> bindParam(":consecutivoDocumento" ,  $datos["consecutivoDocumento"], PDO::PARAM_STR);
        $select -> bindParam(":cune" ,  $datos["cune"], PDO::PARAM_STR);
        $select -> bindParam(":trackId" ,  $datos["trackId"], PDO::PARAM_STR);
        $select -> bindParam(":reglasNotificacionesEmision" ,  $datos["reglasNotificacionesEmision"], PDO::PARAM_STR);
        $select -> bindParam(":reglasNotificacionesDIAN" ,  $datos["reglasNotificacionesDIAN"], PDO::PARAM_STR);
        $select -> bindParam(":reglasRechazoEmision" ,  $datos["reglasRechazoEmision"], PDO::PARAM_STR);
        $select -> bindParam(":reglasRechazoDIAN" ,  $datos["reglasRechazoDIAN"], PDO::PARAM_STR);
        $select -> bindParam(":nitEmpleador" ,  $datos["nitEmpleador"], PDO::PARAM_STR);
        $select -> bindParam(":nitEmpleado" ,  $datos["nitEmpleado"], PDO::PARAM_STR);
        $select -> bindParam(":idSoftware" ,  $datos["idSoftware"], PDO::PARAM_STR);
        $select -> bindParam(":qr" ,  $datos["qr"], PDO::PARAM_STR);
        $select -> bindParam(":esvalidoDIAN" ,  $datos["esvalidoDIAN"], PDO::PARAM_STR);
        $select -> bindParam(":xml" ,  $datos["xml"], PDO::PARAM_STR);
        $select -> bindParam(":ficha" ,  $datos["ficha"], PDO::PARAM_STR);
        $select -> bindParam(":cedula" ,  $datos["cedula"], PDO::PARAM_STR);

        $select -> execute();
        $id = $conexion->lastInsertId();

        $conexion->commit();
        return $id;

    }
    
    static public function ActualizarNominaIndivual( $data ){

        $conexion = Conexion::conectar();
        $conexion->beginTransaction();

        $sql = "UPDATE emision_col_detalle_response  
        SET cune                 = :cune,
            mensaje              = :mensaje,
            resultado            = :resultado,
            reglasRechazoEmision = :reglasRechazoEmision,
            trackId              = :trackId,
            reglasRechazoDIAN    = :reglasRechazoDIAN,
            qr                   = :qr,
            esvalidoDIAN         = :esvalidoDIAN
        WHERE id_detalle_request = :id_detalle_request ; ";
        $update = $conexion->prepare($sql);
        $update -> bindParam(":id_detalle_request" , $data["id_detalle_request"] , PDO::PARAM_STR);
        $update -> bindParam(":cune" ,  $data["cune"], PDO::PARAM_STR);
        $update -> bindParam(":mensaje" ,  $data["mensaje"], PDO::PARAM_STR);
        $update -> bindParam(":resultado" ,  $data["resultado"], PDO::PARAM_STR);
        $update -> bindParam(":reglasRechazoEmision" ,  $data["reglasRechazoEmision"], PDO::PARAM_STR);
        $update -> bindParam(":trackId" ,  $data["trackId"], PDO::PARAM_STR);
        $update -> bindParam(":reglasRechazoDIAN" ,  $data["reglasRechazoDIAN"], PDO::PARAM_STR);
        $update -> bindParam(":qr" ,  $data["qr"], PDO::PARAM_STR);
        $update -> bindParam(":esvalidoDIAN" ,  $data["esvalidoDIAN"], PDO::PARAM_INT);
        $update -> execute();
        $conexion->commit();
        return $update;

    }
    
    static public function ActualizarEstatusCabecera( $id_cabecera ){
        $conexion = Conexion::conectar();
        $conexion->beginTransaction();

        $sql = "UPDATE emision_col_cabecera  
        SET 
            estatus         = '1'
        WHERE id_cabecera = :id_cabecera ; ";
        $update = $conexion->prepare($sql);
        $update -> bindParam(":id_cabecera" , $id_cabecera , PDO::PARAM_STR);
        $update -> execute();
        $conexion->commit();
        return $update;

    }

    
    static public function GetDatosCabecera( $id_cabecera ){
        $conexion = Conexion::conectar();
        $sql = "SELECT *, cast(fecha_inicio as date) fecha_ini,  cast(fecha_fin as date) fecha_final from emision_col_cabecera  
        WHERE id_cabecera = :id_cabecera ; ";
        $select = $conexion->prepare($sql);
        $select -> bindParam(":id_cabecera" , $id_cabecera , PDO::PARAM_STR);
        $select -> execute();
        $parametros = $select ->fetchAll(PDO::FETCH_ASSOC);
        return $parametros[0];

    }

}