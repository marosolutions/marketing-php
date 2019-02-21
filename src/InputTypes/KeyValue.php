<?php

namespace Maropost\Api\InputTypes;

use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\ResultTypes\GetResult;

class KeyValue
{
    /**
     * @var string
     */
    public $key;
    public $value;
}