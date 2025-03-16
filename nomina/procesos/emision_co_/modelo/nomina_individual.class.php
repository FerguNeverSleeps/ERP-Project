<?php

class AMX_NominaElectronicaResponse {
    public $data=[];

    public function __construct($data) {
        $this->data=$data;
    }

     public function get($indice){
        if(!isset($this->data[$indice]))
            return NULL;
        return $this->data[$indice];
    }

    public function get_base64($indice){
        if(!isset($this->data[$indice]))
            return NULL;
        return base64_encode(json_encode($this->data[$indice]));
    }

} 

class AMX_NominaElectronica {

    public $raiz=[];
    public $data=[];
    public $campo=[];
    public $respuesta="";
    public $respuesta_string="";
    public $obj_response=NULL;

    public function __construct($raiz) {
        $this->raiz=$raiz;

        $this->data = [
            "consecutivoDocumentoNom"     => "",
            "deducciones"              => [],
            "devengados"               => [],
            "documentosReferenciadosNom"  => [],
            "extrasNom"                   => NULL,
            "fechaEmisionNom"             => "",
            "notas"                    => [],
            "novedad"                  => "",
            "novedadCUNE"              => "",
            "lugarGeneracionXML"       => [],
            "pagos"                    => [],
            "periodoNomina"            => "",
            "periodos"                 => [],
            "rangoNumeracionNom"          => "",
            "redondeo"                 => "",
            "tipoDocumentoNom"            => "",
            "tipoMonedaNom"               => "",
            "tipoNota"                 => "",
            "totalComprobante"         => "",
            "totalDeducciones"         => "",
            "totalDevengados"          => "",
            "trm"                      => "",
            "trabajador"               => []
        ];

        //definicion del contenido de las subclases

        $this->data["deducciones"]=[
            "afc"                 => "",
            "anticiposNom"           => [],
            "cooperativa"         => "",
            "deuda"               => "",
            "educacion"           => "",
            "extras"              => NULL,
            "embargoFiscal"       => "",
            "fondosPensiones"     => [],
            "fondosSP"            => [],
            "libranzas"           => [],
            "otrasDeducciones"    => [],
            "pagosTerceros"       => [],
            "pensionVoluntaria"   => "",
            "planComplementarios" => "",
            "reintegro"           => "",
            "retencionFuente"     => "",
            "salud"               => [],
            "sanciones"           => [],
            "sindicatos"          => []
        ];

        $this->data["devengados"] =[
            "anticiposNom"                  => [],
            "auxilios"                   => [],
            "apoyoSost"                  => "",
            "basico"                     => [],
            "bonificaciones"             => [],
            "bonifRetiro"                => "",
            "bonoEPCTVs"                 => [],
            "cesantias"                  => [],
            "comisiones"                 => [],
            "compensaciones"             => [],
            "dotacion"                   => "",
            "extras"                     => NULL,
            "horasExtras"                => [],
            "huelgasLegales"             => [],
            "indemnizacion"              => "",
            "incapacidades"              => [],
            "licencias"                  => [
                "extras"                 => NULL,
                "licenciaMP"             => [],
                "licenciaNR"             => [],
                "licenciaR"              => [],
            ],
            "otrosConceptos"             => [],
            "pagosTerceros"              => [],
            "primas"                     => [],
            "reintegro"                  => "",
            "teletrabajo"                => "",
            "transporte"                 => [],
            "vacaciones"                 => [
                "extras"                 => NULL,
                "vacacionesComunes"      => [],
                "vacacionesCompensadas"  => [],
            ]
        ];

        $this->data["documentosReferenciadosNom"]=[];

        $this->data["notas"]=[];

        $this->data["lugarGeneracionXML"]=[
            "departamentoEstado"  => "",
            "extras"              => NULL,
            "idioma"              => "",
            "municipioCiudad"     => "",
            "pais"                => ""
        ];

        $this->data["pagos"]=[];       

        $this->data["periodos"]=[];

        $this->data["trabajador"]=[
            "altoRiesgoPension"               => "",
            "codigoTrabajador"                => "",
            "email"                           => "",
            "extras"                          => NULL,
            "lugarTrabajoDepartamentoEstado"  => "",
            "lugarTrabajoDireccion"           => "",
            "lugarTrabajoMunicipioCiudad"     => "",
            "lugarTrabajoPais"                => "",
            "numeroDocumento"                 => "",
            "otrosNombres"                    => "",
            "primerApellido"                  => "",
            "primerNombre"                    => "",
            "salarioIntegral"                 => "",
            "segundoApellido"                 => "",
            "subTipoTrabajador"               => "",
            "sueldo"                          => "",
            "tipoContrato"                    => "",
            "tipoIdentificacion"              => "",
            "tipoTrabajador"                  => ""
        ];


        //definir los campo disponibles para el nivel mas bajo
        $this->campo["deducciones"]=[
            "anticiposNom"         => ["extras","montoanticipo"],
            "fondosPensiones"   => ["deduccion","extras","porcentaje"],
            "fondosSP"          => ["deduccionSP","deduccionSub","extras","porcentaje","porcentajeSub"],
            "libranzas"         => ["deduccion","descripcion","extras"],
            "otrasDeducciones"  => ["extras","montootraDeduccion"],
            "pagosTerceros"     => ["montopagotercero","extras"],
            "salud"             => ["deduccion","extras","porcentaje"],
            "sanciones"         => ["extras","sancionPublic","sancionPriv"],
            "sindicatos"        => ["deduccion","extras","porcentaje"]
        ];

        $this->campo["devengados"]=[
            "anticiposNom"             => ["extras","montoanticipo"],
            "auxilios"              => ["auxilioNS","auxilioS","extras"],
            "basico"                => ["diasTrabajados","extras","sueldoTrabajado"],
            "bonificaciones"        => ["bonificacionNS","bonificacionS","extras"],
            "bonoEPCTVs"            => ["pagoAlimentacionNS","pagoAlimentacionS","extras","pagoNS","pagoS"],
            "cesantias"             => ["extras","pago","pagoIntereses","porcentaje"],
            "comisiones"            => ["extras","montocomision"],
            "compensaciones"        => ["compensacionE","compensacionO","extras"],
            "horasExtras"           => ["cantidad","extras","horaInicio","horaFin","pago","porcentaje","tipoHorasExtra"],
            "huelgasLegales"        => ["cantidad","extras","fechaInicio","fechaFin"],
            "incapacidades"         => ["cantidad","extras","fechaInicio","fechaFin","pago","tipo"],
            "licenciaMP"            => ["cantidad","extras","fechaInicio","fechaFin","pago"],
            "licenciaNR"            => ["cantidad","extras","fechaInicio","fechaFin","pago"],
            "licenciaR"             => ["cantidad","extras","fechaInicio","fechaFin","pago"],
            "otrosConceptos"        => ["conceptoNS","conceptoS","descripcionConcepto","extras"],
            "pagosTerceros"         => ["montopagotercero","extras"],
            "primas"                => ["cantidad","extras","pago","pagoNS"],
            "transporte"            => ["auxilioTransporte","extras","sueldoTrabajado","viaticoManuAlojNS"],
            "vacacionesComunes"     => ["cantidad","extras","fechaInicio","fechaFin","pago"],
            "vacacionesCompensadas" => ["cantidad","extras","fechaInicio","fechaFin","pago"]
        ];

        $this->campo["documentosReferenciadosNom"]=["cunePred","extrasNom","fechaGenPred","numeroPred"];

        $this->campo["notas"]=["descripcion","extras"];

        $this->campo["lugarGeneracionXML"]=["departamentoEstado","extras","idioma","municipioCiudad","pais"];

        $this->campo["pagos"]=["extras","fechasPagos","metodoDePago","medioPago","nombreBanco","tipoCuenta","numeroCuenta"];   
        $this->campo["fechasPagos"]=["extras","fechapagonomina"];

        $this->campo["periodos"]=["extras","fechaIngreso","fechaLiquidacionInicio","fechaLiquidacionFin","fechaRetiro","tiempoLaborado"];

        $this->campo["trabajador"]=["altoRiesgoPension","codigoTrabajador","email","extras","lugarTrabajoDepartamentoEstado","lugarTrabajoDireccion","lugarTrabajoMunicipioCiudad","lugarTrabajoPais","numeroDocumento","otrosNombres","primerApellido","primerNombre","salarioIntegral","segundoApellido","subTipoTrabajador","sueldo","tipoContrato","tipoIdentificacion","tipoTrabajador"];
    }

    public function Enviar(){
        $curl = curl_init();
        /*
        //deducciones
        if(count($this->data["deducciones"]["anticiposNom"])==0)
            $this->data["deducciones"]["anticiposNom"][]=["montoanticipo"=>NULL];
        if(count($this->data["deducciones"]["fondosPensiones"])==0)
            $this->data["deducciones"]["fondosPensiones"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];
        if(count($this->data["deducciones"]["fondosSP"])==0)
            $this->data["deducciones"]["fondosSP"][]=["deduccionSP"=>NULL,"deduccionSub"=>NULL,"porcentaje"=>NULL,"porcentajeSub"=>NULL];
        if(count($this->data["deducciones"]["libranzas"])==0)
            $this->data["deducciones"]["libranzas"][]=["deduccion"=>"0.00","descripcion"=>""];
        if(count($this->data["deducciones"]["libranzas"])==0)
            $this->data["deducciones"]["libranzas"][]=["deduccion"=>"0.00","descripcion"=>""];
        if(count($this->data["deducciones"]["otrasDeducciones"])==0)
            $this->data["deducciones"]["otrasDeducciones"][]=["montootraDeduccion"=>NULL];
        if(count($this->data["deducciones"]["pagosTerceros"])==0)
            $this->data["deducciones"]["pagosTerceros"][]=["montopagotercero"=>NULL];
        if(count($this->data["deducciones"]["pagosTerceros"])==0)
            $this->data["deducciones"]["pagosTerceros"][]=["montopagotercero"=>NULL];
        if(count($this->data["deducciones"]["salud"])==0)
            $this->data["deducciones"]["salud"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];
        if(count($this->data["deducciones"]["sanciones"])==0)
            $this->data["deducciones"]["sanciones"][]=["sancionPublic"=>"0.00","sancionPriv"=>"0.00"];
        if(count($this->data["deducciones"]["sindicatos"])==0)
            $this->data["deducciones"]["sindicatos"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];

        //devengados
        if(count($this->data["devengados"]["anticiposNom"])==0)
            $this->data["devengados"]["anticiposNom"][]=["montoanticipo"=>NULL];
        if(count($this->data["devengados"]["auxilios"])==0)
            $this->data["devengados"]["auxilios"][]=["auxilioNS"=>NULL,"auxilioS"=>NULL];
        if(count($this->data["devengados"]["basico"])==0)
            $this->data["devengados"]["basico"][]=["diasTrabajados"=>"0","sueldoTrabajado"=>"0.00"];
        if(count($this->data["devengados"]["bonificaciones"])==0)
            $this->data["devengados"]["bonificaciones"][]=["bonificacionNS"=>NULL,"bonificacionS"=>NULL];
        if(count($this->data["devengados"]["bonoEPCTVs"])==0)
            $this->data["devengados"]["bonoEPCTVs"][]=["pagoAlimentacionNS"=>NULL,"pagoAlimentacionS"=>NULL,"pagoNS"=>NULL,"pagoS"=>NULL];
        if(count($this->data["devengados"]["compensaciones"])==0)
            $this->data["devengados"]["compensaciones"][]=["compensacionE"=>"0.00","compensacionO"=>"0.00"];
        if(count($this->data["devengados"]["horasExtras"])==0)
            $this->data["devengados"]["horasExtras"][]=["cantidad"=>"0","horaInicio"=>"","horaFin"=>"","pago"=>"0.00","porcentaje"=>"0","tipoHorasExtra"=>"0"];
        if(count($this->data["devengados"]["huelgasLegales"])==0)
            $this->data["devengados"]["huelgasLegales"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>""];
        if(count($this->data["devengados"]["incapacidades"])==0)
            $this->data["devengados"]["incapacidades"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00","tipo"=>"1"];
        if(count($this->data["devengados"]["licenciaMP"])==0)
            $this->data["devengados"]["licenciaMP"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        if(count($this->data["devengados"]["licenciaNR"])==0)
            $this->data["devengados"]["licenciaNR"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        if(count($this->data["devengados"]["licenciaR"])==0)
            $this->data["devengados"]["licenciaR"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        if(count($this->data["devengados"]["otrosConceptos"])==0)
            $this->data["devengados"]["otrosConceptos"][]=["conceptoNS"=>NULL,"conceptoS"=>NULL,"descripcionConcepto"=>""];
        if(count($this->data["devengados"]["pagosTerceros"])==0)
            $this->data["devengados"]["pagosTerceros"][]=["montopagotercero"=>NULL];
        if(count($this->data["devengados"]["primas"])==0)
            $this->data["devengados"]["primas"][]=["cantidad"=>"30","pago"=>"0.00","pagoNS"=>"0.00"];
        if(count($this->data["devengados"]["transporte"])==0)
            $this->data["devengados"]["transporte"][]=["auxilioTransporte"=>NULL,"sueldoTrabajado"=>NULL,"viaticoManuAlojNS"=>NULL];
        if(count($this->data["devengados"]["vacacionesComunes"])==0)
            $this->data["devengados"]["vacacionesComunes"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>NULL];
        if(count($this->data["devengados"]["vacacionesCompensadas"])==0)
            $this->data["devengados"]["vacacionesCompensadas"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>NULL];
        */
        /*
        //deducciones
        if(count($this->data["deducciones"]["anticiposNom"])==0)
            $this->data["deducciones"]["anticiposNom"][]=["montoanticipo"=>"0.00"];
        if(count($this->data["deducciones"]["fondosPensiones"])==0)
            $this->data["deducciones"]["fondosPensiones"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];
        if(count($this->data["deducciones"]["fondosSP"])==0)
            $this->data["deducciones"]["fondosSP"][]=["deduccionSP"=>"0.00","deduccionSub"=>"0.00","porcentaje"=>"0.00","porcentajeSub"=>"0.00"];
        if(count($this->data["deducciones"]["libranzas"])==0)
            $this->data["deducciones"]["libranzas"][]=["deduccion"=>"0.00","descripcion"=>""];
        if(count($this->data["deducciones"]["libranzas"])==0)
            $this->data["deducciones"]["libranzas"][]=["deduccion"=>"0.00","descripcion"=>""];
        if(count($this->data["deducciones"]["otrasDeducciones"])==0)
            $this->data["deducciones"]["otrasDeducciones"][]=["montootraDeduccion"=>"0.00"];
        if(count($this->data["deducciones"]["pagosTerceros"])==0)
            $this->data["deducciones"]["pagosTerceros"][]=["montopagotercero"=>"0.00"];
        if(count($this->data["deducciones"]["pagosTerceros"])==0)
            $this->data["deducciones"]["pagosTerceros"][]=["montopagotercero"=>"0.00"];
        if(count($this->data["deducciones"]["salud"])==0)
            $this->data["deducciones"]["salud"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];
        if(count($this->data["deducciones"]["sanciones"])==0)
            $this->data["deducciones"]["sanciones"][]=["sancionPublic"=>"0.00","sancionPriv"=>"0.00"];
        if(count($this->data["deducciones"]["sindicatos"])==0)
            $this->data["deducciones"]["sindicatos"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];

        //devengados
        if(count($this->data["devengados"]["anticiposNom"])==0)
            $this->data["devengados"]["anticiposNom"][]=["montoanticipo"=>"0.00"];
        if(count($this->data["devengados"]["auxilios"])==0)
            $this->data["devengados"]["auxilios"][]=["auxilioNS"=>"0.00","auxilioS"=>"0.00"];
        if(count($this->data["devengados"]["basico"])==0)
            $this->data["devengados"]["basico"][]=["diasTrabajados"=>"0","sueldoTrabajado"=>"0.00"];
        if(count($this->data["devengados"]["bonificaciones"])==0)
            $this->data["devengados"]["bonificaciones"][]=["bonificacionNS"=>"0.00","bonificacionS"=>"0.00"];
        if(count($this->data["devengados"]["bonoEPCTVs"])==0)
            $this->data["devengados"]["bonoEPCTVs"][]=["pagoAlimentacionNS"=>"0.00","pagoAlimentacionS"=>"0.00","pagoNS"=>"00.00","pagoS"=>"0.00"];
        if(count($this->data["devengados"]["compensaciones"])==0)
            $this->data["devengados"]["compensaciones"][]=["compensacionE"=>"0.00","compensacionO"=>"0.00"];
        if(count($this->data["devengados"]["horasExtras"])==0)
            $this->data["devengados"]["horasExtras"][]=["cantidad"=>"0.00","horaInicio"=>"","horaFin"=>"","pago"=>"0.00","porcentaje"=>"0.00","tipoHorasExtra"=>"0"];
        if(count($this->data["devengados"]["huelgasLegales"])==0)
            $this->data["devengados"]["huelgasLegales"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>""];
        if(count($this->data["devengados"]["incapacidades"])==0)
            $this->data["devengados"]["incapacidades"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00","tipo"=>"1"];
        if(count($this->data["devengados"]["licenciaMP"])==0)
            $this->data["devengados"]["licenciaMP"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        if(count($this->data["devengados"]["licenciaNR"])==0)
            $this->data["devengados"]["licenciaNR"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        if(count($this->data["devengados"]["licenciaR"])==0)
            $this->data["devengados"]["licenciaR"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        if(count($this->data["devengados"]["otrosConceptos"])==0)
            $this->data["devengados"]["otrosConceptos"][]=["conceptoNS"=>"0.00","conceptoS"=>"0.00","descripcionConcepto"=>""];
        if(count($this->data["devengados"]["pagosTerceros"])==0)
            $this->data["devengados"]["pagosTerceros"][]=["montopagotercero"=>"0.00"];
        if(count($this->data["devengados"]["primas"])==0)
            $this->data["devengados"]["primas"][]=["cantidad"=>"30","pago"=>"0.00","pagoNS"=>"0.00"];
        if(count($this->data["devengados"]["transporte"])==0)
            $this->data["devengados"]["transporte"][]=["auxilioTransporte"=>"0.00","sueldoTrabajado"=>"0.00","viaticoManuAlojNS"=>"0.00"];
        if(count($this->data["devengados"]["vacacionesComunes"])==0)
            $this->data["devengados"]["vacacionesComunes"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        if(count($this->data["devengados"]["vacacionesCompensadas"])==0)
            $this->data["devengados"]["vacacionesCompensadas"][]=["cantidad"=>"0","fechaInicio"=>"","fechaFin"=>"","pago"=>"0.00"];
        */

        //deducciones
        if(count($this->data["deducciones"]["anticiposNom"])==0)
            unset($this->data["deducciones"]["anticiposNom"]);
        if(count($this->data["deducciones"]["fondosPensiones"])==0)
            $this->data["deducciones"]["fondosPensiones"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];
        if(count($this->data["deducciones"]["fondosSP"])==0)
            unset($this->data["deducciones"]["fondosSP"]);
        if(count($this->data["deducciones"]["libranzas"])==0)
            unset($this->data["deducciones"]["libranzas"]);
        if(count($this->data["deducciones"]["libranzas"])==0)
            unset($this->data["deducciones"]["libranzas"]);
        if(count($this->data["deducciones"]["otrasDeducciones"])==0)
            unset($this->data["deducciones"]["otrasDeducciones"]);
        if(count($this->data["deducciones"]["pagosTerceros"])==0)
            unset($this->data["deducciones"]["pagosTerceros"]);
        if(count($this->data["deducciones"]["pagosTerceros"])==0)
            unset($this->data["deducciones"]["pagosTerceros"]);
        if(count($this->data["deducciones"]["salud"])==0)
            $this->data["deducciones"]["salud"][]=["deduccion"=>"0.00","porcentaje"=>"0.00"];
        if(count($this->data["deducciones"]["sanciones"])==0)
            unset($this->data["deducciones"]["sanciones"]);
        if(count($this->data["deducciones"]["sindicatos"])==0)
            unset($this->data["deducciones"]["sindicatos"]);

        //devengados
        if(count($this->data["devengados"]["anticiposNom"])==0)
            unset($this->data["devengados"]["anticiposNom"]);
        if(count($this->data["devengados"]["auxilios"])==0)
            unset($this->data["devengados"]["auxilios"]);
        if(count($this->data["devengados"]["basico"])==0)
            $this->data["devengados"]["basico"][]=["diasTrabajados"=>"0","sueldoTrabajado"=>"0.00"];
        if(count($this->data["devengados"]["bonificaciones"])==0)
            unset($this->data["devengados"]["bonificaciones"]);
        if(count($this->data["devengados"]["bonoEPCTVs"])==0)
            unset($this->data["devengados"]["bonoEPCTVs"]);
        if(count($this->data["devengados"]["compensaciones"])==0)
            unset($this->data["devengados"]["compensaciones"]);
        if(count($this->data["devengados"]["horasExtras"])==0)
            unset($this->data["devengados"]["horasExtras"]);
        if(count($this->data["devengados"]["huelgasLegales"])==0)
            unset($this->data["devengados"]["huelgasLegales"]);
        if(count($this->data["devengados"]["incapacidades"])==0)
            unset($this->data["devengados"]["incapacidades"]);
        if(count($this->data["devengados"]["licenciaMP"])==0)
            unset($this->data["devengados"]["licenciaMP"]);
        if(count($this->data["devengados"]["licenciaNR"])==0)
            unset($this->data["devengados"]["licenciaNR"]);
        if(count($this->data["devengados"]["licenciaR"])==0)
            unset($this->data["devengados"]["licenciaR"]);
        if(count($this->data["devengados"]["otrosConceptos"])==0)
            unset($this->data["devengados"]["otrosConceptos"]);
        if(count($this->data["devengados"]["pagosTerceros"])==0)
            unset($this->data["devengados"]["pagosTerceros"]);
        if(count($this->data["devengados"]["primas"])==0)
            unset($this->data["devengados"]["primas"]);
        if(count($this->data["devengados"]["transporte"])==0)
            unset($this->data["devengados"]["transporte"]);
        if(count($this->data["devengados"]["vacacionesComunes"])==0)
            unset($this->data["devengados"]["vacacionesComunes"]);
        if(count($this->data["devengados"]["vacacionesCompensadas"])==0)
            unset($this->data["devengados"]["vacacionesCompensadas"]);

        if(count($this->data["documentosReferenciadosNom"])==0){
            unset($this->data["documentosReferenciadosNom"]);
        }

        if(count($this->data["periodos"])==0){
            unset($this->data["periodos"]);
        }

        if(count($this->data["pagos"])==0){
            unset($this->data["pagos"]);
        }

        if(!$this->data["lugarGeneracionXML"]["departamentoEstado"]){
            unset($this->data["lugarGeneracionXML"]);
        }

        if(!$this->data["novedadCUNE"]){
            unset($this->data["novedadCUNE"]);
        }


        if($this->data["tipoNota"]=="2"){
            $this->data["trabajador"]=NULL;
            $this->data["deducciones"]=NULL;
            $this->data["devengados"]=NULL;
            $this->data["periodos"]=NULL;
            $this->data["novedad"]=NULL;
            $this->data["extrasNom"]=NULL;
            unset($this->data["novedad"]);
            unset($this->data["deducciones"]);
            unset($this->data["devengados"]);

        }

        //unset($this->data["trabajador"]["lugarTrabajoDepartamentoEstado"]);
        //unset($this->data["trabajador"]["lugarTrabajoMunicipioCiudad"]);
        
        file_put_contents("1.txt",$this->json());

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://demo-nomina-rest.thefactoryhka.com.co/Enviar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $this->json()
        ));

        $this->respuesta_string = curl_exec($curl);
        curl_close($curl);
        $this->respuesta=json_decode($this->respuesta_string,true);
        $this->obj_response=new AMX_NominaElectronicaResponse($this->respuesta);
        return $this->respuesta;
    }

    public function Respuesta(){
        return $this->obj_response;
    }

    public function json(){
        $retorno=$this->raiz;
        $retorno["objNomina"]=$this->data;
        return json_encode($retorno,JSON_PRETTY_PRINT);
    }

    public function objecto(){
        $retorno=$this->raiz;
        $retorno["objNomina"]=$this->data;
        return $retorno;
    }

    public function set($indice,$valor){
        $this->data[$indice]=$valor;
    }

    public function Asignar($indice,$valor){
        $this->data[$indice]=$valor;
    }

    public function get($indice){
        if(!isset($this->data[$indice]))
            return NULL;
        return $this->data[$indice];
    }

    public function get_base64($indice){
        if(!isset($this->data[$indice]))
            return NULL;
        return base64_encode(json_encode($this->data[$indice]));
    }

    public function Agregar($subcampo,$obj){
        $tmp=[];
        for($i=0; $i < count($this->campo["$subcampo"]); $i++) { 
            $campo=$this->campo["$subcampo"][$i];
            $tmp[$campo]="";
            if($campo=="extras")
                $tmp[$campo]=NULL;
            if($campo=="extrasNom")
                $tmp[$campo]=NULL;
            if(isset($obj[$campo]))
                $tmp[$campo]=$obj[$campo];
        }
        if(in_array($subcampo,["trabajador","lugarGeneracionXML"])){
            $this->data["$subcampo"]=$tmp;
        }
        else{
            $this->data["$subcampo"][]=$tmp;            
        }
    }  

    public function DocumentosReferenciados($obj){
        $this->Agregar("documentosReferenciadosNom",$obj);
    }

    public function Notas($obj){
        $this->Agregar("notas",$obj);
    }

    public function LugarGeneracionXML($obj){
        $this->Agregar("lugarGeneracionXML",$obj);
    }

    public function Pagos($obj){
        $this->Agregar("pagos",$obj);
    }

    public function Periodos($obj){
        $this->Agregar("periodos",$obj);
    }

    public function Trabajador($obj){
        $this->Agregar("trabajador",$obj);
    }




    public function Deducciones($indice,$valor){
        $this->data["deducciones"][$indice]=$valor;
    }

    public function Deducciones__Agregar($subcampo,$obj){
        $tmp=[];
        for($i=0; $i < count($this->campo["deducciones"]["$subcampo"]); $i++) { 
            $campo=$this->campo["deducciones"]["$subcampo"][$i];
            $tmp[$campo]="";
            if($campo=="extras")
                $tmp[$campo]=NULL;
            if(isset($obj[$campo]))
                $tmp[$campo]=$obj[$campo];
        }
        $this->data["deducciones"]["$subcampo"][]=$tmp;
    }    

    public function Deducciones__Agregar_Anticipos($obj){
        $this->Deducciones__Agregar("anticiposNom",$obj);
    }

    public function Deducciones__Agregar_FondosPensiones($obj){
        $this->Deducciones__Agregar("fondosPensiones",$obj);
    }

    public function Deducciones__Agregar_FondosSP($obj){
        $this->Deducciones__Agregar("fondosSP",$obj);
    }

    public function Deducciones__Agregar_Libranzas($obj){
        $this->Deducciones__Agregar("libranzas",$obj);
    }

    public function Deducciones__Agregar_OtrasDeducciones($obj){
        $this->Deducciones__Agregar("otrasDeducciones",$obj);
    }

    public function Deducciones__Agregar_PagosTerceros($obj){
        $this->Deducciones__Agregar("pagosTerceros",$obj);
    }

    public function Deducciones__Agregar_Salud($obj){
        $this->Deducciones__Agregar("salud",$obj);
    }

    public function Deducciones__Agregar_Sanciones($obj){
        $this->Deducciones__Agregar("sanciones",$obj);
    }

    public function Deducciones__Agregar_Sindicatos($obj){
        $this->Deducciones__Agregar("sindicatos",$obj);
    }



    public function Devengados($indice,$valor){
        $this->data["devengados"][$indice]=$valor;
    }

    public function Devengados__Agregar($subcampo,$obj){
        $tmp=[];
        for($i=0; $i < count($this->campo["devengados"]["$subcampo"]); $i++) { 
            $campo=$this->campo["devengados"]["$subcampo"][$i];
            $tmp[$campo]="";
            if($campo=="extras")
                $tmp[$campo]=NULL;
            if(isset($obj[$campo]))
                $tmp[$campo]=$obj[$campo];
        }

        if(in_array($subcampo,["licenciaMP","licenciaNR","licenciaR"])){
            $this->data["devengados"]["licencias"]["$subcampo"][]=$tmp;
        }
        else if(in_array($subcampo,["vacacionesComunes","vacacionesCompensadas"])){
            $this->data["devengados"]["vacaciones"]["$subcampo"][]=$tmp;
        }
        else{
            $this->data["devengados"]["$subcampo"][]=$tmp;
        }
    }

    public function Devengados__Agregar_Anticipos($obj){
        $this->Devengados__Agregar("anticiposNom",$obj);
    }

    public function Devengados__Agregar_Auxilios($obj){
        $this->Devengados__Agregar("auxilios",$obj);
    }

    public function Devengados__Agregar_Basico($obj){
        $this->Devengados__Agregar("basico",$obj);
    }

    public function Devengados__Agregar_Bonificaciones($obj){
        $this->Devengados__Agregar("bonificaciones",$obj);
    }

    public function Devengados__Agregar_BonoEPCTVs($obj){
        $this->Devengados__Agregar("bonoEPCTVs",$obj);
    }

    public function Devengados__Agregar_Cesantias($obj){
        $this->Devengados__Agregar("cesantias",$obj);
    }

    public function Devengados__Agregar_Comisiones($obj){
        $this->Devengados__Agregar("comisiones",$obj);
    }

    public function Devengados__Agregar_Compensaciones($obj){
        $this->Devengados__Agregar("compensaciones",$obj);
    }

    public function Devengados__Agregar_HorasExtras($obj){
        $this->Devengados__Agregar("horasExtras",$obj);
    }

    public function Devengados__Agregar_HuelgasLegales($obj){
        $this->Devengados__Agregar("huelgasLegales",$obj);
    }

    public function Devengados__Agregar_Incapacidades($obj){
        $this->Devengados__Agregar("incapacidades",$obj);
    }

    public function Devengados__Agregar_LicenciaMP($obj){
        $this->Devengados__Agregar("licenciaMP",$obj);
    }

    public function Devengados__Agregar_LicenciaNR($obj){
        $this->Devengados__Agregar("licenciaNR",$obj);
    }

    public function Devengados__Agregar_LicenciaR($obj){
        $this->Devengados__Agregar("licenciaR",$obj);
    }

    public function Devengados__Agregar_OtrosConceptos($obj){
        $this->Devengados__Agregar("otrosConceptos",$obj);
    }

    public function Devengados__Agregar_PagosTerceros($obj){
        $this->Devengados__Agregar("pagosTerceros",$obj);
    }

    public function Devengados__Agregar_Primas($obj){
        $this->Devengados__Agregar("primas",$obj);
    }

    public function Devengados__Agregar_Transporte($obj){
        $this->Devengados__Agregar("transporte",$obj);
    }

    public function Devengados__Agregar_VacacionesComunes($obj){
        $this->Devengados__Agregar("vacacionesComunes",$obj);
    }

    public function Devengados__Agregar_VacacionesCompensadas($obj){
        $this->Devengados__Agregar("vacacionesCompensadas",$obj);
    } 

}


function conceptos_suman_restan($conceptos){    
    $tmp=explode(",", $conceptos);
    $suman=[];
    $restan=[];
    for($i=0; $i < count($tmp); $i++){ 
        $tmp[$i]=trim($tmp[$i]);
        if(substr($tmp[$i], 0,1)=="-")
            $restan[]=trim($tmp[$i],"-");
        else
            $suman[]=$tmp[$i];
    }
    return [
        "conceptos_suman"  => implode(",",$suman),
        "conceptos_restan" => implode(",",$restan)
    ];
}

/*
$parametros=[
    "tokenEnterprise"  => "5a509adeab1d47138eaf393ea6173c14bd22b8f5",
    "tokenPassword"    => "51d174f254b14107a2f5040b8d15b2f8cd3a8b04",
    "idSoftware"       => "123456",
    "nitEmpleador"     => "900390126",
];

$nomina=new AMX_NominaElectronica($parametros);

$nomina->Deducciones("afc","10000.00");
$nomina->Deducciones("cooperativa","10000.00");
$nomina->Deducciones("deuda","10000.00");
$nomina->Deducciones("educacion","10000.00");
$nomina->Deducciones("embargoFiscal","10000.00");
$nomina->Deducciones("pensionVoluntaria","10000.00");
$nomina->Deducciones("planComplementarios","10000.00");
$nomina->Deducciones("reintegro","10000.00");
$nomina->Deducciones("retencionFuente","10000.00");

$nomina->Deducciones__Agregar_Anticipos(["montoanticipo"=>"10000.00"]);
$nomina->Deducciones__Agregar_Anticipos(["montoanticipo"=>"10000.00"]);
$nomina->Deducciones__Agregar_FondosPensiones(["deduccion"=>"10000.00","porcentaje"=>"10.00"]);
$nomina->Deducciones__Agregar_FondosSP(["deduccionSP"=>"10000.00","deduccionSub"=>"1000.00","porcentaje"=>"10.00","porcentajeSub"=>"10.00"]);
$nomina->Deducciones__Agregar_Libranzas(["deduccion"=>"10000.00","descripcion"=>"Descripcion General de Libranza"]);
$nomina->Deducciones__Agregar_OtrasDeducciones(["montootraDeduccion"=>"10000.00"]);
$nomina->Deducciones__Agregar_PagosTerceros(["montopagotercero"=>"10000.00"]);
$nomina->Deducciones__Agregar_Salud(["deduccion"=>"10000.00","porcentaje"=>"10.00"]);
$nomina->Deducciones__Agregar_Sanciones(["sancionPublic"=>"10000.00","sancionPriv"=>"10000.00"]);
$nomina->Deducciones__Agregar_Sanciones(["sancionPublic"=>"10000.00","sancionPriv"=>"10000.00"]);
$nomina->Deducciones__Agregar_Sindicatos(["deduccion"=>"10000.00","porcentaje"=>"10.00"]);
$nomina->Deducciones__Agregar_Sindicatos(["deduccion"=>"10000.00","porcentaje"=>"10.00"]);


$nomina->Devengados("apoyoSost","10000.00");
$nomina->Devengados("bonifRetiro","10000.00");
$nomina->Devengados("dotacion","10000.00");
$nomina->Devengados("reintegro","10000.00");
$nomina->Devengados("teletrabajo","10000.00");
$nomina->Devengados__Agregar_Anticipos(["montoanticipo"=>"10000.00"]);
$nomina->Devengados__Agregar_Anticipos(["montoanticipo"=>"10000.00"]);
$nomina->Devengados__Agregar_Auxilios(["auxilioNS"=>"10000.00","auxilioS"=>"10000.00"]);
$nomina->Devengados__Agregar_Auxilios(["auxilioNS"=>"10000.00","auxilioS"=>"10000.00"]);
$nomina->Devengados__Agregar_Basico(["diasTrabajados"=>"15","sueldoTrabajado"=>"500000.00"]);
$nomina->Devengados__Agregar_Bonificaciones(["bonificacionNS"=>"10000.00","bonificacionS"=>"10000.00"]);
$nomina->Devengados__Agregar_BonoEPCTVs(["pagoAlimentacionNS"=>"10000.00","pagoAlimentacionS"=>"10000.00","pagoNS"=>"10000.00","pagoS"=>"10000.00"]);
$nomina->Devengados__Agregar_BonoEPCTVs(["pagoAlimentacionNS"=>"10000.00","pagoAlimentacionS"=>"10000.00","pagoNS"=>"10000.00","pagoS"=>"10000.00"]);
//$nomina->Devengados__Agregar_Cesantias(["montocomision"=>"10000.00"]);
$nomina->Devengados__Agregar_Compensaciones(["compensacionE"=>"10000.00","compensacionO"=>"10000.00"]);
$nomina->Devengados__Agregar_Compensaciones(["compensacionE"=>"10000.00","compensacionO"=>"10000.00"]);
$nomina->Devengados__Agregar_HorasExtras(["cantidad"=>"10000.00","horaInicio"=>"2021-03-02 12:00:00","horaFin"=>"2021-03-02 12:00:00","pago"=>"10000.00","porcentaje"=>"25.00","tipoHorasExtra"=>"0"]);
$nomina->Devengados__Agregar_HorasExtras(["cantidad"=>"10000.00","horaInicio"=>"2021-03-02 12:00:00","horaFin"=>"2021-03-02 12:00:00","pago"=>"10000.00","porcentaje"=>"25.00","tipoHorasExtra"=>"1"]);
$nomina->Devengados__Agregar_HuelgasLegales(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16"]);
$nomina->Devengados__Agregar_HuelgasLegales(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16"]);
$nomina->Devengados__Agregar_Incapacidades(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00","tipo"=>"2"]);
$nomina->Devengados__Agregar_Incapacidades(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00","tipo"=>"2"]);
$nomina->Devengados__Agregar_LicenciaMP(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_LicenciaMP(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_LicenciaNR(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_LicenciaNR(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_LicenciaR(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_LicenciaR(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_OtrosConceptos(["conceptoNS"=>"10000.00","conceptoS"=>"10000.00","descripcionConcepto"=>"Descipcion Generica"]);
$nomina->Devengados__Agregar_OtrosConceptos(["conceptoNS"=>"10000.00","conceptoS"=>"10000.00","descripcionConcepto"=>"Descipcion Generica"]);
$nomina->Devengados__Agregar_PagosTerceros(["montopagotercero"=>"10000.00"]);
$nomina->Devengados__Agregar_PagosTerceros(["montopagotercero"=>"10000.00"]);
$nomina->Devengados__Agregar_Primas(["cantidad"=>"30","pago"=>"10000.00","pagoNS"=>"10000.00"]);
$nomina->Devengados__Agregar_Transporte(["auxilioTransporte"=>"10000.00","sueldoTrabajado"=>"10000.00","viaticoManuAlojNS"=>"10000.00"]);
$nomina->Devengados__Agregar_Transporte(["auxilioTransporte"=>"10000.00","sueldoTrabajado"=>"10000.00","viaticoManuAlojNS"=>"10000.00"]);
$nomina->Devengados__Agregar_VacacionesComunes(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_VacacionesComunes(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_VacacionesCompensadas(["cantidad"=>"20","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);
$nomina->Devengados__Agregar_VacacionesCompensadas(["cantidad"=>"20","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"10000.00"]);

$nomina->DocumentosReferenciados(["cunePred"=>"d91deada982e658a3f69bf8eb2d173c7c10142fe34dee71f9c3c75b3d066b2f535083f02657a79369efdd6d9dc11240e","fechaGenPred"=>"2021-03-04","numeroPred"=>"DSNE1"]);
$nomina->Notas(["descripcion"=>"Nota de prueba 1"]);
$nomina->Notas(["descripcion"=>"Nota de prueba 1"]);
$nomina->LugarGeneracionXML(["departamentoEstado"=>"11","idioma"=>"es","municipioCiudad"=>"11001","pais"=>"CO"]);
$nomina->Pagos(["fechasPagos"=>[["fechapagonomina"=>"2021-03-15"]],"metodoDePago"=>"1","medioPago"=>"ZZZ","nombreBanco"=>"Nombre del Banco","tipoCuenta"=>"Ahorro","numeroCuenta"=>"123456789"]);
$nomina->Periodos(["fechaIngreso"=>"2021-03-02","fechaLiquidacionInicio"=>"2021-03-03","fechaLiquidacionFin"=>"2021-03-04","fechaRetiro"=>"2021-03-02","tiempoLaborado"=>"3"]);
$nomina->Trabajador(["altoRiesgoPension"=>"0","codigoTrabajador"=>"A527","email"=>"asgarcia@thefactoryhka.com","lugarTrabajoDepartamentoEstado"=>"11","lugarTrabajoDireccion"=>"Direccion de prueba","lugarTrabajoMunicipioCiudad"=>"11001","lugarTrabajoPais"=>"CO","numeroDocumento"=>"900390126","otrosNombres"=>null,"primerApellido"=>"Garcia","primerNombre"=>"Anderson","salarioIntegral"=>"0","segundoApellido"=>"rojas","subTipoTrabajador"=>"00","sueldo"=>"500000.00","tipoContrato"=>"1","tipoIdentificacion"=>"31","tipoTrabajador"=>"01"]);


$nomina->Asignar("consecutivoDocumentoNom","FSNE20210801");
$nomina->Asignar("fechaEmisionNom","2021-03-02 12:00:00");
$nomina->Asignar("novedad","0");
$nomina->Asignar("novedadCUNE","d91deada982e658a3f69bf8eb2d173c7c10142fe34dee71f9c3c75b3d066b2f535083f02657a79369efdd6d9dc11240e");
$nomina->Asignar("periodoNomina","4");
$nomina->Asignar("rangoNumeracionNom","FSNE-1");
$nomina->Asignar("redondeo","0.00");
$nomina->Asignar("tipoDocumentoNom","102");
$nomina->Asignar("tipoMonedaNom","COP");
$nomina->Asignar("tipoNota","1");
$nomina->Asignar("totalComprobante","410000.00");
$nomina->Asignar("totalDeducciones","40000.00");
$nomina->Asignar("totalDevengados","500000.00");
$nomina->Asignar("trm","3000.00");



//$retorno=$nomina->objecto();
//$retorno=$nomina->json();
//print $retorno;
$retorno=$nomina->Enviar();
print_r($retorno);



print " mensaje: ".$nomina->Respuesta()->get("mensaje");
print " mensaje bse64: ".$nomina->Respuesta()->get_base64("mensaje");



?>