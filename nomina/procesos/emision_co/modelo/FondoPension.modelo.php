<?php

class FondoPension
{

    /**
     * @var string $porcentaje
     */
    public $porcentaje = null;

    /**
     * @var string $deduccion
     */
    public $deduccion = null;

    /*
    public function __construct()
    {
        $this->porcentaje = $porcentaje;
        $this->deduccion  = $deduccion;
    
    }*/

    /**
     * @return string
     */
    public function getPorcentaje()
    {
      return $this->porcentaje;
    }

    /**
     * @param string $porcentaje
     * @return FondoPension
     */
    public function setPorcentaje($porcentaje)
    {
      $this->porcentaje = $porcentaje;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeduccion()
    {
      return $this->deduccion;
    }

    /**
     * @param string $deduccion
     * @return FondoPension
     */
    public function setDeduccion($deduccion)
    {
      $this->deduccion = $deduccion;
      return $this;
    }

}
