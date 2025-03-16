<?php

class SaveTransactions
{

    /**
     * @var ArrayOfBatchInputDTO $batchInput
     */
    protected $batchInput = null;

    /**
     * @var BatchInputProcessType $parameters
     */
    protected $parameters = null;

    /**
     * @var int $period
     */
    protected $period = null;

    /**
     * @var string $batchId
     */
    protected $batchId = null;

    /**
     * @var WSCredentials $credentials
     */
    protected $credentials = null;

    /**
     * @param ArrayOfBatchInputDTO $batchInput
     * @param BatchInputProcessType $parameters
     * @param int $period
     * @param string $batchId
     * @param WSCredentials $credentials
     */
    public function __construct($batchInput, $parameters, $period, $batchId, $credentials)
    {
      $this->batchInput = $batchInput;
      $this->parameters = $parameters;
      $this->period = $period;
      $this->batchId = $batchId;
      $this->credentials = $credentials;
    }

    /**
     * @return ArrayOfBatchInputDTO
     */
    public function getBatchInput()
    {
      return $this->batchInput;
    }

    /**
     * @param ArrayOfBatchInputDTO $batchInput
     * @return SaveTransactions
     */
    public function setBatchInput($batchInput)
    {
      $this->batchInput = $batchInput;
      return $this;
    }

    /**
     * @return BatchInputProcessType
     */
    public function getParameters()
    {
      return $this->parameters;
    }

    /**
     * @param BatchInputProcessType $parameters
     * @return SaveTransactions
     */
    public function setParameters($parameters)
    {
      $this->parameters = $parameters;
      return $this;
    }

    /**
     * @return int
     */
    public function getPeriod()
    {
      return $this->period;
    }

    /**
     * @param int $period
     * @return SaveTransactions
     */
    public function setPeriod($period)
    {
      $this->period = $period;
      return $this;
    }

    /**
     * @return string
     */
    public function getBatchId()
    {
      return $this->batchId;
    }

    /**
     * @param string $batchId
     * @return SaveTransactions
     */
    public function setBatchId($batchId)
    {
      $this->batchId = $batchId;
      return $this;
    }

    /**
     * @return WSCredentials
     */
    public function getCredentials()
    {
      return $this->credentials;
    }

    /**
     * @param WSCredentials $credentials
     * @return SaveTransactions
     */
    public function setCredentials($credentials)
    {
      $this->credentials = $credentials;
      return $this;
    }

}
