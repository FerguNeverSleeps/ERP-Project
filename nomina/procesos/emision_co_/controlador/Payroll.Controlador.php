<?php

Class PayrollControlador
{
    static function setPeriodo($datos)
    {
        $Periodo = new Periodo();
        
        $Periodo->setFechaIngreso($datos["fecha_ingreso"]);;
        $Periodo->setFechaLiquidacionInicio($datos["fecha_liquidacion_inicio"]);
        $Periodo->setFechaLiquidacionFin($datos["fecha_liquidacion_fin"]);
        $Periodo->setTiempoLaborado($datos["tiempo_laborado"]);
        $Periodo->setFechaGen($datos["fecha_gen"]);
        return array(
          "fecha_ingreso" => $Periodo->getFechaIngreso(),
          "fecha_liquidacion_inicio" => $Periodo->getFechaLiquidacionInicio(),
          "fecha_liquidacion_fin" => $Periodo->getFechaLiquidacionFin(),
          "tiempo_laborado" => $Periodo->getTiempoLaborado(),
          "fecha_gen" => $Periodo->getFechaGen()
  
        );
        
        
    }
    static function setNumeroSecuenciaXML($datos)
    {        
        $NumeroSecuenciaXML = new NumeroSecuenciaXML();
        $NumeroSecuenciaXML->setCodigoTrabajador($datos["codigo_trabajador"]);;
        $NumeroSecuenciaXML->setPrefijo($datos["prefijo"]);
        $NumeroSecuenciaXML->setConsecutivo($datos["consecutivo"]);
        
        return array(
          "codigo_trabajador" => $NumeroSecuenciaXML->getCodigoTrabajador(),
          "prefijo" => $NumeroSecuenciaXML->getPrefijo(),
          "consecutivo" => $NumeroSecuenciaXML->getConsecutivo()
  
        );
    }
    static function setLugarGeneracionXML($datos)
    {
        $LugarGeneracionXML = new LugarGeneracionXML();
        $LugarGeneracionXML->setPais($datos["pais"]);
        $LugarGeneracionXML->setMunicipio($datos["municipio"]);
        $LugarGeneracionXML->setDepartamento($datos["departamento"]);
        $LugarGeneracionXML->setIdioma($datos["idioma"]);

        return array(
            "pais" => $LugarGeneracionXML->getPais(),
            "departamento" => $LugarGeneracionXML->getDepartamento(),
            "municipio" => $LugarGeneracionXML->getMunicipio(),
            "idioma" => $LugarGeneracionXML->getIdioma()

        );
    }
    static function setProveedorXML($datos)
    {
        $ProveedorXML = new ProveedorXML();
        
        $ProveedorXML->setRazonSocial($datos["razon_social"]);;
        $ProveedorXML->setNit($datos["nit"]);
        $ProveedorXML->setDv($datos["dv"]);
        $ProveedorXML->setSoftwareId($datos["software_id"]);
        $ProveedorXML->setSoftwarePin($datos["software_pin"]);
        return array(
          "razon_social" => $ProveedorXML->getRazonSocial(),
          "nit"          => $ProveedorXML->getNit(),
          "dv"           => $ProveedorXML->getDv(),
          "software_id"  => $ProveedorXML->getSoftwareId(),
          "software_pin" => $ProveedorXML->getSoftwarePin()
        );
    }
    static function setInformacionGeneral($datos)
    {
        $InformacionGeneral = new InformacionGeneral();
        
        $InformacionGeneral->setAmbiente($datos["ambiente"]);;
        $InformacionGeneral->setTipoXml($datos["tipo_xml"]);
        $InformacionGeneral->setFechaGen($datos["fecha_gen"]);
        $InformacionGeneral->setHoraGen($datos["hora_gen"]);
        $InformacionGeneral->setPeriodoNomina($datos["periodo_nomina"]);
        $InformacionGeneral->setTipoMoneda($datos["tipo_moneda"]);
        return array(
          "ambiente"       => $InformacionGeneral->getAmbiente(),
          "tipo_xml"       => $InformacionGeneral->getTipoXml(),
          "fecha_gen"      => $InformacionGeneral->getFechaGen(),
          "hora_gen"       => $InformacionGeneral->getHoraGen(),
          "periodo_nomina" => $InformacionGeneral->getPeriodoNomina(),
          "tipo_moneda"    => $InformacionGeneral->getTipoMoneda()
        );
    }
    static function setEmpleador($datos)
    {
        $Empleador = new Empleador();
        
        $Empleador->setRazonSocial($datos["razon_social"]);
        $Empleador->setNit($datos["nit"]);
        $Empleador->setDv($datos["dv"]);
        $Empleador->setPais($datos["pais"]);
        $Empleador->setMunicipio($datos["municipio"]);
        $Empleador->setDepartamento($datos["departamento"]);
        $Empleador->setDireccion($datos["direccion"]);

        return array(
            "razon_social" => $Empleador->getRazonSocial(),
            "nit"          => $Empleador->getNit(),
            "dv"           => $Empleador->getDv(),
            "pais"         => $Empleador->getPais(),
            "departamento" => $Empleador->getDepartamento(),
            "municipio"    => $Empleador->getMunicipio(),
            "direccion"    => $Empleador->getDireccion()
        );
    }
    static function setTrabajador($datos)
    {
        $Trabajador = new TrabajadorModelo();
        
        $Trabajador->setTipoTrabajador($datos["tipo_trabajador"]);
        $Trabajador->setSubtipoTrabajador($datos["subtipo_trabajador"]);
        $Trabajador->setAltoRiesgo($datos["alto_riesgo"]);
        $Trabajador->setTipoDocumento($datos["tipo_documento"]);
        $Trabajador->setNumeroDocumento($datos["numero_documento"]);
        $Trabajador->setPrimerApellido($datos["primer_apellido"]);
        $Trabajador->setSegundoApellido($datos["segundo_apellido"]);;
        $Trabajador->setPrimerNombre($datos["primer_nombre"]);
        $Trabajador->setOtrosNombres($datos["otros_nombres"]);
        $Trabajador->setPaisTrabajo($datos["pais_trabajo"]);
        $Trabajador->setMunicipioTrabajo($datos["municipio_trabajo"]);
        $Trabajador->setDireccionTrabajo($datos["direccion_trabajo"]);
        $Trabajador->setSalarioIntegral($datos["salario_integral"]);
        $Trabajador->setTipoContrato($datos["tipo_contrato"]);
        $Trabajador->setSueldo($datos["sueldo"]);
        $Trabajador->setcodigoTrabajador($datos["codigo_trabajador"]);
        return array(
          "tipo_trabajador"    => $Trabajador->getTipoTrabajador(),
          "subtipo_trabajador" => $Trabajador->getSubtipoTrabajador(),
          "alto_riesgo"        => $Trabajador->getAltoRiesgo(),
          "tipo_documento"     => $Trabajador->getTipoDocumento(),
          "numero_documento"   => $Trabajador->getNumeroDocumento(),
          "primer_apellido"    => $Trabajador->getPrimerApellido(),
          "segundo_apellido"   => $Trabajador->getSegundoApellido(),
          "primer_nombre"      => $Trabajador->getPrimerNombre(),
          "otros_nombres"      => $Trabajador->getOtrosNombres(),
          "pais_trabajo"       => $Trabajador->getPaisTrabajo(),
          "municipio_trabajo"  => $Trabajador->getMunicipioTrabajo(),
          "direccion_trabajo"  => $Trabajador->getDireccionTrabajo(),
          "salario_integral"   => $Trabajador->getSalarioIntegral(),
          "tipo_contrato"      => $Trabajador->getTipoContrato(),
          "sueldo"             => $Trabajador->getSueldo(),
          "codigo_trabajador"  => $Trabajador->getcodigoTrabajador()
        );
    }
    static function setPago($datos)
    {
        $Pago = new Pago();
        
        $Pago->setForma($datos["forma"]);
        $Pago->setMetodo($datos["metodo"]);
        $Pago->setBanco($datos["banco"]);
        $Pago->setTipoCuenta($datos["tipo_cuenta"]);
        $Pago->setNumeroCuenta($datos["numero_cuenta"]);
        return array(
          "forma"    => $Pago->getForma(),
          "metodo" => $Pago->getMetodo(),
          "banco"        => $Pago->getBanco(),
          "tipo_cuenta"     => $Pago->getTipoCuenta(),
          "numero_cuenta"   => $Pago->getNumeroCuenta()
        );
    }
    static function setSalud($datos)
    {
        $Salud = new Salud();
        
        $Salud->setPorcentaje($datos["porcentaje"]);
        $Salud->setDeduccion($datos["deduccion"]);
        
        return array(
            "porcentaje"  => $Salud->getPorcentaje(),
            "deduccion" => $Salud->getDeduccion()
        );
    }
    static function setFondoPension($datos)
    {
        /*$FondoPension = new FondoPension();
        
        $FondoPension->setPorcentaje();
        $FondoPension->setDeduccion();*/
        
        return array(
            "porcentaje"  => $datos["porcentaje"],
            "deduccion" => $datos["deduccion"]
        );
    }
    static function setDevengados($datos)
    {
        $Devengados = new Devengados();
        if(isset($datos["transporte"]))
            $transporte          = $Devengados->setTransporte($datos["transporte"]);
        if(isset($datos["heds"]))
            $heds                = $Devengados->setHorasExtrasDiurnas($datos["heds"]);
        if(isset($datos["hens"]))
            $hens                = $Devengados->setHorasExtrasNocturnas($datos["hens"]);
        if(isset($datos["hrns"]))
            $hrns                = $Devengados->setHorasRecargoNocturnas($datos["hrns"]);
        if(isset($datos["heddfs"]))
            $heddfs              = $Devengados->setHorasExtrasDiurnasDomingosFeriados($datos["heddfs"]);
        if(isset($datos["hrddfs"]))
            $hrddfs              = $Devengados->setHorasRecargoDiurnasDomingosFeriados($datos["hrddfs"]);
        if(isset($datos["hendfs"]))
            $hendfs              = $Devengados->setHorasExtrasNocturnasDomingosFeriados($datos["hendfs"]);
        if(isset($datos["vacaciones"]))
            $vacaciones          = $Devengados->setVacaciones($datos["vacaciones"]);
        if(isset($datos["primas"]))
            $primas              = $Devengados->setPrimas($datos["primas"]);
        if(isset($datos["incapacidades"]))
            $incapacidades       = $Devengados->setIncapacidades($datos["incapacidades"]);
        if(isset($datos["bonificaciones"]))
            $bonificaciones      = $Devengados->setBonificaciones($datos["bonificaciones"]);
        if(isset($datos["auxilios"]))
            $auxilios            = $Devengados->setAuxilios($datos["auxilios"]);
        if(isset($datos["huelgas_legales"]))
            $huelgas_legales     = $Devengados->setHuelgasLegales($datos["huelgas_legales"]);
        if(isset($datos["compensaciones"]))
            $compensaciones      = $Devengados->setCompensaciones($datos["compensaciones"]);
        if(isset($datos["bonos_epctv"]))
            $bonos_epctv         = $Devengados->setEpctv($datos["bonos_epctv"]);
        if(isset($datos["comisiones"]))
            $comisiones          = $Devengados->setComisiones($datos["comisiones"]);
        if(isset($datos["pagos_terceros"]))
            $pagos_terceros      = $Devengados->setPagosTerceros($datos["pagos_terceros"]);
        if(isset($datos["anticipos"]))
            $anticipos           = $datos["anticipos"];
        if(isset($datos["dotacion"]))
            $dotacion            = $datos["dotacion"];
        if(isset($datos["bonificacion_retiro"]))
            $bonificacion_retiro = $datos["bonificacion_retiro"];
        if(isset($datos["indemnizacion"]))
            $indemnizacion       = $datos["indemnizacion"];
        if(isset($datos["reintegro"]))
            $reintegro           = $datos["reintegro"];
        if(isset($datos["otros_conceptos"]))
            $otros_conceptos     = $datos["otros_conceptos"];
        $arrayDevengados = [];
        $arrayDevengados["dias_trabajados"] = $datos["dias_trabajados"];
        $arrayDevengados["sueldo_trabajado"] = $datos["sueldo_trabajado"];
        if(isset($datos["transporte"]) )
            $arrayDevengados["transporte"] = $datos["transporte"];
        if(isset($datos["heds"]) )
            $arrayDevengados["heds"] = $datos["heds"];
        if(isset($datos["hens"]) )
            $arrayDevengados["hens"] = $datos["hens"];
        if(isset($datos["hrns"]) )
            $arrayDevengados["hrns"] = $datos["hrns"];
        if(isset($datos["heddfs"]) )
            $arrayDevengados["heddfs"] = $datos["heddfs"];
        if(isset($datos["hrddfs"]) )
            $arrayDevengados["hrddfs"] = $datos["hrddfs"];
        if(isset($datos["hendfs"]) )
            $arrayDevengados["hendfs"] = $datos["hendfs"];
        if(isset($datos["hrndfs"]) )
            $arrayDevengados["hrndfs"] = $datos["hrndfs"];
        if(isset($datos["vacaciones"]) )
            $arrayDevengados["vacaciones"] = $datos["vacaciones"];
        if(isset($datos["primas"]) )
            $arrayDevengados["primas"] = $datos["primas"];
        if(isset($datos["incapacidades"]) )
            $arrayDevengados["incapacidades"] = $datos["incapacidades"];
        if(isset($datos["bonificaciones"]) )
            $arrayDevengados["bonificaciones"] = $datos["bonificaciones"];
        if(isset($datos["auxilios"]) )
            $arrayDevengados["auxilios"] = $datos["auxilios"];
        if(isset($datos["huelgas_legales"]) )
            $arrayDevengados["huelgas_legales"] = $datos["huelgas_legales"];
        if(isset($datos["compensaciones"]) )
            $arrayDevengados["compensaciones"] = $datos["compensaciones"];
        if(isset($datos["bonos_epctv"]) )
            $arrayDevengados["bonos_epctv"] = $datos["bonos_epctv"];
        if(isset($datos["comisiones"]) )
            $arrayDevengados["comisiones"] = $datos["comisiones"];
        if(isset($datos["pagos_terceros"]) )
            $arrayDevengados["pagos_terceros"] = $datos["pagos_terceros"];
        if(isset($datos["anticipos"]) )
            $arrayDevengados["anticipos"] = $datos["anticipos"];
        if(isset($datos["dotacion"]) )
            $arrayDevengados["dotacion"] = $datos["dotacion"];
        if(isset($datos["bonificacion_retiro"]) )
            $arrayDevengados["bonificacion_retiro"] = $datos["bonificacion_retiro"];
        if(isset($datos["indemnizacion"]) )
            $arrayDevengados["indemnizacion"] = $datos["indemnizacion"];
        if(isset($datos["reintegro"]) )
            $arrayDevengados["reintegro"] = $datos["reintegro"];
        if(isset($datos["otros_conceptos"]) )
            $arrayDevengados["otros_conceptos"] = $datos["otros_conceptos"];
        

        return $arrayDevengados;
    }
    static function setDeducciones($datos)
    {
        $Deducciones = new Deducciones();
        
        $Deducciones->setSalud( $datos["salud"] );
        $Deducciones->setFondoPension( $datos["fondo_pensiones"] );
        if(isset($datos["fondo_sp"]) )
            $fondo_sp = $Deducciones -> setFondoSP($datos["fondo_sp"]);
        if(isset($datos["sindicatos"]) )
            $sindicatos = $Deducciones -> setSindicactos($datos["sindicatos"]);
        if(isset($datos["sanciones"]) )
            $sanciones = $Deducciones -> setSanciones($datos["sanciones"]);
        if(isset($datos["pagos_terceros"]) )    
            $pagos_terceros = $Deducciones -> setPagosTerceros($datos["pagos_terceros"]);
        if(isset($datos["anticipos"]) )    
            $anticipos = $Deducciones -> setAnticipos($datos["anticipos"]);
        if(isset($datos["otras_deducciones"]) )
            $otras_deducciones = $Deducciones -> setOtrasDeducciones($datos["otras_deducciones"]);
        if(isset($datos["pension_voluntaria"]) )
            $pension_voluntaria = $datos["pension_voluntaria"];
        if(isset($datos["retencion_fuente"]) )
            $retencion_fuente = $datos["retencion_fuente"];
        if(isset($datos["afc"]) )
            $afc = $datos["afc"];
        if(isset($datos["cooperativa"]) )
            $cooperativa = $datos["cooperativa"];
        if(isset($datos["embargo_fiscal"]) )
            $embargo_fiscal = $datos["embargo_fiscal"];
        if(isset($datos["plan_complementarios"]) )
            $plan_complementarios = $datos["plan_complementarios"];
        if(isset($datos["educacion"]) )
            $educacion = $datos["educacion"];
        if(isset($datos["reintegro"]) )
            $reintegro = $datos["reintegro"];
        if(isset($datos["deuda"]) )
            $deuda = $datos["deuda"];
        
        $arrayDeducciones = [];
        $arrayDeducciones["salud"] = $datos["salud"];
        $arrayDeducciones["fondo_pension"] = $datos["fondo_pensiones"];

        if($fondo_sp != "")
            $arrayDeducciones["fondo_sp"] = $fondo_sp;
        if($sindicatos != "")
            $arrayDeducciones["sindicatos"] = $sindicatos;
        if($sanciones != "")
            $arrayDeducciones["sanciones"] = $sanciones;
        if($sanciones != "")
            $arrayDeducciones["sanciones"] = $sanciones;
        if($pagos_terceros != "")
            $arrayDeducciones["pagos_terceros"] = $pagos_terceros;
        if($anticipos != "")
            $arrayDeducciones["anticipos"] = $anticipos;
        if($otras_deducciones != "")
            $arrayDeducciones["otras_deducciones"] = $otras_deducciones;
        if($pension_voluntaria != "")
            $arrayDeducciones["pension_voluntaria"] = $pension_voluntaria;
        if($retencion_fuente != "")
            $arrayDeducciones["retencion_fuente"] = $retencion_fuente;
        if($afc != "")
            $arrayDeducciones["afc"] = $afc;
        if($cooperativa != "")
            $arrayDeducciones["cooperativa"] = $cooperativa;
        if($embargo_fiscal != "")
            $arrayDeducciones["embargo_fiscal"] = $embargo_fiscal;
        if($plan_complementarios != "")
            $arrayDeducciones["plan_complementarios"] = $plan_complementarios;
        if($educacion != "")
            $arrayDeducciones["educacion"] = $educacion;
        if($reintegro != "")
            $arrayDeducciones["reintegro"] = $reintegro;
        if($deuda != "")
            $arrayDeducciones["deuda"] = $deuda;
            return $arrayDeducciones;
    }
}