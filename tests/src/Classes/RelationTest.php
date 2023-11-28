<?php

declare(strict_types=1);

namespace Lazer\Test\Classes;

use Lazer\Classes\Database as DB;
use Lazer\Classes\Relation;
use Lazer\Test\VfsHelper\Config as TestHelper;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase {

    use TestHelper;

    /**
     * @var Database
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->setUpFilesystem();
        $this->object = new Relation();
    }

    public function testDummy()
    {
       $this->markTestSkipped('TODO tests for relation');
    }

    public function testHasMany()
    {
        $tableUsers = 'test-users';
        $tablePosts = 'test-posts';

        $tableName = $tableUsers;

        // Remove $tableName if exists
        try {
            DB::remove($tableName);
        } catch (\Throwable $e) {
            unset($e);
        }

        // Create table $tableName
        DB::create($tableName, array(
            'id' => 'integer',
            'name' => 'string',
        ));

        // Insert records in $tableName
        $row = DB::table($tableName);
        foreach (range(1, 2) as $number) {
            $row->name = 'user No_' . $number;
            $row->save();
        }

        $tableName = $tablePosts;

        // Remove $tableName if exists
        try {
            DB::remove($tableName);
        } catch (\Throwable $e) {
            unset($e);
        }

        // Create table $tableName
        DB::create($tableName, array(
            'id' => 'integer',
            'users_id' => 'integer',
            'text' => 'string',
        ));

        // Insert records in $tableName
        $row = DB::table($tableName);
        foreach (range(1, 2) as $number) {
            $row->text = 'post No_' . $number;
            $row->users_id = 1;
            $row->save();
        }

        // Create Relations
        Relation::table($tablePosts)->belongsTo($tableUsers)->localKey('users_id')->foreignKey('id')->setRelation();
        Relation::table($tableUsers)->hasMany($tablePosts)->localKey('id')->foreignKey('users_id')->setRelation();

        // Get Users with Posts
        $users = DB::table($tableUsers)->with($tablePosts)->findAll();

        $i = 1;

        foreach($users as $user) {
            $this->assertSame($user->name, 'user No_' . $i);
            // Check relation 'Posts'
            $posts = $user->{ucfirst($tablePosts)};

            $this->assertIsObject($posts);

            if ($user->id === 1) {
                // User with id 1 must have 2 posts
                $this->assertSame(count($posts), 2);
            }

            if ($user->id === 2) {
                // User with id 2 has 0 posts
                $this->assertSame(count($posts), 0);
            }
            
            $i++;            
        }

        // Clean up test database
        DB::remove($tableUsers);
        DB::remove($tablePosts);
    }
}
