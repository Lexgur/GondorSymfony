<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageControllerTest extends WebTestCase
{
    public function testSuccessfulRedirectionToWeaklingController(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'Welcome traveler,');
        $this->assertSelectorTextContains('p', 'So what is');

    }
}
