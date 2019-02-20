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

    public function __construct($accountId, $authToken)
    {
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'contacts';
    }

    /**
     * Searches a contact with email and get all the details of the contact
     *
     * @param string $email
     * @return OperationResult
     */
    public function getForEmail(string $email) : OperationResult
    {
        $emailInUriFormat = [
            'contact[email]' => $email
        ];

        return $this->_get('email', $emailInUriFormat);
    }

    /**
     * Gets a list of opens for a contact
     *
     * @param int $contactId
     * @return OperationResult
     */
    public function getOpens(int $contactId) : OperationResult
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
    public function getClicks(int $contactId) : OperationResult
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
    public function getForList(int $listId) : OperationResult
    {
        $overrideResource = "lists/{$listId}";

        return $this->_get('contacts', [], $overrideResource);
    }

    public function createOrUpdateForList() : OperationResult
    {

    }

    public function createOrUpdateContact() : OperationResult
    {

    }

    public function createOrUpdateContacts() : OperationResult
    {


    }

    public function deleteAll() : OperationResult
    {

    }

    public function deleteContact() : OperationResult
    {

    }

    public function deleteContactForUid() : OperationResult
    {

    }

    public function deleteListContact() : OperationResult
    {

    }

    public function unsubscribeAll() : OperationResult
    {

    }

}