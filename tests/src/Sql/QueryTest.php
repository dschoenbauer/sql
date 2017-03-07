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

namespace DSchoenbauer\Tests\Sql;

use DSchoenbauer\Sql\Command\Create;
use DSchoenbauer\Sql\Command\Delete;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Sql\Command\Update;
use DSchoenbauer\Sql\Query;
use PHPUnit_Framework_TestCase;

/**
 * Description of SqlTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class QueryTest extends PHPUnit_Framework_TestCase {

    protected $_object;

    protected function setUp() {
        $this->_object = new Query();
    }

    public function testWith() {
        $this->assertInstanceOf(Query::class, Query::with());
    }

    public function testCreate() {
        $this->assertInstanceOf(Create::class, $this->_object->create('test', []));
    }

    public function testSelect() {
        $this->assertInstanceOf(Select::class, $this->_object->select('test'));
    }

    public function testUpdate() {
        $this->assertInstanceOf(Update::class, $this->_object->update('test',[]));
    }

    public function testDelete() {
        $this->assertInstanceOf(Delete::class, $this->_object->delete('test'));
    }

}
