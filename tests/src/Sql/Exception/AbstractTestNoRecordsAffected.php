<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Tests\Sql\Exception;

use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use DSchoenbauer\Sql\Exception\SqlExceptionInterface;
use PHPUnit\Framework\TestCase;

/**
 * Description of AbstractNoRecordsAffectedTest
 *
 * @author David Schoenbauer
 */
abstract class AbstractTestNoRecordsAffected extends TestCase
{
    protected $object;
    
    public function testParent(){
        $this->assertInstanceOf(NoRecordsAffectedException::class, $this->object);
    }
    public function testHasInterface(){
        $this->assertInstanceOf(SqlExceptionInterface::class, $this->object);
    }
}
