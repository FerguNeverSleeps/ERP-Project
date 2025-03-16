<?php

class BatchInputSaveResponse
{

    /**
     * @var string $BatchId
     */
    protected $BatchId = null;

    /**
     * @var int $OrderNo
     */
    protected $OrderNo = null;

    /**
     * @var string $ReturnResponse
     */
    protected $ReturnResponse = null;

    /**
     * @var int $ReturnCode
     */
    protected $ReturnCode = null;

    /**
     * @var string $ReturnText
     */
    protected $ReturnText = null;

    /**
     * @param int $OrderNo
     * @param int $ReturnCode
     */
    public function __construct($OrderNo, $ReturnCode)
    {
      $this->OrderNo = $OrderNo;
      $this->ReturnCode = $ReturnCode;
    }

    /**
     * @return string
     */
    public function getBatchId()
    {
      return $this->BatchId;
    }

    /**
     * @param string $BatchId
     * @return BatchInputSaveResponse
     */
    public function setBatchId($BatchId)
    {
      $this->BatchId = $BatchId;
      return $this;
    }

    /**
     * @return int
     */
    public function getOrderNo()
    {
      return $this->OrderNo;
    }

    /**
     * @param int $OrderNo
     * @return BatchInputSaveResponse
     */
    public function setOrderNo($OrderNo)
    {
      $this->OrderNo = $OrderNo;
      return $this;
    }

    /**
     * @return string
     */
    public function getReturnResponse()
    {
      return $this->ReturnResponse;
    }

    /**
     * @param string $ReturnResponse
     * @return BatchInputSaveResponse
     */
    public function setReturnResponse($ReturnResponse)
    {
      $this->ReturnResponse = $ReturnResponse;
      return $this;
    }

    /**
     * @return int
     */
    public function getReturnCode()
    {
      return $this->ReturnCode;
    }

    /**
     * @param int $ReturnCode
     * @return BatchInputSaveResponse
     */
    public function setReturnCode($ReturnCode)
    {
      $this->ReturnCode = $ReturnCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getReturnText()
    {
      return $this->ReturnText;
    }

    /**
     * @param string $ReturnText
     * @return BatchInputSaveResponse
     */
    public function setReturnText($ReturnText)
    {
      $this->ReturnText = $ReturnText;
      return $this;
    }

}
