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
use DSchoenbauer\Sql\Exception\ExecutionErrorException;
use DSchoenbauer\Sql\Where\WhereStatementInterface;
use PDO;

/**
 * updates values in a PDO connected resource
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 * @since v1.0.0
 */
class Update implements CommandInterface
{

    private $table;
    private $data = [];

    use WhereTrait;

    /**
     * @param string $table table with which you wish to append to
     * @param array $data  a single level associative array containing keys that
     * represent the fields and values that represent new values to be added into
     * the table
     * @param null|WhereStatementInterface $where an object that is designed to return
     * a where statement to limit the data that is affected by the update
     * @since v1.0.0
     */
    public function __construct(
        $table,
        array $data,
        WhereStatementInterface $where = null
    ) {
    
        $this->setTable($table)->setData($data)->setWhere($where);
    }

    /**
     * takes the SQL and the data provided and executes the query with the data
     * @param PDO $pdo a connection object that defines where the connection is
     * to be executed
     * @return bool TRUE on success or FALSE on failure.
     * @throws EmptyDatasetException  if no data has been set no fields can be
     * discerned and no query can be made
     * @throws ExecutionErrorException thrown when any exception or SQL failure
     * occurs
     * @since v1.0.0
     */
    public function execute(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare($this->getSql());
            return $stmt->execute($this->getCombinedData());
        } catch (EmptyDatasetException $exc) {
            throw $exc;
        } catch (\Exception $exc) {
            throw new ExecutionErrorException($exc->getMessage());
        }
    }

    /**
     * Generates a SQL statement ready to be prepared for execution with the
     * intent of updating data
     * @return string a string that represents an update statement ready to be
     * prepared by PDO
     * @throws EmptyDatasetException if no data has been set no fields can be
     * discerned and no query can be made
     * @since v1.0.0
     */
    public function getSql()
    {
        if (count($this->getData()) === 0) {
            throw new EmptyDatasetException();
        }

        $sets = array_map(function ($value) {
            return sprintf('%1$s = :%1$s', $value);
        }, array_keys($this->getData()));
        $where = $this->getWhereStatement();
        $sqlTemplate = "UPDATE %s SET %s %s";
        return trim(sprintf(
            $sqlTemplate,
            $this->getTable(),
            implode(',', $sets),
            $where
        ));
    }

    /**
     * retrieves the table with which you wish to update
     * @return string  table with which you wish to update
     * @since v1.0.0
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * defines a table with which you wish to update
     * @param string $table a table with which you wish to update
     * @return Update for method chaining
     * @since v1.0.0
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Returns a combination of data from Where statement and from this object
     * @return array
     * @since v1.0.0
     */
    public function getCombinedData()
    {
        $whereData = $this->getWhereData();
        return array_merge($this->getData(), $whereData);
    }

    /**
     * retrieves the data that is used to generate the update statement. The
     * fields of the array are used to generate the field list.
     * @return array a single level associative array containing keys that
     * represent the fields and values that represent new values to be updated in the table
     * @since v1.0.0
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * sets the data that is used to generate the update statement. The fields
     * of the array are used to generate the field list.
     * @param array $data a single level associative array containing keys that
     * represent the fields and values that represent values to be updated into the table
     * @return Update for method chaining
     * @since v1.0.0
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
