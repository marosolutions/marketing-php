<?php

namespace Maropost\Api;

use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\Abstractions\Api;
use Maropost\Api\ResultTypes\GetResult;

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
            'contact[email]' => $email,
        ];

        return $this->_get('email', $emailInUriFormat);
    }

    /**
     * Gets a list of opens for a contact
     *
     * @param int $contactId Id of the Contact
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getOpens(int $contactId, int $page): OperationResult
    {
        $resource = "{$contactId}/open_report";

        return $this->_get($resource, ['page' => $page]);
    }

    /**
     * Get a list of clicks for a contact
     *
     * @param int $contactId
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getClicks(int $contactId, int $page): OperationResult
    {
        $resource = "{$contactId}/click_report";

        return $this->_get($resource, ['page' => $page]);
    }

    /**
     * Get a list of contacts in the specified list
     *
     * @param int $listId
     * @param int $page page #. (>= 1)
     * @return OperationResult
     */
    public function getForList(int $listId, int $page): OperationResult
    {
        $overrideResource = "lists/{$listId}";

        return $this->_get('contacts', ['page' => $page], $overrideResource);
    }

    /**
     * Get the specified contact from the specified list
     *
     * @param int $listId
     * @param int $contactId
     * @return OperationResult
     */
    public function getContactForList(int $listId, int $contactId): OperationResult
    {
        $overrideResource = "lists/{$listId}";

        return $this->_get("contacts/{$contactId}", [], $overrideResource);
    }

    /**
     * Create a contact within a list. Updates if previous contact is matched by email
     *
     * @param int $listId ID of the list for which the contact is being created
     * @param string $email Email address for the contact to be created|updated
     * @param string|null $firstName First name of Contact
     * @param string|null $lastName Last name of Contact
     * @param string|null $phone Phone number of Contact
     * @param string|null $fax Fax number of Contact
     * @param string|null $uid UID for the Contact
     * @param array $customField Custom Fields passed as associative array. Keys represent the field names while values represent the values
     * @param array $addTags Tags to add to the contact. Simple array of tag names
     * @param array $removeTags Tags to remove from the contact. Simple array of tag names
     * @param bool $removeFromDNM Set this true to subscribe contact to the list, and remove it from DNM)
     * @param bool $subscribe Set this true to subscribe contact to the list; false otherwise
     * @return OperationResult
     */
    public function createOrUpdateForList(
        int $listId,
        string $email,
        string $firstName = null,
        string $lastName = null,
        string $phone = null,
        string $fax = null,
        string $uid = null,
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

        $object = new \stdClass();
        $object->contact = (object)$contact;
        $results = $this->getForEmail($email);
        if ($results->isSuccess) {
            $contactId = $results->getData()->id;
            if (!is_null($contact)) {
                // already exists. need to update.
                return $this->_put('contacts/'.$contactId, [], $object, $overrideResource);
            }
        }
        return $this->_post('contacts', [], $object, $overrideResource);

    }

    /**
     * Create a contact within a list. Updates if previous contact is matched by email
     *
     * @param int $listId ID of the list to which the contact being updated belongs
     * @param int $contactId ID of the contact being updated
     * @param string $email Email address for the contact to be updated
     * @param string|null $firstName first name of Contact
     * @param string|null $lastName last name of Contact
     * @param string|null $phone phone number of Contact
     * @param string|null $fax fax number of Contact
     * @param string|null $uid UID for the Contact
     * @param array $customField custom fields passed as associative array. Keys represent the field names while values represent the values
     * @param array $addTags tags to add to the contact. Simple array of tag names
     * @param array $removeTags tags to remove from the contact. Simple array of tag names
     * @param bool $removeFromDNM set this true to subscribe contact to the list, and remove it from DNM)
     * @param bool $subscribe set this true to subscribe contact to the list; false otherwise
     * @return OperationResult
     */
    public function updateForListAndContact(
        int $listId,
        int $contactId,
        string $email,
        string $firstName = null,
        string $lastName = null,
        string $phone = null,
        string $fax = null,
        string $uid = null,
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

        $object = new \stdClass();
        $object->contact = (object)$contact;
        return $this->_put('contacts/'.$contactId, [], $object, $overrideResource);
    }

    /**
     * Create a contact without a list. Updates if already existing email is passed.
     *
     * @param string $email email address for the contact to be created|updated
     * @param string|null $firstName first name of Contact
     * @param string|null $lastName last name of Contact
     * @param string|null $phone phone number of Contact - no symbols  (e.g., "5555555555", not "555-555-5555")
     * @param string|null $fax fax number of Contact - no symbols (e.g., "5555555555", not "555-555-5555")
     * @param string|null $uid UID the contact belongs to
     * @param array $customField Custom Field passed as array. Keys represent the field names while values represent the values
     * @param array $addTags Tags to add to the contact. Non associated array of tagnames
     * @param array $removeTags Tags to remove from the contact. Non associative array of tagnames
     * @param bool $removeFromDNM Set this true to subscribe contact to the list, and remove it from DNM)
     * @return OperationResult
     */
    public function createOrUpdateContact(
        string $email,
        string $firstName = null,
        string $lastName = null,
        string $phone = null,
        string $fax = null,
        string $uid = null,
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
        $object = new \stdClass();
        $object->contact = (object)$contact;
        $results = $this->getForEmail($email);
        if ($results->isSuccess) {
            $contactId = $results->getData()->id;
            if (!is_null($contact)) {
                // already exists. need to update.
                return $this->_put($contactId, [], $object);
            }
        }
        return $this->_post("", [], $object);
    }

    /**
     * Creates or Updates Contact - Multiple lists can be subscribed, unsubscribed. Multiple workflows can be unsubscribed.
     *
     * @param string $email Email address for the contact to be created|updated
     * @param string|null $firstName first name of Contact
     * @param string|null $lastName last name of Contact
     * @param string|null $phone phone number of Contact
     * @param string|null $fax fax number of Contact
     * @param string|null $uid UID the contact belongs to
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
    public function createOrUpdateForListsAndWorkflows(
        string $email,
        string $firstName = null,
        string $lastName = null,
        string $phone = null,
        string $fax = null,
        string $uid = null,
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
            'subscribe_list_ids' => implode(",", $subscribeListIds),
            'unsubscribe_list_ids' => implode(",", $unsubscribeListIds),
            'unsubscribe_workflow_ids'  => implode(",", $unsubscribeWorkflowIds),
            'unsubscribe_campaign'  => $unsubscribeCampaign
        ];
        $options = $this->_discardNullAndEmptyValues($options);
        $contact['options'] = $options;

        $object = new \stdClass();
        $object->contact = (object)$contact;
        $results = $this->getForEmail($email);
        if ($results->isSuccess) {
            $contactId = $results->getData()->id;
            if (!is_null($contact)) {
                // already exists. need to update.
                return $this->_put($contactId, [], $object);
            }
        }
        return $this->_post('', [], $object);
    }

    /**
     * Delete contacts from all list having the email as passed
     *
     * @param string $email
     * @return OperationResult
     */
    public function deleteFromAllLists(string $email): OperationResult
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
    public function deleteFromLists(int $contactId, array $listIds = []): OperationResult
    {
        $params = [];
        if (!empty($listIds)) {
            $params['list_ids'] = implode(',', $listIds);
        }
        return $this->_delete($contactId, $params);

    }

    /**
     * Delete contacts having the specified UID
     *
     * @param string $uid
     * @return OperationResult
     */
    public function deleteContactForUid(string $uid): OperationResult
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
    public function deleteListContact(int $listId, int $contactId): OperationResult
    {
        $overrideResource = "lists/{$listId}";
        $resource = "contacts/{$contactId}";
        return $this->_delete($resource, [], $overrideResource);
    }

    /**
     * Unsubscribe contact having the email|uid as specified by the Value parameter
     *
     * @param string $contactFieldValue The value of the field to search the contact based on
     * @param string $contactFieldName The name of the field for which the value is being passed. For now, the possible
     *        values are: email or uid
     * @return OperationResult
     */
    public function unsubscribeAll(string $contactFieldValue, string $contactFieldName = 'email'): OperationResult
    {
        $params = [
            "contact[{$contactFieldName}]" => $contactFieldValue
        ];
        return $this->_put('unsubscribe_all', $params);
    }

    /**
     * @return OperationResult
     */
    public function getAllLists(): OperationResult {
        $params = array(
            'no_counts' => true,
        );

        return $this->_get( '', $params, 'lists' );
    }

    public function updateContact( $id, $args ): OperationResult {
        $params = array(
            'id' => $id,
        );

        return $this->_put( 'contacts', $params, (object) $args );
    }

    public function getAllBrands(): OperationResult {
        $params = array(
            'no_counts' => true,
        );

        return $this->_get( '', $params, 'brands' );
    }

    public function unsubscribe_by_id($contact_id, $params): OperationResult {

        return $this->_delete('contacts/'.$contact_id, $params, '');
    }

    public function get_contact_by_id($id): OperationResult {

        return $this->_get('contacts/'.$id, [], '');
    }

    public function do_not_mail($email){
        $obj = (object) array(
            'global_unsubscribe' => array(
                'email' => $email
            ),
        );

        return $this->_post('global_unsubscribes', [], $obj, '');

    }

    public function remove_from_do_not_mail($email){

        return $this->_delete('delete', ['email' => $email], 'global_unsubscribes', null);

    }

    public function get_do_not_mail($email){

        return $this->_get('email', ['contact[email]' => $email], 'global_unsubscribes');

    }

    public function subscribe_multiple($email, $list_ids){

        $obj = (object) array(
            'contact' => array(
                'email' => $email
            ),
        );
        $params = array(
            'list_ids' => $list_ids,
        );

        return $this->_post('contacts', $params, $obj, '');

    }

}
