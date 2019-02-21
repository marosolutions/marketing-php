<?php

namespace Maropost\Api\InputTypes;

use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\ResultTypes\GetResult;

class OrderItemInput
{
    /**
     * @var string
     */
    public $item_id;
    /**
     * @var string
     */
    public $price;
    /**
     * @var string
     */
    public $quantity;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $adcode;
    /**
     * @var string
     */
    public $category;
}