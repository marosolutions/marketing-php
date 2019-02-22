<?php

namespace Maropost\Api;

use Maropost\Api\Abstractions\Api;
use Maropost\Api\Abstractions\OperationResult;

/**
 * Class Campaigns
 * @package Maropost\Api
 */
class Campaigns
{
    use Api;

    /**
     * Campaigns constructor.
     * @param $accountId
     * @param $authToken
     */
    public function __construct($accountId, $authToken)
    {
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'campaigns';
    }

    /**
     * Gets the list of campaigns (200 campaigns per page).
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function get(int $page = 1): OperationResult
    {
        return $this->_get(null, ['page' => $page]);
    }

    /**
     * Gets the list of delivered report for the specified campaign
     *
     * @param int $id The campaign ID
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getDeliveredReports(int $id, int $page = 1): OperationResult
    {
        $overrideUrl = $this->resource . "/$id";
        return $this->_get('delivered_report', ['page' => $page], $overrideUrl);
    }

    /**
     * Gets a list of Open Reports for the specified Campaign
     *
     * @param int $id The campaign ID
     * @param bool|null $unique Gets for unique contacts
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getOpenReports(int $id, bool $unique = null, int $page = 1): OperationResult
    {
        $params = ['page' => $page];
        if (!empty($unique)) {
            $params['unique'] = $unique === true ? 'true' : 'false';
        }

        $overrideUrl = $this->resource . "/$id";
        return $this->_get('open_report', $params, $overrideUrl);
    }

    /**
     * Gets a list of Click Reports for the specified Campaign
     *
     * @param int $id The campaign ID
     * @param bool|null $unique Gets for unique contacts
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getClickReports(int $id, bool $unique = null, int $page = 1): OperationResult
    {
        $params = ['page' => $page];
        if (!empty($unique)) {
            $params['unique'] = $unique === true ? 'true' : 'false';
        }

        $overrideUrl = $this->resource . "/$id";
        return $this->_get('click_report', $params, $overrideUrl);
    }

    /**
     * Gets a list of Link Reports for the specified Campaign
     *
     * @param int $id The campaign ID
     * @param bool|null $unique Gets for unique contacts
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getLinkReports(int $id, bool $unique = null, int $page = 1): OperationResult
    {
        $params = ['page' => $page];
        if (!empty($unique)) {
            $params['unique'] = $unique === true ? 'true' : 'false';
        }

        $overrideUrl = $this->resource . "/$id";
        return $this->_get('link_report', $params, $overrideUrl);

    }

    /**
     * Gets a list of Bounce Reports for the specified Campaign
     *
     * @param int $id The campaign ID
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getBounceReports(int $id, int $page = 1): OperationResult
    {
        $overrideUrl = $this->resource . "/$id";
        return $this->_get('bounce_report', ['page' => $page], $overrideUrl);
    }

    /**
     * Gets a list of soft bounce reports for the specified Campaign
     *
     * @param int $id The campaign ID
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getSoftBounceReports(int $id, int $page): OperationResult
    {
        $overrideUrl = $this->resource . "/$id";
        return $this->_get('soft_bounce_report', ['page' => $page], $overrideUrl);
    }

    /**
     * Gets a list of hard bounces for the specified campaigns
     *
     * @param int $id The campaign ID
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getHardBounceReports(int $id, int $page): OperationResult
    {
        $overrideUrl = $this->resource . "/$id";
        return $this->_get('hard_bounce_report', ['page' => $page], $overrideUrl);
    }

    /**
     * Gets a list of unsubscribe reports for the specified campaign
     *
     * @param int $id The campaign ID
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getUnsubscribeReports(int $id, int $page = 1): OperationResult
    {

        $overrideUrl = $this->resource . "/$id";
        return $this->_get('unsubscribe_report', ['page' => $page], $overrideUrl);
    }

    /**
     * Gets a list of complain reports for the specified campaign
     *
     * @param int $id The campaign ID
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getComplaintReports(int $id, int $page = 1): OperationResult
    {

        $overrideUrl = $this->resource . "/$id";
        return $this->_get('complaint_report', ['page' => $page], $overrideUrl);
    }


}