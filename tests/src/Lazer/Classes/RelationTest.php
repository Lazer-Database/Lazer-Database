<?php

namespace Lazer\Classes;


use vfsHelper\Config as testConfig;

class RelationTest extends \PHPUnit_Framework_TestCase {

    use testConfig;

    /**
     * @var Database
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->setUpFilesystem();
        $this->object = new Relation();
    }


    public function testDummy()
    {
       $this->markTestSkipped('TODO tests for relation');
    }


}
