<?php

namespace Maropost\Api;

use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\Abstractions\Api;

/**
 * Class Contacts
 * @package Maropost\Api
 */
class Contacts
{
    use Api;

    /**
     * Contacts constructor.
     * @param $accountId
     * @param $authToken
     */
    public function __construct($accountId, $authToken)
    {
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'contacts';
    }

    /**
     * Searches a contact with email and get all the details of the contact
     *
     * @param string $email Email for which to get the contact
     * @return OperationResult
     */
    public function getForEmail(string $email): OperationResult
    {
        $emailInUriFormat = [
            'contact[email]' => $email
        ];

        return $this->_get('email', $emailInUriFormat);
    }

    /**
     * Gets a list of opens for a contact
     *
     * @param int $contactId Id of the Contact
     * @return OperationResult
     */
    public function getOpens(int $contactId): OperationResult
    {
        $resource = "{$contactId}/open_report";

        return $this->_get($resource);
    }

    /**
     * Get a list of clicks for a contact
     *
     * @param int $contactId
     * @return OperationResult
     */
    public function getClicks(int $contactId): OperationResult
    {
        $resource = "{$contactId}/click_report";

        return $this->_get($resource);
    }

    /**
     * Get a list of contacts in the specified list
     *
     * @param int $listId
     * @return OperationResult
     */
    public function getForList(int $listId): OperationResult
    {
        $overrideResource = "lists/{$listId}";

        return $this->_get('contacts', [], $overrideResource);
    }

    /**
     * Create a contact within a list. Updates if previous contact is matched by email
     *
     * @param int $listId ID of the list for which the contact is being created
     * @param string $email Email address for the contact to be created|updated
     * @param string|null $firstName Firstname of Contact
     * @param string|null $lastName Last Name of Contact
     * @param string|null $phone Phone number of Contact
     * @param string|null $fax Fax of Contact
     * @param int|null $uid UID the contact belongs to
     * @param array $customField Custom Field passed as array. Keys represent the field names while values represent the values
     * @param array $addTags Tags to add to the contact. Non associated array of tagnames
     * @param array $removeTags Tags to remove from the contact. Non associative array of tagnames
     * @param bool $removeFromDNM Set this true to subcribe contact to the list, and remove it from DNM)
     * @param bool $subscribe
     * @return OperationResult
     */
    public function createOrUpdateForList(
        int $listId,
        string $email,
        string $firstName = null,
        string $lastName = null,
        string $phone = null,
        string $fax = null,
        int $uid = null,
        array $customField = [],
        array $addTags = [],
        array $removeTags = [],
        bool $removeFromDNM = true,
        bool $subscribe = true
    ): OperationResult
    {
        $contact = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'fax' => $fax,
            'uid' => $uid,
            'custom_field' => $customField,
            'add_tags' => $addTags,
            'remove_tags' => $removeTags,
            'subscribe' => $subscribe,
            'remove_from_dnm' => $removeFromDNM,
        ];
        $contact = $this->_discardNullAndEmptyValues($contact);

        $overrideResource = "lists/{$listId}";

        return $this->_post('contacts', [], (object)$contact, $overrideResource);

    }

    /**
     * Create a contact without a list. Updates if already existing email is passed.
     *
     * @param int $contactId ID of the contact
     * @param string $email Email address for the contact to be created|updated
     * @param string|null $firstName Firstname of Contact
     * @param string|null $lastName Last Name of Contact
     * @param string|null $phone Phone number of Contact
     * @param string|null $fax Fax of Contact
     * @param int|null $uid UID the contact belongs to
     * @param array $customField Custom Field passed as array. Keys represent the field names while values represent the values
     * @param array $addTags Tags to add to the contact. Non associated array of tagnames
     * @param array $removeTags Tags to remove from the contact. Non associative array of tagnames
     * @param bool $removeFromDNM Set this true to subcribe contact to the list, and remove it from DNM)
     * @return OperationResult
     */
    public function createOrUpdateContact(
        int $contactId,
        string $email,
        string $firstName = null,
        string $lastName = null,
        string $phone = null,
        string $fax = null,
        int $uid = null,
        array $customField = [],
        array $addTags = [],
        array $removeTags = [],
        bool $removeFromDNM = true,
        bool $subscribe = true
    ): OperationResult
    {
        $contact = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'fax' => $fax,
            'uid' => $uid,
            'custom_field' => $customField,
            'add_tags' => $addTags,
            'remove_tags' => $removeTags
        ];
        $contact = $this->_discardNullAndEmptyValues($contact);

        return $this->_put($contactId, [], (object)$contact);
    }

    /**
     * Creates or Updates Contact - Multiple lists can be subscribed, unsubscribed. Multiple workflows can be unsubscribed.
     *
     * @param string $email Email address for the contact to be created|updated
     * @param string|null $firstName Firstname of Contact
     * @param string|null $lastName Last Name of Contact
     * @param string|null $phone Phone number of Contact
     * @param string|null $fax Fax of Contact
     * @param int|null $uid UID the contact belongs to
     * @param array $customField Custom Field passed as array. Keys represent the field names while values represent the values
     * @param array $addTags Tags to add to the contact. Non associated array of tagnames
     * @param array $removeTags Tags to remove from the contact. Non associative array of tagnames
     * @param bool $removeFromDNM Set this true to subcribe contact to the list, and remove it from DNM)
     * @param array $subscribeListIds Array of IDs of lists to subscribe the contact to
     * @param array $unsubscribeListIds Array of IDs of Lists to unsubscribe the contact from
     * @param array $unsubscribeWorkflowIds Array of list of IDs of workflows to unsubscribe the contact from
     * @param string|null $unsubscribeCampaign CampaignID to unsubscribe the contact from
     * @return OperationResult
     */
    public function createOrUpdateContacts(
        string $email,
        string $firstName = null,
        string $lastName = null,
        string $phone = null,
        string $fax = null,
        int $uid = null,
        array $customField = [],
        array $addTags = [],
        array $removeTags = [],
        bool $removeFromDNM = false,
        array $subscribeListIds = [],
        array $unsubscribeListIds = [],
        array $unsubscribeWorkflowIds = [],
        string $unsubscribeCampaign = null
    ): OperationResult
    {
        $contact = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'fax' => $fax,
            'uid' => $uid,
            'custom_field' => $customField,
            'add_tags' => $addTags,
            'remove_tags' => $removeTags,
            'remove_from_dnm'   => $removeFromDNM,
        ];
        $contact = $this->_discardNullAndEmptyValues($contact);

        $options = [
            'subscribe_list_ids' => $subscribeListIds,
            'unsubscribe_list_ids' => $unsubscribeListIds,
            'unsubscribe_workflow_ids'  => $unsubscribeWorkflowIds,
            'unsubscribe_campaign'  => $unsubscribeCampaign
        ];
        $options = $this->_discardNullAndEmptyValues($options);

        if (!empty($options)) {
            $contact['options'] = $options;
        }

        return $this->_post('', [], (object) $contact);
    }

    /**
     * Delete contacts from all list having the email as passed
     *
     * @param string $email
     * @return OperationResult
     */
    public function deleteAll(
        string $email
    ): OperationResult
    {
        $emailAsArray = [
            'contact[email]' => $email
        ];

        return $this->_delete('delete_all', $emailAsArray);
    }

    /**
     * Delete the contact from the specified listIDs
     *
     * @param int $contactId
     * @param array $listIds
     * @return OperationResult
     */
    public function deleteContact(
        int $contactId,
        array $listIds = []
    ): OperationResult
    {
        $params = [];
        if (!empty($listIds) {
            $params['list_ids'] = implode(',', $listIds);
        }

        return $this->_delete($contactId, $params));

    }

    /**
     * Delete contacts having the specified UID
     *
     * @param string $uid
     * @return OperationResult
     */
    public function deleteContactForUid(
        string $uid
    ): OperationResult
    {
        $params = ['uid' => $uid];

        return $this->_delete('delete_all', $params);
    }

    /**
     * Delete contact from the specified List
     *
     * @param int $listId
     * @param int $contactId
     * @return OperationResult
     */
    public function deleteListContact(
        int $listId,
        int $contactId
    ): OperationResult
    {
        $overrideResource = "lists/{$listId}";
        $resource = "contacts/{$contactId}";

        return $this->_delete($resource, [], $overrideResource);
    }

    /**
     * Unsubscribe contact having the email|uid as specified by the Value paramemter
     *
     * @param string $contactFieldValue The value of the field to search the contact based on
     * @param string $contactFieldName The name of the field for which the value is being passed. For now, the possible
     *        values are: email or uid
     * @return OperationResult
     */
    public function unsubscribeAll(
        string $contactFieldValue,
        string $contactFieldName = 'email'
    ): OperationResult
    {
        $params = [
            "contact[{$contactFieldName}]" => $contactFieldValue
        ];

        return $this->_put('unsubscribe_all', $params);
    }

}