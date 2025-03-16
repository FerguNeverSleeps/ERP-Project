<?php

class LugarGeneracionXML
{

    /**
     * @var string $pais
     */
    public $pais = null;

    /**
     * @var string $departamento
     */
    public $departamento = null;

    /**
     * @var string $Municipio
     */
    public $municipio = null;

    /**
     * @var string $Idioma
     */
    public $idioma = null;


    /**
     * @return string
     */
    public function getPais()
    {
      return $this->pais;
    }

    /**
     * @param string $Pais
     * @return LugarGeneracionXML
     */
    public function setPais($pais)
    {
      $this->pais = $pais;
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
     * @param string $departamento
     * @return LugarGeneracionXML
     */
    public function setDepartamento($departamento)
    {
      $this->departamento = $departamento;
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
     * @return LugarGeneracionXML
     */
    public function setMunicipio($municipio)
    {
      $this->municipio = $municipio;
      return $this;
    }

    /**
     * @return string
     */
    public function getIdioma()
    {
      return $this->idioma;
    }

    /**
     * @param string $idioma
     * @return LugarGeneracionXML
     */
    public function setIdioma($idioma)
    {
      $this->idioma = $idioma;
      return $this;
    }

}
