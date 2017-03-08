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
    private $_whereData = [];
    private $_fieldOperator = 'and';
    private $_rowOperator = 'and';
    private $_useParanthesis;
    private $_saltSeed = 1;

    public function __construct($whereData, $fieldOperator = 'and', $rowOperator = 'or', $setUseParanthesis = true) {
        $this->setWhereData($whereData)
                ->setFieldOperator($fieldOperator)
                ->setRowOperator($rowOperator)
                ->setUseParanthesis($setUseParanthesis);
    }

    public function getStatement() {
        return $this->recursiveStatement($this->getWhereData());
    }

    protected function recursiveStatement(array $data) {
        $sql = [];
        if (!$this->isAssocArray($data)) {
            foreach ($data as $row) {
                $sql[] = $this->recursiveStatement($row);
            }
        } else {
            $sql[] = $this->buildRow($data, $this->_saltSeed++);
        }
        return "(" . implode(") " . $this->getRowOperator() . " (", $sql) . ")";
    }

    public function buildRow(array $assocArray, $keySalt) {
        $prefix = $keySuffix = ($this->getUseParanthesis() ? "(" : "");
        $suffix = $keyPrefix = ($this->getUseParanthesis() ? ")" : "");

        return $prefix . implode($keyPrefix . ' ' . $this->getFieldOperator() . ' ' . $keySuffix, array_map(function($key, $value) use ($keySalt) {
                            $saltedKey = $key . "-" .$keySalt;
                            $this->addData($saltedKey, $value);
                            return sprintf('%s = :%s', $key, $saltedKey);
                        }, array_keys($assocArray), array_values($assocArray))) . $suffix;
    }

    protected function isAssocArray(array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    public function getFieldOperator() {
        return $this->_fieldOperator;
    }

    public function setFieldOperator($logicalOperator) {
        $this->_fieldOperator = $logicalOperator;
        return $this;
    }

    public function getRowOperator() {
        return $this->_rowOperator;
    }

    public function setRowOperator($rowOperator) {
        $this->_rowOperator = $rowOperator;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getData() {
        return $this->_data;
    }

    public function addData($key, $value) {
        $this->_data[$key] = $value;
        return $this;
    }

    public function getUseParanthesis() {
        return $this->_useParanthesis;
    }

    public function setUseParanthesis($useParanthesis = true) {
        $this->_useParanthesis = $useParanthesis;
        return $this;
    }

    public function getWhereData() {
        return $this->_whereData;
    }

    public function setWhereData($whereData) {
        $this->_whereData = $whereData;
        return $this;
    }

}
