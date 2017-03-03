<?php

/*
 * The MIT License
 *
 * Copyright 2017 David Schoenbauer <dschoenbauer@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace DSchoenbauer\Sql\Command;

use DSchoenbauer\Sql\Where\WhereStatementInterface;
use DSchoenbauer\Tests\Sql\MockPdo;
use PDO;
use PDOStatement;
use PHPUnit_Framework_TestCase;

/**
 * Description of SelectTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class SelectTest extends PHPUnit_Framework_TestCase {

    private $_object;

    protected function setUp() {
        $this->_object = new Select("someTable");
    }

    public function testFetchStyleConstructor() {
        $this->assertEquals(PDO::FETCH_ASSOC, $this->_object->getFetchStyle());
    }

    public function testFetchStyle() {
        $test = \PDO::FETCH_UNIQUE || \PDO::FETCH_ASSOC;
        $this->assertEquals($test, $this->_object->setFetchStyle($test)->getFetchStyle());
    }

    public function testFieldsConstructor() {
        $this->assertEquals(["*"], $this->_object->getFields());
    }

    public function testFields() {
        $fields = ['id', 'name'];
        $this->assertEquals($fields, $this->_object->setFields($fields)->getFields());
    }

    public function testFieldsNull() {
        $fields = null;
        $this->assertEquals(["*"], $this->_object->setFields($fields)->getFields());
    }

    public function testFieldsEmpty() {
        $fields = [];
        $this->assertEquals(["*"], $this->_object->setFields($fields)->getFields());
    }

    public function testTable() {
        $this->assertEquals('someOtherTest', $this->_object->setTable('someOtherTest')->getTable());
    }

    public function testTableConstructor() {
        $this->assertEquals('someTable', $this->_object->getTable());
    }

    public function testFetchFlatConstructor() {
        $this->assertFalse($this->_object->getFetchFlat());
    }

    public function testDefaultValueConstructor() {
        $this->assertEquals([], $this->_object->getDefaultValue());
    }

    public function testData() {
        $this->assertEquals([], $this->_object->getData());
        $this->assertEquals(['id', 'name'], $this->_object->setData(['id', 'name'])->getData());
    }

    public function testSql() {
        $expected = "SELECT * FROM someTable";
        $this->assertEquals($expected, $this->_object->getSql());
    }

    public function testExecuteFetchFlatWithWhereDefaultData() {
        $this->assertEquals([], $this->_object
                        ->setFetchFlat()
                        ->setWhere($this->getWhere(['id' => 1], "id = 1"))
                        ->execute($this->getMockPDO(true, ['id' => 1], false)));
    }

    public function testExecuteFetchFlatNoWhereDefaultData() {
        $this->assertEquals([], $this->_object
                        ->setFetchFlat()
                        ->execute($this->getMockPDO(true, null, false)));
    }

    public function testExecuteFetchFlatWithWhereWithData() {
        $data = ['id' => 1, 'name' => 'Bob'];
        $this->assertEquals($data, $this->_object
                        ->setFetchFlat()
                        ->setWhere($this->getWhere(['id' => 1], "id = 1"))
                        ->execute($this->getMockPDO(true, ['id' => 1], $data)));
    }

    public function testExecuteFetchFlatNoWhereWithData() {
        $data = ['id' => 1, 'name' => 'Bob'];
        $this->assertEquals($data, $this->_object
                        ->setFetchFlat()
                        ->execute($this->getMockPDO(true, null, $data)));
    }

    public function testExecuteFetchFullWithWhereDefaultData() {
        $this->assertEquals([], $this->_object
                        ->setFetchFlat(false)
                        ->setWhere($this->getWhere(['id' => 1], "id = 1"))
                        ->execute($this->getMockPDO(false, ['id' => 1], false)));
    }

    public function testExecuteFetchFullNoWhereDefaultData() {
        $this->assertEquals([], $this->_object
                        ->setFetchFlat(false)
                        ->execute($this->getMockPDO(false, null, false)));
    }

    public function testExecuteFetchFullWithWhereWithData() {
        $data = ['id' => 1, 'name' => 'Bob'];
        $this->assertEquals($data, $this->_object
                        ->setFetchFlat(false)
                        ->setWhere($this->getWhere(['id' => 1], "id = 1"))
                        ->execute($this->getMockPDO(false, ['id' => 1], $data)));
    }

    public function testExecuteFetchFullNoWhereWithData() {
        $data = ['id' => 1, 'name' => 'Bob'];
        $this->assertEquals($data, $this->_object
                        ->setFetchFlat(false)
                        ->execute($this->getMockPDO(false, null, $data)));
    }

    public function getWhere(array $whereData = [], $statement = null) {
        $mock = $this->getMockBuilder(WhereStatementInterface::class)->getMock();
        $mock->expects($this->once())
                ->method('getData')->willReturn($whereData);
        $mock->expects($this->exactly(2))
                ->method('getStatement')->willReturn($statement);
        return $mock;
    }

    public function getMockPDO($fetchFlat, $whereData = null, $fetchedData = true) {
        $mockStatement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $execute = $mockStatement->expects($this->once())
                ->method('execute')
                ->willReturn(true);
        if ($whereData) {
            $execute->with($whereData);
        }
        $mockStatement->
                expects($fetchFlat ? $this->once() : $this->never())
                ->method('fetch')->willReturn($fetchedData);
        $mockStatement->
                expects(!$fetchFlat ? $this->once() : $this->never())
                ->method('fetchAll')->willReturn($fetchedData);

        $mockPdo = $this->getMockBuilder(MockPdo::class)->disableOriginalConstructor()->getMock();
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->with($this->_object->getSql())
                ->willReturn($mockStatement);
        return $mockPdo;
    }

}
