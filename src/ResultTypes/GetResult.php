<?php

namespace Maropost\Api\ResultTypes;

use Maropost\Api\Abstractions\OperationResult;
use Httpful\Response;

/**
 * Maps an Httpful Response onto an OperationResult.
 * @package Maropost\Api\ResultTypes\Reports
 */
class GetResult extends OperationResult
{
    /**
     * @param Response|null $apiResponse
     * @param string|null $errorMessage only used if $apiResponse is null.
     */
    public function __construct(Response $apiResponse = null, string $errorMessage = null)
    {
        if (is_null($apiResponse)) {
            $this->isSuccess = false;
            $this->errorMessage = $errorMessage;
        }
        else {
            $this->data = $apiResponse->body;
            $this->errorMessage = !empty($this->errorMessage) ? $this->errorMessage : isset($apiResponse->body->error) ? $apiResponse->body->error : '';
            if ($apiResponse->code >= 200 && $apiResponse->code < 300) {
                $this->isSuccess = (strlen($this->errorMessage) == 0);
            }
            else {
                $this->isSuccess = false;
                if (empty($this->errorMessage)) {
                    if (isset($apiResponse->body->message) && !empty($apiResponse->body->message)) {
                        $this->errorMessage = $apiResponse->body->message;
                    }
                    elseif ($apiResponse->code >= 500) {
                        $this->errorMessage = $apiResponse->code.": Maropost experienced a server error and could not complete your request.";
                    }
                    elseif ($apiResponse->code >= 400) {
                        $this->errorMessage = $apiResponse->code.": Either your accountId, authToken, or one (or more) of your function arguments are invalid.";
                    }
                    elseif ($apiResponse->code >= 300) {
                        $this->errorMessage = $apiResponse->code.": This Maropost API function is currently unavailable.";
                    }
                    else {
                        $this->errorMessage = $apiResponse->code.": Unexpected final response from Maropost.";
                    }
                }
            }
        }
    }
}
