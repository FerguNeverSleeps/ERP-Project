<?php


/**
 * Version V201605. First version of BatchInput Web Service
 */
class BatchInput extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'CanDeleteBatchInput' => '\\CanDeleteBatchInput',
      'WSCredentials' => '\\WSCredentials',
      'CanDeleteBatchInputResponse' => '\\CanDeleteBatchInputResponse',
      'DeleteBatchInput' => '\\DeleteBatchInput',
      'DeleteBatchInputResponse' => '\\DeleteBatchInputResponse',
      'SaveTransactions' => '\\SaveTransactions',
      'ArrayOfBatchInputDTO' => '\\ArrayOfBatchInputDTO',
      'BatchInputDTO' => '\\BatchInputDTO',
      'BatchInputProcessType' => '\\BatchInputProcessType',
      'ArrayOfProcessParameters' => '\\ArrayOfProcessParameters',
      'ProcessParameters' => '\\ProcessParameters',
      'SaveTransactionsResponse' => '\\SaveTransactionsResponse',
      'BatchInputSaveResponse' => '\\BatchInputSaveResponse',
      'About' => '\\About',
      'AboutResponse' => '\\AboutResponse',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = null)
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
        'soap_version' => 1,
        'features' => 1,
      ), $options);
      if (!$wsdl) {

        // URL del servidor SOAP - Produccion
        $wsdl = 'https://ubw.unit4cloud.com/co_ses_prod_webservices/service.svc?BatchInputService/BatchInput';

        // URL del servidor SOAP - Pruebas
        // $wsdl = 'https://ubw-accept01.unit4cloud.com/co_ses_acpt_webservices/service.svc?BatchInputService/BatchInput';
      }
      parent::__construct($wsdl, $options);
    }

    /**
     * Check if can delete BatchInput
     *
     * @param CanDeleteBatchInput $parameters
     * @return CanDeleteBatchInputResponse
     */
    public function CanDeleteBatchInput(CanDeleteBatchInput $parameters)
    {
      return $this->__soapCall('CanDeleteBatchInput', array($parameters));
    }

    /**
     * Delete BatchInput
     *
     * @param DeleteBatchInput $parameters
     * @return DeleteBatchInputResponse
     */
    public function DeleteBatchInput(DeleteBatchInput $parameters)
    {
      return $this->__soapCall('DeleteBatchInput', array($parameters));
    }

    /**
     * Saves transactions to be imported
     *
     * @param SaveTransactions $parameters
     * @return SaveTransactionsResponse
     */
    public function SaveTransactions(SaveTransactions $parameters)
    {
      return $this->__soapCall('SaveTransactions', array($parameters));
    }

    /**
     * Diagnostics method that checks for presence of nessecary components and database connection
     *
     * @param About $parameters
     * @return AboutResponse
     */
    public function About(About $parameters)
    {
      return $this->__soapCall('About', array($parameters));
    }

}
