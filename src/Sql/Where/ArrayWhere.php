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

/**
 * Description of Where
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class ArrayWhere implements WhereStatementInterface {

    private $_data = [];
    private $_logicalOperator = 'and';
    private $_useParanthesis;

    public function __construct($data, $logicalOperator = 'and', $setUseParanthesis = true) {
        $this->setData($data)
                ->setLogicalOperator($logicalOperator)
                ->setUseParanthesis($setUseParanthesis);
    }

    public function getStatement() {
        $keys = array_keys($this->getData());
        $prefix = $keySuffix = ($this->getUseParanthesis() ? "(" : "");
        $suffix = $keyPrefix = ($this->getUseParanthesis() ? ")" : "");

        return $prefix . implode($keyPrefix . ' ' . $this->getLogicalOperator() . ' ' . $keySuffix, array_map(function($key) {
                            return sprintf('%1$s = :%1$s', $key);
                        }, $keys)) . $suffix;
    }

    public function getLogicalOperator() {
        return $this->_logicalOperator;
    }

    public function setLogicalOperator($logicalOperator) {
        $this->_logicalOperator = $logicalOperator;
        return $this;
    }

    public function getData() {
        return $this->_data;
    }

    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }

    public function getUseParanthesis() {
        return $this->_useParanthesis;
    }

    public function setUseParanthesis($useParanthesis = true) {
        $this->_useParanthesis = $useParanthesis;
        return $this;
    }

}
