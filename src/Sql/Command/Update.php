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
use DSchoenbauer\Sql\Where\WhereStatementInterface;
use PDO;

/**
 * Description of Update
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Update implements CommandInterface {

    private $_table;
    private $_data = [];
    
    use WhereTrait;

    public function __construct($table, array $data, WhereStatementInterface $where = null) {
        $this->setTable($table)->setData($data)->setWhere($where);
    }

    public function execute(PDO $pdo) {
        $s = $pdo->prepare($this->getSql());
        return $s->execute($this->getCombinedData());
    }

    public function getSql() {
        if (count($this->getData()) === 0) {
            throw new EmptyDatasetException();
        }

        $sets = array_map(function($value) {
            return sprintf('%1$s = :%1$s', $value);
        }, array_keys($this->getData()));
        $where = $this->getWhereStatement();
        $sqlTemplate = "UPDATE %s SET %s %s";
        return trim(sprintf($sqlTemplate, $this->getTable(), implode(',', $sets), $where));
    }

    public function getTable() {
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
        return $this;
    }

    public function getCombinedData() {
        $whereData = $this->getWhereData();
        return array_merge($this->getData(), $whereData);
    }

    public function getData() {
        return $this->_data;
    }

    public function setData($data) {
        $this->_data = $data;
        return $this;
    }

}
