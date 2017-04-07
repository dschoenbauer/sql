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
namespace DSchoenbauer\Sql\Where;

/**
 * Description of Where
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class ArrayWhere implements WhereStatementInterface
{

    private $data = [];
    private $whereData = [];
    private $fieldOperator = 'and';
    private $rowOperator = 'or';
    private $useParanthesis;
    private $saltSeed = 1;
    private $statement;

    public function __construct($whereData, $fieldOperator = 'and', $rowOperator = 'or', $setUseParanthesis = true)
    {
        $this->setFieldOperator($fieldOperator)
            ->setRowOperator($rowOperator)
            ->setUseParanthesis($setUseParanthesis)
            ->setWhereData($whereData);
    }

    public function getStatement()
    {
        $this->build($this->getWhereData());
        return $this->statement;
    }

    public function setStatement($statement)
    {
        $this->statement = $statement;
        return $this;
    }

    protected function build(array $data)
    {
        $this->saltSeed = 1;
        $this->setStatement($this->recursiveStatement($data));
    }

    protected function recursiveStatement(array $data)
    {
        $sqlStatements = [];
        if (!$this->isAssocArray($data)) {
            foreach ($data as $row) {
                $sqlStatements[] = $this->recursiveStatement($row);
            }
        } else {
            $sqlStatements[] = $this->buildRow($data, $this->saltSeed);
            $this->saltSeed++;
        }

        list($prefix, $keyPrefix, $keySuffix, $suffix) = $this->getParenthesis($sqlStatements, 1);

        return $prefix . implode($keySuffix . $this->getRowOperator() . $keyPrefix, $sqlStatements) . $suffix;
    }

    private function getParenthesis(array $qualifyingArray = [], $count = -1)
    {
        //prefix, keyPrefix, keySuffix, suffix
        $parenthesis = [null, null, null, null];
        if ($this->getUseParanthesis() && count($qualifyingArray) > $count) {
            $parenthesis = ["(", " (", ") ", ")"];
        }
        return $parenthesis;
    }

    public function buildRow(array $assocArray, $keySalt)
    {
        list($prefix, $keySuffix, $keyPrefix,  $suffix) = $this->getParenthesis();

        $glue = $keyPrefix . $this->getFieldOperator() . $keySuffix;
        return $prefix . implode($glue, array_map(function ($key, $value) use ($keySalt) {
                    $saltedKey = $key . "-" . $keySalt;
                    $this->addData($saltedKey, $value);
                    return sprintf('%s = :%s', $key, $saltedKey);
        }, array_keys($assocArray), array_values($assocArray))) . $suffix;
    }

    protected function isAssocArray(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    public function getFieldOperator()
    {
        return $this->fieldOperator;
    }

    public function setFieldOperator($logicalOperator)
    {
        $this->fieldOperator = $logicalOperator;
        return $this;
    }

    public function getRowOperator()
    {
        return $this->rowOperator;
    }

    public function setRowOperator($rowOperator)
    {
        $this->rowOperator = $rowOperator;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getData()
    {
        $this->build($this->getWhereData());
        return $this->data;
    }

    public function addData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function getUseParanthesis()
    {
        return $this->useParanthesis;
    }

    public function setUseParanthesis($useParanthesis = true)
    {
        $this->useParanthesis = $useParanthesis;
        return $this;
    }

    public function getWhereData()
    {
        return $this->whereData;
    }

    public function setWhereData($whereData)
    {
        $this->whereData = $whereData;
        return $this;
    }
}
