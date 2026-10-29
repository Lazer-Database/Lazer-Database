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
       $this->assertTrue(true);
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

    public function testBelongsTo()
    {
        $tableUsers = 'test-users';
        $tablePosts = 'test-posts';

        // Remove $tableName if exists
        try {
            DB::remove($tableUsers);
        } catch (\Throwable $e) {
            unset($e);
        }

        // Create users table
        DB::create($tableUsers, array(
            'id' => 'integer',
            'name' => 'string',
        ));

        // Insert records in users table
        $row = DB::table($tableUsers);
        foreach (range(1, 2) as $number) {
            $row->name = 'user No_' . $number;
            $row->save();
        }

        // Remove $tableName if exists
        try {
            DB::remove($tablePosts);
        } catch (\Throwable $e) {
            unset($e);
        }

        // Create posts table
        DB::create($tablePosts, array(
            'id' => 'integer',
            'users_id' => 'integer',
            'text' => 'string',
        ));

        // Insert records in posts table: posts 1 & 2 belong to user 1, post 3 to user 2
        $row = DB::table($tablePosts);
        foreach (range(1, 3) as $number) {
            $row->text = 'post No_' . $number;
            $row->users_id = ($number <= 2) ? 1 : 2;
            $row->save();
        }

        // Create Relation: a post belongs to a user
        Relation::table($tablePosts)->belongsTo($tableUsers)->localKey('users_id')->foreignKey('id')->setRelation();

        // Check that relation configuration is stored correctly
        $relation = DB::table($tablePosts)->relations($tableUsers);
        $this->assertSame('belongsTo', $relation['type']);
        $this->assertSame('users_id', $relation['keys']['local']);
        $this->assertSame('id', $relation['keys']['foreign']);

        // Get Posts with their Users
        $posts = DB::table($tablePosts)->with($tableUsers)->findAll();

        $this->assertSame(3, count($posts));

        $expectedUsers = [
            1 => 'user No_1',
            2 => 'user No_1',
            3 => 'user No_2',
        ];

        foreach ($posts as $post) {
            $this->assertIsObject($post);

            $user = $post->{ucfirst($tableUsers)};

            $this->assertIsObject($user);
            $this->assertSame($post->users_id, $user->id);
            $this->assertSame($expectedUsers[$post->id], $user->name);
        }

        // Clean up test database
        DB::remove($tableUsers);
        DB::remove($tablePosts);
    }

    public function testHasAndBelongsToMany()
    {
        $tableNews = 'test-news';
        $tableTags = 'test-tags';
        $junction = 'test-news_test-tags';

        $this->removeIfExists([$tableNews, $tableTags, $junction]);

        DB::create($tableNews, array(
            'id' => 'integer',
            'topic' => 'string',
        ));
        $row = DB::table($tableNews);
        foreach (range(1, 3) as $number) {
            $row->topic = 'news No_' . $number;
            $row->save();
        }

        DB::create($tableTags, array(
            'id' => 'integer',
            'name' => 'string',
        ));
        $row = DB::table($tableTags);
        foreach (range(1, 3) as $number) {
            $row->name = 'tag No_' . $number;
            $row->save();
        }

        // many-to-many relation
        Relation::table($tableNews)->hasAndBelongsToMany($tableTags)->localKey('id')->foreignKey('id')->setRelation();

        // Junction table must be created automatically
        $this->assertSame($junction, Relation::table($tableNews)->with($tableTags)->getJunction());
        $this->assertTrue(DB::table($junction)->findAll() instanceof DB);

        // news 1 -> tags 1, 2 ; news 2 -> tags 2, 3
        $join = DB::table($junction);
        foreach ([[1, 1], [1, 2], [2, 2], [2, 3]] as $pair) {
            $join->{$tableNews . '_id'} = $pair[0];
            $join->{$tableTags . '_id'} = $pair[1];
            $join->save();
        }

        $news = DB::table($tableNews)->with($tableTags)->findAll();
        $this->assertSame(3, count($news));

        $expectedTags = [
            1 => ['tag No_1', 'tag No_2'],
            2 => ['tag No_2', 'tag No_3'],
            3 => [], // news without any tag in the junction table
        ];

        foreach ($news as $post) {
            $tags = $post->{ucfirst($tableTags)};

            $this->assertIsObject($tags);
            $this->assertSame($expectedTags[$post->id], $tags->findAll()->asArray(null, 'name'));
        }

        // Clean up, including the auto-created junction table
        DB::remove($tableNews);
        DB::remove($tableTags);
        DB::remove($junction);
    }

    public function testGetJunctionReturnsFalseForOtherRelationTypes()
    {
        $tableUsers = 'test-users';
        $tablePosts = 'test-posts';

        $this->removeIfExists([$tableUsers, $tablePosts]);

        DB::create($tableUsers, array('id' => 'integer', 'name' => 'string'));
        DB::create($tablePosts, array('id' => 'integer', 'users_id' => 'integer', 'text' => 'string'));

        Relation::table($tablePosts)->belongsTo($tableUsers)->localKey('users_id')->foreignKey('id');

        // no relation type set yet on a fresh instance
        $this->assertFalse(Relation::table($tablePosts)->getJunction());

        $this->removeIfExists([$tableUsers, $tablePosts]);
    }

    public function testGetRelation()
    {
        $tableUsers = 'test-users';
        $tablePosts = 'test-posts';

        $this->removeIfExists([$tableUsers, $tablePosts]);

        DB::create($tableUsers, array('id' => 'integer', 'name' => 'string'));
        DB::create($tablePosts, array('id' => 'integer', 'users_id' => 'integer', 'text' => 'string'));

        $relation = Relation::table($tablePosts)
            ->belongsTo($tableUsers)
            ->localKey('users_id')
            ->foreignKey('id');

        $this->assertSame([
            'tables' => ['local' => $tablePosts, 'foreign' => $tableUsers],
            'keys' => ['local' => 'users_id', 'foreign' => 'id'],
            'type' => 'belongsTo',
        ], $relation->getRelation());

        $this->removeIfExists([$tableUsers, $tablePosts]);
    }

    public function testSetRelationThrowsWhenKeysMissing()
    {
        $tableUsers = 'test-users';
        $tablePosts = 'test-posts';

        $this->removeIfExists([$tableUsers, $tablePosts]);

        DB::create($tableUsers, array('id' => 'integer', 'name' => 'string'));
        DB::create($tablePosts, array('id' => 'integer', 'users_id' => 'integer', 'text' => 'string'));

        $this->expectException(\Lazer\Classes\LazerException::class);

        Relation::table($tablePosts)->belongsTo($tableUsers)->setRelation();
    }

    public function testRemoveRelation()
    {
        $tableUsers = 'test-users';
        $tablePosts = 'test-posts';

        $this->removeIfExists([$tableUsers, $tablePosts]);

        DB::create($tableUsers, array('id' => 'integer', 'name' => 'string'));
        DB::create($tablePosts, array('id' => 'integer', 'users_id' => 'integer', 'text' => 'string'));

        Relation::table($tablePosts)->belongsTo($tableUsers)->localKey('users_id')->foreignKey('id')->setRelation();
        $this->assertArrayHasKey($tableUsers, DB::table($tablePosts)->relations());

        Relation::table($tablePosts)->with($tableUsers)->removeRelation();

        $this->assertArrayNotHasKey($tableUsers, DB::table($tablePosts)->relations());

        $this->removeIfExists([$tableUsers, $tablePosts]);
    }

    public function testNestedWithRelations()
    {
        $tableUsers = 'test-users';
        $tableNews = 'test-news';
        $tableComments = 'test-comments';

        $this->removeIfExists([$tableUsers, $tableNews, $tableComments]);

        DB::create($tableUsers, array('id' => 'integer', 'name' => 'string', 'email' => 'string'));
        $row = DB::table($tableUsers);
        foreach (range(1, 2) as $number) {
            $row->name = 'user No_' . $number;
            $row->email = 'user' . $number . '@example.com';
            $row->save();
        }

        DB::create($tableNews, array('id' => 'integer', 'topic' => 'string', 'author_id' => 'integer'));
        $row = DB::table($tableNews);
        $row->topic = 'news No_1';
        $row->author_id = 1;
        $row->save();

        DB::create($tableComments, array('id' => 'integer', 'content' => 'string', 'author_id' => 'integer', 'news_id' => 'integer'));
        $row = DB::table($tableComments);
        $row->content = 'comment No_1';
        $row->author_id = 2;
        $row->news_id = 1;
        $row->save();
        $row->content = 'comment No_2';
        $row->author_id = 1;
        $row->news_id = 1;
        $row->save();

        // news -> comments (hasMany) and comments -> users (belongsTo)
        Relation::table($tableNews)->hasMany($tableComments)->localKey('id')->foreignKey('news_id')->setRelation();
        Relation::table($tableComments)->belongsTo($tableUsers)->localKey('author_id')->foreignKey('id')->setRelation();

        // Chained relation: comments and, for each comment, its author
        $news = DB::table($tableNews)->with($tableComments . ':' . $tableUsers)->findAll();
        $this->assertSame(1, count($news));

        $expectedAuthors = [
            'comment No_1' => 'user No_2',
            'comment No_2' => 'user No_1',
        ];

        foreach ($news as $post) {
            $this->assertSame('news No_1', $post->topic);

            $comments = $post->{ucfirst($tableComments)};
            $this->assertInstanceOf(DB::class, $comments);

            $authors = [];
            foreach ($comments as $comment) {
                $this->assertIsObject($comment->{ucfirst($tableUsers)});
                $authors[$comment->content] = $comment->{ucfirst($tableUsers)}->name;
            }

            $this->assertSame($expectedAuthors, $authors);
        }

        $this->removeIfExists([$tableUsers, $tableNews, $tableComments]);
    }

    public function testRemoveManyToManyRelationCleansJunctionRelations()
    {
        $tableNews = 'test-news';
        $tableTags = 'test-tags';
        $junction = 'test-news_test-tags';

        $this->removeIfExists([$tableNews, $tableTags, $junction]);

        DB::create($tableNews, array('id' => 'integer', 'topic' => 'string'));
        DB::create($tableTags, array('id' => 'integer', 'name' => 'string'));

        Relation::table($tableNews)->hasAndBelongsToMany($tableTags)->localKey('id')->foreignKey('id')->setRelation();
        Relation::table($tableTags)->hasAndBelongsToMany($tableNews)->localKey('id')->foreignKey('id')->setRelation();

        // Both relation directions stored on the junction table
        $this->assertArrayHasKey($tableNews, DB::table($junction)->relations());
        $this->assertArrayHasKey($tableTags, DB::table($junction)->relations());

        Relation::table($tableNews)->with($tableTags)->removeRelation();

        // Relation to the foreign table and junction relations must be gone
        $this->assertArrayNotHasKey($tableTags, DB::table($tableNews)->relations());
        $this->assertArrayNotHasKey($tableNews, DB::table($junction)->relations());
        $this->assertArrayNotHasKey($tableTags, DB::table($junction)->relations());

        $this->removeIfExists([$tableNews, $tableTags, $junction]);
    }

    public function testRelationsReturnsSupportedTypes()
    {
        $this->assertSame(
            ['belongsTo', 'hasMany', 'hasAndBelongsToMany'],
            Relation::relations()
        );
    }

    /**
     * Remove tables from the vfs, ignoring tables that do not exist.
     *
     * @param array $tables
     */
    private function removeIfExists(array $tables)
    {
        foreach ($tables as $table) {
            try {
                DB::remove($table);
            } catch (\Throwable $e) {
                unset($e);
            }
        }
    }
}
