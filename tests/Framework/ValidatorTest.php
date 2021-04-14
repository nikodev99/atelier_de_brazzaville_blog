<?php

namespace Test\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertCount;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator([]);
    }

    public function testRequiredFailed()
    {
        $errors = $this->validator
            ->setParams(['name' => 'joe'])
            ->required('name', 'content')
            ->getErrors();
        self::assertCount(1, $errors);
        self::assertEquals("Le champ content est requis", $errors['content']);
    }

    public function testRequiredSuccess()
    {
        $errors = $this->validator
            ->setParams(['name' => 'joe', 'content' => 'content'])
            ->required('name', 'content')
            ->getErrors();
        self::assertCount(0, $errors);
    }

    public function testValueEmpty()
    {
        $errors = $this->validator
            ->setParams(['name' => 'joe', 'content' => ''])
            ->unEmptied('content')
            ->getErrors();
        self::assertCount(1, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = $this->validator
            ->setParams(['slug' => 'aze-aze-retry34'])
            ->slug('slug')
            ->getErrors();
        self::assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->validator
            ->setParams([
                'slug' => 'aze-aZe-retRy34',
                'slug2' => 'aze_aze-Retry34',
                'slug3' => 'aze-aze--retry34',
                'slug4' => 'aze-aze-azer-'
            ])->slug('slug', 'slug2', 'slug3', 'slug4')
            ->getErrors();
        self::assertEquals(['slug', 'slug2', 'slug3', 'slug4',], array_keys($errors));
    }

    public function testLength1()
    {
        self::assertCount(0, $this->validator->setParams(['title' =>  '123456789'])->length('title', 3)->getErrors());
    }

    public function testLength2()
    {
        $errors = $this->validator->setParams(['title' =>  '123456789'])->length('title', 12)->getErrors();
        self::assertCount(1, $errors);
        self::assertEquals("Le champ title doit contenir au moins plus de 12 caractères", (string)$errors['title']);
    }

    public function testLength3()
    {
        self::assertCount(1, $this->validator->setParams(['title' =>  '123456789'])->length('title', 3, 4)->getErrors());
    }

    public function testLength4()
    {
        self::assertCount(0, $this->validator->setParams(['title' =>  '123456789'])->length('title', 3, 30)->getErrors());
    }

    public function testLength5()
    {
        self::assertCount(0, $this->validator->setParams(['title' =>  '123456789'])->length('title', null, 30)->getErrors());
    }

    public function testLength6()
    {
        self::assertCount(1, $this->validator->setParams(['title' =>  '123456789'])->length('title', null, 8)->getErrors());
    }

    public function testDatetime()
    {
        $this->assertCount(0, $this->validator->setParams(['date' => '2012-12-12 11:12:45'])->datetime('date')->getErrors());
        $this->assertCount(0, $this->validator->setParams(['date' => '2012-12-12'])->datetime('date')->getErrors());
        $this->assertCount(1, $this->validator->setParams(['date' => '2012-20-12 11:12:45'])->datetime('date')->getErrors());
        $this->assertCount(1, $this->validator->setParams(['date' => '2013-02-29 11:12:45'])->datetime('date')->getErrors());
    }

    public function testExists()
    {

    }
}
