<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Sql\Command;

use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Description of ErrorTraitTest
 *
 * @author David Schoenbauer
 */
class ErrorTraitTest extends TestCase
{

    /**
     *
     * @var ErrorTrait
     */
    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForTrait(ErrorTrait::class);
    }

    public function testIsStrict()
    {
        $this->assertTrue($this->object->setIsStrict()->getIsStrict());
    }

    public function testIsStrictTrue()
    {
        $this->assertTrue($this->object->setIsStrict(True)->getIsStrict());
    }

    public function testIsStrictNonBooleanStringTrue()
    {
        $this->assertTrue($this->object->setIsStrict("This should be evaled as true")->getIsStrict());
    }

    public function testIsStrictNonBooleanStringFalse()
    {
        $this->assertFalse($this->object->setIsStrict(0)->getIsStrict());
    }

    public function testIsStrictFalse()
    {
        $this->assertFalse($this->object->setIsStrict(False)->getIsStrict());
    }

    public function testCheckAffectedRecordNotStrict()
    {
        $this->assertTrue(
            $this->object
                ->setIsStrict(false)
                ->checkAffected($this->getPdoStatement(100))
        );
    }

    public function testCheckNotAffectedRecordNotStrict()
    {
        $this->assertTrue(
            $this->object
                ->setIsStrict(false)
                ->checkAffected($this->getPdoStatement(0))
        );
    }

    public function testCheckAffectedRecordStrict()
    {
        $this->assertTrue(
            $this->object
                ->setIsStrict(true)
                ->checkAffected($this->getPdoStatement(100))
        );
    }

    public function testCheckNotAffectedRecordStrict()
    {
        $this->expectException(NoRecordsAffectedException::class);
        $this->object->setIsStrict(true)->checkAffected($this->getPdoStatement(0));
    }

    public function getPdoStatement($affectedRecords = 0)
    {
        $stmt = $this->getMockBuilder(PDOStatement::class)->getMock();
        $stmt->expects($this->any())->method('rowCount')->willReturn($affectedRecords);
        return $stmt;
    }
}
