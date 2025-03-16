<?php

class Deducciones
{

  /**
   * @var Salud $salud
   */
  public $salud = null;
  /**
   * @var FondoPension $fondo_pension
   */
  public $fondo_pension = null;
  
  public function __construct($salud = "",$fondo_pension="")
  {
      $this->salud    = $salud;
      $this->fondo_pension    = $fondo_pension;
  }
  /**
   * @return Salud
   */
  public function getSalud()
  {
    return $this->salud;
  }

  /**
   * @param Salud $salud
   * @return Deducciones
   */
  public function setSalud($salud)
  {
    $this->salud = $salud;
    return $this;
  }

  /**
   * @return Deducciones
   */
  public function getFondoPension()
  {
    return $this->salud;
  }

  /**
   * @param FondoPension $fondo_pension
   * @return Deducciones
   */
  public function setFondoPension($fondo_pension)
  {
    $this->fondo_pension = $fondo_pension;
    return $this;
  }
  public function setFondoSP($datos)
  {
    return array(
      "porcentaje"  => $datos["porcentaje"],
      "porcentaje_sub"  => $datos["porcentaje_sub"],
      "deduccion" => $datos["deduccion"]
    );
  }
  public function setSindicactos($datos)
  {
    return array(
      "porcentaje"  => $datos["porcentaje"],
      "deduccion" => $datos["deduccion"]
    );
  }
  public function setSanciones($datos)
  {
    return array(
      "privada"  => $datos["privada"],
      "publica" => $datos["publica"]
    );
  }
  public function setLibranzas($datos)
  {
    $array = [];
    foreach ($datos as $key => $value) {
      $array[] = array(
        "deduccion"  => $value["deduccion"],
        "descripcion" => $value["descripcion"]
      );
    }
    return $array;
  }
  public function setPagosTerceros($datos)
  {
    $array = [];
    forEach($datos as $key=>$value){
        array_push($array, $value);
    }
    return $array;
  }
  public function setAnticipos($datos)
  {
    $array = [];
    forEach($datos as $key=>$value){
        array_push($array, $value);
    }
    return $array;
  }
  public function setOtrasDeducciones($datos)
  {
    $array = [];
    forEach($datos as $key=>$value){
        array_push($array, $value);
    }
    return $array;
  }

}
