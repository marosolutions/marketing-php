<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\Campaigns;

final class CampaignsTests extends TestCase
{
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "";

    public function testGet()
    {
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
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

        // also test that 2nd page returns different data.
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

        $this->assertNotEquals($id2, $id, "potential bug re: pagination in underlying REST API");
    }
}
