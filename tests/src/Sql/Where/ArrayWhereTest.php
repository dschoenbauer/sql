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
        $this->assertEquals("((id = :id-1))", $this->_object->getStatement());
        $this->assertEquals(['id-1' => 1], $this->_object->getData());
    }

    public function testGetStatementOffConstructorExplicitWithParentesis() {
        $this->assertEquals("((id = :id-1))", $this->_object->setUseParanthesis(true)->getStatement());
        $this->assertEquals(['id-1' => 1], $this->_object->getData());
    }

    public function testGetStatementOffConstructorImplicitWithParentesis() {
        $this->assertEquals("((id = :id-1))", $this->_object->setUseParanthesis()->getStatement());
        $this->assertEquals(['id-1' => 1], $this->_object->getData());
    }

    public function testGetStatementWithMoreDataWithParentesis() {
        $data = [
            'id' => 2,
            'active' => true
        ];
        $this->assertEquals("((id = :id-1) and (active = :active-1))", $this->_object->setWhereData($data)->getStatement());
        $this->assertEquals(['id-1' => 2, 'active-1' => true], $this->_object->getData());
    }

    public function testGetStatementWithMoreDataWithParentesisOr() {
        $data = [
            'id' => 2,
            'active' => true
        ];
        $this->assertEquals("((id = :id-1) or (active = :active-1))", $this->_object->setFieldOperator('or')->setWhereData($data)->getStatement());
        $this->assertEquals(['id-1' => 2, 'active-1' => true], $this->_object->getData());
    }

    public function testGetStatementOffConstructorNoParentesis() {
        $this->assertEquals("(id = :id-1)", $this->_object->setUseParanthesis(false)->getStatement());
        $this->assertEquals(["id-1" => 1], $this->_object->getData());
    }

    public function testFieldOperatorOffConstructor() {
        $this->assertEquals("and", $this->_object->getFieldOperator());
    }

    public function testFieldOperatorThroughSet() {
        $this->assertEquals("or", $this->_object->setFieldOperator('or')->getFieldOperator());
    }

    public function testRowOperatorOffConstructor() {
        $this->assertEquals("or", $this->_object->getRowOperator());
    }

    public function testRowOperatorThroughSet() {
        $this->assertEquals("and", $this->_object->setRowOperator('and')->getRowOperator());
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

    public function testWhereDataOffConstructor() {
        $this->assertEquals(['id' => 1], $this->_object->getWhereData());
    }

    public function testWhereData() {
        $data = ['id' => 1447];
        $this->assertEquals($data, $this->_object->setWhereData($data)->getWhereData());
    }

    public function testComplexData() {
        $data = [
            ['id' => 1, 'active' => false],
            ['id' => 2, 'active' => false],
            ['id' => 3, 'active' => false],
            ['id' => 4, 'active' => false],
        ];
        $results = ['id-1' => 1, 'active-1' => false, 'id-2' => 2, 'active-2' => false,'id-3' => 3, 'active-3' => false,'id-4' => 4, 'active-4' => false];

        $stmt = "(((id = :id-1) and (active = :active-1))) or (((id = :id-2) and (active = :active-2))) or (((id = :id-3) and (active = :active-3))) or (((id = :id-4) and (active = :active-4)))";
        $this->assertEquals($stmt, $this->_object->setWhereData($data)->getStatement());
        $this->assertEquals($results, $this->_object->getData());
    }

}
