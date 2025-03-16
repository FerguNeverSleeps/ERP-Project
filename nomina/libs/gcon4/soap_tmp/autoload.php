<?php


 function autoload_ab841901379795f8fd8457a9c916f2ee($class)
{
    $classes = array(
        'BatchInput' => __DIR__ .'/BatchInput.php',
        'CanDeleteBatchInput' => __DIR__ .'/CanDeleteBatchInput.php',
        'WSCredentials' => __DIR__ .'/WSCredentials.php',
        'CanDeleteBatchInputResponse' => __DIR__ .'/CanDeleteBatchInputResponse.php',
        'DeleteBatchInput' => __DIR__ .'/DeleteBatchInput.php',
        'DeleteBatchInputResponse' => __DIR__ .'/DeleteBatchInputResponse.php',
        'SaveTransactions' => __DIR__ .'/SaveTransactions.php',
        'ArrayOfBatchInputDTO' => __DIR__ .'/ArrayOfBatchInputDTO.php',
        'BatchInputDTO' => __DIR__ .'/BatchInputDTO.php',
        'BatchInputProcessType' => __DIR__ .'/BatchInputProcessType.php',
        'ArrayOfProcessParameters' => __DIR__ .'/ArrayOfProcessParameters.php',
        'ProcessParameters' => __DIR__ .'/ProcessParameters.php',
        'SaveTransactionsResponse' => __DIR__ .'/SaveTransactionsResponse.php',
        'BatchInputSaveResponse' => __DIR__ .'/BatchInputSaveResponse.php',
        'About' => __DIR__ .'/About.php',
        'AboutResponse' => __DIR__ .'/AboutResponse.php'
    );
    if (!empty($classes[$class])) {
        include $classes[$class];
    };
}

spl_autoload_register('autoload_ab841901379795f8fd8457a9c916f2ee');

// Do nothing. The rest is just leftovers from the code generation.
{
}
