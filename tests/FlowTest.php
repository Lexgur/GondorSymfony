<?php

declare(strict_types=1);

namespace App\Tests;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\PantherTestCase;

class FlowTest extends PantherTestCase
{
    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     * @throws Exception
     */
    public function testSomething(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/user/register');

        $this->assertSelectorTextContains('h1', 'REGISTER');

        $client->clickLink('Login'); // This waits for navigation

        $client->waitFor('form input[name="_username"]');

        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

}
