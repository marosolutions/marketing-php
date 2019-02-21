<?php

namespace Maropost\Api\InputTypes;

use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\ResultTypes\GetResult;

class RelationalTableColumn
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $data_type;
    /**
     * @var bool
     */
    public $primary_key;
    /**
     * @var int
     */
    public $field_length;
    /**
     * @var bool
     */
    public $auto_increment;
    /**
     * @var bool
     */
    public $allow_null;
    /**
     * @var bool
     */
    public $sendable;

    /**
     * if ->isSuccess is true, then the instance properties are valid. Otherwise, they're not, and ->errorMessage will
     * convey the problem.
     *
     * @return OperationResult
     */
    public function validate() : OperationResult
    {
        if(!is_string($this->name)) {
            return new GetResult(null, "Value of column 'name' must be a string.");
        }
        if(!is_string($this->key)) {
            return new GetResult(null, "Value of column 'key' must be a string.");
        }
        if(!is_string($this->data_type)) {
            return new GetResult(null, "Value of column 'data_type' must be a string.");
        }
        if(!is_bool($this->primary_key)) {
            return new GetResult(null, "Value of column 'primary_key' must be a bool.");
        }
        if(!is_bool($this->auto_increment)) {
            return new GetResult(null, "Value of column 'auto_increment' must be a bool.");
        }
        if(!is_bool($this->allow_null)) {
            return new GetResult(null, "Value of column 'allow_null' must be a bool.");
        }
        if(!is_bool($this->sendable)) {
            return new GetResult(null, "Value of column 'sendable' must be a bool.");
        }
        if(!is_int($this->field_length)) {
            return new GetResult(null, "Value of column 'field_length' must be an int.");
        }
        $result = new GetResult(null, "");
        $result->isSuccess = true;
        return $result;
    }
}