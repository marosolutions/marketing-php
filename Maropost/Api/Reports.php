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

	public function get($params) : GetResult
    {
        try {
            return $this->_get('', $params);
        } catch (\Exception $e) {
            die('exception ');
        }
    }

    public function getOpens(array $params = []) : GetResult
    {
        try {
            return $this->_get('opens', $params);
        } catch (\Exception $e) {
            die('exception ');
        }
    }

    public function getClicks(array $params = []) : GetResult
    {
        try {
            return $this->_get('clicks', $params);
        } catch (\Exception $e) {

        }
    }

    public function getBounces(array $params = []) : GetResult
    {
        try {
            return $this->_get('bounces', $params);
        } catch (\Exception $e) {

        }
    }

    public function getUnsubscribes(array $params = []) : GetResult
    {
        try {
            return $this->_get('unsubscribes', $params);
        } catch (\Exception $e) {

        }
    }

    public function getComplaints(array $params = []) : GetResult
    {
        try {
            return $this->_get('complaints', $params);
        } catch (\Exception $e) {

        }
    }

    public function getAbReports(array $params = []) : GetResult
    {
        try {
            $this->resource = 'ab_reports';
            return $this->_get('', $params);
        } catch (\Exception $e) {

        }
    }

    public function getJourneys(array $params = []) : GetResult
    {
        try {
            return $this->_get('journeys', $params);
        } catch (\Exception $e) {

        }
    }

    /**
     * @param string|null $resource
     * @param array $params
     * @return GetResult
     */
    private function _get(string $resource = null, array $params = []) : GetResult
    {

        try {
            $url = $this->url();
            $url .= !empty($resource) ? '/' . $resource : '';

            // be explicit about json format
            $url .= '.json';
            $url .= $this->getQueryString($params);
echo "calling {$url}\n";
            $this->apiResponse = Request::get($url)->send();

        } catch (\Exception $e) {

        }

        return new GetResult($this->apiResponse);
    }

}
