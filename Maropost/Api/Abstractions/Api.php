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
     * @var string
     */
    private $auth_token;
    /**
     * @var int
     */
    private $accountId;
    /**
     * @var
     */
    private $apiResponse;
    /**
     * @var string
     */
    private $resource;

    /**
     * returns an associative array representing the HTTP Headers.
     * @return array
     */
    private function getHttpHeaders() : array {
        return array(
            "Content-type"=>"application/json",
            "Accept"=>"application/json"
        );
    }

    /**
     * @param array $keyValuePairs
     * @return string
     */
    private function getQueryString(array $keyValuePairs)
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
            $url .= $this->getQueryString($params);

            $json = json_encode($object);
            $this->apiResponse = Request::post($url, $json)
                ->addHeaders($this->getHttpHeaders())
                ->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }

    /**
     * @param string $resource
     * @param array $params
     * @param object $object a PHP object. Will be PUT as serialized JSON.
     * @param string|null $overrideRootResource if "truthy", it replaces (for this call only) the value set for $this->resource. (Not to be confused with $resource, which is more specific.)
     * @return GetResult
     */
    private function _put(string $resource, array $params, $object, string $overrideRootResource = null) : GetResult
    {
        try {
            $url = $this->url($overrideRootResource);
            $url .= !empty($resource) ? '/' . $resource : '';
            $url .= $this->getQueryString($params);

            $json = json_encode($object);
            $this->apiResponse = Request::put($url, $json)
                ->addHeaders($this->getHttpHeaders())
                ->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }

    /**
     * Deletes the given resource at the url().
     *
     * @param string $resource
     * @param array $params
     * @param string|null $overrideRootResource
     * @return OperationResult
     */
    private function _delete(string $resource, array $params = [], string $overrideRootResource = null) : OperationResult
    {
        try {
            $url = $this->url($overrideRootResource);
            $url .= !empty($resource) ? '/' . $resource : '';
            $url .= $this->getQueryString($params);

            $this->apiResponse = Request::delete($url)
                ->addHeaders($this->getHttpHeaders())
                ->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }
}
