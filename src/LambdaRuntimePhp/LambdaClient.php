<?php

namespace LukeWaite\LambdaRuntimePhp;

class LambdaClient
{

    protected $awsLambdaRuntimeApi;

    public function __construct()
    {
        $this->awsLambdaRuntimeApi = getenv('AWS_LAMBDA_RUNTIME_API');
    }

    public function fetchEvent()
    {
        $ch = curl_init("http://{$this->awsLambdaRuntimeApi}/2018-06-01/runtime/invocation/next");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        $invocation_id = '';
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$invocation_id) {
            if (!preg_match('/:\s*/', $header)) {
                return strlen($header);
            }
            $parsedHeader = preg_split('/:\s*/', $header, 2);
            if (strtolower($parsedHeader[0]) == 'lambda-runtime-aws-request-id') {
                $invocation_id = trim($parsedHeader[1]);
            }
            return strlen($header);
        });
        $body = '';
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$body) {
            $body .= $chunk;
            return strlen($chunk);
        });
        curl_exec($ch);
        if (curl_error($ch)) {
            die('Failed to fetch next Lambda invocation: ' . curl_error($ch) . "\n");
        }
        if ($invocation_id == '') {
            die('Failed to determine Lambda invocation ID');
        }
        curl_close($ch);

        $event['payload'] = json_decode($body, true);
        $event['lambda_invocation_id'] = $invocation_id;

        return $event;
    }

    function sendResponse($invocation_id, $response)
    {
        $ch = curl_init("http://{$this->awsLambdaRuntimeApi}/2018-06-01/runtime/invocation/$invocation_id/response");

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($response)
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    function sendError($invocation_id, \Exception $exception)
    {
        $response = array();
        $response['errorMessage'] = $exception->getMessage();
        $response['errorType'] = get_class($exception);
        $response['errorTrace'] = $exception->getTraceAsString();
        $response = json_encode($response);

        $ch = curl_init("http://{$this->awsLambdaRuntimeApi}/2018-06-01/runtime/invocation/$invocation_id/error");

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($response)
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    function sendInitializationError(\Exception $exception)
    {
        $response = array();
        $response['errorMessage'] = $exception->getMessage();
        $response['errorType'] = get_class($exception);
        $response['errorTrace'] = $exception->getTraceAsString();
        $response = json_encode($response);

        $ch = curl_init("http://{$this->awsLambdaRuntimeApi}/runtime/init/error");

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($response)
        ));
        curl_exec($ch);
        curl_close($ch);
    }
}
