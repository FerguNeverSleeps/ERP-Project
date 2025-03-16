<?php 
require_once 'soap_tmp/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function saveTransactionsUsingSoap()
{
    // URL del servidor SOAP
    $wsdl = 'https://ubw-preview.unit4cloud.com/co_ses_prev_webservices/service.svc?BatchInputService/BatchInput';

    // Crear el cliente SOAP
    $client = new SoapClient($wsdl, array('trace' => 1, 'exception' => 0));

    // Crea una instancia del cliente BatchInput
    $batchInputClient = new BatchInput();

    // Ahora, crea un array de objetos BatchInputDTO y añádelo a batchInput
    $batchInputDTOs = array();

    // Crea el primer objeto BatchInputDTO con los datos del primer bloque de XML
    $batchInputDTO1 = new BatchInputDTO(
            '51057801',
            0,
            204000.00,
            'EM-100',
            'P',
            new \DateTime('2023-07-25T12:39:27'),
            0,
            204000.00,
            204000.00,
            'ISE',
            false,
            new \DateTime('2023-07-25T12:39:27'),
            204000.00,
            'COP',
            1,
            'Cocepto de nomina',
            'ISE',
            '1004',
            'EM-100',
            null,
            'PEM10001',
            '5105',
            '1',
            new \DateTime('2023-07-25T12:39:27'),
            0.00,
            new \DateTime('2023-07-25T12:39:27'),
            false,
            0.00,
            0.00,
            0.00,
            '202307Q1',
            'NM',
            0,
            0,
            false,
            0,
            0,
            0,
            'NOMINA',
            1,
            1,
            '0',
            false,
            new \DateTime('2023-07-25T12:39:27'),
            'GL',
            0.00,
            0.00,
            0.00,
            new \DateTime('2023-07-25T12:39:27'),
            0,
            0,
            'NM');

    // Agrega el primer BatchInputDTO al array de batchInputDTOs
    $batchInputDTOs[] = $batchInputDTO1;

    // Crea el segundo objeto BatchInputDTO con los datos del segundo bloque de XML
    $batchInputDTO2 = new BatchInputDTO(
            '25050101',
            0,
            -204000.00,
            'PS10031',
            'P',
            new \DateTime('2023-07-25T12:39:27'),
            1,
            -204000.00,
            -204000.00,
            'ISE',
            false,
            new \DateTime('2023-07-25T12:39:27'),
            -204000.00,
            'COP',
            -1,
            '1104 nomina por pagar',
            'ISE',
            null,
            'S018',
            'EM-100',
            null,
            null,
            null,
            new \DateTime('2023-07-25T12:39:27'),
            0.00,
            new \DateTime('2023-07-25T12:39:27'),
            false,
            0.00,
            0.00,
            0.00,
            '202307Q1',
            'NM',
            0,
            0,
            false,
            0,
            0,
            0,
            'NOMINA',
            0,
            0,
            '0',
            false,
            new \DateTime('2023-07-25T12:39:27'),
            'AP',
            0.00,
            0.00,
            0.00,
            new \DateTime('2023-07-25T12:39:27'),
            0,
            0,
            'NM');

    // Agrega el segundo BatchInputDTO al array de batchInputDTOs
    $batchInputDTOs[] = $batchInputDTO2;

    // Crear el objeto ArrayOfBatchInputDTO y asignarle el array de objetos BatchInputDTO
    $arrayOfBatchInputDTO = new ArrayOfBatchInputDTO();
    $arrayOfBatchInputDTO->setBatchInputDTO($batchInputDTOs);

    // Crear el objeto BatchInputProcessType
    $batchInputProcessType = new BatchInputProcessType('99');
    // Crear la lista de ProcessParameters
    $parameterList = new ArrayOfProcessParameters();

    // Agregar los parámetros a la lista
    $parameter = new ProcessParameters();
    $parameter->setName('batch_id');
    $parameter->setValue('202307Q1-EM10001');
    $parameterList->setProcessParameters([$parameter]);

    // Asignar la lista de parámetros al objeto BatchInputProcessType
    $batchInputProcessType->setParameterList($parameterList);

    // Crear el objeto WSCredentials y configurar las credenciales
    $wsCredentials = new WSCredentials();
    $wsCredentials->setUsername('nomina');
    $wsCredentials->setClient('ISE');
    $wsCredentials->setPassword('hqusZCC4gC!iW2');

    // Crear el objeto SaveTransactions y asignarle los datos
    $saveTransactions = new SaveTransactions(
        $arrayOfBatchInputDTO,
        $batchInputProcessType,
        '202307',
        '202307Q1-EM10001',
        $wsCredentials
    );

    // return $saveTransactions;

    // Crear una instancia de la clase BatchInput pasando las opciones
    $batchInput = new BatchInput();
    try {
        // Código para llamar al método del Web Service
        // Por ejemplo:
        $response = $batchInput->SaveTransactions($saveTransactions);
        return $response;

    } catch (SoapFault $e) {
        $errorMessage = $e->getMessage();
        $errorCode = $e->getCode();
        $errorFile = $e->getFile();
        $errorLine = $e->getLine();
        $errorTrace = $e->getTraceAsString();

        echo "Error en la llamada al Web Service: $errorMessage (Código: $errorCode)\n";
        echo "Archivo: $errorFile\n";
        echo "Línea: $errorLine\n";
        echo "Traza de la pila de llamadas:\n$errorTrace\n";
    }

}

echo '<pre>';
print_r(saveTransactionsUsingSoap());
echo '</pre>';
// saveTransactionsUsingSoap();