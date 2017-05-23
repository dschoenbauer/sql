<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Sql\Command;

use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use PDOStatement;

/**
 * Description of ErrorTrait
 *
 * @author David Schoenbauer
 */
trait ErrorTrait
{

    private $isStrict = false;

    public function getIsStrict()
    {
        return $this->isStrict;
    }

    public function setIsStrict($isStrict = true)
    {
        $this->isStrict = boolval($isStrict);
        return $this;
    }

    /**
     * Throws an exception if no records are found / affected
     * @param PDOStatement $statement
     * @return boolean
     * @throws NoRecordsAffectedException
     */
    public function checkAffected(PDOStatement $statement)
    {
        if (!boolval($statement->rowCount()) && $this->getIsStrict()) {
            throw new NoRecordsAffectedException();
        }
        return true;
    }
}
