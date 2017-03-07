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
use PDO;
use PDOStatement;

/**
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Select implements CommandInterface {

    private $_table;
    private $_fields = ['*'];
    private $_data = [];
    private $_fetchStyle = \PDO::FETCH_ASSOC;
    private $_fetchFlat = false;
    private $_defaultValue = [];

    use WhereTrait;

    public function __construct($table, $fields = [], WhereStatementInterface $where = null, $fetchStyle = \PDO::FETCH_ASSOC, $fetchFlat = false, $defaultValue = []) {
        $this->setTable($table)->setFields($fields)->setWhere($where)->setFetchStyle($fetchStyle)->setFetchFlat($fetchFlat)->setDefaultValue($defaultValue);
    }

    public function execute(PDO $pdo) {
        try {
            $stmt = $pdo->prepare($this->getSql());
            $this->statementExecute($stmt, $this->getWhereData());
            $data = $this->fetchData($stmt, $this->getFetchFlat(), $this->getFetchStyle());
            $this->setData($data ?: $this->getDefaultValue());
            return $this->getData();
        } catch (\Exception $exc) {
            throw new ExecutionErrorException($exc->getMessage());
        }
    }

    protected function statementExecute(PDOStatement $stmt, array $whereData) {
        if (count($whereData) > 0) {
            return $stmt->execute($whereData);
        }
        return $stmt->execute();
    }

    protected function fetchData(PDOStatement $stmt, $fetchFlat, $fetchStyle) {
        if ($fetchFlat) {
            return $stmt->fetch($fetchStyle);
        }
        return $stmt->fetchAll($fetchStyle);
    }

    public function getSql() {               
        $sqlTemplate = "SELECT %s FROM %s %s";
        $fieldsCompiled = implode(',', $this->getFields());
        return trim(sprintf($sqlTemplate, $fieldsCompiled, $this->getTable(), $this->getWhereStatement()));
    }

    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }

    public function getData() {
        return $this->_data;
    }

    public function getTable() {
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
        return $this;
    }

    public function getFields() {
        return $this->_fields;
    }

    public function setFields(array $fields = null) {
        $this->_fields = $fields ?: ["*"];
        return $this;
    }

    public function getFetchStyle() {
        return $this->_fetchStyle;
    }

    public function setFetchStyle($fetchStyle) {
        $this->_fetchStyle = $fetchStyle;
        return $this;
    }

    public function getFetchFlat() {
        return $this->_fetchFlat;
    }

    public function setFetchFlat($fetchFlat = true) {
        $this->_fetchFlat = $fetchFlat;
        return $this;
    }

    public function getDefaultValue() {
        return $this->_defaultValue;
    }

    public function setDefaultValue($defaultValue = []) {
        $this->_defaultValue = $defaultValue;
        return $this;
    }

}
