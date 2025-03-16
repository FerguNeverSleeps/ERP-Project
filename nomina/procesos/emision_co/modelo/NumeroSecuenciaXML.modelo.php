<?php

class NumeroSecuenciaXML
{

    /**
     * @var string $codigo_trabajador
     */
    public $codigo_trabajador = null;

    /**
     * @var string $Prefijo
     */
    public $prefijo = null;

    /**
     * @var string $consecutivo
     */
    public $consecutivo = null;

    
    /*public function __construct()
    {
        $this->codigo_trabajador           = $codigo_trabajador;
        $this->prefijo = $prefijo;
        $this->consecutivo    = $consecutivo;
    
    }*/

    /**
     * @return string
     */
    public function getCodigoTrabajador()
    {
      return $this->codigo_trabajador;
    }

    /**
     * @param string $codigo_trabajador
     * @return NumeroSecuenciaXML
     */
    public function setCodigoTrabajador($codigo_trabajador)
    {
      $this->codigo_trabajador = $codigo_trabajador;
      return $this;
    }

    /**
     * @return string
     */
    public function getPrefijo()
    {
      return $this->prefijo;
    }

    /**
     * @param string $prefijo
     * @return NumeroSecuenciaXML
     */
    public function setPrefijo($prefijo)
    {
      $this->prefijo = $prefijo;
      return $this;
    }

    /**
     * @return string
     */
    public function getConsecutivo()
    {
      return $this->consecutivo;
    }

    /**
     * @param string $consecutivo
     * @return NumeroSecuenciaXML
     */
    public function setConsecutivo($consecutivo)
    {
      $this->consecutivo = $consecutivo;
      return $this;
    }

}
