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

namespace DSchoenbauer\Sql\Where;

use PHPUnit_Framework_TestCase;

/**
 * Description of ArrayWhereTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class ArrayWhereTest extends PHPUnit_Framework_TestCase {

    private $_object;

    protected function setUp() {
        $data = ['id' => 1];
        $this->_object = new ArrayWhere($data);
    }

    public function testGetStatementOffConstructorDefaultWithParentesis() {
        $this->assertEquals("(id = :id)", $this->_object->getStatement());
    }

    public function testGetStatementOffConstructorExplicitWithParentesis() {
        $this->assertEquals("(id = :id)", $this->_object->setUseParanthesis(true)->getStatement());
    }

    public function testGetStatementOffConstructorImplicitWithParentesis() {
        $this->assertEquals("(id = :id)", $this->_object->setUseParanthesis()->getStatement());
    }

    public function testGetStatementWithMoreDataWithParentesis() {
        $data = [
            'id' => 2,
            'active' => true
        ];
        $this->assertEquals("(id = :id) and (active = :active)", $this->_object->setData($data)->getStatement());
    }

    public function testGetStatementWithMoreDataWithParentesisOr() {
        $data = [
            'id' => 2,
            'active' => true
        ];
        $this->assertEquals("(id = :id) or (active = :active)", $this->_object->setLogicalOperator('or')->setData($data)->getStatement());
    }

    public function testGetStatementOffConstructorNoParentesis() {
        $this->assertEquals("id = :id", $this->_object->setUseParanthesis(false)->getStatement());
    }

    public function testLogicalOperatorOffConstructor() {
        $this->assertEquals("and", $this->_object->getLogicalOperator());
    }

    public function testLogicalOperatorThroughSet() {
        $this->assertEquals("or", $this->_object->setLogicalOperator('or')->getLogicalOperator());
    }

    public function testUseParenthesisOffConstructor() {
        $this->assertTrue($this->_object->getUseParanthesis());
    }

    public function testUseParenthesisExlicit() {
        $this->assertFalse($this->_object->setUseParanthesis(false)->getUseParanthesis());
    }

    public function testUseParenthesisImplicit() {
        $this->assertTrue($this->_object->setUseParanthesis()->getUseParanthesis());
    }

    public function testDataOffConstructor() {
        $this->assertEquals(['id' => 1], $this->_object->getData());
    }

    public function testData() {
        $data = ['id' => 1447];
        $this->assertEquals($data, $this->_object->setData($data)->getData());
    }

}
