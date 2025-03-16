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
    public $tipnom;
    public $ficha;
    public $id_cabecera;
    public $id_detalle;

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

        $datos_trabajador =  $Trabajador::GetDatosTrabajadorNomina( $this );
        $nominas_periodo = $Nomina::GetNominasPeriodo($this);
        //
        if(!$datos_trabajador)
            return array("error" =>0);
        if(!$nominas_periodo)
            return array("error" =>0);
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
        foreach ($datos_trabajador as $key1 => $trabajador)  {
            $this->ficha = $trabajador["ficha"];
            $nominas_trabajador = $Nomina::GetNominasTrabajadorMesAnio( $this );
            //foreach ($nominas_mensuales as $key => $nomina) {
                
            $datos_parametros = $Parametros::getParametros();
            $incremento_contador = ((int)$datos_parametros["correlativo_contador"]) + 1;
            $array_nomina = array(
                "ficha"     => $trabajador["ficha"],
                "tipnom"    => $this->tipnom,
                "codnom"    => $nominas_trabajador["nominas"]
            );
            $total_devengado = $Trabajador::GetDevengados( $array_nomina );
            $total_deducciones= $Trabajador::GetDeducciones( $array_nomina );

            $salario_integral = $total_devengado["monto"] - $total_deducciones["monto"];
            /* DATOS DEl PERIODO DE LA  NÓMINA */
            $datos_periodo = array(
                "fecha_ingreso"            => $trabajador["fecing"],
                "fecha_liquidacion_inicio" => $this->fecha_inicio,
                "fecha_liquidacion_fin"    => $this->fecha_fin,
                "tiempo_laborado"          => 30,
                "fecha_gen"                => $this->fecha_fin
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
                "ambiente"       => strval($datos_parametros["ambiente"]),
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
                "tipo_trabajador"    => $trabajador["t_trabajador"],
                "subtipo_trabajador" =>  $trabajador["st_contrato"],
                "alto_riesgo"        => $trabajador["t_alto_riesgo"],
                "tipo_documento"     => $trabajador["t_documento"],
                "numero_documento"   => $trabajador["cedula"],
                "primer_apellido"    => $trabajador["apellidos"],
                "segundo_apellido"   => $trabajador["apellido_materno"],
                "primer_nombre"      => $trabajador["nombres"],
                "otros_nombres"      => $trabajador["nombres2"],
                "pais_trabajo"       => $datos_parametros["pais_empleador"],
                "municipio_trabajo"  => $datos_parametros["mun_empleador"],
                "direccion_trabajo"  => $datos_parametros["dir_empleador"],
                "salario_integral"   => $trabajador["t_salario_integral"],
                "tipo_contrato"      => $trabajador["t_contrato"],
                "sueldo"             => (int)$trabajador["suesal"],
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
            $array_transporte = [];
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
                            "sueldo_trabajado" => (int)$monto["monto"]
                        );
                        //array_push($array_devengados, $datos_devengados);
                        $array_devengados["basico"] = $datos_devengados;
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
                        //array_push($array_devengados, $datos_bonificaciones);
                        $array_devengados["bonificaciones"] = $datos_bonificaciones;
                        break;
                    }
                    case 'comisiones':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_comisiones = $valor2["monto"];
                        //array_push($array_devengados, $datos_comisiones);
                        $array_devengados["comisiones"] = $datos_comisiones;
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
                        //array_push($array_devengados, $datos_horasExtras);
                        $array_devengados["horasExtras"] = $datos_horasExtras;
                        break;
                    }

                    case 'auxilios':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_transporte = array(
                            
                            "auxilio" => (int)$monto["monto"]
                        );
                        //array_push($array_devengados, $datos_transporte);
                        $array_devengados["transporte"] = $datos_transporte;
                        break;
                    }          

                    case 'incapacidades':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_incapacidades = array(
                            
                            "pago" => (int)$monto["monto"],
                            "tipo" => 1,
                            "cantidad" => (int)$monto["valor"]
                        );
                        //array_push($array_devengados, $datos_incapacidades);
                        $array_devengados["incapacidades"] = $datos_incapacidades;
                        break;
                    }              

                    case 'cesantias':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_cesantias = array(
                            
                            "pago" => $monto["monto"]
                        );
                        $array_devengados["cesantias"]["pago"] = $monto["monto"];
                        //array_push($array_devengados , $datos_cesantias);
                        break;
                    } 
                    case 'intereses_cesantias':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $array_nomina2 = $array_nomina;
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $porcentaje = 12;
                        $datos_cesantias = array(
                            
                            "porcentaje"  => (int)$porcentaje,
                            "pago_intereses" => $pago_intereses
                        );
                        $array_devengados["cesantias"]["porcentaje"] = $porcentaje;
                        $array_devengados["cesantias"]["pago_intereses"] = $monto;
                        //array_push($array_devengados , $datos_cesantias);
                        break;
                    }
                    

                    case 'primas':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_primas = array(
                            "cantidad" =>  (int)$monto["valor"],
                            "pago" => (int)$monto["monto"]
                        );
                        //array_push($array_devengados, $datos_primas);
                        $array_devengados["primas"] = $datos_primas;
                        break;
                    }                       

                    case 'vacaciones':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_vacaciones = array(
                            
                            "comunes" => array(
                                "fecha_inicio" =>$this->fecha_inicio,
                                "fecha_fin" => $this->fecha_fin,
                                "cantidad" =>  (int)$monto["valor"],
                                "pago" => (int)$monto["monto"]
                            )
                            /*,
                            "compensadas" => array(
                                "cantidad" =>  (int)$monto["valor"],
                                "pago" => $monto["monto"]
                            ),*/
                        );
                        //array_push($array_devengados, $datos_vacaciones);
                        $array_devengados["vacaciones"] = $datos_vacaciones;
                        break;
                    }
                    default:
                        break;
                }
            }

            $Devengados = $PayrollController->setDevengados($array_devengados);

            $DatosConceptos = $Trabajador::GetDatosConceptosDeducciones();
            $array_deducciones = $Salud = $FondoPension= [];
            foreach($DatosConceptos as $key2 => $valor2){               

                switch ( $valor2["nombre"] ) {
                    case 'salud':{
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'D');
                        /* DATOS DE SALUD */
                        $datos_salud = array(
                            
                            "porcentaje" => 4,
                            "deduccion"  => (int)$monto["monto"]
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
                            
                            "porcentaje" => 4,
                            "deduccion"  => (int)$monto["monto"]
                        );    
                        $FondoPension = $PayrollController->SetFondoPension($datos_FondoPension);
                        break;
                    }
                    case 'pagosTerceros':
                    {
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        /* DATOS DE SALUD */
                        $monto = $Trabajador::GetMontoConcepto($array_nomina,  'D');
                        
            
                        /* DATOS DE FONDO PENSION */
                        $datos_pagos_terceros = array(
                            
                            ($monto["monto"] != null) ? (int)$monto["monto"] : 0
                        );    
                        break;
                    }
                    case 'otrasDeducciones':
                    {
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        /* DATOS DE SALUD */
                        $monto = $Trabajador::GetMontoConcepto($array_nomina,  'D');
                        
            
                        /* DATOS DE FONDO PENSION */
                        if($monto["monto"] != null){
                            $datos_otras_deducciones = array(
                                
                                (int)$monto["monto"] 
                            );    

                        }
                        break;
                    }
                }
                    
            }

            
            $Deducciones = array(
                "salud" => $Salud,
                "fondo_pension"=> $FondoPension,
                "pagos_terceros"=> $datos_pagos_terceros,
                "otras_deducciones"=> $datos_otras_deducciones
            );
            /*if( $Deducciones["pagos_terceros"] == "0.00" AND $Deducciones["pagos_terceros"]== null){
                unset($Deducciones["pagos_terceros"]);
            }*/
            if( $Deducciones["otras_deducciones"][0] == "0.00" OR $Deducciones["otras_deducciones"][0] == null){
                unset($Deducciones["otras_deducciones"]);
            }
            if(!isset($Deducciones["pagos_terceros"]) OR $Deducciones["pagos_terceros"] == "0.00" OR $Deducciones["pagos_terceros"] == null){
                unset($Deducciones["pagos_terceros"]) ;
            }
            $transporte = array($array_transporte);
            
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
                "fechas_pago"          => array($this->fecha_fin),
                "devengados"           => $Devengados,
                "deducciones"          => $Deducciones,
                "redondeo"             => 0,
                "devengados_total"     => (int)$total_devengado["monto"],
                "deducciones_total"    => (int)$total_deducciones["monto"],
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
            sleep(1);
            $response = json_decode($respuesta, true);
            $archivo = $datos_parametros["nit_empleador"]."_".$datos_parametros["correlativo_prefijo"]."_".$datos_parametros["correlativo_contador"]."_".$trabajador["ficha"]."_request.txt";
            $archivo2 = $datos_parametros["nit_empleador"]."_".$datos_parametros["correlativo_prefijo"]."_".$datos_parametros["correlativo_contador"]."_".$trabajador["ficha"].".txt";
            $ruta1 = "request/".$archivo;
            $ruta2 = "response/".$archivo2;
            file_put_contents($ruta1, $enviar);
            file_put_contents($ruta2, $respuesta);
            //Se obtiene la nómina
            $array_detalle_request = array(
                "id_cabecera"                => $id_cabecera,
                "consecutivoDocumentoNom"    => $NumeroSecuenciaXML["consecutivo"],
                "deducciones"                => $total_deducciones["monto"],
                "devengados"                 => $total_devengado["monto"],   
                "documentosReferenciadosNom" => "",
                "extrasNom"                  => "",
                "fechaEmisionNom"            => $this->fecha_fin,
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
                "totalComprobante"           => $salario_integral,
                "totalDeducciones"           => $total_deducciones["monto"]*1.00,
                "totalDevengados"            => $total_devengado["monto"]*1.00,
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
                $esvalidoDIAN                = (isset($response['esvalidoDIAN'])) ? $response['esvalidoDIAN']: 0;
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
                $status = 1;
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
                $consulta = TokenControlador::consultarNominaIndividual( $response["id"] );
                $response["error"] = 0;
                $response["codigo"] = 400;
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
                $esvalidoDIAN                = (isset($response['esvalidoDIAN'])) ? $response['esvalidoDIAN']: 0;
                $xml                         = (isset($response['xml'])) ? $response['xml']: '';
                $array_detalle_response = array(
                    "id_cabecera"                 => $id_cabecera,
                    "id_detalle_request"          => $id_detalle_request,
                    "codigo"                      => 400,
                    "mensaje"                     => $response['message'],
                    "resultado"                   => json_encode( $response['errors']),
                    "consecutivoDocumento"        => $NumeroSecuenciaXML["consecutivo"],
                    "cune"                        => $cune,
                    "trackId"                     => $consulta["trackId"],
                    "reglasNotificacionesEmision" => $reglasNotificacionesEmision,
                    "reglasNotificacionesDIAN"    => $reglasNotificacionesDIAN,
                    "reglasRechazoEmision"        => json_encode ( $response['errors'] ),
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
                $array_detalle_response["reglasRechazoEmision"] = $response;
                $id_detalle_request = Parametros::insertEmisionDetalleRequest( $array_detalle_request );

                $response["error"] = 0;
                $response["ficha"] = $trabajador["ficha"];
                $resp = json_encode($response);
                $return_nomina[] = $respuesta;
                $status = 0;
            }
            
        }
            
        $Nomina::ActualizarStatusEnvioDian( $nominas_periodo["nominas"], $this->tipnom, $status );
        return $return_nomina;
    }


    public function reenviarNominaIndividual( ){
        $PayrollController = new PayrollControlador();
        $Nomina = new NominaControlador();
        $Parametros = new ParametrosControlador();
        $Trabajador = new TrabajadorControlador();
        $return_nomina = [];
        $datos_request = $Parametros::GetDatosCabecera($this->id_cabecera);
        $datos_trabajador =  $Trabajador::GetDatosReenviarTrabajadorNomina( $datos_request );
        $nominas_periodo = $Nomina::GetNominasReenviarPeriodo($datos_request);
        //
        if(!$datos_trabajador)
            return array("error" =>0);
        if(!$nominas_periodo)
            return array("error" =>0);
        /*$array_cabecera = array(
            "fecha_inicio"   => $datos_request['fecha_ini'],
            "fecha_fin"      => $datos_request['fecha_fin'],
            "usuario"        => $this->usuario,
            "mes"            => $datos_request["mes"],
            "anio"           => $datos_request["anio"],
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "descripcion"    => $data["descripcion"],
            "estatus"        => 0
        );*/
        $id_cabecera = $this->id_cabecera;
        $return_nomina = [];
        foreach ($datos_trabajador as $key1 => $trabajador)  {
            $datos_request["ficha"] = $trabajador["ficha"];
            $nominas_trabajador = $Nomina::GetNominasReenviarTrabajadorMesAnio( $datos_request );
            //foreach ($nominas_mensuales as $key => $nomina) {
                
            $datos_parametros = $Parametros::getParametros();
            $incremento_contador = ((int)$datos_parametros["correlativo_contador"]) + 1;
            $array_nomina = array(
                "ficha"     => $trabajador["ficha"],
                "tipnom"    => $datos_request["tipo"],
                "codnom"    => $nominas_trabajador["nominas"]
            );
            $total_devengado = $Trabajador::GetDevengados( $array_nomina );
            $total_deducciones= $Trabajador::GetDeducciones( $array_nomina );

            $salario_integral = $total_devengado["monto"] - $total_deducciones["monto"];
            /* DATOS DEl PERIODO DE LA  NÓMINA */
            $datos_periodo = array(
                "fecha_ingreso"            => $trabajador["fecing"],
                "fecha_liquidacion_inicio" => $datos_request["fecha_ini"],
                "fecha_liquidacion_fin"    => $datos_request["fecha_final"],
                "tiempo_laborado"          => 30,
                "fecha_gen"                => $datos_request["fecha_final"]
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
                "ambiente"       => strval($datos_parametros["ambiente"]),
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
                "tipo_trabajador"    => $trabajador["t_trabajador"],
                "subtipo_trabajador" =>  $trabajador["st_contrato"],
                "alto_riesgo"        => $trabajador["t_alto_riesgo"],
                "tipo_documento"     => $trabajador["t_documento"],
                "numero_documento"   => $trabajador["cedula"],
                "primer_apellido"    => $trabajador["apellidos"],
                "segundo_apellido"   => $trabajador["apellido_materno"],
                "primer_nombre"      => $trabajador["nombres"],
                "otros_nombres"      => $trabajador["nombres2"],
                "pais_trabajo"       => $datos_parametros["pais_empleador"],
                "municipio_trabajo"  => $datos_parametros["mun_empleador"],
                "direccion_trabajo"  => $datos_parametros["dir_empleador"],
                "salario_integral"   => $trabajador["t_salario_integral"],
                "tipo_contrato"      => $trabajador["t_contrato"],
                "sueldo"             => (int)$trabajador["suesal"],
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
            $array_transporte = [];
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
                            "sueldo_trabajado" => (int)$monto["monto"]
                        );
                        //array_push($array_devengados, $datos_devengados);
                        $array_devengados["basico"] = $datos_devengados;
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
                        //array_push($array_devengados, $datos_bonificaciones);
                        $array_devengados["bonificaciones"] = $datos_bonificaciones;
                        break;
                    }
                    case 'comisiones':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_comisiones = $valor2["monto"];
                        //array_push($array_devengados, $datos_comisiones);
                        $array_devengados["comisiones"] = $datos_comisiones;
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
                        //array_push($array_devengados, $datos_horasExtras);
                        $array_devengados["horasExtras"] = $datos_horasExtras;
                        break;
                    }

                    case 'auxilios':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_transporte = array(
                            
                            "auxilio" => (int)$monto["monto"]
                        );
                        //array_push($array_devengados, $datos_transporte);
                        $array_devengados["transporte"] = $datos_transporte;
                        break;
                    }          

                    case 'incapacidades':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_incapacidades = array(
                            
                            "pago" => (int)$monto["monto"],
                            "tipo" => 1,
                            "cantidad" => (int)$monto["valor"]
                        );
                        //array_push($array_devengados, $datos_incapacidades);
                        $array_devengados["incapacidades"] = $datos_incapacidades;
                        break;
                    }              

                    case 'cesantias':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_cesantias = array(
                            
                            "pago" => $monto["monto"]
                        );
                        $array_devengados["cesantias"]["pago"] = $monto["monto"];
                        //array_push($array_devengados , $datos_cesantias);
                        break;
                    } 
                    case 'intereses_cesantias':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $array_nomina2 = $array_nomina;
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $porcentaje = 12;
                        $datos_cesantias = array(
                            
                            "porcentaje"  => (int)$porcentaje,
                            "pago_intereses" => $pago_intereses
                        );
                        $array_devengados["cesantias"]["porcentaje"] = $porcentaje;
                        $array_devengados["cesantias"]["pago_intereses"] = $monto;
                        //array_push($array_devengados , $datos_cesantias);
                        break;
                    }
                    

                    case 'primas':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_primas = array(
                            "cantidad" =>  (int)$monto["valor"],
                            "pago" => (int)$monto["monto"]
                        );
                        //array_push($array_devengados, $datos_primas);
                        $array_devengados["primas"] = $datos_primas;
                        break;
                    }                       

                    case 'vacaciones':
                    {
                        /* DATOS DE LOS DEVENGADOS */
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'A');
                        $datos_vacaciones = array(
                            
                            "comunes" => array(
                                "fecha_inicio" =>$datos_request["fecha_ini"],
                                "fecha_fin" =>$datos_request["fecha_final"],
                                "cantidad" =>  (int)$monto["valor"],
                                "pago" => (int)$monto["monto"]
                            )
                            /*,
                            "compensadas" => array(
                                "cantidad" =>  (int)$monto["valor"],
                                "pago" => $monto["monto"]
                            ),*/
                        );
                        //array_push($array_devengados, $datos_vacaciones);
                        $array_devengados["vacaciones"] = $datos_vacaciones;
                        break;
                    }
                    default:
                        break;
                }
            }

            $Devengados = $PayrollController->setDevengados($array_devengados);

            $DatosConceptos = $Trabajador::GetDatosConceptosDeducciones();
            $array_deducciones = $Salud = $FondoPension= [];
            foreach($DatosConceptos as $key2 => $valor2){               

                switch ( $valor2["nombre"] ) {
                    case 'salud':{
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        $monto = $Trabajador::GetMontoConcepto($array_nomina, 'D');
                        /* DATOS DE SALUD */
                        $datos_salud = array(
                            
                            "porcentaje" => 4,
                            "deduccion"  => (int)$monto["monto"]
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
                            
                            "porcentaje" => 4,
                            "deduccion"  => (int)$monto["monto"]
                        );    
                        $FondoPension = $PayrollController->SetFondoPension($datos_FondoPension);
                        break;
                    }
                    case 'pagosTerceros':
                    {
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        /* DATOS DE SALUD */
                        $monto = $Trabajador::GetMontoConcepto($array_nomina,  'D');
                        
            
                        /* DATOS DE FONDO PENSION */
                        $datos_pagos_terceros = array(
                            
                            ($monto["monto"] != null) ? (int)$monto["monto"] : 0
                        );    
                        break;
                    }
                    case 'otrasDeducciones':
                    {
                        $array_nomina["concepto"] = $valor2["conceptos"];
                        /* DATOS DE SALUD */
                        $monto = $Trabajador::GetMontoConcepto($array_nomina,  'D');
                        
            
                        /* DATOS DE FONDO PENSION */
                        if($monto["monto"] != null){
                            $datos_otras_deducciones = array(
                                
                                (int)$monto["monto"] 
                            );    

                        }
                        break;
                    }
                }
                    
            }

            
            $Deducciones = array(
                "salud" => $Salud,
                "fondo_pension"=> $FondoPension,
                "pagos_terceros"=> $datos_pagos_terceros,
                "otras_deducciones"=> $datos_otras_deducciones
            );
            /*if( $Deducciones["pagos_terceros"] == "0.00" AND $Deducciones["pagos_terceros"]== null){
                unset($Deducciones["pagos_terceros"]);
            }*/
            if( $Deducciones["otras_deducciones"][0] == "0.00" OR $Deducciones["otras_deducciones"][0] == null){
                unset($Deducciones["otras_deducciones"]);
            }
            if(!isset($Deducciones["pagos_terceros"]) OR $Deducciones["pagos_terceros"] == "0.00" OR $Deducciones["pagos_terceros"] == null){
                unset($Deducciones["pagos_terceros"]) ;
            }
            $transporte = array($array_transporte);
            
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
                "fechas_pago"          => array($datos_request["fecha_final"]),
                "devengados"           => $Devengados,
                "deducciones"          => $Deducciones,
                "redondeo"             => 0,
                "devengados_total"     => (int)$total_devengado["monto"],
                "deducciones_total"    => (int)$total_deducciones["monto"],
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
            sleep(1);
            $response = json_decode($respuesta, true);
            $archivo = $datos_parametros["nit_empleador"]."_".$datos_parametros["correlativo_prefijo"]."_".$datos_parametros["correlativo_contador"]."_".$trabajador["ficha"]."_request.txt";
            $archivo2 = $datos_parametros["nit_empleador"]."_".$datos_parametros["correlativo_prefijo"]."_".$datos_parametros["correlativo_contador"]."_".$trabajador["ficha"].".txt";
            $ruta1 = "request/".$archivo;
            $ruta2 = "response/".$archivo2;
            file_put_contents($ruta1, $enviar);
            file_put_contents($ruta2, $respuesta);
            //Se obtiene la nómina
            $array_detalle_request = array(
                "id_cabecera"                => $id_cabecera,
                "consecutivoDocumentoNom"    => $NumeroSecuenciaXML["consecutivo"],
                "deducciones"                => $total_deducciones["monto"],
                "devengados"                 => $total_devengado["monto"],   
                "documentosReferenciadosNom" => "",
                "extrasNom"                  => "",
                "fechaEmisionNom"            => $datos_request["fecha_final"],
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
                "totalComprobante"           => $salario_integral,
                "totalDeducciones"           => $total_deducciones["monto"]*1.00,
                "totalDevengados"            => $total_devengado["monto"]*1.00,
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
                $esvalidoDIAN                = (isset($response['esvalidoDIAN'])) ? $response['esvalidoDIAN']: 0;
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
                $status = 1;
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
                $consulta = TokenControlador::consultarNominaIndividual( $response["id"] );
                $response["error"] = 0;
                $response["codigo"] = 400;
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
                $esvalidoDIAN                = (isset($response['esvalidoDIAN'])) ? $response['esvalidoDIAN']: 0;
                $xml                         = (isset($response['xml'])) ? $response['xml']: '';
                $array_detalle_response = array(
                    "id_cabecera"                 => $id_cabecera,
                    "id_detalle_request"          => $id_detalle_request,
                    "codigo"                      => 400,
                    "mensaje"                     => $response['message'],
                    "resultado"                   => json_encode( $response['errors']),
                    "consecutivoDocumento"        => $NumeroSecuenciaXML["consecutivo"],
                    "cune"                        => $cune,
                    "trackId"                     => $consulta["trackId"],
                    "reglasNotificacionesEmision" => $reglasNotificacionesEmision,
                    "reglasNotificacionesDIAN"    => $reglasNotificacionesDIAN,
                    "reglasRechazoEmision"        => json_encode ( $response['errors'] ),
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
                $array_detalle_response["reglasRechazoEmision"] = $response;
                $id_detalle_request = Parametros::insertEmisionDetalleRequest( $array_detalle_request );

                $response["error"] = 0;
                $response["ficha"] = $trabajador["ficha"];
                $resp = json_encode($response);
                $return_nomina[] = $respuesta;
                $status = 0;
            }
            
        }
            
        $Nomina::ActualizarStatusEnvioDian( $nominas_periodo["nominas"], $this->tipnom, $status );
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
    $payroll->tipnom = $_POST['codtip'];

    $payroll->fecha = $fecha->format('Y-m-d');
    $payroll->hora = $fecha->format('h:i:s');
    $payroll->fecha_format = $fecha->format('Y-m-d h:i:s');
    $payroll->usuario = $_SESSION['usuario'];

    $resp = $payroll -> enviarNominaIndividual($_POST);
    echo json_encode($resp);exit;

}
if(isset($_POST['reenviarNominaIndividual']) AND $_POST['reenviarNominaIndividual'] == "yes"){
    $payroll = new AjaxPayroll();
    $payroll->id_cabecera = $_POST['id_cabecera'];
    $payroll->id_detalle  = $_POST['id_detalle'];
    $resp = $payroll -> reenviarNominaIndividual();
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