<?php

error_reporting(E_ALL | E_STRICT);

require __DIR__ . '/LambdaClient.php';
$client = new \LukeWaite\LambdaRuntimePhp\LambdaClient();

if (getenv('_HANDLER') === false) {
    die('Error: _HANDLER not defined in environment.');
}

if (getenv('LAMBDA_TASK_ROOT') === false) {
    die('Error: LAMBDA_TASK_ROOT not defined in environment.');
}

if (getenv('AWS_LAMBDA_RUNTIME_API') === false) {
    die('Error: AWS_LAMBDA_RUNTIME_API not defined in environment.');
}

$handler = explode('.', $_ENV['_HANDLER']);
$handlerFile = $handler[0];
$handlerFunction = $handler[1];

try {
    require_once $_ENV['LAMBDA_TASK_ROOT'] . '/' . $handlerFile . '.php';
} catch (Exception $e) {
    $client->sendInitializationError($e);
    throw $e;
}

while (true) {

    $event = $client->fetchEvent();
    $response = null;

    try {
        $response = $handlerFunction($event['payload']);
        $client->sendResponse($event['lambda_invocation_id'], $response);
    } catch (Exception $e) {
        $client->sendError($event['lambda_invocation_id'], $e);
    }
}
