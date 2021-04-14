<?php

namespace Test\Database;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Test\Framework\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    private PostTable $postTable;

    public function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable($this->pdo);
    }

    public function testFind()
    {
        $this->seedDatabase();
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord()
    {
        $post = $this->postTable->find(1);
        $this->assertNull($post);
    }

    public function testUpdateFields()
    {
        $this->seedDatabase();
        $updated = $this->postTable->update(1, ['title' => 'Hello World', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        self::assertEquals(true, $updated);
        self::assertEquals('Hello World', $post->title);
        self::assertEquals('demo', $post->slug);
    }

    public function testInsert()
    {
        $id = $this->postTable->add([
            'title' => 'titre de demo', 'slug' => 'titre-de-demo', 'content' => 'contenu de demo', 'created_date' => '2020-6-6 12:00:00',
            'apdated_date' => '2020-6-6 12:00:00', 'view' => '0'
        ]);
        $post = $this->postTable->find($id);
        self::assertEquals(true, is_int($id));
        self::assertEquals('titre de demo', $post->title);
        self::assertEquals('titre-de-demo', $post->slug);
        self::assertEquals('contenu de demo', $post->content);
    }
}
