<?php

namespace Maropost\Api\Abstractions;

use Httpful\Response;

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
     * @return array|mixed|void
     */
    public function getData()
    {
        $data = $this->data;
        $dataType = getType($data);

        if (!in_array($dataType, ['string', 'object', 'array'])) {
            if (is_null($data)) {
                return null;
            }
            return; // TODO: returning null, is okay, but should never be void.
        }

        if ($dataType === 'array') {
            $data = array_map(function ($value) {
                return (object) $value;
            }, $data);
        }

        if ($dataType === 'string') {
            $data = json_decode($data);
        }

        return $data;
    }
}
