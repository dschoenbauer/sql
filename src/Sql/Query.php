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
namespace DSchoenbauer\Sql;

use DSchoenbauer\Sql\Command\Create;
use DSchoenbauer\Sql\Command\Delete;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Sql\Command\Update;
use DSchoenbauer\Sql\Where\WhereStatementInterface;
use PDO;

/**
 * a facade object that allows easier implementation of the SQL library
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 *
 */
class Query
{

    /**
     * provides a means for functional access to the objects of this library
     * @return \static a new instance of this object
     * @since v1.0.0
     */
    public static function with()
    {
        return new static();
    }

    /**
     * adds new data into a PDO connected resource
     * @param string $table table with which you wish to append to
     * @param array $data  a single level associative array containing keys that
     * represent the fields and values that represent new values to be added
     * into the table
     * @return Create a create object that manages the addition of new records
     * @since v1.0.0
     */
    public function create($table, array $data)
    {
        return new Create($table, $data);
    }

    /**
     * retrieves data from a PDO connected resource
     * @param string $table name of the table that houses the data
     * @param array $fields optional default value: empty array - defines which
     * fields are returned if no fields defined a star will be used
     * @param WhereStatementInterface $where optional default value: null -
     * object used to limit the returned results
     * @param integer $fetchStyle optional default value: PDO::FETCH_ASSOC -
     * sets how the PDO statement will return records
     * @param boolean $fetchFlat optional default value: false - true will
     * return one record, false will return all records
     * @param mixed $defaultValue optional default value: empty array -
     * value to be returned on query failure
     * @return Select the select object responsible for retrieving records
     * @since v1.0.0
     */
    public function select(
        $table,
        $fields = [],
        WhereStatementInterface $where = null,
        $fetchStyle = PDO::FETCH_ASSOC,
        $fetchFlat = false,
        $defaultValue = []
    ) {
        return new Select($table, $fields, $where, $fetchStyle, $fetchFlat, $defaultValue);
    }

    /**
     * changes values of existing data in a PDO connected resource
     * @param string $table table with which you wish to update
     * @param array $data a single level associative array containing keys that
     * represent the fields and values that represent new values to be updated
     * into the table
     * @param WhereStatementInterface $where an object that is designed to
     * return a where statement to limit the data that is affected by the update
     * @return Update the update object responsible to handling the update of
     * persistent records.
     * @since v1.0.0
     */
    public function update($table, array $data, WhereStatementInterface $where = null)
    {
        return new Update($table, $data, $where);
    }

    /**
     * removes records from a PDO connected resource
     * @param string $table table with which you wish to remove records from
     * @param WhereStatementInterface $where  an object that is designed to
     * return a where statement to limit the data that is affected by the delete
     * @return Delete the delete object responsible for handling removal of
     * persistent records
     * @since v1.0.0
     */
    public function delete($table, WhereStatementInterface $where = null)
    {
        return new Delete($table, $where);
    }
}
