<?php

class Pago
{

    /**
     * @var string $forma
     */
    public $forma = null;

    /**
     * @var string $metodo
     */
    public $metodo = null;

    /**
     * @var string $banco
     */
    public $banco = null;

    /**
     * @var string $tipo_cuenta
     */
    public $tipo_cuenta = null;

    /**
     * @var string $numero_cuenta
     */
    public $numero_cuenta = null;
    
    /*public function __construct()
    {
        $this->forma           = $forma;
        $this->metodo = $metodo;
        $this->banco    = $banco;
        $this->tipo_cuenta         = $tipo_cuenta;
        $this->numero_cuenta               = $numero_cuenta;    
    }*/

    /**
     * @return string
     */
    public function getForma()
    {
      return $this->forma;
    }

    /**
     * @param string $Forma
     * @return Pago
     */
    public function setForma($forma)
    {
      $this->forma = $forma;
      return $this;
    }

    /**
     * @return string
     */
    public function getMetodo()
    {
      return $this->metodo;
    }

    /**
     * @param string $Metodo
     * @return Pago
     */
    public function setMetodo($metodo)
    {
      $this->metodo = $metodo;
      return $this;
    }

    /**
     * @return string
     */
    public function getBanco()
    {
      return $this->banco;
    }

    /**
     * @param string $banco
     * @return Pago
     */
    public function setBanco($banco)
    {
      $this->banco = $banco;
      return $this;
    }

    /**
     * @return string
     */
    public function getTipoCuenta()
    {
      return $this->tipo_cuenta;
    }

    /**
     * @param string $TipoCuenta
     * @return Pago
     */
    public function setTipoCuenta($tipo_cuenta)
    {
      $this->tipo_cuenta = $tipo_cuenta;
      return $this;
    }

    /**
     * @return string
     */
    public function getNumeroCuenta()
    {
      return $this->numero_cuenta;
    }

    /**
     * @param string $numero_cuenta
     * @return Pago
     */
    public function setNumeroCuenta($numero_cuenta)
    {
      $this->numero_cuenta = $numero_cuenta;
      return $this;
    }

}
