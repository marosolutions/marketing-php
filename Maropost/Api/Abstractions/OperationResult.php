<?php

namespace Maropost\Api\Abstractions;

/**
 * Class OperationResult
 * @package Maropost\Api\Abstractions
 */
abstract class OperationResult
{
    /**
     * @var
     */
    public $isSuccess;
    /**
     * @var
     */
    public $errorMessage;
    /**
     * @var
     */
    public $exception;
    /**
     * @var
     */
    protected $data;

    /**
     * Gets the data of the Maropost response to the API call.
     * @return array|mixed|null
     */
    public function getData()
    {
        $data = $this->data;
        if (is_string($data)) {
            return json_decode($data);
        }
        elseif (is_array($data)) {
            // if the array elements themselves are associative arrays, convert them to objects.
            return array_map(function ($value) {
                return (object) $value;
            }, $data);
        }
        elseif (!is_object($data)) {
            return null;
        }
        return $data;
    }
}
