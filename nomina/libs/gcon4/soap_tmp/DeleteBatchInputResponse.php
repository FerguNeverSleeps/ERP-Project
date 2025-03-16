<?php

class DeleteBatchInputResponse
{

    /**
     * @var string $DeleteBatchInputResult
     */
    protected $DeleteBatchInputResult = null;

    /**
     * @param string $DeleteBatchInputResult
     */
    public function __construct($DeleteBatchInputResult)
    {
      $this->DeleteBatchInputResult = $DeleteBatchInputResult;
    }

    /**
     * @return string
     */
    public function getDeleteBatchInputResult()
    {
      return $this->DeleteBatchInputResult;
    }

    /**
     * @param string $DeleteBatchInputResult
     * @return DeleteBatchInputResponse
     */
    public function setDeleteBatchInputResult($DeleteBatchInputResult)
    {
      $this->DeleteBatchInputResult = $DeleteBatchInputResult;
      return $this;
    }

}
