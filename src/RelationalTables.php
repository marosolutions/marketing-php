<?php

namespace Maropost\Api;

use Maropost\Api\Abstractions\Api;
use Maropost\Api\InputTypes\KeyValue;
use Maropost\Api\ResultTypes\GetResult;
use Maropost\Api\Abstractions\OperationResult;

/**
 * Class RelationalTables
 * @package Maropost\Api
 */
class RelationalTables
{
    use Api;

    /**
     * @param int $accountId
     * @param string $authToken
     * @param string $tableName name of the table to act against for
     */
	public function __construct(int $accountId, string $authToken, string $tableName)
	{
		$this->auth_token = $authToken;
		$this->accountId = $accountId;
        $this->resource = $tableName;
	}

    /**
     * Gets the records of the Relational Table
     * @return GetResult
     */
	public function get() : OperationResult
	{
        return $this->_get("", []);
	}

    /**
     * Gets the specified record from the Relational Table
     *
     * @param string $idFieldName name of the field representing the unique identifier (E.g., "id", "email")
     * @param mixed $idFieldValue value of the identifier field, for the record to get.
     * @return OperationResult
     */
    public function show(string $idFieldName, $idFieldValue) : OperationResult
    {
        $object = (object)array("record" => (object)array($idFieldName => $idFieldValue));
        return $this->_post("show", [], $object);
    }

    /**
     * Adds a record to the Relational Table.
     *
     * @param KeyValue ...$keyValues a list of field name/values for the record to be updated.
     * @return OperationResult
     */
	public function create(KeyValue... $keyValues) : OperationResult
	{
	    // validate columns input
	    //foreach ($keyValues as $key => $value) {
        //}

	    $object = (object)array("record" => (object)$keyValues);
	    return $this->_post("create", [], $object);
	}

    /**
     * Updates a record in the Relational Table.
     *
     * @param KeyValue ...$keyValues a list of field name/values for the record to be updated.
     * @return OperationResult
     */
	public function update(KeyValue... $keyValues) : OperationResult
    {
        $object = (object)array("record" => (object)$keyValues);
        return $this->_put("update", [], (object)$keyValues);
    }

    /**
     * Creates or updates a record in the Relational Table.
     *
     * @param KeyValue ...$keyValues a list of field name/values for the record to be created (or updated).
     * @return OperationResult
     */
    public function upsert(KeyValue... $keyValues) : OperationResult
    {
        $object = (object)array("record" => (object)$keyValues);
        return $this->_put("upsert", [], $object);
    }

    /**
     * Deletes the given record of the Relational Table
     *
     * @param string $idFieldName name of the field representing the unique identifier (E.g., "id", "email")
     * @param mixed $idFieldValue value of the identifier field, for the record to delete.
     * @return OperationResult
     */
	public function delete(string $idFieldName, $idFieldValue) : OperationResult
    {
        return $this->_delete("delete", array($idFieldName => $idFieldValue));
    }

    /**
     * @param string|null $overrideResource ignored
     * @return string
     */
    private function url(string $overrideResource = null) : string
    {
        return 'https://rdb.maropost.com/'.$this->accountId.'/'.$this->resource;
    }

    /**
     * Updates/switches which table this service is acting against
     * @param string $newTableName name of the table to use for successive calls.
     */
    public function _setTableName(string $newTableName) { $this->resource = $newTableName; }
    /**
     * @return string name of the table this service is acting against.
     */
    public function _getTableName() { return $this->resource; }

}