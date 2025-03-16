<?php

class DeleteBatchInput
{

    /**
     * @var string $batchId
     */
    protected $batchId = null;

    /**
     * @var string $interfaceId
     */
    protected $interfaceId = null;

    /**
     * @var WSCredentials $credentials
     */
    protected $credentials = null;

    /**
     * @param string $batchId
     * @param string $interfaceId
     * @param WSCredentials $credentials
     */
    public function __construct($batchId, $interfaceId, $credentials)
    {
      $this->batchId = $batchId;
      $this->interfaceId = $interfaceId;
      $this->credentials = $credentials;
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
     * @return DeleteBatchInput
     */
    public function setBatchId($batchId)
    {
      $this->batchId = $batchId;
      return $this;
    }

    /**
     * @return string
     */
    public function getInterfaceId()
    {
      return $this->interfaceId;
    }

    /**
     * @param string $interfaceId
     * @return DeleteBatchInput
     */
    public function setInterfaceId($interfaceId)
    {
      $this->interfaceId = $interfaceId;
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
     * @return DeleteBatchInput
     */
    public function setCredentials($credentials)
    {
      $this->credentials = $credentials;
      return $this;
    }

}
