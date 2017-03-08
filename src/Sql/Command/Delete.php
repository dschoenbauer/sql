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
use DSchoenbauer\Sql\Exception\MethodNotValidException;
use DSchoenbauer\Sql\Where\WhereStatementInterface;
use PDO;

/**
 * Description of Delete
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Delete implements CommandInterface {

    private $_table;

    use WhereTrait;

    public function __construct($table, WhereStatementInterface $where = null) {
        $this->setTable($table)->setWhere($where);
    }

    public function execute(PDO $pdo) {
        try {
            $stmt = $pdo->prepare($this->getSql());
            if (count($this->getWhereData()) > 0) {
                return $stmt->execute($this->getWhereData());
            }
            return $stmt->execute();
        } catch (\Exception $exc) {
            throw new ExecutionErrorException($exc->getMessage());
        }
    }

    public function getData() {
        throw new MethodNotValidException();
    }

    public function getSql() {
        $sqlTemplate = 'DELETE FROM %1$s %2$s';
        return trim(sprintf($sqlTemplate, $this->getTable(), $this->getWhereStatement()));
    }

    public function getTable() {
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
        return $this;
    }

}