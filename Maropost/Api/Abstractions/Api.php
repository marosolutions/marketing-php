<?php

namespace Maropost\Api\Abstractions;

use Httpful\Request;
use Maropost\Api\ResultTypes\GetResult;

/**
 * Trait Api
 * @package Maropost\Api\Abstractions
 */
trait Api {

    /**
     * @var
     */
    private $auth_token;
    /**
     * @var
     */
    private $accountId;
    /**
     * @var
     */
    private $apiResponse;
    /**
     * @var
     */
    private $rescource;

    /**
     * @param $keyValuePairs
     * @return string
     */
    private function getQueryString($keyValuePairs)
    {
        $queryStr = '?auth_token=' . $this->auth_token;
        foreach ($keyValuePairs as $key => $value) {
            $queryStr .= '&';
            $queryStr .= $key . '=' . $value;
        }

        return $queryStr;
    }

    /**
     * @return string
     */
    private function url() : string
    {
        $url = 'api.maropost.com/accounts/';
        $url .= empty($this->resource) ? $this->accountId : $this->accountId . '/' . $this->resource;

        return $url;
    }
}
