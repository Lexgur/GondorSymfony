<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controller\DashboardController;
use App\Repository\ChallengeRepository;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    /**
     * @throws Exception
     */
    public function testHandleRedirect()
    {
        $controller = new DashboardController($this->createMock(ChallengeRepository::class));

        $result1 = $controller->handleRedirect(0);
        $result2 = $controller->handleRedirect(5);

        $this->assertEquals('/user/weakling', $result1);
        $this->assertEquals('/user/quests', $result2);
    }
}
