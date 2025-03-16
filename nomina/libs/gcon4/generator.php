<?php
require './vendor/autoload.php';


$generator = new \Wsdl2PhpGenerator\Generator();
$generator->generate(
    new \Wsdl2PhpGenerator\Config(array(
                                      // 'inputFile' => 'http://demoemision.thefactoryhka.com.pa/ws/obj/v1.0/Service.svc?wsdl',
                                      'inputFile' => 'https://ubw-preview.unit4cloud.com/co_ses_prev_webservices/service.svc?BatchInputService/BatchInput',
                                      'outputDir' => './soap',
                                  ))
);

$generator->generate(
    new \Wsdl2PhpGenerator\Config(array(
                                      //'inputFile' => 'http://demoservice.thefactoryhka.com.pa:8080/PaServicePDF/ws/ServicePDF.wsdl',
                                      // 'inputFile' => 'http://demoemision.thefactoryhka.com.pa/ws/obj/v1.0/Service.svc?wsdl',
                                      'inputFile' => 'https://ubw-preview.unit4cloud.com/co_ses_prev_webservices/service.svc?BatchInputService/BatchInput',
                                      //'outputDir' => './soapPDF',
                                      'outputDir' => './soap_tmp',
                                      'soapClientOptions' => array(
                                          'soap_version' => SOAP_1_1
                                      )
                                  ))
);