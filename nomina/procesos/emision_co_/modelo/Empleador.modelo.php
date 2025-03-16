<?php

class Empleador
{

    /**
     * @var string $razon_social
     */
    public $razon_social = null;

    /**
     * @var string $nit
     */
    public $nit = null;

    /**
     * @var string $dv
     */
    public $dv = null;

    /**
     * @var string $hora_gen
     */
    public $hora_gen = null;

    /**
     * @var string $pais
     */
    public $pais = null;

    /**
     * @var string $departamento
     */
    public $departamento = null;

    /**
     * @var string $municipio
     */
    public $municipio = null;

    /**
     * @var string $direccion
     */
    public $direccion = null;
/*
    
    public function __construct()
    {
        $this->razon_social           = $razon_social;
        $this->nit           = $nit;
        $this->dv = $dv;
        $this->hora_gen    = $hora_gen;
        $this->pais    = $pais;
        $this->municipio    = $municipio;
        $this->direccion    = $direccion;
    
    }*/

    /**
     * @return string
     */
    public function getRazonSocial()
    {
      return $this->razon_social;
    }

    /**
     * @param string $razon_social
     * @return Empleador
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
     * @return Empleador
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
     * @return Empleador
     */
    public function setDv($dv)
    {
      $this->dv = $dv;
      return $this;
    }

    /**
     * @return string
     */
    public function getPais()
    {
      return $this->pais;
    }

    /**
     * @param string $departamento
     * @return Empleador
     */
    public function setDepartamento($departamento)
    {
      $this->departamento = $departamento;
      return $this;
    }

    /**
     * @return string
     */
    public function getDepartamento()
    {
      return $this->departamento;
    }

    /**
     * @param string $pais
     * @return Empleador
     */
    public function setPais($pais)
    {
      $this->pais = $pais;
      return $this;
    }

    /**
     * @return string
     */
    public function getMunicipio()
    {
      return $this->municipio;
    }

    /**
     * @param string $municipio
     * @return Empleador
     */
    public function setMunicipio($municipio)
    {
      $this->municipio = $municipio;
      return $this;
    }

    /**
     * @return string
     */
    public function getDireccion()
    {
      return $this->direccion;
    }

    /**
     * @param string $direccion
     * @return Empleador
     */
    public function setDireccion($direccion)
    {
      $this->direccion = $direccion;
      return $this;
    }
}
