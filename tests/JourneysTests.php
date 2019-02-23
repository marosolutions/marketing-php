<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\Journeys;

final class JourneysTests extends TestCase
{
    // TODO: Whenever you want to run these tests, set appropriate auth_token, journey, contact and uid
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "";
    const JOURNEY_ID = 0;
    const CONTACT_ID = 0;
    const UID = "";

    public function testGet()
    {
        // page 1
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertObjectHasAttribute("id", $data[0]);
        $id = $data[0]->id;

        // page 2
        $results = $svc->get(2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertObjectHasAttribute("id", $data[0]);
        $id2 = $data[0]->id;

        $this->assertNotEquals($id, $id2, "first id on page 2 (". $id2 . ") should not equal first id on page 1 (". $id . ").");
    }

    public function testGetCampaigns()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->getCampaigns(self::JOURNEY_ID, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);

        // TODO: if we find a journey with more than 200 campaigns, then we can test page 2.
    }

    public function testGetContacts()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->getCampaigns(self::JOURNEY_ID, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);

        // TODO: if we find a journey with more than 200 contacts, then we can test page 2.
    }

    public function testPauseJourneyForContact()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);

        // first make sure this journey is active.
        $results = $svc->startJourneyForContact(self::JOURNEY_ID, self::CONTACT_ID);

        $results = $svc->pauseJourneyForContact(self::JOURNEY_ID, self::CONTACT_ID);
        $this->assertInstanceOf(OperationResult::class, $results);
        // TODO: consider enhancing the test.
    }

    public function testPauseJourneyForUid()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);

        // first make sure this journey is active.
        $results = $svc->startJourneyForUid(self::JOURNEY_ID, self::UID);

        $results = $svc->pauseJourneyForUid(self::JOURNEY_ID, self::UID);
        $this->assertInstanceOf(OperationResult::class, $results);
        // TODO: consider enhancing the test.
    }

    public function testResetJourneyForContact()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);
        // works for active OR paused journeys
        $results = $svc->resetJourneyForContact(self::JOURNEY_ID, self::CONTACT_ID);
        $this->assertInstanceOf(OperationResult::class, $results);
        // TODO: consider enhancing the test.
    }

    public function testResetJourneyForUid()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);
        // works for active OR paused journeys
        $results = $svc->resetJourneyForUid(self::JOURNEY_ID, self::UID);
        $this->assertInstanceOf(OperationResult::class, $results);
        // TODO: consider enhancing the test.
    }

    public function testStartJourneyForContact()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);

        // first make sure this journey is paused.
        $svc->pauseJourneyForContact(self::JOURNEY_ID, self::CONTACT_ID);

        $results = $svc->startJourneyForContact(self::JOURNEY_ID, self::CONTACT_ID);
        $this->assertInstanceOf(OperationResult::class, $results);
        // TODO: consider enhancing the test.
    }

    public function testStartJourneyForUid()
    {
        $svc = new Journeys(self::ACCOUNT_ID, self::AUTH_TOKEN);

        // first ensure this journey is paused.
        $svc->pauseJourneyForUid(self::JOURNEY_ID, self::UID);

        $results = $svc->startJourneyForUid(self::JOURNEY_ID, self::UID);
        $this->assertInstanceOf(OperationResult::class, $results);
        // TODO: consider enhancing the test.
    }
}
