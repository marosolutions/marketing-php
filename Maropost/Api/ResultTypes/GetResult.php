<?php

namespace Maropost\Api\ResultTypes;

use Maropost\Api\Abstractions\OperationResult;
use Httpful\Response;

/**
 * Class GetResult
 * @package Maropost\Api\ResultTypes\Reports
 */
class GetResult extends OperationResult
{
    /**
     * GetResult constructor.
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
            $this->isSuccess = ($apiResponse->code < 300 && strlen($this->errorMessage) == 0);
        }
    }
}
