<?php

namespace Test\Twig;

use DateTime;
use App\Blog\Twig\DateTimeTwigExtension;
use PHPUnit\Framework\TestCase;

class TimeTwigExceptionTest extends TestCase
{
    private DateTimeTwigExtension $time;

    protected function setUp(): void
    {
        $this->time = new DateTimeTwigExtension();
    }

    public function testDateFormat()
    {
        $date = new DateTime("2021-3-25 19:00:00");
        $this->assertEquals('il y a 2 jours', $this->time->ago($date));
    }
}
