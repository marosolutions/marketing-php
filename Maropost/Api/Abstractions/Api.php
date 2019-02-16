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
     * @param string|null $overrideResource If "truthy", it replaces (for this call only) that specified by $this->resource.
     * @return string
     */
    private function url(string $overrideResource) : string
    {
        $url = 'https://api.maropost.com/accounts/';
        $url .= empty($this->resource) ? $this->accountId : $this->accountId . '/' . $overrideResource?:$this->resource;

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
     * @param string|null $overrideRootResource if "truthy", it replaces (for this call only) the value set for $this->resource. (Not to be confused with $resource, which is more specific.)
     * @return GetResult
     */
    private function _get(string $resource = null, array $params = [], string $overrideRootResource = null): GetResult
    {

        try {
            $url = $this->url($overrideRootResource);
            $url .= !empty($resource) ? '/' . $resource : '';

            // be explicit about json format
            $url .= '.json';
            $url .= $this->getQueryString($params);

            $this->apiResponse = Request::get($url)->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }

    /**
     * @param string|null $resource
     * @param array $params
     * @param object $object a PHP object. Will be posted as serialized JSON.
     * @param string|null $overrideRootResource if "truthy", it replaces (for this call only) the value set for $this->resource. (Not to be confused with $resource, which is more specific.)
     * @return GetResult
     */
    private function _post(string $resource, array $params, $object, string $overrideRootResource = null) : GetResult
    {

        try {
            $url = $this->url($overrideRootResource);
            $url .= !empty($resource) ? '/' . $resource : '';

            // be explicit about json format
            $url .= '.json';
            $url .= $this->getQueryString($params);
            echo "calling {$url}\n";
            $json = json_encode($object);
            $this->apiResponse = Request::post($url)
                ->addHeaders(array(
                    "Content-type"=>"application/json",
                    "Accept"=>"application/json"
                ))
                ->body($json)
                ->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }

    /**
     * @param string|null $resource
     * @param array $params
     * @param string|null $overrideRootResource if "truthy", it replaces (for this call only) the value set for $this->resource. (Not to be confused with $resource, which is more specific.)
     * @return GetResult
     */
    private function _put(string $resource = null, array $params = [], string $overrideRootResource = null) : GetResult
    {

        try {
            $url = $this->url($overrideRootResource);
            $url .= !empty($resource) ? '/' . $resource : '';

            // be explicit about json format
            $url .= '.json';
            $url .= $this->getQueryString($params);
            echo "calling {$url}\n";
            $this->apiResponse = Request::put($url)
                ->addHeaders(array(
                    "Content-type"=>"application/json",
                    "Accept"=>"application/json"
                ))
                ->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }
}
