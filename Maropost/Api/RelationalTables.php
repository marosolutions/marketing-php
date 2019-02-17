<?php

namespace Maropost\Api;

use Maropost\Api\Abstractions\Api;
use Maropost\Api\InputTypes\RelationalTableColumn;
use Maropost\Api\ResultTypes\GetResult;
use Maropost\Api\Abstractions\OperationResult;

class RelationalTables
{
    use Api;

	public function __construct(int $accountId, string $authToken)
	{
		$this->auth_token = $authToken;
		$this->accountId = $accountId;
        $this->resource = 'relational_tables';
	}

    /**
     * Gets the list of Relational Tables
     *
     * @return GetResult
     */
	public function get() : OperationResult
	{
        return $this->_get('', []);
	}

    /**
     * Gets a specific Relational Table
     *
     * @param int $id ID of the specific Relational Table
     * @return GetResult
     */
    public function getForId(int $id) : OperationResult
    {
        return $this->_get($id, []);
    }

    /**
     * Updates the given Relational Table
     *
     * @param int $id ID of the existing table you wish to update
     * @param string $name new name for the table
     * @param RelationalTableColumn ...$columns replacement columns for the table.
     * @return OperationResult
     */
    protected function update(int $id, string $name, RelationalTableColumn ...$columns) : OperationResult
    {
        // validate columns input
        foreach ($columns as $column) {
            $result = $column->validate();
            if (!$result->isSuccess) {
                return $result;
            }
        }

        $table = (object)[
            "name" => $name,
            "relational_columns_attributes" => $columns
        ];
        $object = (object)["relational_table" => $table];
        $result = $this->_put($id, [], $object);
        return $result;
    }

    /**
     * Creates a Relational Table
     *
     * @param string $name name for the table you wish to create
     * @param RelationalTableColumn ...$columns
     * @return OperationResult
     */
	public function create(string $name, RelationalTableColumn... $columns) : OperationResult
	{
	    // validate columns input
	    foreach ($columns as $column) {
	        $result = $column->validate();
	        if (!$result->isSuccess) {
	            return $result;
            }
        }

	    $table = (object)[
	      "name" => $name,
          "relational_columns_attributes" => $columns
        ];
	    $object = (object)["relational_table" => $table];
	    $result = $this->_post("", [], $object);
		return $result;
	}

    /**
     * Deletes the given Relational Table (and the records therein)
     *
     * @param int $id ID of the Relational Table to delete
     * @return OperationResult
     */
	public function delete(int $id) : OperationResult
    {
        return $this->_delete($id);
    }

    /**
     * Deletes the records of the given Relational Table, but keeps the table
     *
     * @param int $id ID of the Relational Table whose records you want to delete
     * @return OperationResult
     */
    public function deleteRecords(int $id) : OperationResult
    {
        return $this->_get($id."/truncate", []);
    }
}