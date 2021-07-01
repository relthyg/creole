<?php

require_once 'PHPUnit2/Framework/TestCase.php';

require_once 'creole/util/sql/SQLStatementExtractor.php';

/**
 * TestCase for SQLStatementExtractorTest class
 * Generated by PHPEdit.XUnit Plugin.
 */
class SQLStatementExtractorTest extends PHPUnit2_Framework_TestCase
{
    /**
     * Path to file containing sql statements to extract.
     *
     * @var string
     */
    private $file;

    /**
     * Called before the test functions will be executed this function is defined in PHPUnit_TestCase and overwritten here.
     */
    public function setUp()
    {
        $this->file = CREOLE_TEST_BASE.'/etc/SQLStatementExtractor.sql';
    }

    private function assertExpectedStatements($statements)
    {
        $this->assertEquals(4, count($statements), 'Expected to find only 4 SQL statements.');
        $this->assertEquals("create function foobar AS ' select name as dog_name from animals where type = 'dog'; select name as monkey_name from animals where type = 'monkey'; ' language=plpgsql", $statements[0]);
        $this->assertEquals("insert into animals (id,name, description) VALUS (1, 'fred', ' Fred is a very, very special monkey; he is green. He ends lines in semi-colons; Just to throw off parsers; ')", $statements[1]);
        $this->assertEquals("insert into animals (id, name) values (1, 'frogger')", $statements[2]);
        $this->assertEquals("-- this is a normal line\n insert into animals (id, name) values (2, 'dogger')", $statements[3]);
    }

    /**
     * Regression test for SQLStatementExtractor.extractFile method.
     *
     * @test
     */
    public function extractFile()
    {
        $statements = SQLStatementExtractor::extractFile($this->file);
        $this->assertExpectedStatements($statements);
    }

    /**
     * Regression test for SQLStatementExtractor.extract method.
     *
     * @test
     */
    public function extract()
    {
        $buffer = file_get_contents($this->file);
        $statements = SQLStatementExtractor::extract($buffer);
        $this->assertExpectedStatements($statements);
    }
}
