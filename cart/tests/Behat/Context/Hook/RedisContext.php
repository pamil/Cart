<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Predis\Client;

final class RedisContext implements Context
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** @BeforeScenario */
    public function purge(): void
    {
        $this->client->flushdb();
    }
}
