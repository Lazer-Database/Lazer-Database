<?php

namespace Lazer\Classes;


class RelationTest extends \PHPUnit_Framework_TestCase {

    use \vfsHelper\Config;

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
