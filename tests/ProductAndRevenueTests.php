<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\InputTypes\OrderItemInput;
use \Maropost\Api\ProductAndRevenue;

final class ProductAndRevenueTests extends TestCase
{
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q";

    public function testGetOrder()
    {
        // order 233
        $svc = new ProductAndRevenue(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->getOrder(233);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertObjectHasAttribute("id", $data);
        $id = $data->id;

        // order 234
        $results = $svc->getOrder(234);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertObjectHasAttribute("id", $data);
        $id2 = $data->id;

        $this->assertNotEquals($id, $id2, "first order (". $id . ") should not equal second order (". $id2 . ").");
    }

    public function testGetOrderForOriginalOrderId()
    {
        // order 233
        $svc = new ProductAndRevenue(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $results = $svc->getOrderForOriginalOrderId("PhpUnit_2017-02-23-00-29-09-00000");
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertObjectHasAttribute("id", $data);
        $id = $data->id;

        // order 234
        $results = $svc->getOrderForOriginalOrderId("PhpUnit_2017-02-23-00-29-10-00000");
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertObjectHasAttribute("id", $data);
        $id2 = $data->id;

        $this->assertNotEquals($id, $id2, "first order (". $id . ") should not equal second order (". $id2 . ").");
    }

    public function testCreateOrder()
    {
        $svc = new ProductAndRevenue(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $rootOrigOrderId = "phpUnit_";
        $originalOrderId = $rootOrigOrderId;
        try {
            $originalOrderId = $originalOrderId . (new DateTime())->format('Y-m-d H:i:s:u');
        }
        catch (Exception $e) {
            $this->assertNull($e);
        }
        $orderItems = array(
            new OrderItemInput("7", "1300", "2", "book", "adc1", "asdf"),
            new OrderItemInput("9", "1350", "1", "movie", "adc1", "poiu")
        );
        $results = $svc->createOrder(true, "info@maropost.com", "TestFirstName",
            "TestLastName", "2017-10-13T18:05:24-04:00", "Processed", $originalOrderId,
            $orderItems);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertNotEmpty($data);
        $this->assertObjectHasAttribute("id", $data);
    }
}
