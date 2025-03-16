<?php

class ProveedorXML
{

    /**
     * @var string $razon_social
     */
    protected $razon_social = null;

    /**
     * @var string $nit
     */
    protected $nit = null;

    /**
     * @var string $dv
     */
    protected $dv = null;

    /**
     * @var string $software_id
     */
    protected $software_id = null;

    /**
     * @var string $software_pin
     */
    protected $software_pin = null;


    /**
     * @return string
     */
    public function getRazonSocial()
    {
      return $this->razon_social;
    }

    /**
     * @param string $razon_social
     * @return ProveedorXML
     */
    public function setRazonSocial($razon_social)
    {
      $this->razon_social = $razon_social;
      return $this;
    }

    /**
     * @return string
     */
    public function getNit()
    {
      return $this->nit;
    }

    /**
     * @param string $nit
     * @return ProveedorXML
     */
    public function setNit($nit)
    {
      $this->nit = $nit;
      return $this;
    }

    /**
     * @return string
     */
    public function getDv()
    {
      return $this->dv;
    }

    /**
     * @param string $dv
     * @return ProveedorXML
     */
    public function setDv($dv)
    {
      $this->dv = $dv;
      return $this;
    }

    /**
     * @return string
     */
    public function getSoftwareId()
    {
      return $this->software_id;
    }

    /**
     * @param string $software_id
     * @return ProveedorXML
     */
    public function setSoftwareId($software_id)
    {
      $this->software_id = $software_id;
      return $this;
    }

    /**
     * @return string
     */
    public function getSoftwarePin()
    {
      return $this->software_pin;
    }

    /**
     * @param string $software_pin
     * @return ProveedorXML
     */
    public function setSoftwarePin($software_pin)
    {
      $this->software_pin = $software_pin;
      return $this;
    }

}
