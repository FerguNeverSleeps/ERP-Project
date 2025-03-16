<?php

class InformacionGeneral
{

    /**
     * @var string $ambiente
     */
    public $ambiente = null;

    /**
     * @var string $tipo_xml
     */
    public $tipo_xml = null;

    /**
     * @var string $fecha_gen
     */
    public $fecha_gen = null;

    /**
     * @var string $hora_gen
     */
    public $hora_gen = null;

    /**
     * @var string $hora_gen
     */
    public $periodo_nomina = null;

    /**
     * @var string $hora_gen
     */
    public $tipo_moneda = null;

    /*
    public function __construct()
    {
        $this->ambiente           = $ambiente;
        $this->tipo_xml           = $tipo_xml;
        $this->fecha_gen = $fecha_gen;
        $this->hora_gen    = $hora_gen;
        $this->periodo_nomina    = $periodo_nomina;
        $this->tipo_moneda    = $tipo_moneda;
    
    }*/

    /**
     * @return string
     */
    public function getAmbiente()
    {
      return $this->ambiente;
    }

    /**
     * @param string $ambiente
     * @return InformacionGeneral
     */
    public function setAmbiente($ambiente)
    {
      $this->ambiente = $ambiente;
      return $this;
    }

    /**
     * @return string
     */
    public function getTipoXML()
    {
      return $this->tipo_xml;
    }

    /**
     * @param string $tipo_xml
     * @return InformacionGeneral
     */
    public function setTipoXML($tipo_xml)
    {
      $this->tipo_xml = $tipo_xml;
      return $this;
    }


    /**
     * @return string
     */
    public function getFechaGen()
    {
      return $this->fecha_gen;
    }

    /**
     * @param string $fecha_gen
     * @return InformacionGeneral
     */
    public function setFechaGen($fecha_gen)
    {
      $this->fecha_gen = $fecha_gen;
      return $this;
    }


    /**
     * @return string
     */
    public function getHoraGen()
    {
      return $this->hora_gen;
    }

    /**
     * @param string $hora_gen
     * @return InformacionGeneral
     */
    public function setHoraGen($hora_gen)
    {
      $this->hora_gen = $hora_gen;
      return $this;
    }

    /**
     * @return string
     */
    public function getPeriodoNomina()
    {
      return $this->periodo_nomina;
    }

    /**
     * @param string $periodo_nomina
     * @return InformacionGeneral
     */
    public function setPeriodoNomina($periodo_nomina)
    {
      $this->periodo_nomina = $periodo_nomina;
      return $this;
    }

    /**
     * @return string
     */
    public function getTipoMoneda()
    {
      return $this->tipo_moneda;
    }

    /**
     * @param string $tipo_moneda
     * @return InformacionGeneral
     */
    public function setTipoMoneda($tipo_moneda)
    {
      $this->tipo_moneda = $tipo_moneda;
      return $this;
    }
}
