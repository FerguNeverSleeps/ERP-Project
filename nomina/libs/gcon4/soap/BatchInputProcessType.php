<?php

class BatchInputProcessType
{

    /**
     * @var ArrayOfProcessParameters $ParameterList
     */
    protected $ParameterList = null;

    /**
     * @var int $ReportVariant
     */
    protected $ReportVariant = null;

    /**
     * @param int $ReportVariant
     */
    public function __construct($ReportVariant)
    {
      $this->ReportVariant = $ReportVariant;
    }

    /**
     * @return ArrayOfProcessParameters
     */
    public function getParameterList()
    {
      return $this->ParameterList;
    }

    /**
     * @param ArrayOfProcessParameters $ParameterList
     * @return BatchInputProcessType
     */
    public function setParameterList($ParameterList)
    {
      $this->ParameterList = $ParameterList;
      return $this;
    }

    /**
     * @return int
     */
    public function getReportVariant()
    {
      return $this->ReportVariant;
    }

    /**
     * @param int $ReportVariant
     * @return BatchInputProcessType
     */
    public function setReportVariant($ReportVariant)
    {
      $this->ReportVariant = $ReportVariant;
      return $this;
    }

}
