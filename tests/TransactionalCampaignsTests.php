<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\TransactionalCampaigns;

final class TransactionalCampaignsTests extends TestCase
{
    // TODO: Whenever you want to run these tests, set appropriate auth_token, recipient, and campaign_id.
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "";
    //const TEST_FIELDNAME = "email";
    //const TEST_FIELDVALUE = "asdfg@maropost.com";
    const SEND_RECIPIENT = "";
    const SEND_RECIPIENT_FIRST_NAME = "";
    const SEND_RECIPIENT_LAST_NAME = "";
    const SEND_SENDER_NAME = "unitTest Sender";
    const SEND_SENDER_EMAIL = "info@maropost.com";
    const SEND_SENDER_REPLYTO = "noreply@maropost.com";
    const SEND_CONTENT_ID = 162;

    const SEND_CAMPAIGN_ID = 0;

    public function testGet()
    {
        $svc = new TransactionalCampaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->get(1);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertTrue(is_array($data));
    }

    /*
    public function testCreate()
    {
        $svc = new TransactionalCampaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $rootCampaignName = "unitTest Campaign ";
        $campaignName = $rootCampaignName;
        try {
            $campaignName = $campaignName . (new DateTime())->format('Y-m-d H:i:s:u');
        }
        catch (Exception $e) {
            $this->assertNull($e);
        }
        $results = $svc->create($campaignName, "unitTest subject", "unitTest preheader",
            self::SEND_SENDER_NAME, self::SEND_SENDER_EMAIL, self::SEND_SENDER_REPLYTO,
            self::SEND_CONTENT_ID, false, "123 Main St., San Luis Obispo, CA 93401, USA",
            "en", "tag1", "tag2");
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        try {
            $campaignName = $campaignName . (new DateTime())->format('Y-m-d H:i:s:u');
        }
        catch (Exception $e) {
            $this->assertNull($e);
        }
        $results = $svc->create($campaignName, "unitTest subject", "unitTest preheader",
            self::SEND_SENDER_NAME, self::SEND_SENDER_EMAIL, self::SEND_SENDER_REPLYTO,
            self::SEND_CONTENT_ID, false, "123 Main St., San Luis Obispo, CA 93401, USA",
            "en");
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
    }
    */

    public function testSend()
    {
        $svc = new TransactionalCampaigns(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->sendEmail(self::SEND_CAMPAIGN_ID, null, null, null,
            null, null, null, true, null, self::SEND_RECIPIENT,
            self::SEND_RECIPIENT_FIRST_NAME, self::SEND_RECIPIENT_LAST_NAME);
            //null, null, null, null, null,
            //null, null, null, null);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // can't send both contentId AND content field(s)
        $results = $svc->sendEmail(self::SEND_CAMPAIGN_ID, 162, "test name", null,
            null, null, null, true, null, self::SEND_RECIPIENT,
            self::SEND_RECIPIENT_FIRST_NAME, self::SEND_RECIPIENT_LAST_NAME);
        //null, null, null, null, null,
        //null, null, null, null);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertFalse($results->isSuccess);
        $this->assertNotEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // all field values
        $results = $svc->sendEmail(self::SEND_CAMPAIGN_ID, null, "test name", "<h2>Custom HTML</h2>",
            "Custom Text", null, null, true, null, self::SEND_RECIPIENT,
            self::SEND_RECIPIENT_FIRST_NAME, self::SEND_RECIPIENT_LAST_NAME,
        ["city" => "San Luis Obispo", "state" => "California"], null, "override name", self::SEND_SENDER_REPLYTO, "subject override",
        self::SEND_SENDER_EMAIL, "Address override", ["field1" => "value1", "field2" => 2], ["ctag1", "ctag2"]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
    }
}
