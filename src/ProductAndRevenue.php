<?php

namespace Maropost\Api;

use Maropost\Api\ResultTypes\GetResult;
use Maropost\Api\Abstractions\Api;
use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\InputTypes\OrderItemInput;

/**
 * Class ProductAndRevenue
 * @package Maropost\Api
 */
class ProductAndRevenue
{
    use Api;

	public function __construct(int $accountId, string $authToken)
	{
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'orders';
	}

    /**
     * Gets a the specified order.
     *
     * @param int $id
     * @return OperationResult
     */
	public function getOrder(int $id) : OperationResult
	{
        return $this->_get("find", ["where[id]" => $id]);
	}

    /**
     * Gets a the specified order.
     *
     * @param string $originalOrderId matches the original_order_id field of the order.
     * @return OperationResult
     */
    public function getOrderForOriginalOrderId(string $originalOrderId) : OperationResult
    {
        return $this->_get($originalOrderId, []);
    }

    /**
     * Creates an order
     *
     * @param bool $requireUnique true to validate that the order has a unique original_order_id for the given contact.
     * @param string $contactEmail
     * @param string $contactFirstName
     * @param string $contactLastName
     * @param string $orderDateTime uses the format: "YYYY-MM-DDTHH:MM:SS-05:00"
     * @param string $orderStatus
     * @param string $originalOrderId sets the original_order_id field
     * @param array $orderItems an array of \Maropost\Api\InputTypes\OrderItemInput objects.
     * @param array|null $customFields associative array where the key (string) represents the field name and the value is the field value
     * @param array|null $addTags simple array of tags to add (scalar values)
     * @param array|null $removeTags simple array of tags to remove (scalar values)
     * @param string|null $uid
     * @param string|null $listIds CSV list of IDs (e.g, "12,13")
     * @param string|null $grandTotal
     * @param int|null $campaignId
     * @param string|null $couponCode
     * @return OperationResult
     */
	public function createOrder(bool $requireUnique, string $contactEmail, string $contactFirstName, string $contactLastName,
                                string $orderDateTime, string $orderStatus, string $originalOrderId, array $orderItems,
                                array $customFields = null, array $addTags = null, array $removeTags = null,
                                string $uid = null, string $listIds = null, string $grandTotal = null,
                                int $campaignId = null, string $couponCode = null) : OperationResult
    {
        if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            return new GetResult(null, "The provided 'contactEmail' is not a well-formed email address.");
        }
        if (empty($orderItems)) {
            return new GetResult(null, "No orderItems were provided. Each order must contain at least one orderItem.");
        }
        foreach ($orderItems as $orderItem) {
            if (!($orderItem instanceof OrderItemInput)) {
                return new GetResult(null, "All orderItems must be instances of \\Maropost\\Api\\InputTypes\\OrderItemInput. At least one orderItem was not.");
            }
        }

        $order = (object)array(
            "contact" => array("email" => $contactEmail, "first_name" => $contactFirstName, "last_name" => $contactLastName),
            "order_date" => $orderDateTime,
            "order_status" => $orderStatus,
            "original_order_id" => $originalOrderId,
            "order_items" => $orderItems,
            "uid" => $uid,
            "campaign_id" => $campaignId,
            "coupon_code" => $couponCode,
            "grand_total" => $grandTotal
        );
        if ($listIds) {
            $order->list_ids = $listIds;
        }

        if (!empty($customFields)) {
            if (!is_array($customFields)) {
                return new GetResult(null, "Provided 'customFields' array is not actually an array.");
                // TODO: Given the type-hinting in the function signature, is this even possible?
            }
            foreach ($customFields as $key => $value) {
                if (!is_string($key)) {
                    return new GetResult(null, "All keys in your 'customFields' array must be strings.");
                }
                if (!is_scalar($value)) {
                    return new GetResult(null, "All values in your 'customFields' array must be non-null scalars (string, float, bool, int).");
                }
            }
            $order->custom_field = (object)$customFields;
        }
        if (!empty($addTags)) {
            foreach ($addTags as $addTag) {
                if (!is_scalar($addTag)) {
                    return new GetResult(null, "All values in your 'addTags' array must be non-null scalars (string, float, bool, int).");
                }
            }
            $order->add_tags = $addTags;
        }
        if (!empty($removeTags)) {
            foreach ($removeTags as $removeTag) {
                if (!is_scalar($removeTag)) {
                    return new GetResult(null, "All values in your 'removeTags' array must be non-null scalars (string, float, bool, int).");
                }
            }
            $order->remove_tags = $removeTags;
        }
        $object = (object)array("order" => $order);
        $params = ($requireUnique ? array("unique"=>"true") : array());
        return $this->_post("", $params, $object);
    }

    /**
     * Updates an existing eCommerce order using unique original_order_id if the details are changed due to partial
     * return or some other update.
     *
     * @param string $originalOrderId matches the original_order_id field of the order
     * @param string $orderDateTime uses the format: YYYY-MM-DDTHH:MM:SS-05:00
     * @param string $orderStatus
     * @param array $orderItems restates the orderItems as as array of OrderItemInput objects.
     * @param int|null $campaignId
     * @param string|null $couponCode
     * @return OperationResult
     */
    public function updateOrderForOriginalOrderId(string $originalOrderId, string $orderDateTime, string $orderStatus,
                                                  array $orderItems, int $campaignId = null, string $couponCode = null
    ) : OperationResult
    {
        if (empty($orderItems)) {
            return new GetResult(null, "No orderItems were provided. Each order must contain at least one orderItem.");
        }
        foreach ($orderItems as $orderItem) {
            if (!($orderItem instanceof OrderItemInput)) {
                return new GetResult(null, "All orderItems must be instances of \\Maropost\\Api\\InputTypes\\OrderItemInput. At least one orderItem was not.");
            }
        }

        $order = (object)array(
            "order_date" => $orderDateTime,
            "order_status" => $orderStatus,
            "campaign_id" => $campaignId,
            "coupon_code" => $couponCode,
            "order_items" => $orderItems
        );
        $object = (object)array("order" => $order);
        return $this->_put($originalOrderId, [], $object);
    }

    /**
     * Updates an existing eCommerce order using unique order_id if the details are changed due to partial return or
     * some other update.
     *
     * @param int $orderId matches the Maropost order_id field of the order
     * @param string $orderDateTime uses the format: YYYY-MM-DDTHH:MM:SS-05:00
     * @param string $orderStatus
     * @param array $orderItems restates the orderItems as as array of OrderItemInput objects.
     * @param int|null $campaignId
     * @param string|null $couponCode
     * @return OperationResult
     */
    public function updateOrderForOrderId(int $orderId, string $orderDateTime, string $orderStatus,
                                          array $orderItems, int $campaignId = null, string $couponCode = null
    ) : OperationResult
    {
        if (empty($orderItems)) {
            return new GetResult(null, "No orderItems were provided. Each order must contain at least one orderItem.");
        }
        foreach ($orderItems as $orderItem) {
            if (!($orderItem instanceof OrderItemInput)) {
                return new GetResult(null, "All orderItems must be instances of \\Maropost\\Api\\InputTypes\\OrderItemInput. At least one orderItem was not.");
            }
        }

        $order = (object)array(
            "order_date" => $orderDateTime,
            "order_status" => $orderStatus,
            "campaign_id" => $campaignId,
            "coupon_code" => $couponCode,
            "order_items" => $orderItems
        );
        $object = (object)array("order" => $order);
        return $this->_put("find", array("where[id]" => $orderId), $object);
    }

    /**
     * Deletes the complete eCommerce order if the order is cancelled or returned using unique original order id.
     *
     * @param string $originalOrderId matches the original_order_id field of the order
     * @return OperationResult
     */
	public function deleteForOriginalOrderId(string $originalOrderId) : OperationResult
    {
        return $this->_delete($originalOrderId);
    }

    /**
     * Deletes the complete eCommerce order if the order is cancelled or returned using Maropost order id.
     *
     * @param int $id
     * @return OperationResult
     */
    public function deleteForOrderId(int $id) : OperationResult
    {
        return $this->_delete("find", array("where[id]"=>$id));
    }

    /**
     * Deletes the specified product(s) from a complete eCommerce order if the product(s) is cancelled or returned,
     * using unique original_order_id.
     *
     * @param string $originalOrderId matches the original_order_id field of the order
     * @param array $productIds the product(s) to delete from the order
     * @return OperationResult
     */
    public function deleteProductsForOriginalOrderId(string $originalOrderId, array $productIds) : OperationResult
    {
        if (empty($productIds)) {
            return new GetResult(null, "No productIds were provided.");
        }
        foreach ($productIds as $productId) {
            if (!is_int($productId)) {
                if (!is_string($productId) || strpos($productId, ",") !== false) {
                    return new GetResult(null, "At least one productId is invalid");
                }
            }
        }

        return $this->_delete($originalOrderId, array("product_ids"=>implode(",", $productIds)));
    }

    /**
     * Deletes the specified product(s) from a complete eCommerce order if the product(s) is cancelled or returned,
     * using Maropost order_id.
     *
     * @param int $id
     * @param array $productIds the product(s) to delete from the order
     * @return OperationResult
     */
    public function deleteProductsForOrderId(int $id, array $productIds) : OperationResult
    {
        if (empty($productIds)) {
            return new GetResult(null, "No productIds were provided.");
        }
        foreach ($productIds as $productId) {
            if (!is_int($productId)) {
                if (!is_string($productId) || strpos($productId, ",") !== false) {
                    return new GetResult(null, "At least one productId is invalid");
                }
            }
        }

        return $this->_delete("find", array(
            "product_ids" => implode(",", $productIds),
            "where[id]" => $id
        ));
    }
}