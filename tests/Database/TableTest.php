<?php

namespace Test\Database;

use Framework\Database\Table;
use PHPUnit\Framework\TestCase;
use PDO;
use ReflectionClass;
use stdClass;

class TableTest extends TestCase
{
    private Table $table;

    protected function setUp(): void
    {
        $pdo = new PDO("sqlite::memory:", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
        $pdo->exec("CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )");

        $this->table = new Table($pdo);
        $reflection = new ReflectionClass($this->table);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->table, 'test');
    }

    public function testFind()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        $test = $this->table->find(1);
        self::assertInstanceOf(stdClass::class, $test);
        self::assertEquals('a1', $test->name);
    }

    public function testFindList()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        self::assertEquals(['1' => 'a1', '2' => 'a2'], $this->table->findList());
    }

    public function testExists()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        self::assertTrue($this->table->exists(1));
        self::assertTrue($this->table->exists(2));
        self::assertFalse($this->table->exists(2677));
    }
}
