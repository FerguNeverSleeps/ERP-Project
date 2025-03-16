<?php

class Payroll
{

    /**
     * @var string $Periodo
     */
    public $Periodo = null;

    /**
     * @var string $NumeroSecuenciaXML
     */
    public $NumeroSecuenciaXML = null;

    /**
     * @var string $LugarGeneracionXML
     */
    public $LugarGeneracionXML = null;

    /**
     * @var string $ProveedorXML
     */
    public $ProveedorXML = null;

    /**
     * @var string $InformacionGeneral
     */
    public $InformacionGeneral = null;

    /**
     * @var string $Empleador
     */
    public $Empleador = null;

    /**
     * @var string $Trabajador
     */
    public $Trabajador = null;

    /**
     * @var string $Pago
     */
    public $Pago = null;

    /**
     * @var string $FechasPago
     */
    public $FechasPago = null;

    /**
     * @var string $Devengados
     */
    public $Devengados = null;

    /**
     * @var string $Deducciones
     */
    public $Deducciones = null;

    /**
     * @var string $DevengadosTotal
     */
    public $DevengadosTotal = null;

    /**
     * @var string $DeduccionesTotal
     */
    public $DeduccionesTotal = null;

    /**
     * @var string $ComprobanteTotal
     */
    public $ComprobanteTotal = null;

    
    
    public function __construct($Periodo, $NumeroSecuenciaXML, $LugarGeneracionXML, $ProveedorXML, $InformacionGeneral, $Empleador, $Trabajador, $Pago, $FechasPago, $Devengados, $Deducciones, $DevengadosTotal, $DeduccionesTotal, $ComprobanteTotal)
    {
        $this->Periodo            = $Periodo;
        $this->NumeroSecuenciaXML = $NumeroSecuenciaXML;
        $this->LugarGeneracionXML = $LugarGeneracionXML;
        $this->ProveedorXML       = $ProveedorXML;
        $this->InformacionGeneral = $InformacionGeneral;
        $this->Empleador          = $Empleador;
        $this->Trabajador         = $Trabajador;
        $this->Pago               = $Pago;
        $this->FechasPago         = $FechasPago;
        $this->Devengados         = $Devengados;
        $this->Deducciones        = $Deducciones;
        $this->DevengadosTotal    = $DevengadosTotal;
        $this->DeduccionesTotal   = $DeduccionesTotal;
        $this->ComprobanteTotal   = $ComprobanteTotal;
    
    }

    /**
     * @return Periodo
     */
    public function getPeriodo()
    {
      return $this->Periodo;
    }

    /**
     * @param Periodo $Periodo
     * @return Payroll
     */
    public function setPeriodo($Periodo)
    {
      $this->Periodo = $Periodo;
      return $this;
    }

    /**
     * @return NumeroSecuenciaXML
     */
    public function getNumeroSecuenciaXML()
    {
      return $this->NumeroSecuenciaXML;
    }

    /**
     * @param NumeroSecuenciaXML $NumeroSecuenciaXML
     * @return Payroll
     */
    public function setNumeroSecuenciaXML($NumeroSecuenciaXML)
    {
      $this->NumeroSecuenciaXML = $NumeroSecuenciaXML;
      return $this;
    }

    /**
     * @return LugarGeneracionXML
     */
    public function getLugarGeneracionXML()
    {
      return $this->LugarGeneracionXML;
    }

    /**
     * @param LugarGeneracionXML $LugarGeneracionXML
     * @return Payroll
     */
    public function setLugarGeneracionXML($LugarGeneracionXML)
    {
      $this->LugarGeneracionXML = $LugarGeneracionXML;
      return $this;
    }

    /**
     * @return ProveedorXML
     */
    public function getProveedorXML()
    {
      return $this->ProveedorXML;
    }

    /**
     * @param ProveedorXML $ProveedorXML
     * @return Payroll
     */
    public function setProveedorXML($ProveedorXML)
    {
      $this->ProveedorXML = $ProveedorXML;
      return $this;
    }

    /**
     * @return InformacionGeneral
     */
    public function getInformacionGeneral()
    {
      return $this->InformacionGeneral;
    }

    /**
     * @param InformacionGeneral $InformacionGeneral
     * @return Payroll
     */
    public function setInformacionGeneral($InformacionGeneral)
    {
      $this->InformacionGeneral = $InformacionGeneral;
      return $this;
    }

    /**
     * @return Empleador
     */
    public function getEmpleador()
    {
      return $this->Empleador;
    }

    /**
     * @param Empleador $Empleador
     * @return Payroll
     */
    public function setEmpleador($Empleador)
    {
      $this->Empleador = $Empleador;
      return $this;
    }

    /**
     * @return Trabajador
     */
    public function getTrabajador()
    {
      return $this->Trabajador;
    }

    /**
     * @param Trabajador $Trabajador
     * @return Payroll
     */
    public function setTrabajador($Trabajador)
    {
      $this->Trabajador = $Trabajador;
      return $this;
    }

    /**
     * @return Pago
     */
    public function getPago()
    {
      return $this->Pago;
    }

    /**
     * @param Pago $Pago
     * @return Payroll
     */
    public function setPago($Pago)
    {
      $this->Pago = $Pago;
      return $this;
    }

    /**
     * @return array
     */
    public function getFechasPago()
    {
      return $this->FechasPago;
    }

    /**
     * @param array $FechasPago
     * @return Payroll
     */
    public function setFechasPago($FechasPago)
    {
      $this->FechasPago = $FechasPago;
      return $this;
    }

    /**
     * @return Devengados
     */
    public function getDevengados()
    {
      return $this->Devengados;
    }

    /**
     * @param Devengados $Devengados
     * @return Payroll
     */
    public function setDevengados($Devengados)
    {
      $this->Devengados = $Devengados;
      return $this;
    }

    /**
     * @return Deducciones
     */
    public function getDeducciones()
    {
      return $this->Deducciones;
    }

    /**
     * @param Deducciones $Deducciones
     * @return Payroll
     */
    public function setDeducciones($Deducciones)
    {
      $this->Deducciones = $Deducciones;
      return $this;
    }

    /**
     * @return string
     */
    public function getDevengadosTotal()
    {
      return $this->DevengadosTotal;
    }

    /**
     * @param string $DevengadosTotal
     * @return Payroll
     */
    public function setDevengadosTotal($DevengadosTotal)
    {
      $this->DevengadosTotal = $DevengadosTotal;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeduccionesTotal()
    {
      return $this->DeduccionesTotal;
    }

    /**
     * @param string $DeduccionesTotal
     * @return Payroll
     */
    public function setDeduccionesTotal($DeduccionesTotal)
    {
      $this->DeduccionesTotal = $DeduccionesTotal;
      return $this;
    }

    /**
     * @return string
     */
    public function getComprobanteTotal()
    {
      return $this->ComprobanteTotal;
    }

    /**
     * @param string $ComprobanteTotal
     * @return Payroll
     */
    public function setComprobanteTotal($ComprobanteTotal)
    {
      $this->ComprobanteTotal = $ComprobanteTotal;
      return $this;
    }

}
