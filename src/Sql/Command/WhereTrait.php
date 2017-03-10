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

use DSchoenbauer\Sql\Where\WhereStatementInterface;

/**
 * Common functionality all items using a where statement use.
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
trait WhereTrait
{

    private $where;

    /**
     * Adds the prefix WHERE to what the where object has provided as a where statement
     * @return string returns a full WHERE statement will return null if not statement provided
     * @since v1.0.0
     */
    public function getWhereStatement()
    {
        if ($this->hasWhere()) {
            return sprintf("WHERE %s", $this->getWhere()->getStatement());
        }
        return null;
    }

    /**
     * checks to see if a where statement has been provided
     * @return bool checks to see if a where statement has been set
     * @since v1.0.0
     */
    protected function hasWhere()
    {
        return $this->where instanceof WhereStatementInterface;
    }

    /**
     * returns where statement given to object
     * @return WhereStatementInterface provides stored where statement object
     * @since v1.0.0
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * adds a where statement to a given statement
     * @param WhereStatementInterface $where where statement to be used added
     * @return inherit bubbling
     * @since v1.0.0
     */
    public function setWhere(WhereStatementInterface $where = null)
    {
        $this->where = $where;
        return $this;
    }

    /**
     * returns an array that can be used in a prepared statement
     * @return array an array of data specific to the data of the where statement
     * @since v1.0.0
     */
    public function getWhereData()
    {
        $whereData = [];
        if ($this->hasWhere()) {
            $whereData = $this->getWhere()->getData();
        }
        return $whereData;
    }
}
