<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Sql\Exception;

use PHPUnit\Framework\TestCase;

/**
 * Description of NoRecordsAffectedExceptionTest
 *
 * @author David Schoenbauer
 */
class NoRecordsAffectedExceptionTest extends TestCase
{

    protected function setUp()
    {
        $this->object = new NoRecordsAffectedException();
    }

    public function testHasBaseInterface()
    {
        $this->assertInstanceOf(SqlExceptionInterface::class, $this->object);
    }
    
    public function testHasCorrectParent(){
        
    }
}
