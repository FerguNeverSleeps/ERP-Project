<?php

class Enviar
{

    /**
     * @var string $bearerToken
     */
    protected $bearerToken = null;

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @var Payroll $payroll
     */
    protected $payroll = null;

    /**
     * @param string $bearerToken
     * @param string $type
     * @param Payroll $payroll
     */
    public function __construct($bearerToken, $type, $payroll)
    {
      $this->bearerToken = $bearerToken;
      $this->type = $type;
      $this->payroll = $payroll;
    }

    /**
     * @return string
     */
    public function getbearerToken()
    {
      return $this->bearerToken;
    }

    /**
     * @param string $bearerToken
     * @return Enviar
     */
    public function setbearerToken($bearerToken)
    {
      $this->bearerToken = $bearerToken;
      return $this;
    }

    /**
     * @return string
     */
    public function gettype()
    {
      return $this->type;
    }

    /**
     * @param string $type
     * @return Enviar
     */
    public function settype($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return Payroll
     */
    public function getPayroll()
    {
      return $this->payroll;
    }

    /**
     * @param Payroll $payroll
     * @return Enviar
     */
    public function setPayroll($payroll)
    {
      $this->payroll = $payroll;
      return $this;
    }

}
