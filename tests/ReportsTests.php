<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\Reports;

final class ReportsTests extends TestCase
{
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "";

    private function makeBasicAssertions($results)
    {
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
    }

    public function testGet()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $report->get(1);
        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertObjectHasAttribute("created_at", $data[0]);
        $createdAt = $data[0]->created_at;

        // also test that 2nd page returns different data.
        $results = $report->get(2);
        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertObjectHasAttribute("created_at", $data[0]);
        $createdAt2 = $data[0]->created_at;

        $this->assertNotEquals($createdAt2, $createdAt, "potential bug re: pagination in underlying REST API");
    }

    public function testGetReport()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $report->getReport(1);
        $this->makeBasicAssertions($results);
    }

    public function testGetOpens()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $report->getOpens(1);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertObjectHasAttribute("recorded_at", $data[0]);
        $recordedAt = $data[0]->recorded_at;

        // test for page 2
        $results = $report->getOpens(2);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertObjectHasAttribute("recorded_at", $data[0]);
        $recordedAt2 = $data[0]->recorded_at;

        $this->assertNotEquals($recordedAt, $recordedAt2, 'Page 1 and Page 2 recorded_at is same');
    }

    public function testGetOpensWithFields()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $fields = ['email', 'first_name', 'last_name'];
        $results = $report->getOpens(1, $fields);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertObjectHasAttribute("recorded_at", $data[0]);
        $recordedAt = $data[0]->recorded_at;
        $firstContact = $data[0]->contact;

        $this->assertObjectHasAttribute('email', $firstContact);
        $this->assertObjectHasAttribute('first_name', $firstContact);
        $this->assertObjectHasAttribute('last_name', $firstContact);
    }

    public function testgetOpensWorksWithOtherOptionalParams()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $fields = ['email', 'first_name', 'last_name'];
        $from = new \DateTime('2016-06-28');
        $to = new \DateTime('2017-06-28');
        $unique = true;
        $per = 4;
        $results = $report->getOpens(1, $fields, $from, $to, $unique, null, null, $per);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertCount($per, $data);
        $this->assertLessThanOrEqual($to, new \DateTime($data[0]->recorded_at));
        $this->assertLessThanOrEqual(new \DateTime($data[0]->recorded_at), $from);
        $this->assertNotEquals($data[0]->contact->email, $data[1]->contact->email);

    }

    public function testGetClicks()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $report->getClicks(1);
        $this->makeBasicAssertions($results);

        $data = $results->getData();
        $ip1 = $data[0]->ip_address;

        $this->assertNotEmpty($ip1);

        // test for page 2
        $results = $report->getClicks(2);
        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $ip2 = $data[0]->ip_address;

        $this->assertNotEmpty($ip2);
        $this->assertNotEquals($ip1, $ip2);
    }

    public function testGetClicksWithOtherParams()
    {
        $fields = ['email', 'last_name'];
        $from = new DateTime('2016-01-31');
        $to = new DateTime('2016-12-01');
        $unique = true;
        $per = 3;

        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $report->getClicks(1, $fields, $from, $to, $unique, null, null, $per);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertLessThanOrEqual($per, count($data));
        $this->assertLessThanOrEqual($to, new \DateTime($data[0]->recorded_at));
        $this->assertLessThanOrEqual(new \DateTime($data[0]->recorded_at), $from);
        $this->assertObjectHasAttribute('last_name', $data[0]->contact);
        $this->assertObjectHasAttribute('email', $data[0]->contact);
    }

    public function testGetBounces()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $report->getBounces(1);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertNotEmpty($data[0]->recorded_on);
        $recordedOn = $data[0]->recorded_on;

        // test for page 2
        $results = $report->getBounces(2);
        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $recordedOn2 = $data[0]->recorded_on;

        $this->assertNotEmpty($recordedOn2);
        $this->assertNotEquals($recordedOn, $recordedOn2);
    }

    public function testGetBouncesWithOtherParams()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $fields = ['last_name'];
        $from = new \DateTime('2016-01-19');
        $to = new \DateTime('2017-01-19');
        $unique = true;
        $type = 'hard';
        $per = 3;

        $results = $report->getBounces(1, $fields, $from, $to, $unique, null, null, $type, $per);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $recordedOn = $data[0]->recorded_on;
        $this->assertNotEmpty($recordedOn);
        $this->assertLessThanOrEqual($per, count($data));
        $this->assertLessThanOrEqual($to, new \DateTime($data[0]->recorded_on));
        $this->assertLessThanOrEqual(new \DateTime($data[0]->recorded_on), $from);
        $this->assertNotEquals($data[0]->contact->email, $data[1]->contact->email);
    }

    public function testGetUnsubscribes()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);

        $results = $report->getUnsubscribes(1);

        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $recordedOn = $data[0]->recorded_on;

        // test for page 2
        $results = $report->getUnsubscribes(2);
        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $recordedOn2 = $data[0]->recorded_on;

        $this->assertNotEmpty($recordedOn2);
        $this->assertNotEquals($recordedOn, $recordedOn2);
    }

    public function testGetUnsubscribesWithOtherParams()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $fields = ['email', 'first_name', 'last_name'];
        $from = new DateTime('2016-01-01');
        $to = new DateTime('2017-01-01');
        $unique = true;
        $per = 5;

        $results = $report->getUnsubscribes(1, $fields, $from, $to, $unique, null, null, $per);

        $this->makeBasicAssertions($results);
        $data = $results->getData();

        $this->assertLessThanOrEqual($per, count($data));
        $this->assertLessThanOrEqual($to, new \DateTime($data[0]->recorded_on));
        $this->assertLessThanOrEqual(new \DateTime($data[0]->recorded_on), $from);
        $this->assertNotEquals($data[0]->contact->email, $data[1]->contact->email);
        $this->assertObjectHasAttribute('first_name', $data[0]->contact);
        $this->assertObjectHasAttribute('last_name', $data[0]->contact);
        $this->assertObjectHasAttribute('email', $data[0]->contact);
    }

    public function testGetComplaints()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);

        $results = $report->getComplaints(1);

        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $recordedOn = $data[0]->recorded_on;

        // test for page 2
        $results = $report->getComplaints(2);
        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $recordedOn2 = $data[0]->recorded_on;

        $this->assertNotEmpty($recordedOn2);
        $this->assertNotEquals($recordedOn, $recordedOn2);
    }

    public function testGetComplaintsWithOtherParams()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $fields = ['email', 'first_name', 'last_name'];
        $from = new DateTime('2016-01-01');
        $to = new DateTime('2017-01-01');
        $unique = true;
        $per = 5;
        $results = $report->getComplaints(1, $fields, $from, $to, $unique, null, null, $per);

        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $recordedOn = $data[0]->recorded_on;

        $this->assertLessThanOrEqual($per, count($data));
        $this->assertLessThanOrEqual($to, new \DateTime($data[0]->recorded_on));
        $this->assertLessThanOrEqual(new \DateTime($data[0]->recorded_on), $from);
        $this->assertNotEquals($data[0]->contact->email, $data[1]->contact->email);
        $this->assertObjectHasAttribute('first_name', $data[0]->contact);
        $this->assertObjectHasAttribute('last_name', $data[0]->contact);
        $this->assertObjectHasAttribute('email', $data[0]->contact);
    }

    public function testGetAbReports()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);

        $results = $report->getAbReports('Test', 1);

        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $id = $data[0]->id;
        $this->assertLessThan($data[0]->total_pages, 0);

        // test for page 2
        $results = $report->getAbReports('Test', 2, null, null, 2);
        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $id1 = $data[0]->id;

        $this->assertNotEquals($id, $id1);
    }

    public function testGetJourneys()
    {
        $report = new Reports(self::ACCOUNT_ID, self::AUTH_TOKEN);

        $results = $report->getJourneys(1);

        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $createdAt = $data[0]->created_at;
        $this->assertNotEmpty($createdAt);
        $this->assertLessThan($data[0]->total_pages, 0);

        // test for page 2
        $results = $report->getJourneys(2);
        $this->makeBasicAssertions($results);
        $data = $results->getData();
        $createdAt1 = $data[0]->created_at;

        $this->assertNotEmpty($createdAt1);
        $this->assertNotEquals($createdAt, $createdAt1);
    }
}
