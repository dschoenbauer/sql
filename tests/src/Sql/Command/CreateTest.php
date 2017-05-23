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

use DSchoenbauer\Sql\Exception\EmptyDatasetException;
use DSchoenbauer\Sql\Exception\ExecutionErrorException;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use DSchoenbauer\Tests\Sql\MockPdo;
use PDOStatement;
use PHPUnit_Framework_TestCase;

/**
 * Description of CreateTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class CreateTest extends PHPUnit_Framework_TestCase
{

    private $_object;

    protected function setUp()
    {
        $this->_object = new Create('someTable', []);
    }

    public function testTable()
    {
        $this->assertEquals("someTable", $this->_object->getTable());
        $this->assertEquals("someOtherTable", $this->_object->setTable('someOtherTable')->getTable());
    }

    public function testData()
    {
        $this->assertEquals([], $this->_object->getData());
        $data = ['test' => 'test'];
        $this->assertEquals($data, $this->_object->setData($data)->getData());
    }

    public function testGetSqlEmptyData()
    {
        $this->expectException(EmptyDatasetException::class);
        $this->_object->getSql();
    }

    public function testGetSqlSingleField()
    {
        $sql = "INSERT INTO someTable (data) VALUES (:data)";
        $this->assertEquals($sql, $this->_object->setData(['data' => 'notUsedHere'])->getSql());
    }

    public function testGetSqlMoreFields()
    {
        $sql = "INSERT INTO someTable (id, name, data) VALUES (:id, :name, :data)";
        $data = ['id' => 'name', 'name' => 'someName', 'data' => 'notUsedHere'];
        $this->assertEquals($sql, $this->_object->setData($data)->getSql());
    }

    public function testExecute()
    {
        $this->_object->setData(['data' => 1]);
        $mockStatement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $mockStatement->expects($this->once())
            ->method('execute')
            ->with($this->_object->getData())
            ->willReturn(true);

        $mock = $this->getMockBuilder(MockPdo::class)->disableOriginalConstructor()->getMock();
        $mock->expects($this->once())
            ->method('prepare')
            ->with($this->_object->getSql())
            ->willReturn($mockStatement);
        $mock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(1447);
        $this->assertEquals(1447, $this->_object->execute($mock));
    }

    public function testExecuteNoRecords()
    {
        $this->expectException(NoRecordsAffectedException::class);
        $this->_object->setData(['data' => 1]);
        $mockStatement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $mockStatement->expects($this->once())->method('execute')->with($this->_object->getData())->willReturn(true);

        $mock = $this->getMockBuilder(MockPdo::class)->disableOriginalConstructor()->getMock();
        $mock->expects($this->once())->method('prepare')->with($this->_object->getSql())->willReturn($mockStatement);
        $this->_object->setIsStrict()->execute($mock);
    }

    public function testExecuteFail()
    {
        $this->_object->setData(['data' => 1]);
        $mockStatement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $mockStatement->expects($this->once())
            ->method('execute')
            ->with($this->_object->getData())
            ->willReturn(false);

        $mock = $this->getMockBuilder(MockPdo::class)->disableOriginalConstructor()->getMock();
        $mock->expects($this->once())
            ->method('prepare')
            ->with($this->_object->getSql())
            ->willReturn($mockStatement);
        $this->expectException(ExecutionErrorException::class);
        $this->_object->execute($mock);
    }
}
