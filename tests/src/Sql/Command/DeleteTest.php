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

use DSchoenbauer\Sql\Exception\ExecutionErrorException;
use DSchoenbauer\Sql\Where\WhereStatementInterface;
use DSchoenbauer\Tests\Sql\MockPdo;
use PDOStatement;
use PHPUnit_Framework_TestCase;

/**
 * Description of DeleteTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class DeleteTest extends PHPUnit_Framework_TestCase {

    private $_object;

    protected function setUp() {
        $this->_object = new Delete('someTable');
    }

    public function testTableFromConstructor() {
        $this->assertEquals('someTable', $this->_object->getTable());
    }

    public function testTable() {
        $this->assertEquals('someOtherTable', $this->_object->setTable('someOtherTable')->getTable());
    }

    public function testGetData() {
        $this->assertEquals([], $this->_object->getData());
    }

    public function testGetDataWithWhere() {
        $mockWhere = $this->getMockBuilder(WhereStatementInterface::class)->getMock();
        $mockWhere->expects($this->any())
                ->method('getData')->willReturn(['field' => 'where', 'where' => 1]);
        $this->assertEquals(['field' => 'where', 'where' => 1], $this->_object->setWhere($mockWhere)->getData());
    }

    public function testGetSql() {
        $this->assertEquals("DELETE FROM someTable", $this->_object->getSql());
    }

    public function testGetSqlWithWhere() {
        $mock = $this->getMockBuilder(WhereStatementInterface::class)->getMock();
        $mock->expects($this->once())
                ->method('getStatement')
                ->willReturn('1 = 1');
        $this->assertEquals("DELETE FROM someTable WHERE 1 = 1", $this->_object->setWhere($mock)->getSql());
    }

    public function testExecuteWithWhere() {
        $mockWhere = $this->getMockBuilder(WhereStatementInterface::class)->getMock();
        $mockWhere->expects($this->any())
                ->method('getData')->willReturn(['field' => 'where', 'where' => 1]);
        $mockWhere->expects($this->once())
                ->method('getStatement')->willReturn('1 = 1');

        $this->_object->setWhere($mockWhere);

        $mockStatement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $mockStatement->expects($this->once())
                ->method('execute')
                ->with($this->_object->getWhereData())
                ->willReturn(true);


        $mockPdo = $this->getMockBuilder(MockPdo::class)->disableOriginalConstructor()->getMock();
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->with('DELETE FROM someTable WHERE 1 = 1')
                ->willReturn($mockStatement);

        $this->assertTrue($this->_object->execute($mockPdo));
    }

    public function testExecuteNoWhere() {
        $mockStatement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $mockStatement->expects($this->once())
                ->method('execute')
                ->with()
                ->willReturn(true);


        $mockPdo = $this->getMockBuilder(MockPdo::class)->disableOriginalConstructor()->getMock();
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->with('DELETE FROM someTable')
                ->willReturn($mockStatement);

        $this->assertTrue($this->_object->execute($mockPdo));
    }

    public function testExecuteNoFail() {
        $mockStatement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $mockStatement->expects($this->once())
                ->method('execute')
                ->with()
                ->willThrowException(new \Exception('test'));


        $mockPdo = $this->getMockBuilder(MockPdo::class)->disableOriginalConstructor()->getMock();
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->with('DELETE FROM someTable')
                ->willReturn($mockStatement);
        $this->expectException(ExecutionErrorException::class);
        $this->expectExceptionMessage('test');
        $this->_object->execute($mockPdo);
    }

}
