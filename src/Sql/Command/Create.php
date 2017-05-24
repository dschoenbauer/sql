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
use DSchoenbauer\Sql\Exception\NoRecordsAffectedCreateException;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use PDO;

/**
 * adds values to a PDO connected resource
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 * @since v1.0.0
 */
class Create extends AbstractCommand
{


    private $table;
    private $data;

    /**
     * @param string $table table with which you wish to append to
     * @param array $data  a single level associative array containing keys that
     * represent the fields and values that represent new values to be added into
     * the table
     * @since v1.0.0
     */
    public function __construct($table, $data)
    {
        $this->setTable($table)->setData($data);
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

        $sqlTemplate = "INSERT INTO %s (%s) VALUES (:%s)";
        return sprintf(
            $sqlTemplate,
            $this->getTable(),
            implode(', ', array_keys($this->getData())),
            implode(', :', array_keys($this->getData()))
        );
    }

    /**
     * takes the SQL and the data provided and executes the query with the data
     * @param PDO $pdo a connection object that defines where the connection is
     * to be executed
     * @return string will return the lastInsertId from the PDO connection object
     * @throws ExecutionErrorException thrown when any exception or SQL failure
     * occurs
     * @since v1.0.0
     */
    public function execute(PDO $pdo)
    {
        try {
            $sql = $this->getSql();
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute($this->getData())) {
                throw new ExecutionErrorException($stmt->errorInfo()[2]);
            }
            $this->checkAffected($stmt, new NoRecordsAffectedCreateException());
            return $pdo->lastInsertId();
        } catch (NoRecordsAffectedException $exc) {
            throw $exc;
        } catch (\Exception $exc) {
            throw new ExecutionErrorException($exc->getMessage());
        }
    }

    /**
     * retrieves the table with which you wish to append to
     * @return string  table with which you wish to append to
     * @since v1.0.0
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * retrieves the data that is used to generate the create statement. The
     * fields of the array are used to generate the field list.
     * @return array a single level associative array containing keys that
     * represent the fields and values that represent new values to be added
     * into the table
     * @since v1.0.0
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * defines a table with which you wish to append to
     * @param string $table a table with which you wish to append to
     * @return Create for method chaining
     * @since v1.0.0
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * sets the data that is used to generate the create statement. The fields
     * of the array are used to generate the field list.
     * @param array $data a single level associative array containing keys that
     * represent the fields and values that represent new values to be added
     * into the table
     * @return Create for method chaining
     * @since v1.0.0
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }
}
