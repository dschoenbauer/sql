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
use PDO;

/**
 * Description of Create
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Create implements CommandInterface {

    private $_table;
    private $_data;

    public function __construct($table, $data) {
        $this->setTable($table)->setData($data);
    }

    /**
     * Generates a sql statement ready to be prepared for execution
     * @return string
     * @throws EmptyDatasetException
     */
    public function getSql() {
        if (count($this->getData()) === 0) {
            throw new EmptyDatasetException();
        }

        $sqlTemplate = "INSERT INTO %s (%s) VALUES (:%s)";
        return sprintf($sqlTemplate, $this->getTable(), implode(', ', array_keys($this->getData())), implode(', :', array_keys($this->getData())));
    }

    public function execute(PDO $pdo) {
        try {
            $sql = $this->getSql();
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute($this->getData())) {
                throw new ExecutionErrorException($stmt->errorInfo()[2]);
            }
            return $pdo->lastInsertId();
        } catch (\Exception $exc) {
            throw new ExecutionErrorException($exc->getMessage());
        }
    }

    public function getTable() {
        return $this->_table;
    }

    public function getData() {
        return $this->_data;
    }

    public function setTable($table) {
        $this->_table = $table;
        return $this;
    }

    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }

}
