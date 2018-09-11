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
use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedSelectException;
use DSchoenbauer\Sql\Where\WhereStatementInterface;
use PDO;
use PDOStatement;

/**
 * retrieves data from a PDO connected resource
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Select extends AbstractCommand
{

    private $table;
    private $fields = ['*'];
    private $data = null;
    private $fetchStyle = \PDO::FETCH_ASSOC;
    private $fetchFlat = false;
    private $defaultValue = [];

    use WhereTrait;

    /**
     * retrieves data from a PDO connected resource
     * @param string $table name of the table that houses the data
     * @param array $fields optional default value: empty array - defines which
     * fields are returned if no fields defined a star will be used
     * @param null|WhereStatementInterface $where optional default value: null -
     * object used to limit the returned results
     * @param integer $fetchStyle optional default value: PDO::FETCH_ASSOC -
     * sets how the PDO statement will return records
     * @param boolean $fetchFlat optional default value: false - true will
     * return one record, false will return all records
     * @param mixed $defaultValue optional default value: empty array -
     * value to be returned on query failure
     * @since v1.0.0
     */
    public function __construct(
        $table,
        $fields = [],
        WhereStatementInterface $where = null,
        $fetchStyle = \PDO::FETCH_ASSOC,
        $fetchFlat = false,
        $defaultValue = []
    ) {
    


        $this->setTable($table)
            ->setFields($fields)
            ->setFetchStyle($fetchStyle)
            ->setFetchFlat($fetchFlat)
            ->setDefaultValue($defaultValue)
            ->setWhere($where);
    }

    /**
     * Runs a query and returns the result of the SQL
     * @param PDO $pdo a PDO  connection object
     * @return mixed with return the result set as defined by fetchStyle
     * @throws ExecutionErrorException on SQL error with the message of the exception
     * @since v1.0.0
     */
    public function execute(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare($this->getSql());
            $this->statementExecute($stmt, $this->getWhereData());
            $this->checkAffected($stmt, new NoRecordsAffectedSelectException());
            $data = $this->fetchData(
                $stmt,
                $this->getFetchFlat(),
                $this->getFetchStyle()
            );
            $this->setData($data ?: $this->getDefaultValue());
            return $this->getData();
        } catch (NoRecordsAffectedException $exc) {
            throw $exc;
        } catch (\Exception $exc) {
            throw new ExecutionErrorException($exc->getMessage());
        }
    }

    /**
     * Runs the sql statement inserting the query's data
     * @param PDOStatement $stmt PDO statement of a prepared SQL statement
     * @param array $whereData data to be used to fill out a where statement
     * @return bool true query succeeds, false there was an error executing the query
     * @since v1.0.0
     */
    protected function statementExecute(PDOStatement $stmt, array $whereData)
    {
        if (count($whereData) > 0) {
            return $stmt->execute($whereData);
        }
        return $stmt->execute();
    }

    /**
     * Fetches the data from PDO resource
     * @param PDOStatement $stmt PDO statement of a prepared SQL statement
     * @param bool $fetchFlat true returns one record, false returns all records
     * @param integer $fetchStyle a \PDO::FETCH_* variable defining the format of
     * the returned object
     * @return array returns the results of the query
     * @since v1.0.0
     */
    protected function fetchData(PDOStatement $stmt, $fetchFlat, $fetchStyle)
    {
        if ($fetchFlat) {
            return $stmt->fetch($fetchStyle);
        }
        return $stmt->fetchAll($fetchStyle);
    }

    /**
     * returns a PDO SQL string that has parameter syntax
     * @return string
     * @since v1.0.0
     */
    public function getSql()
    {
        $sqlTemplate = "SELECT %s FROM %s %s";
        $fieldsCompiled = implode(',', $this->getFields());
        return trim(sprintf(
            $sqlTemplate,
            $fieldsCompiled,
            $this->getTable(),
            $this->getWhereStatement()
        ));
    }

    /**
     * acts as a cache to house ran queries
     * @param mixed $data data to be stored
     * @return Select for method chaining
     * @since v1.0.0
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Acts as a cache array that holds the data returned from a query
     * @return mixed
     * @since v1.0.0
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * retrieves the table with which you wish to select from
     * @return string  table with which you wish to select from
     * @since v1.0.0
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * defines a table with which you wish to append to
     * @param string $table a table with which you wish to append to
     * @return Select for method chaining
     * @since v1.0.0
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Returns the fields to be returned, if no fields defined all fields are returned
     * @return array
     * @since v1.0.0
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Defines the fields to be returned, if no fields defined all fields are returned
     * @param null|array $fields
     * @return Select for method chaining
     * @since v1.0.0
     */
    public function setFields(array $fields = null)
    {
        $this->fields = $fields ?: ["*"];
        return $this;
    }

    /**
     * defines how data is returned
     * @return  int $fetchStyle one of the PDO::FETCH_*
     * @since v1.0.0
     */
    public function getFetchStyle()
    {
        return $this->fetchStyle;
    }

    /**
     * used to define how data is returned
     * @param int $fetchStyle one of the PDO::FETCH_*
     * @return Select for method chaining
     * @since v1.0.0
     */
    public function setFetchStyle($fetchStyle)
    {
        $this->fetchStyle = $fetchStyle;
        return $this;
    }

    /**
     * sets if one or many records will be returned.
     * @return bool true for one record, false for all records
     * @since v1.0.0
     */
    public function getFetchFlat()
    {
        return $this->fetchFlat;
    }

    /**
     * sets if one or many records will be returned.
     * @param boolean $fetchFlat optional default value: false - true will
     * return one record, false will return all records
     * @return Select for method chaining
     * @since v1.0.0
     */
    public function setFetchFlat($fetchFlat = true)
    {
        $this->fetchFlat = $fetchFlat;
        return $this;
    }

    /**
     * Value to be returned if no data is found or query fails
     * @return mixed return the value used when the query returns false
     * @since v1.0.0
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Value to be returned if no data is found or query fails
     * @param array $defaultValue optional default value: empty array - value to
     * be returned on query failure
     * @return Select for method chaining
     * @since v1.0.0
     */
    public function setDefaultValue($defaultValue = [])
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
}
