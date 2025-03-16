<?php
session_start();
$Modelos = ["Salud","FondoPension","Deducciones","Devengados","Pago","Trabajador","Empleador","InformacionGeneral","ProveedorXML","LugarGeneracionXML","NumeroSecuenciaXML","Periodo", "Token", "Parametros", "Nomina", "Trabajador"];
$Controladores = ["Payroll", "Token", "Parametros", "Nomina", "Trabajador"];

foreach ($Modelos as $key => $value)
{    
    require_once "../modelo/{$value}.modelo.php";
}
foreach ($Controladores as $key => $value)
{   
    require_once "../controlador/{$value}.controlador.php";
}



class AjaxPayroll {
    public $hora;
    public $fecha;
    public $fecha_format;
    public $fecha_inicio;
    public $fecha_fin;
    public $usuario;
    public function format_fecha($fecha){
        $f = explode("/",$fecha);
        return $f[2]."-".$f[1]."-".$f[0];

    }
	public function enviarNominaIndividual( $data ){
        $PayrollController = new PayrollControlador();
        $Nomina = new NominaControlador();
        $Parametros = new ParametrosControlador();
        $Trabajador = new TrabajadorControlador();
        $return_nomina = [];
        $datos_nominas_mensuales["fecha_inicio"] = $this->fecha_inicio;
        $datos_nominas_mensuales["fecha_fin"] = $this->fecha_fin;
        $nominas_mensuales = $Nomina::GetNominaMesAnio( $this );
        $array_cabecera = array(
            "fecha_inicio"   => $this->fecha_inicio,
            "fecha_fin"      => $this->fecha_fin,
            "usuario"        => $this->usuario,
            "mes"            => $data["mes"],
            "anio"           => $data["anio"],
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "descripcion"    => $data["descripcion"],
            "estatus"        => 0
        );
        $id_cabecera = Parametros::insertEmisionCabecera( $array_cabecera );
        
        $return_nomina = [];
        foreach ($nominas_mensuales as $key => $nomina) {
            $datos_trabajador =  $Trabajador::GetDatosTrabajador($nomina["tipnom"]);
            foreach ($datos_trabajador as $key1 => $trabajador) {
                
                $datos_parametros = $Parametros::getParametros();
                $incremento_contador = ((int)$datos_parametros["correlativo_contador"]) + 1;
                $array_nomina = array(
                    "fecha_ini" => $nomina["periodo_ini"],
                    "fecha_fin" => $nomina["periodo_fin"],
                    "ficha"     => $trabajador["ficha"],
                    "tipnom"    => $nomina["tipnom"],
                    "codnom"    => $nomina["codnom"]
                );
                $total_devengado = $Trabajador::GetDevengados( $array_nomina );
                $total_deducciones= $Trabajador::GetDeducciones( $array_nomina );
                $salario_integral = $total_devengado["monto"] - $total_deducciones["monto"];
                /* DATOS DEl PERIODO DE LA  NÓMINA */
                $datos_periodo = array(
                    "fecha_ingreso"            => $trabajador["fecing"],
                    "fecha_liquidacion_inicio" => $nomina["periodo_ini"],
                    "fecha_liquidacion_fin"    => $nomina["periodo_fin"],
                    "tiempo_laborado"          => 30,
                    "fecha_gen"                => $nomina["fechapago"]
                );

                $Periodo = $PayrollController->setPeriodo($datos_periodo);


                /* DATOS DEL NUMERO DE SECUENCIA XML */
                $datos_numero_secuencia = array(
                    "codigo_trabajador"=> $trabajador['ficha'],
                    "prefijo"=> $datos_parametros["correlativo_prefijo"],
                    "consecutivo"=> $datos_parametros["correlativo_contador"]
                );
                
                $NumeroSecuenciaXML = $PayrollController->SetNumeroSecuenciaXML($datos_numero_secuencia);


                /* DATOS DEL LUGAR DE GENERACION XML */
                $datos_lugar_generacion = array(
                    "pais"         => $datos_parametros['pais_empleador'],
                    "departamento" => $datos_parametros['dep_empleador'],
                    "municipio"    => $datos_parametros['mun_empleador'],
                    "idioma"       => "es"
                );
                $LugarGeneracionXML = $PayrollController->SetLugarGeneracionXML($datos_lugar_generacion);


                /* DATOS DEL PROVEEDOR XML */
                $datos_proveedor_xml = array(
                    "razon_social" => $datos_parametros["razonsocial_software"],
                    "nit"          => $datos_parametros["nit_software"],
                    "dv"           => $datos_parametros["dv_software"],
                    "software_id"  => $datos_parametros["id_software"],
                    "software_pin" => $datos_parametros["pin_software"]
                );
                $ProveedorXML = $PayrollController->SetProveedorXML($datos_proveedor_xml);

                /* DATOS DE LA INFORMACIÓN GENERAL */
                $datos_informacion_general = array(
                    "version"       => "1.0",
                    "ambiente"       => "2",
                    "tipo_xml"       => "102",
                    "fecha_gen"      => date("Y-m-d"),
                    "hora_gen"       => date("H:i:s"),
                    "periodo_nomina" => 5,
                    "tipo_moneda"    => "COP",
                    "trm"            => 1
                );
                $InformacionGeneral = $PayrollController->SetInformacionGeneral($datos_informacion_general);


                /* DATOS DEL NUMERO DEL EMPLEADOR */
                $datos_empleador = array( 
                    "razon_social" => $datos_parametros["razonsocial_empleador"],
                    "nit"          => $datos_parametros["nit_empleador"],
                    "dv"           => $datos_parametros["dv_empleador"],
                    "pais"         => $datos_parametros["pais_empleador"],
                    "departamento" => $datos_parametros["dep_empleador"],
                    "municipio"    => $datos_parametros["mun_empleador"],
                    "direccion"    => $datos_parametros["dir_empleador"]
                );
                $Empleador = $PayrollController->SetEmpleador($datos_empleador);
                
                /* DATOS DEL TRABAJADOR */
                $datos_trabajador = array(
                    "tipo_trabajador"    => $trabajador["tipo_trabajador"],
                    "subtipo_trabajador" => $trabajador["subtipo_trabajador"],
                    "alto_riesgo"        => false,
                    "tipo_documento"     => $trabajador["tipo_identificacion"],
                    "numero_documento"   => $trabajador["cedula"],
                    "primer_apellido"    => $trabajador["apellidos"],
                    "segundo_apellido"   => $trabajador["apellido_materno"],
                    "primer_nombre"      => $trabajador["nombres"],
                    "otros_nombres"      => $trabajador["nombres2"],
                    "pais_trabajo"       => $datos_parametros["pais_empleador"],
                    "municipio_trabajo"  => $datos_parametros["mun_empleador"],
                    "direccion_trabajo"  => $datos_parametros["dir_empleador"],
                    "salario_integral"   => false,
                    "tipo_contrato"      => $trabajador["tipo_contrato"],
                    "sueldo"             => $trabajador["suesal"],
                    "codigo_trabajador"  => $trabajador["ficha"]
                );
                $ArrayTrabajador = $PayrollController->SetTrabajador($datos_trabajador);

                switch ($trabajador["forcob"]) {
                    case 'Efectivo':
                        $metodo = "10";
                        break;
                    case 'Cuenta Corriente':
                        $metodo = "20";
                        break;

                    case 'Cuenta Ahorro':
                        $metodo = "20";
                        break;
                    
                    case 'Cheque':
                        $metodo = "20";
                        break;
                    
                    case 'Deposito':
                        $metodo = "20";
                        break;
                    
                    case 'Tarjeta':
                        $metodo = "20";
                        break;
                    
                    default:{
                        $metodo = "10";
                        break;
                    }
                }

                /* DATOS DEL PAGO */
                $datos_pago = array(
                    "forma"=>"1",
                    "metodo"        => $metodo,
                    "banco"         => $trabajador["descripcion_banco"],
                    "tipo_cuenta"   => $trabajador["forcob"],
                    "numero_cuenta" => $trabajador["cuentacob"]
                );
                $Pago = $PayrollController->SetPago($datos_pago);

                $array_devengados = [];
                $DatosConceptos = $Trabajador::GetDatosConceptosDevengos();
                foreach($DatosConceptos as $key2 => $valor2){
                    switch ( $valor2["nombre_corto"] ) {
                        case 'basico':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $datos_devengados = array(
                                
                                "dias_trabajados" => (int)$monto["valor"] ,
                                "sueldo_trabajado" => $monto["monto"]
                            );
                            array_push($array_devengados, $datos_devengados);
                            break;
                        }
                        case 'bonificaciones':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $datos_bonificaciones = array(
                                
                                "s" => $valor2["monto"]
                            );
                            array_push($array_devengados, $datos_bonificaciones);
                            break;
                        }
                        case 'comisiones':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $datos_comisiones = $valor2["monto"];
                            array_push($array_devengados, $datos_comisiones);
                            break;
                        }
                        case 'horasExtras':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $datos_horasExtras = array(
                                
                                "dias_trabajados"  => $monto["valor"],
                                "sueldo_trabajado" => $monto["monto"]
                            );
                            array_push($array_devengados, $datos_horasExtras);
                            break;
                        }

                        case 'transporte':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $datos_transporte = array(
                                
                                "auxilio" => $monto["monto"],
                                "aloj_s"  => 0,
                                "aloj_ns" => 0
                            );
                            array_push($array_devengados, $datos_transporte);
                            break;
                        }                       

                        case 'cesantias':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $array_nomina2 = $array_nomina;
                            $array_nomina2["concepto"] = 96;
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $monto_2 = $Trabajador::GetMontoConcepto($array_nomina2, 'A');
                            $datos_cesantias = array(
                                
                                "cesantias" => $monto["monto"],
                                "porcentaje "  => 1,
                                "intereses_cesantias" => $monto_2["monto"]
                            );
                            array_push($array_devengados , $datos_cesantias);
                            break;
                        } 
                        

                        case 'primas':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $datos_primas = array(
                                "cantidad" =>  (int)$monto["valor"],
                                "pago" => $monto["monto"]
                            );
                            array_push($array_devengados, $datos_primas);
                            break;
                        }                       

                        case 'vacaciones':
                        {
                            /* DATOS DE LOS DEVENGADOS */
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                            $datos_vacaciones = array(
                                
                                "comunes" => array(
                                    "fecha_inicio" =>$nomina["periodo_ini"],
                                    "fecha_fin" => $nomina["periodo_fin"],
                                    "cantidad" =>  (int)$monto["valor"],
                                    "pago" => $monto["monto"]
                                )
                                /*,
                                "compensadas" => array(
                                    "cantidad" =>  (int)$monto["valor"],
                                    "pago" => $monto["monto"]
                                ),*/
                            );
                            array_push($array_devengados, $datos_vacaciones);
                            break;
                        }
                        default:
                            # code...
                            break;
                    }
                }
                $Devengados = $PayrollController->SetDevengados($array_devengados);

                
                $DatosConceptos = $Trabajador::GetDatosConceptosDeducciones();
                $array_deducciones = $Salud = $FondoPension= [];
                
                foreach($DatosConceptos as $key2 => $valor2){               

                    switch ( $valor2["nombre"] ) {
                        case 'salud':{
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            $monto = $Trabajador::GetMontoConcepto($array_nomina, 'D');
                            /* DATOS DE SALUD */
                            $datos_salud = array(
                                
                                "porcentaje" => $monto["valor"],
                                "deduccion"  => $monto["monto"]
                            );
                            $Salud = $PayrollController->SetSalud($datos_salud);  
                        break;}
                        case 'fondosPensiones':
                        {
                            $array_nomina["concepto"] = $valor2["conceptos"];
                            /* DATOS DE SALUD */
                            $monto = $Trabajador::GetMontoConcepto($array_nomina,  'D');
                            /* DATOS DE FONDO PENSION */
                            $datos_FondoPension = array(
                                
                                "porcentaje" => $monto["valor"],
                                "deduccion"  => $monto["monto"]
                            );    
                            $FondoPension = $PayrollController->SetFondoPension($datos_FondoPension);
                            break;
                        }
                    }
                        
                }
  

                
                $Deducciones = array(
                    "salud" => $Salud,
                    "fondo_pension"=> $FondoPension
                );
                //$Deducciones = $PayrollController->SetDeducciones($datos_deducciones);
                //$datos_nomina = $Nomina::consultaConceptos();
                $datos_parametros = $Parametros::getParametros();
                # code...
                
                /* ESTRUCTURA DEL PAYROLL */
                $Payroll = array(
                    "periodo"              => $Periodo,
                    "numero_secuencia_xml" => $NumeroSecuenciaXML,
                    "lugar_generacion_xml" => $LugarGeneracionXML,
                    "proveedor_xml"        => $ProveedorXML,
                    "informacion_general"  => $InformacionGeneral,
                    "empleador"            => $Empleador,
                    "trabajador"           => $ArrayTrabajador,
                    "pago"                 => $Pago,
                    "fechas_pago"          => array($nomina["fechapago"]),
                    "devengados"           => $Devengados,
                    "deducciones"          => $Deducciones,
                    "redondeo"             => 0,
                    "devengados_total"     => $total_devengado["monto"],
                    "deducciones_total"    => $total_deducciones["monto"],
                    "comprobante_total"    => $salario_integral
                );

                /* DATOS DE LA NOMINA INDIVIDUAL */
                $NominaIndividual = array(
                    "has_certificate" => false,
                    "type" => "102",
                    "payroll" => $Payroll
                );
                $enviar = json_encode($NominaIndividual);
                
                $respuesta = TokenControlador::enviarNominaIndividual($enviar);
                $response = json_decode($respuesta, true);
                //Se obtiene la nómina
                $array_detalle_request = array(
                    "id_cabecera"                => $id_cabecera,
                    "consecutivoDocumentoNom"    => $NumeroSecuenciaXML["consecutivo"],
                    "deducciones"                => $total_deducciones["monto"],
                    "devengados"                 => $total_devengado["monto"],   
                    "documentosReferenciadosNom" => "",
                    "extrasNom"                  => "",
                    "fechaEmisionNom"            => $nomina["fechapago"],
                    "novedad"                    => "",
                    "lugarGeneracionXML"         => $datos_parametros['pais_empleador']."-".$datos_parametros['mun_empleador'],
                    "pagos"                      => "",
                    "periodoNomina"              => $datos_informacion_general["periodo_nomina"],
                    "periodos"                   => "",
                    "rangoNumeracionNom"         => $NumeroSecuenciaXML["prefijo"].$NumeroSecuenciaXML["consecutivo"],
                    "redondeo"                   => "",
                    "tipoDocumentoNom"           => $trabajador["tipo_identificacion"],
                    "tipoMonedaNom"              => $datos_informacion_general["tipo_moneda"],
                    "tipoNota"                   => "",
                    "totalComprobante"           => "102",
                    "totalDeducciones"           => $total_devengado["monto"],
                    "totalDevengados"            => $total_deducciones["monto"],
                    "trm"                        => $datos_informacion_general["trm"],
                    "trabajador"                 => $trabajador["apenom"],
                    "ficha"                      => $trabajador["ficha"],
                    "cedula"                     => $trabajador["cedula"]
                );

                if(isset($response["id"]))
                {
                    Parametros::updateContador( $incremento_contador );

                    $consulta = TokenControlador::consultarNominaIndividual( $response["id"] );
                    $response["error"] = 1;
                    $response["ficha"] = $trabajador["ficha"];
                    $return_nomina[] = $response;

                    //Parametros::insertEmisionDetalle($nomina);
                    //Parametros::insertEmisionDetalle($nomina);

                    $id_detalle_request          = Parametros::insertEmisionDetalleRequest( $array_detalle_request );
                    $reglasNotificacionesEmision = (isset($response['reglasNotificacionesEmision']))? $response['reglasNotificacionesEmision']: '';
                    $reglasNotificacionesDIAN    = (isset($response['reglasNotificacionesDIAN'])) ? $response['reglasNotificacionesDIAN']: '';
                    $cune                        = (isset($response['cune'])) ? $response['cune']: '';
                    $reglasRechazoEmision        = (isset($response['reglasRechazoEmision'])) ? $response['reglasRechazoEmision']: '';
                    $reglasRechazoDIAN           = (isset($response['reglasRechazoDIAN'])) ? $response['reglasRechazoDIAN']: '';
                    $qr                          = (isset($response['qr'])) ? $response['qr']: '';
                    $esvalidoDIAN                = (isset($response['esvalidoDIAN'])) ? $response['esvalidoDIAN']: '';
                    $xml                         = (isset($response['xml'])) ? $response['xml']: '';
                    $array_detalle_response = array(
                        "id_cabecera"                 => $id_cabecera,
                        "id_detalle_request"          => $id_detalle_request,
                        "codigo"                      => $response['id'],
                        "mensaje"                     => $response['message'],
                        "resultado"                   => $resultado,
                        "consecutivoDocumento"        => $NumeroSecuenciaXML["consecutivo"],
                        "cune"                        => $cune,
                        "trackId"                     => $consulta["trackId"],
                        "reglasNotificacionesEmision" => $reglasNotificacionesEmision,
                        "reglasNotificacionesDIAN"    => $reglasNotificacionesDIAN,
                        "reglasRechazoEmision"        => $reglasRechazoEmision,
                        "reglasRechazoDIAN"           => $reglasRechazoDIAN,
                        "nitEmpleador"                => $datos_parametros["nit_empleador"],
                        "nitEmpleado"                 => $nitEmpleado,
                        "idSoftware"                  => $datos_parametros["id_software"],
                        "qr"                          => $qr,
                        "esvalidoDIAN"                => $esvalidoDIAN,
                        "xml"                         => $xml,
                        "ficha"                       => $trabajador["ficha"],
                        "cedula"                      => $trabajador["cedula"]
                    );
                    $id_detalle_request = Parametros::insertEmisionDetalleResponse( $array_detalle_response );
                    /*
                    $array_detalle_response = array(
                        "id_detalle_request" => $id_detalle_request,
                        "id_cabecera" => $id_cabecera,
                        "codigo" => $codigo,
                        "mensaje" => $mensaje,
                        "resultado" => $resultado,
                        "consecutivoDocumento" => $consecutivoDocumento,
                        "novedad" => $novedad,
                        "cune" => $cune,
                        "trackId" => $trackId,
                        "periodoNomina" => $periodoNomina,
                        "periodos" => $periodos,
                        "rangoNumeracionNom" => $rangoNumeracionNom,
                        "redondeo" => $redondeo,
                        "tipoDocumentoNom" => $tipoDocumentoNom,
                        "tipoMonedaNom" => $tipoMonedaNom,
                        "nitEmpleador" => $nitEmpleador,
                        "nitEmpleado" => $nitEmpleado,
                        "idSoftware" => $idSoftware,
                        "qr" => $qr,
                        "esvalidoDIAN" => $esvalidoDIAN,
                        "xml" => $xml,
                        "ficha" => $ficha,
                        "cedula" => $cedula
        
                    );
                    Parametros::insertEmisionDetalleResponse( $array_detalle_response );*/
                }
                else
                {
                    $id_detalle_request = Parametros::insertEmisionDetalleRequest( $array_detalle_request );

                    $response["error"] = 0;
                    $response["ficha"] = $trabajador["ficha"];
                    $resp = json_encode($response);
                    $return_nomina[] = $respuesta;
                }EXIT;
            }
        }
        return $return_nomina;
    }
	public function ConsultarNomina( $data ){
        return $data;
    }
}


if(isset($_POST['enviarNominaIndividual']) AND $_POST['enviarNominaIndividual'] == "yes"){
    $payroll = new AjaxPayroll();
    $fecha = new Datetime($payroll->format_fecha($_POST['fecha_fin']));
    $fecha_inicio = new Datetime($payroll->format_fecha($_POST['fecha_inicio']));
    $fecha_fin = new Datetime($payroll->format_fecha($_POST['fecha_fin']));

    $payroll->fecha_inicio = $fecha_inicio->format('Y-m-d');
    $payroll->fecha_fin = $fecha_fin->format('Y-m-d');

    $payroll->fecha = $fecha->format('Y-m-d');
    $payroll->hora = $fecha->format('h:i:s');
    $payroll->fecha_format = $fecha->format('Y-m-d h:i:s');
    $payroll->usuario = $_SESSION['usuario'];

    $resp = $payroll -> enviarNominaIndividual($_POST);
    echo json_encode($resp);exit;

}

if(isset($_POST['ConsultarNomina']) AND $_POST['ConsultarNomina'] == "yes"){
    $payroll = new AjaxPayroll();
    print_r($_POST);exit;

    $fecha = new Datetime($payroll->format_fecha($_POST['fecha_fin']));
    $fecha_inicio = new Datetime($payroll->format_fecha($_POST['fecha_inicio']));
    $fecha_fin = new Datetime($payroll->format_fecha($_POST['fecha_fin']));

    $payroll->fecha_inicio = $fecha_inicio->format('Y-m-d');
    $payroll->fecha_fin = $fecha_fin->format('Y-m-d');

    $payroll->fecha = $fecha->format('Y-m-d');
    $payroll->hora = $fecha->format('h:i:s');
    $payroll->fecha_format = $fecha->format('Y-m-d h:i:s');
    $payroll->usuario = $_SESSION['usuario'];

    $resp = $payroll -> ConsultarNomina($_POST);
    echo json_encode($resp);exit;

}