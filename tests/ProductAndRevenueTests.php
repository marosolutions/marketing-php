<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\InputTypes\OrderItemInput;
use \Maropost\Api\ProductAndRevenue;

final class ProductAndRevenueTests extends TestCase
{
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "";

    private function getTestOrderItems() : array
    {
        return array(
            new OrderItemInput("7", "1300", "2", "book", "adc1", "asdf"),
            new OrderItemInput("9", "1350", "1", "movie", "adc1", "poiu")
        );
    }

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
            $originalOrderId = $originalOrderId . (new DateTime())->format('Y-m-d-H:i:s:u');
        }
        catch (Exception $e) {
            $this->assertNull($e);
        }
        $orderItems = $this->getTestOrderItems();
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
        $svc->deleteForOriginalOrderId($originalOrderId);
    }

    /**
     * Creates a test order. Try to delete this order after you finish testing.
     *
     * @param ProductAndRevenue $svc
     * @return string originalOrderId of the new order. Empty string if failure.
     */
    private function createTestOrder(ProductAndRevenue $svc) : string
    {
        $rootOrigOrderId = "phpUnit_";
        $originalOrderId = $rootOrigOrderId;
        try {
            $originalOrderId = $originalOrderId . (new DateTime())->format('Y-m-d-H:i:s:u');
        }
        catch (Exception $e) {
            $this->assertNull($e);
        }
        $orderItems = $this->getTestOrderItems();
        $results = $svc->createOrder(true, "info@maropost.com", "TestFirstName",
            "TestLastName", "2017-10-13T18:05:24-04:00", "Processed", $originalOrderId,
            $orderItems);
        $this->assertTrue($results->isSuccess);
        if ($results->isSuccess) {
            return $originalOrderId;
        }
        return "";
    }

    public function testUpdateOrderForOrderId()
    {
        // create sample order and update it.
        $svc = new ProductAndRevenue(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $originalOrderId = $this->createTestOrder($svc);
        $this->assertNotEmpty($originalOrderId);
        $results = $svc->getOrderForOriginalOrderId($originalOrderId);
        $this->assertNotNull($results, "order ".$originalOrderId." gets nothing.");
        $this->assertInstanceOf(OperationResult::class, $results, "order ".$originalOrderId." doesn't get OperationResult.");
        $orderData = $results->getData();
        $this->assertObjectHasAttribute("id", $orderData, "order ".$originalOrderId." gets no data.");
        $orderId = $orderData->id;
        $orderItems = $this->getTestOrderItems();
        $orderItems[0]->price = "5";
        $orderItems[0]->quantity = "7";
        $results = $svc->updateOrderForOrderId($orderId, "2018-01-01T15:00:00-07:00", "Shipped",
            $orderItems, null, "ccUpdated");
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // re-pull the order to confirm it's changed, and then delete.
        $results = $svc->getOrderForOriginalOrderId($originalOrderId);
        $orderData = $results->getData();
        //$this->assertEquals("2018-01-01T15:00:00-07:00", $orderData->order_date); // TODO: restore this test after the REST API itself is fixed.
        $this->assertEquals("Shipped", $orderData->order_status);
        //$this->assertEquals("ccUpdated", $orderData->coupon_code); // TODO: restore this test after the REST API itself is fixed.
        $orderItems = $orderData->order_items;
        $this->assertEquals("5", $orderItems[0]->price);
        $this->assertEquals("7", $orderItems[0]->quantity);
        $svc->deleteForOriginalOrderId($originalOrderId);
    }

    public function testUpdateOrderForOriginalOrderId()
    {
        // create sample order and update it.
        $svc = new ProductAndRevenue(self::ACCOUNT_ID, self::AUTH_TOKEN);
        $originalOrderId = $this->createTestOrder($svc);
        $orderItems = $this->getTestOrderItems();
        $orderItems[0]->price = "5";
        $orderItems[0]->quantity = "7";
        $results = $svc->updateOrderForOriginalOrderId($originalOrderId, "2018-01-01T15:00:00-07:00",
            "Shipped", $orderItems, null, "ccUpdated");
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);

        // re-pull the order to confirm it's changed, and then delete.
        $results = $svc->getOrderForOriginalOrderId($originalOrderId);
        $orderData = $results->getData();
        //$this->assertEquals("2018-01-01T15:00:00-07:00", $orderData->order_date); // TODO: restore this test after the REST API itself is fixed.
        $this->assertEquals("Shipped", $orderData->order_status);
        //$this->assertEquals("ccUpdated", $orderData->coupon_code); // TODO: restore this test after the REST API itself is fixed.
        $orderItems = $orderData->order_items;
        $this->assertEquals("5", $orderItems[0]->price);
        $this->assertEquals("7", $orderItems[0]->quantity);
        $svc->deleteForOriginalOrderId($originalOrderId);
    }
    /*
     */
}
