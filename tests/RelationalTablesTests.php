<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Maropost\Api\Abstractions\OperationResult;
use \Maropost\Api\InputTypes\KeyValue;
use \Maropost\Api\RelationalTables;

final class RelationalTablesTests extends TestCase
{
    const ACCOUNT_ID = 1000;
    const AUTH_TOKEN = "wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q";
    const DEFAULT_TABLENAME = "naveen_one";
    const DEFAULT_SHOW_FIELDNAME = "ID";
    const DEFAULT_SHOW_FIELDVALUE = "1121";
    const TEST_TABLE_NAME = "phpunit_testing_for_api";
    const TEST_FIELDNAME = "email";
    const TEST_FIELDVALUE = "dlamb@aplogic.com";

    const INVALID_ACCOUNT_ID = 1000;
    const INVALID_AUTH_TOKEN = "asdf";

    public function testInit()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::DEFAULT_TABLENAME);
        $this->assertInstanceOf(RelationalTables::class, $svc);
    }

    public function testGetAndSetTableName()
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
        $this->assertObjectHasAttribute("email", $records[0]);
    }

    public function testShow()
    {
        $svc = RelationalTables::init(self::ACCOUNT_ID, self::AUTH_TOKEN, self::DEFAULT_TABLENAME);
        $results = $svc->show(self::DEFAULT_SHOW_FIELDNAME, self::DEFAULT_SHOW_FIELDVALUE);
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
            $keyValues = array(new KeyValue(self::TEST_FIELDNAME, self::TEST_FIELDVALUE),
                new KeyValue("firstName", "David"),
                new KeyValue("lastName", "Lamb"));
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
        $keyValues = array(new KeyValue(self::TEST_FIELDNAME, self::TEST_FIELDVALUE),
            new KeyValue("firstName", "David"),
            new KeyValue("lastName", "Lamb"));
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

}
