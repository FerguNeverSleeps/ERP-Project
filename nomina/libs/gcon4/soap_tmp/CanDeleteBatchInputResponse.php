<?php

class CanDeleteBatchInputResponse
{

    /**
     * @var string $CanDeleteBatchInputResult
     */
    protected $CanDeleteBatchInputResult = null;

    /**
     * @param string $CanDeleteBatchInputResult
     */
    public function __construct($CanDeleteBatchInputResult)
    {
      $this->CanDeleteBatchInputResult = $CanDeleteBatchInputResult;
    }

    /**
     * @return string
     */
    public function getCanDeleteBatchInputResult()
    {
      return $this->CanDeleteBatchInputResult;
    }

    /**
     * @param string $CanDeleteBatchInputResult
     * @return CanDeleteBatchInputResponse
     */
    public function setCanDeleteBatchInputResult($CanDeleteBatchInputResult)
    {
      $this->CanDeleteBatchInputResult = $CanDeleteBatchInputResult;
      return $this;
    }

}
