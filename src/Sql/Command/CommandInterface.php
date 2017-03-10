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

/**
 * A common interface used for SQL commands
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
interface CommandInterface
{

    /**
     * A SQL statement that has been formatted per the PDO prepared statement syntax
     */
    public function getSql();

    /**
     * a single level array that fulfills the requirements of the SQL statement
     */
    public function getData();

    /**
     * this will take the sql and the data and execute them in a manner that makes sense for the command
     * @param \PDO $pdo a PDO connection to a given resource
     * @return mixed can return what makes most sense for the command
     */
    public function execute(\PDO $pdo);
}
