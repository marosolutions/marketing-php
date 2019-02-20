<?php

namespace Maropost\Api;

use Maropost\Api\ResultTypes\GetResult;
use Maropost\Api\Abstractions\Api;
use Maropost\Api\Abstractions\OperationResult;

/**
 * Class Journeys
 * @package Maropost\Api
 */
class Journeys
{
    use Api;

	public function __construct(int $accountId, string $authToken)
	{
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'journeys';
	}

    /**
     * Gets the list of journeys.
     *
     * @return GetResult
     */
	public function get() : GetResult
	{
        try {
            return $this->_get('', []);
        } catch (\Exception $e) {
            die('exception ');
        }
	    /*
	    $result = new GetResult();
		$decoded = json_decode($this->getUrl("", null)->data, true);
		$result->journeys = $decoded;
		return $result;
	    */
	}

    /**
     * Gets the list of all campaigns for the specified journey.
     *
     * @param int $journeyId
     * @return GetResult
     */
	public function getCampaigns(int $journeyId) : GetResult //GetCampaignsResult
	{
        try {
            return $this->_get($journeyId."/journey_campaigns", []);
        } catch (\Exception $e) {
            die('exception ');
        }
	    /*
	    $result = new GetCampaignsResult();
	    $decoded = json_decode($this->getUrl($journeyId."/journey_campaigns", null)->data, true);
	    $result->campaigns = $decoded;
		return $result;
	    */
	}

    /**
     * Gets the list of all contacts for the specified journey.
     *
     * @param int $journeyId
     * @return GetResult
     */
	public function getContacts(int $journeyId) : GetResult // GetContactsResult
	{
        try {
            return $this->_get($journeyId."/journey_contacts", []);
        } catch (\Exception $e) {
            die('exception ');
        }
        /*
		$result = new GetContactsResult();
        $decoded = json_decode($this->getUrl($journeyId."/journey_contacts", null)->data, true);
        $result->contacts = $decoded;
		return $result;
        */
	}

    /**
     * Stops all journeys, filtered for the matching parameters.
     *
     * @param int $contactId this filter ignored if not greater than 0.
     * @param string $recipientEmail this filter ignored if null.
     * @param string $uid this filter ignored if null.
     * @return OperationResult
     */
	public function stopAll(
	    int $contactId,
        string $recipientEmail,
        string $uid
    ) : OperationResult
	{
	    $params = [];
	    if ($contactId > 0) {
	        array_push($params, "contact_id", $contactId);
        }
	    if ($recipientEmail != null) {
	        array_push($params, "email", $recipientEmail);
        }
	    if ($uid != null) {
	        array_push($params, "uid", $uid);
        }
		$result = $this->_put("stop_all_journeys", $params);
		return $result;
	}

    /**
     * Pause the specified journey for the specified contact.
     *
     * @param int $journeyId
     * @param int $contactId
     * @return OperationResult
     */
	public function pauseJourneyForContact(int $journeyId, int $contactId) : OperationResult
	{
        $result = $this->_put($journeyId."/stop/".$contactId, null);
        return $result;
	}

    /**
     * Pause the specified journey for the contact having the specified UID.
     *
     * @param int $journeyId
     * @param string $uid
     * @return OperationResult
     */
	public function pauseJourneyForUid(int $journeyId, string $uid) : OperationResult
	{
	    $params["uid"] = $uid;
        $result = $this->_put($journeyId."/stop", $params);
        return $result;
	}

    /**
     * Reset the specified journey for the specified active/paused contact. Resetting a contact to the beginning of the
     * journeys will result in sending of the same journey campaigns as originally sent.
     *
     * @param int $journeyId
     * @param int $contactId
     * @return OperationResult
     */
	public function resetJourneyForContact(int $journeyId, int $contactId) : OperationResult
	{
        $result = $this->_put($journeyId."/reset/".$contactId, null);
		return $result;
	}

    /**
     * Reset the specified journey for the active/paused contact having the specified UID. Resetting a contact to the
     * beginning of the journeys will result in sending of the same journey campaigns as originally sent.
     *
     * @param int $journeyId
     * @param string $uid
     * @return OperationResult
     */
	public function resetJourneyForUid(int $journeyId, string $uid) : OperationResult
	{
	    $params["uid"] = $uid;
        $result = $this->_put($journeyId."/reset", $params);
		return $result;
	}

    /**
     * Restarts a journey for a paused contact. Adds a new contact in journey. Retriggers the journey for a contact
     * who has finished its journey once. (To retrigger, MAKE SURE that "Retrigger Journey" option is enabled.)
     *
     * @param int $journeyId
     * @param int $contactId
     * @return OperationResult
     */
	public function startJourneyForContact(int $journeyId, int $contactId) : OperationResult
	{
        $result = $this->_put($journeyId."/start/".$contactId, null);
        return $result;
	}

    /**
     * Restarts a journey for a paused contact having the specified UID. Adds a new contact in journey. Retriggers the
     * journey for a contact who has finished its journey once. (To retrigger, MAKE SURE that "Retrigger Journey"
     * option is enabled.)
     *
     * @param int $journeyId
     * @param string $uid
     * @return OperationResult
     */
	public function startJourneyForUid(int $journeyId, string $uid) : OperationResult
	{
        $params["uid"] = $uid;
        $result = $this->_put($journeyId."/start/uid", $params);
        return $result;
	}
}