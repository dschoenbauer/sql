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

/**
 * removes records from a PDO connected resource
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Delete implements CommandInterface {

    private $_table;

    use WhereTrait;

    /**
     * @param string $table  table with which you wish to remove records from
     * @param WhereStatementInterface $where an object that is designed to return a where statement to limit the data that is affected by the delete
     * @since v1.0.0
     */
    public function __construct($table, WhereStatementInterface $where = null) {
        $this->setTable($table)->setWhere($where);
    }

    /**
     * takes the SQL and the data provided and executes the query with the data
     * @param PDO $pdo a connection object that defines where the connection is to be executed
     * @return bool TRUE on success or FALSE on failure.
     * @throws ExecutionErrorException  thrown when any exception or SQL failure occurs
     * @since v1.0.0
     */
    public function execute(PDO $pdo) {
        try {
            $stmt = $pdo->prepare($this->getSql());
            if (count($this->getData()) > 0) {
                return $stmt->execute($this->getData());
            }
            return $stmt->execute();
        } catch (\Exception $exc) {
            throw new ExecutionErrorException($exc->getMessage());
        }
    }

    /**
     * retrieves the data that is uses to fulfill the requirements of a prepared statement
     * @return array a single level associative array containing keys that represent the fields and values that represent items to fulfill the requirements of a prepared statement
     * @since v1.0.0
     */
    public function getData() {
        return $this->getWhereData();
    }

    /**
     * Generates a SQL statement ready to be prepared for execution with the intent of removing data
     * @return string a string that represents a delete statement ready to be prepared by PDO
     * @since v1.0.0
     */
    public function getSql() {
        $sqlTemplate = 'DELETE FROM %1$s %2$s';
        return trim(sprintf($sqlTemplate, $this->getTable(), $this->getWhereStatement()));
    }

    /**
     * retrieves the table with which you wish to remove from
     * @return string  table with which you wish to remove from
     * @since v1.0.0
     */
    public function getTable() {
        return $this->_table;
    }

    /**
     * defines a table with which you wish to remove from
     * @param string $table table with which you wish to remove from
     * @return $this for method chaining
     * @since v1.0.0
     */
    public function setTable($table) {
        $this->_table = $table;
        return $this;
    }

}
