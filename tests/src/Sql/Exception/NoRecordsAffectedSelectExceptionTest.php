<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Sql\Exception;

use DSchoenbauer\Tests\Sql\Exception\AbstractTestNoRecordsAffected;

/**
 * Description of NoRecordsAffectedSelectException
 *
 * @author David Schoenbauer
 */
class NoRecordsAffectedSelectExceptionTest extends AbstractTestNoRecordsAffected
{
    protected $object;
    
    protected function setUp()
    {
        $this->object = new NoRecordsAffectedSelectException();
    }
}
