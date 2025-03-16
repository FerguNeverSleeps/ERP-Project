<?php

class SaveTransactionsResponse
{

    /**
     * @var BatchInputSaveResponse $SaveTransactionsResult
     */
    protected $SaveTransactionsResult = null;

    /**
     * @param BatchInputSaveResponse $SaveTransactionsResult
     */
    public function __construct($SaveTransactionsResult)
    {
      $this->SaveTransactionsResult = $SaveTransactionsResult;
    }

    /**
     * @return BatchInputSaveResponse
     */
    public function getSaveTransactionsResult()
    {
      return $this->SaveTransactionsResult;
    }

    /**
     * @param BatchInputSaveResponse $SaveTransactionsResult
     * @return SaveTransactionsResponse
     */
    public function setSaveTransactionsResult($SaveTransactionsResult)
    {
      $this->SaveTransactionsResult = $SaveTransactionsResult;
      return $this;
    }

}
