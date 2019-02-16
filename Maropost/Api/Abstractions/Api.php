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
    private $resource;

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

    private function _discardNullAndEmptyValues(array $params) : array
    {
        $transformedArray = [];
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $transformedArray[$key] = $value;
            }
        }

        return $transformedArray;
    }

    /**
     * @param string|null $resource
     * @param array $params
     * @return GetResult
     */
    private function _get(string $resource = null, array $params = []): GetResult
    {

        try {
            $url = $this->url();
            $url .= !empty($resource) ? '/' . $resource : '';

            // be explicit about json format
            $url .= '.json';
            $url .= $this->getQueryString($params);

            $this->apiResponse = Request::get($url)->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }
}
