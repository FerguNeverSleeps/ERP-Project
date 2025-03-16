<?php

class Periodo
{

    /**
     * @var string $fecha_ingreso
     */
    public $fecha_ingreso = null;

    /**
     * @var string $fecha_liquidacion_inicio
     */
    public $fecha_liquidacion_inicio = null;

    /**
     * @var string $fecha_liquidacion_fin
     */
    public $fecha_liquidacion_fin = null;

    /**
     * @var string $tiempo_laborado
     */
    public $tiempo_laborado = null;

    /**
     * @var string $fecha_gen
     */
    public $fecha_gen = null;

    
    public function __construct( $fecha_ingreso = "", $fecha_liquidacion_inicio = "", $fecha_liquidacion_fin = "", $tiempo_laborado = "", $fecha_gen = "" )
    {
      $this->fecha_ingreso            = $fecha_ingreso !="" ? $fecha_ingreso : "";
      $this->fecha_liquidacion_inicio = $fecha_liquidacion_inicio !="" ? $fecha_liquidacion_inicio : "";
      $this->fecha_liquidacion_fin    = $fecha_liquidacion_fin !="" ? $fecha_liquidacion_fin : "";
      $this->tiempo_laborado          = $tiempo_laborado !="" ? $tiempo_laborado : "";
      $this->fecha_gen                = $fecha_gen !="" ? $fecha_gen : "";
    }

    /**
     * @return string
     */
    public function getFechaIngreso()
    {
      return $this->fecha_ingreso;
    }

    /**
     * @param string $FechaIngreso
     * @return Periodo
     */
    public function setFechaIngreso($fecha_ingreso)
    {
      $this->fecha_ingreso = $fecha_ingreso;
    }

    /**
     * @return string
     */
    public function getFechaLiquidacionInicio()
    {
      return $this->fecha_liquidacion_inicio;
    }

    /**
     * @param string $FechaLiquidacionInicio
     * @return Periodo
     */
    public function setFechaLiquidacionInicio($fecha_liquidacion_inicio)
    {
      $this->fecha_liquidacion_inicio = $fecha_liquidacion_inicio;
    }

    /**
     * @return string
     */
    public function getFechaLiquidacionFin()
    {
      return $this->fecha_liquidacion_fin;
    }

    /**
     * @param string $fecha_liquidacion_fin
     * @return Periodo
     */
    public function setFechaLiquidacionFin($fecha_liquidacion_fin)
    {
      $this->fecha_liquidacion_fin = $fecha_liquidacion_fin;
    }

    /**
     * @return string
     */
    public function getTiempoLaborado()
    {
      return $this->tiempo_laborado;
    }

    /**
     * @param string $TiempoLaborado
     * @return Periodo
     */
    public function setTiempoLaborado($tiempo_laborado)
    {
      $this->tiempo_laborado = $tiempo_laborado;
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
     * @return Periodo
     */
    public function setFechaGen($fecha_gen)
    {
      $this->fecha_gen = $fecha_gen;
    }

}
