<?php

namespace Maropost\Api;

use Maropost\Api\ResultTypes\GetResult;
use Maropost\Api\Abstractions\Api;
use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\InputTypes\OrderItemInput;

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
     * @param int $id matches the original_order_id field of the order.
     * @return OperationResult
     */
	public function getOrder(int $id) : OperationResult
	{
        return $this->_get($id, []);
	}

    /**
     * Creates an order
     *
     * @param bool $requireUnique true to validate that the order has a unique original_order_id for the given contact.
     * @param string $contactEmail
     * @param string $contactFirstName
     * @param string $contactLastName
     * @param array $customFields associative array where the key (string) represents the field name and the value is the field value
     * @param array $addTags simple array of tags to add (scalar values)
     * @param array $removeTags simple array of tags to remove (scalar values)
     * @param string $uid
     * @param string $listIds
     * @param string $orderDateTime uses the format: YYYY-MM-DDTHH:MM:SS-05:00
     * @param string $orderStatus
     * @param string $originalOrderId
     * @param string $grandTotal
     * @param int|null $campaignId
     * @param string|null $couponCode
     * @param OrderItemInput ...$orderItems
     * @return OperationResult
     */
	public function createOrder(bool $requireUnique, string $contactEmail, string $contactFirstName, string $contactLastName,
        array $customFields, array $addTags, array $removeTags, string $uid, string $listIds, string $orderDateTime,
        string $orderStatus, string $originalOrderId, string $grandTotal, int $campaignId = null,
        string $couponCode = null, OrderItemInput... $orderItems
    ) : OperationResult
    {
        if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            return new GetResult(null, "The provided 'contactEmail' is not a well-formed email address.");
        }
        if (!is_array($customFields)) {
            return new GetResult(null, "Provided 'customFields' array is not actually an array.");
            // TODO: Given the type-hinting in the function signature, is this even possible?
        }
        foreach ($customFields as $key => $value) {
            if (!is_string($key)) {
                return new GetResult(null, "All keys in your customFields array must be strings.");
            }
            if (!is_scalar($value)) {
                return new GetResult(null, "All values in your customFields array must be non-null scalars (string, float, bool, int).");
            }
        }
        foreach ($addTags as $addTag) {
            if (!is_scalar($addTag)) {
                return new GetResult(null, "All values in your addTags array must be non-null scalars (string, float, bool, int).");
            }
        }
        foreach ($removeTags as $removeTag) {
            if (!is_scalar($removeTag)) {
                return new GetResult(null, "All values in your removeTags array must be non-null scalars (string, float, bool, int).");
            }
        }

        $order = (object)array(
            "contact" => array("email" => $contactEmail, "first_name" => $contactFirstName, "last_name" => $contactLastName),
            "custom_fields" => $customFields,
            "add_tags" => $addTags,
            "remove_tags" => $removeTags,
            "uid" => $uid,
            "list_ids" => $listIds,
            "order_date" => $orderDateTime,
            "order_status" => $orderStatus,
            "original_order_id" => $originalOrderId,
            "grand_total" => $grandTotal,
            "campaign_id" => $campaignId,
            "coupon_code" => $couponCode,
            "order_items" => $orderItems
        );
        $object = (object)array("order" => $order);
        $params = ($requireUnique ? array("unique"=>"true") : array());
        return $this->_post("", $params, $object);
    }

    /**
     * Deletes the complete eCommerce order if the order is cancelled or returned using unique original order id.
     *
     * @param int $originalOrderId matches the original_order_id field of the order
     * @return OperationResult
     */
	public function deleteForOriginalOrderId(int $originalOrderId) : OperationResult
    {
        return $this->_delete($$originalOrderId);
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
     * @param int $originalOrderId matches the original_order_id field of the order
     * @param int ...$productIds the product(s) to delete from the order
     * @return OperationResult
     */
    public function deleteProductsForOriginalOrderId(int $originalOrderId, int... $productIds) : OperationResult
    {
        return $this->_delete($originalOrderId, array("product_ids"=>implode(",", $productIds)));
    }

    /**
     * Deletes the specified product(s) from a complete eCommerce order if the product(s) is cancelled or returned,
     * using Maropost order_id.
     *
     * @param int $id
     * @param int ...$productIds the product(s) to delete from the order
     * @return OperationResult
     */
    public function deleteProductsForOrderId(int $id, int... $productIds) : OperationResult
    {
        return $this->_delete("find", array(
            "product_ids" => implode(",", $productIds),
            "where[id]" => $id
        ));
    }
}