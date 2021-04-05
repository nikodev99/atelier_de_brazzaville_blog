<?php

namespace Test\Framework\Session;

use Framework\Session\ArraySession;
use Framework\Session\FlashService;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    private ArraySession $session;

    private FlashService $flashService;

    protected function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flashService = new FlashService($this->session);
    }

    public function testDeleteFlashAfterGettingIt()
    {
        $this->flashService->success('Bravo !!');
        self::assertEquals('Bravo !!', $this->flashService->get('success'));
        self::assertNull($this->session->get('flash'));
        self::assertEquals('Bravo !!', $this->flashService->get('success'));
        self::assertEquals('Bravo !!', $this->flashService->get('success'));
        self::assertEquals('Bravo !!', $this->flashService->get('success'));
    }
}
