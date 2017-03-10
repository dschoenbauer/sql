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
class Select implements CommandInterface
{

    private $table;
    private $fields = ['*'];
    private $data = [];
    private $fetchStyle = \PDO::FETCH_ASSOC;
    private $fetchFlat = false;
    private $defaultValue = [];

    use WhereTrait;

    public function __construct($table, $fields = [], WhereStatementInterface $where = null, $fetchStyle = \PDO::FETCH_ASSOC, $fetchFlat = false, $defaultValue = [])
    {
        $this->setTable($table)->setFields($fields)->setWhere($where)->setFetchStyle($fetchStyle)->setFetchFlat($fetchFlat)->setDefaultValue($defaultValue);
    }

    public function execute(PDO $pdo)
    {
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

    protected function statementExecute(PDOStatement $stmt, array $whereData)
    {
        if (count($whereData) > 0) {
            return $stmt->execute($whereData);
        }
        return $stmt->execute();
    }

    protected function fetchData(PDOStatement $stmt, $fetchFlat, $fetchStyle)
    {
        if ($fetchFlat) {
            return $stmt->fetch($fetchStyle);
        }
        return $stmt->fetchAll($fetchStyle);
    }

    public function getSql()
    {
        $sqlTemplate = "SELECT %s FROM %s %s";
        $fieldsCompiled = implode(',', $this->getFields());
        return trim(sprintf($sqlTemplate, $fieldsCompiled, $this->getTable(), $this->getWhereStatement()));
    }

    /**
     *
     * @param array $data
     * @return $this for method chaining
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     *
     * @param string $table
     * @return $this for method chaining
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Defines the fields to be returned, if no fields defined all fields are returned
     * @param array $fields
     * @return $this for method chaining
     */
    public function setFields(array $fields = null)
    {
        $this->fields = $fields ?: ["*"];
        return $this;
    }

    public function getFetchStyle()
    {
        return $this->fetchStyle;
    }

    /**
     * used to define how data is returned
     * @param int $fetchStyle one of the PDO::FETCH_*
     * @return $this for method chaining
     */
    public function setFetchStyle($fetchStyle)
    {
        $this->fetchStyle = $fetchStyle;
        return $this;
    }

    /**
     * sets if one or many records will be returned.
     * @return bool true for one record, false for all records
     */
    public function getFetchFlat()
    {
        return $this->fetchFlat;
    }

    /**
     * sets if one or many records will be returned.
     * @param boolean $fetchFlat optional default value: false - true will return one record, false will return all records
     * @return $this for method chaining
     */
    public function setFetchFlat($fetchFlat = true)
    {
        $this->fetchFlat = $fetchFlat;
        return $this;
    }

    /**
     * Value to be returned if no data is found or query fails
     * @return mixed return the value used when the query returns false
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Value to be returned if no data is found or query fails
     * @param mixed $defaultValue optional default value: empty array - value to be returned on query failure
     * @return $this for method chaining
     */
    public function setDefaultValue($defaultValue = [])
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
}
