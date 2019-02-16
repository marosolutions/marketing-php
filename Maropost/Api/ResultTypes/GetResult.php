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
     * @param Response $apiResponse
     */
    public function __construct(Response $apiResponse)
    {
        $this->data = $apiResponse->body;
        $this->isSuccess = true;
        $this->errorMessage = !empty($this->errorMessage) ? $this->errorMessage : isset($apiResponse->body->error) ? $apiResponse->body->error : '';

    }
}
