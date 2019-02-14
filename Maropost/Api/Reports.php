<?php

namespace Maropost\Api;

use Maropost\Api\ResultTypes\Reports\GetResult;
use Maropost\Api\Abstractions\Api;
use Httpful\Request;

/**
 * Class Reports
 * @package Maropost\Api
 */
class Reports
{
    use Api;

    /**
     * Reports constructor.
     * @param $accountId
     * @param $authToken
     */
    public function __construct($accountId, $authToken)
	{
		$this->auth_token = $authToken;
		$this->accountId = $accountId;
		$this->resource = 'reports';
	}

    /**
     * @param string|null $resource
     * @param array $params
     * @return GetResult
     */
    public function get(string $resource = null, array $params = []) : GetResult
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
