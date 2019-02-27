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

    public function testGetDeliveredReports()
    {
        // Find a campaign with multiple delivered reports.
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $data = $results->getData();
        $campaignId = 0;
        $qtyReports = 0;
        for ($i = 0; $campaignId == 0 && $i < count($data); $i++)
        {
            $testCampaignId = $data[$i]->id;
            $results = $svc->getCampaign($testCampaignId);
            $campaignData = $results->getData();
            $qtyReports = $campaignData->delivered;
            if ($qtyReports > 1) {
                $campaignId = $testCampaignId;
            }
        }

        $results = $svc->getDeliveredReports($campaignId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual($qtyReports, count($data));
        $report1 = $data[0];

        $results = $svc->getDeliveredReports($campaignId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        if(!empty($data))
        {
            $this->assertNotEquals($report1->created_at, $data[0]->created_at);
        }
    }

    public function testGetOpenReports()
    {
        // Find a campaign with multiple delivered reports.
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $data = $results->getData();
        $campaignId = 0;
        $qtyReports = 0;
        for ($i = 0; $campaignId == 0 && $i < count($data); $i++)
        {
            $testCampaignId = $data[$i]->id;
            $results = $svc->getCampaign($testCampaignId);
            $campaignData = $results->getData();
            $qtyReports = $campaignData->opened;
            if ($qtyReports > 1) {
                $campaignId = $testCampaignId;
            }
        }

        $results = $svc->getOpenReports($campaignId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual($qtyReports, count($data));
        $report1 = $data[0];

        $results = $svc->getOpenReports($campaignId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        if(!empty($data))
        {
            $this->assertNotEquals($report1->created_at, $data[0]->created_at);
        }
    }

    public function testGetClickReports()
    {
        // Find a campaign with multiple delivered reports.
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $data = $results->getData();
        $campaignId = 0;
        $qtyReports = 0;
        for ($i = 0; $campaignId == 0 && $i < count($data); $i++)
        {
            $testCampaignId = $data[$i]->id;
            $results = $svc->getCampaign($testCampaignId);
            $campaignData = $results->getData();
            $qtyReports = $campaignData->clicked;
            if ($qtyReports > 1) {
                $campaignId = $testCampaignId;
            }
        }

        $results = $svc->getClickReports($campaignId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual($qtyReports, count($data));
        $report1 = $data[0];

        $results = $svc->getClickReports($campaignId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        if(!empty($data))
        {
            $this->assertNotEquals($report1->created_at, $data[0]->created_at);
        }
    }

    public function testGetLinkReports()
    {
        // Find a campaign with multiple delivered reports.
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $data = $results->getData();
        $campaignId = 0;

        // find a campaign that somebody's clicked. Use that to find a report that has a link.
        for ($i = 0; $campaignId == 0 && $i < count($data); $i++)
        {
            $testCampaignId = $data[$i]->id;
            $results = $svc->getCampaign($testCampaignId);
            $campaignData = $results->getData();
            $qtyReports = $campaignData->clicked;
            if ($qtyReports > 0) {
                $campaignId = $testCampaignId;
            }
        }

        $results = $svc->getLinkReports($campaignId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $report1 = $data[0];

        $results = $svc->getLinkReports($campaignId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        if(!empty($data))
        {
            $this->assertNotEquals($report1->created_at, $data[0]->created_at);
        }
    }

    public function testGetBouncedReports()
    {
        // Find a campaign with multiple delivered reports.
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $data = $results->getData();
        $campaignId = 0;
        $qtyReports = 0;
        for ($i = 0; $campaignId == 0 && $i < count($data); $i++)
        {
            $testCampaignId = $data[$i]->id;
            $results = $svc->getCampaign($testCampaignId);
            $campaignData = $results->getData();
            $qtyReports = $campaignData->bounced;
            if ($qtyReports > 1) {
                $campaignId = $testCampaignId;
            }
        }

        $results = $svc->getBounceReports($campaignId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual($qtyReports, count($data));
        $report1 = $data[0];

        $results = $svc->getBounceReports($campaignId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        if(!empty($data))
        {
            $this->assertNotEquals($report1->created_at, $data[0]->created_at);
        }
    }

    public function testGetSoftBouncedReports()
    {
        // Find a campaign with multiple delivered reports.
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $data = $results->getData();
        $campaignId = 0;
        $qtyReports = 0;
        for ($i = 0; $campaignId == 0 && $i < count($data); $i++)
        {
            $testCampaignId = $data[$i]->id;
            $results = $svc->getCampaign($testCampaignId);
            $campaignData = $results->getData();
            $qtyReports = $campaignData->soft_bounced;
            if ($qtyReports > 1) {
                $campaignId = $testCampaignId;
            }
        }

        $results = $svc->getSoftBounceReports($campaignId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual($qtyReports, count($data));
        $report1 = $data[0];

        $results = $svc->getSoftBounceReports($campaignId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        if(!empty($data))
        {
            $this->assertNotEquals($report1->created_at, $data[0]->created_at);
        }
    }

    public function testGetHardBouncedReports()
    {
        // Find a campaign with multiple delivered reports.
        $svc = new Campaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $data = $results->getData();
        $campaignId = 0;
        $qtyReports = 0;
        for ($i = 0; $campaignId == 0 && $i < count($data); $i++)
        {
            $testCampaignId = $data[$i]->id;
            $results = $svc->getCampaign($testCampaignId);
            $campaignData = $results->getData();
            $qtyReports = $campaignData->hard_bounced;
            if ($qtyReports > 1) {
                $campaignId = $testCampaignId;
            }
        }

        $results = $svc->getHardBounceReports($campaignId, 1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual($qtyReports, count($data));
        $report1 = $data[0];

        $results = $svc->getHardBounceReports($campaignId, 2);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        if(!empty($data))
        {
            $this->assertNotEquals($report1->created_at, $data[0]->created_at);
        }
    }
}
