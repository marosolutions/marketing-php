<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\Contacts;

final class ContactsTests extends TestCase
{
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "";
    const CONTACT_ID = 5;
    const LIST_ID = 1;

    public function testGetForEmail()
    {
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->getForList(self::LIST_ID, 1);
        $contact = $results->getData()[0];
        $contactId = $contact->id;
        $email = $contact->email;
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $contact = $results->getData();
        $this->assertEquals($contactId, $contact->id);
    }

    public function testGetOpens()
    {
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        //$results = $svc->getForList(self::LIST_ID, 1);
        //$contact = $results->getData()[0];
        $contactId = self::CONTACT_ID; //$contact->id;
        $results = $svc->getOpens($contactId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $report1 = $data[0];

        $results = $svc->getOpens($contactId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        if(!empty($data))
        {
            $this->assertNotEquals($report1->recorded_at, $data[0]->recorded_at);
        }
    }

    public function testGetClicks()
    {
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        //$results = $svc->getForList(self::LIST_ID, 1);
        //$contact = $results->getData()[0];
        $contactId = self::CONTACT_ID; // $contact->id;
        $results = $svc->getClicks($contactId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertTrue(is_array($data));
        $report1 = $data[0];

        $results = $svc->getClicks($contactId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        if(!empty($data))
        {
            $this->assertNotEquals($report1->recorded_at, $data[0]->recorded_at);
        }
    }

    public function testGetForList()
    {
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->getForList(self::LIST_ID, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertTrue(is_array($data));
        $contact1 = $data[0];

        $results = $svc->getForList(self::LIST_ID, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        if(!empty($data))
        {
            $this->assertNotEquals($contact1->id, $data[0]->id);
        }
    }

    public function testGetContactForList()
    {
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->getContactForList(self::LIST_ID, self::CONTACT_ID);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
    }

    /**
     * Returns a test email address.
     */
    private function getEmail() : string
    {
        $rootEmail = "phpUnitTest-";
        $email = $rootEmail;
        try {
            $email = $rootEmail . (new DateTime())->format('Ymd-Hisu') . "@maropost.com";
        }
        catch (Exception $e) {
            $this->assertNull($e);
        }
        return $email;
    }

    public function testCreateOrUpdateContact()
    {
        // first, test create a contact
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = $this->getEmail();
        $results = $svc->getForEmail($email);
        $this->assertFalse($results->isSuccess, "New contact ". $email . " shouldn't exist but does. Re-evaluate unit test.");
        $results = $svc->createOrUpdateContact($email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, ["customField1"=>true,"customField2"=>null,"customField3"=>123], ["tag1","tag2"],
            ["removeTag1","removeTag2"], false, false);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to add contact w/ email " . $email);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed added.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly added contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now attempt to update.
        $results = $svc->createOrUpdateContact($email, "UpdatedFirstName", "UpdatedLastName", "444-444-4444",
            "888-888-8888", null, ["customField4"=>false, "customField5"=>"test string", "customField6"=>43.9],
            ["tag3","tag4"], ["removeTag3","removeTag4"], true, true);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to update contact w/ email " . $email);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed updated
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly updated contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertEquals("UpdatedFirstName", $data->first_name);
        $this->assertEquals("UpdatedLastName", $data->last_name);
        $this->assertEquals("444-444-4444", $data->phone, "phone is " . $data->phone . " for email " . $email);
        $this->assertEquals("888-888-8888", $data->fax, "fax is " . $data->fax . " for email " . $email);
        $this->assertNotEmpty($data->updated_at);
        $this->assertNotEquals($data->updated_at, $data->created_at);
    }

    public function testCreateOrUpdateForList()
    {
        // first, test create a contact
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = $this->getEmail();
        $results = $svc->getForEmail($email);
        $this->assertFalse($results->isSuccess, "New contact ". $email . " shouldn't exist but does. Re-evaluate unit test.");
        $results = $svc->createOrUpdateForList(self::LIST_ID, $email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, ["customField1"=>true,"customField2"=>null,"customField3"=>123], ["tag1","tag2"],
            ["removeTag1","removeTag2"], false, false);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to add contact w/ email " . $email);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed added.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly added contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now attempt to update.
        $results = $svc->createOrUpdateForList(self::LIST_ID, $email, "UpdatedFirstName", "UpdatedLastName", "444-444-4444",
            "888-888-8888", null, ["customField4"=>false, "customField5"=>"test string", "customField6"=>43.9],
            ["tag3","tag4"], ["removeTag3","removeTag4"], true, true);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to update contact w/ email " . $email);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed updated
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly updated contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertEquals("UpdatedFirstName", $data->first_name);
        $this->assertEquals("UpdatedLastName", $data->last_name);
        $this->assertEquals("444-444-4444", $data->phone, "phone is " . $data->phone . " for email " . $email);
        $this->assertEquals("888-888-8888", $data->fax, "fax is " . $data->fax . " for email " . $email);
        $this->assertNotEmpty($data->updated_at);
        $this->assertNotEquals($data->updated_at, $data->created_at);
    }

    public function testCreateOrUpdateForListsAndWorkflows()
    {
        // first, test create a contact
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = $this->getEmail();
        $results = $svc->getForEmail($email);
        $this->assertFalse($results->isSuccess, "New contact ". $email . " shouldn't exist but does. Re-evaluate unit test.");
        $results = $svc->createOrUpdateForListsAndWorkflows($email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, ["customField1"=>true,"customField2"=>null,"customField3"=>123], ["tag1","tag2"],
            ["removeTag1","removeTag2"], false, [21,94,95], [], [7,45]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to add contact w/ email " . $email);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed added.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly added contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now attempt to update.
        $results = $svc->createOrUpdateForListsAndWorkflows($email, "UpdatedFirstName", "UpdatedLastName", "444-444-4444",
            "888-888-8888", null, ["customField4"=>false, "customField5"=>"test string", "customField6"=>43.9],
            ["tag3","tag4"], ["removeTag3","removeTag4"], true, [94], [], [7]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to update contact w/ email " . $email);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed updated
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly updated contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertEquals("UpdatedFirstName", $data->first_name);
        $this->assertEquals("UpdatedLastName", $data->last_name);
        $this->assertEquals("444-444-4444", $data->phone, "phone is " . $data->phone . " for email " . $email);
        $this->assertEquals("888-888-8888", $data->fax, "fax is " . $data->fax . " for email " . $email);
        $this->assertNotEmpty($data->updated_at);
        $this->assertNotEquals($data->updated_at, $data->created_at);
    }

    public function testUpdateForListAndContact()
    {
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = $this->getEmail();
        $results = $svc->getForEmail($email);
        $this->assertFalse($results->isSuccess, "New contact ". $email . " shouldn't exist but does. Re-evaluate unit test.");
        $results = $svc->createOrUpdateForListsAndWorkflows($email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, ["customField1"=>true,"customField2"=>null,"customField3"=>123], ["tag1","tag2"],
            ["removeTag1","removeTag2"], false, [21,94,95], [], [7,45]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to add contact w/ email " . $email);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed added.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly added contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now attempt to update.
        $contactId = $results->getData()->id;
        $results = $svc->updateForListAndContact(self::LIST_ID, $contactId, $email,
            "UpdatedFirstName", "UpdatedLastName", "444-444-4444",
            "888-888-8888", null, ["customField4"=>false, "customField5"=>"test string", "customField6"=>43.9],
            ["tag3","tag4"], ["removeTag3","removeTag4"], true, true);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Failed to update contact w/ id " . $contactId);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now affirm that it was indeed updated
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Newly updated contact ". $email . " should exist but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertEquals("UpdatedFirstName", $data->first_name);
        $this->assertEquals("UpdatedLastName", $data->last_name);
        $this->assertEquals("444-444-4444", $data->phone, "phone is " . $data->phone . " for id " . $contactId);
        $this->assertEquals("888-888-8888", $data->fax, "fax is " . $data->fax . " for id " . $contactId);
        $this->assertNotEmpty($data->updated_at);
    }

    public function testDeleteAll()
    {
        // first, setup/configure a contact
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = "phpUnitTestDeleteFromAll@maropost.com";
        $results = $svc->createOrUpdateForListsAndWorkflows($email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, [], [], [], false, [21,94,95]);
        $this->assertTrue($results->isSuccess, $results->errorMessage);

        // now affirm that it was indeed setup.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " should exist, but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now delete from lists, and test effectiveness
        $results = $svc->deleteFromAllLists($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " failed to delete?");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // finally affirm that it was indeed removed.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $listSubscriptions = $results->getData()->list_subscriptions;
        $this->assertCount(0, $listSubscriptions);
    }

    public function testDeleteFromLists()
    {
        // first, setup/configure a contact
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = "phpUnitTestDeleteContact@maropost.com";
        $results = $svc->createOrUpdateForListsAndWorkflows($email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, [], [], [], false, [21,94,95]);
        $this->assertTrue($results->isSuccess, $results->errorMessage);

        // now affirm that it was indeed added.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " should exist, but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now delete from lists, and test effectiveness
        $results = $svc->deleteFromLists($results->getData()->id, [21,95]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " failed to delete?");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // finally affirm that it was indeed removed.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $listSubscriptions = $results->getData()->list_subscriptions;
        $this->assertCount(1, $listSubscriptions);
        $this->assertEquals(94, $listSubscriptions[0]->list_id);
    }

    public function testDeleteListContact()
    {
        // first, setup/configure a contact
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = "phpUnitTestDeleteListContact@maropost.com";
        $results = $svc->createOrUpdateForListsAndWorkflows($email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, [], [], [], false, [21,94]);
        $this->assertTrue($results->isSuccess, $results->errorMessage);

        // now affirm that it was indeed added.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " should exist, but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now delete from list, and test effectiveness
        $results = $svc->deleteListContact(21, $results->getData()->id);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " failed to delete?");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // finally affirm that it was indeed removed.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $listSubscriptions = $results->getData()->list_subscriptions;
        $this->assertCount(1, $listSubscriptions);
        $this->assertEquals(94, $listSubscriptions[0]->list_id);
    }

    public function testUnsubscribeAll()
    {
        // first, setup/configure a contact
        $svc = new Contacts(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $email = "phpUnitTestUnsubscribeAll@maropost.com";
        $results = $svc->createOrUpdateForListsAndWorkflows($email, "TestFirstName", "TestLastName", "555-555-5555",
            "999-999-9999", null, [], [], [], false, [21,94,95]);
        $this->assertTrue($results->isSuccess, "Creating contact ". $email . " failed. Re-evaluate unit test.");

        // now affirm that it was indeed setup.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " should exist, but does not. Possible bug in REST API itself.");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // now unsubscribe all, and test effectiveness
        $results = $svc->unsubscribeAll($email, "email");
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess, "Contact ". $email . " failed to unsubscribe?");
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // finally affirm that it was indeed removed.
        $results = $svc->getForEmail($email);
        $this->assertInstanceOf(OperationResult::class, $results);
        $listSubscriptions = $results->getData()->list_subscriptions;
        $this->assertCount(0, $listSubscriptions, "Action succeeded, but still has subscriptions. Possible bug in REST API itself.");
    }
}
