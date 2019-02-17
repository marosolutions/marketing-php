<?php

namespace Maropost\Api;

use Maropost\Api\ResultTypes\GetResult;
use Maropost\Api\Abstractions\Api;
use Httpful\Request;

/**
 * Class Campaigns
 * @package Maropost\Api
 */
class Campaigns
{
    use Api;

    /**
     * Reports constructor.
     * @param int $accountId
     * @param string $authToken
     */
    public function __construct(int $accountId, string $authToken)
    {
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'campaigns';
    }

}