<?php

require_once 'creole/IdGeneratorTest.php';

/**
 * Tests for the PgSQL IdGenerator class.
 *
 * @author Hans Lellelid <hans@xmpl.org>
 *
 * @version $Revision: 1.1 $
 */
class PgSQLIdGeneratorTest extends IdGeneratorTest
{
    /**
     * Ensures that drivers are implementing the correct Id Method.
     *
     * @test
     */
    public function getMethod()
    {
        $this->assertEquals(IdGenerator::SEQUENCE, $this->idgen->getIdMethod(), 0, 'PgSQL Id method should be SEQUENCE (but is not)');
    }
}
