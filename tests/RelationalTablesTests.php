<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\InputTypes\KeyValue;
use \Maropost\Api\RelationalTables;

final class RelationalTablesTests extends TestCase
{
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "";
    const DEFAULT_TABLENAME = "phpunit_testing_for_api";
    const TEST_TABLE_NAME = "phpunit_testing_for_api";
    const TEST_FIELDNAME = "email";
    const TEST_FIELDVALUE = "asdfg@maropost.com";

    const INVALID_AUTH_TOKEN = "asdf";

    /**
     * @return array - associative array. First record contains sample primary key
     */
    private function getKeyValues() : array
    {
        return array(new KeyValue(self::TEST_FIELDNAME, self::TEST_FIELDVALUE),
            new KeyValue("firstName", "Aaaaa"),
            new KeyValue("lastName", "Bbbbb"));
    }

    public function testInit()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::DEFAULT_TABLENAME);
        $this->assertInstanceOf(RelationalTables::class, $svc);
    }

    public function testGetTableNameAndSetTableName()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::DEFAULT_TABLENAME);
        $this->assertEquals(self::DEFAULT_TABLENAME, $svc->_getTableName(self::DEFAULT_TABLENAME));

        $newTableName = self::DEFAULT_TABLENAME."_changed";
        $svc->_setTableName($newTableName);
        $this->assertEquals($newTableName, $svc->_getTableName($newTableName));
    }

    public function testGet()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::DEFAULT_TABLENAME);
        $results = $svc->get();
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertObjectHasAttribute("records", $data);
        $records = $data->records;
        $this->assertTrue(is_array($records));
        $this->assertFalse(count($records) == 0);
        $this->assertObjectHasAttribute(self::TEST_FIELDNAME, $records[0]);
    }

    public function testShow()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::DEFAULT_TABLENAME);
        $results = $svc->show(self::TEST_FIELDNAME, self::TEST_FIELDVALUE);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertObjectHasAttribute("result", $data);
    }

    public function testDelete()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::TEST_TABLE_NAME);
        $results = $svc->show(self::TEST_FIELDNAME, self::TEST_FIELDVALUE);
        if($results->isSuccess && property_exists($results->getData()->result, "error")) {
            // the record doesn't exist yet; we need to insert one before we can delete it.
            $keyValues = $this->getKeyValues();
            $svc->create($keyValues[0], $keyValues[1], $keyValues[2]);
        }
        $results = $svc->delete(self::TEST_FIELDNAME, self::TEST_FIELDVALUE);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
    }

    public function testCreateAndDelete()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::TEST_TABLE_NAME);
        $results = $svc->show(self::TEST_FIELDNAME, self::TEST_FIELDVALUE);
        $result = $results->getData()->result;
        if (property_exists($result, "record") && property_exists($result->record, self::TEST_FIELDNAME))
        {
            // we need to delete the row before we can insert it.
            $svc->delete(self::TEST_FIELDNAME, self::TEST_FIELDVALUE);
        }
        $keyValues = $this->getKeyValues();
        $results = $svc->create($keyValues[0], $keyValues[1], $keyValues[2]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertObjectHasAttribute("result", $data);
        $result = $data->result;
        $this->assertEquals(1, $result->created);
    }

    public function testUpsert()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::TEST_TABLE_NAME);
        $keyValues = $this->getKeyValues();

        //$results = $svc->show(self::TEST_FIELDNAME, self::TEST_FIELDVALUE);
        //$result = $results->getData()->result;
        //if (property_exists($result, "record") && property_exists($result->record, self::TEST_FIELDNAME))
        //{
            // we need to delete potential row before we can insert it.
            $svc->delete($keyValues[0]->key, $keyValues[0]->value);
        //}

        // test insert via upsert.
        $results = $svc->upsert($keyValues[0], $keyValues[1], $keyValues[2]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertObjectHasAttribute("result", $data);
        $result = $data->result;
        $this->assertObjectHasAttribute("created", $result);
        $this->assertEquals(1, $result->created);

        // test update via upsert.
        $lastIndex = count($keyValues) - 1;
        $origFieldKeyValue = $keyValues[$lastIndex];
        $newFieldValue = $origFieldKeyValue->value . "diff";
        $keyValues[$lastIndex]->value = $newFieldValue;
        $results = $svc->upsert($keyValues[0], $keyValues[1], $keyValues[2]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertTrue($results->isSuccess);
        $this->assertEmpty($results->errorMessage);
        $this->assertNull($results->exception);
        $data = $results->getData();
        $this->assertObjectHasAttribute("result", $data);
        $result = $data->result;
        $this->assertObjectHasAttribute("updated", $result);
        $this->assertEquals(1, $result->updated);

        // test missing primary key fails.
        $results = $svc->upsert($keyValues[1], $keyValues[2]);
        $this->assertInstanceOf(OperationResult::class, $results);
        $this->assertFalse($results->isSuccess);

        // undo new record.
        $svc->delete(self::TEST_FIELDNAME, self::TEST_FIELDVALUE);
    }

}
