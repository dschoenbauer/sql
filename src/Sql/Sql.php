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
 * Description of Sql
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Sql {
    
    public static function with(){
        return new static();
    }

    public function create($table, array $data) {
        return new Create($table, $data);
    }

    public function select($table, $fields = [], WhereStatementInterface $where = null, $fetchStyle = PDO::FETCH_ASSOC, $fetchFlat = false, $defaultValue = []) {
        return new Select($table, $fields, $where, $fetchStyle, $fetchFlat, $defaultValue);
    }

    /**
     * @param string $table
     * @param array $data
     * @param WhereStatementInterface $where
     * @return Update
     */
    public function update($table, array $data, WhereStatementInterface $where = null) {
        return new Update($table, $data, $where);
    }

    /**
     * @param type $table
     * @param WhereStatementInterface $where
     * @return Delete
     */
    public function delete($table, WhereStatementInterface $where = null){
        return new Delete($table, $where);
    }
}
