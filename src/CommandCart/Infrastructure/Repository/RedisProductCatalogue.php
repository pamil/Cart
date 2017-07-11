<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Infrastructure\Repository;

use Predis\Client;

final class RedisProductCatalogue implements ProductCatalogue
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** {@inheritdoc} */
    public function add(string $productId): void
    {
        $this->client->set($this->redisId($productId), true);
    }

    /** {@inheritdoc} */
    public function remove(string $productId): void
    {
        $this->client->del([$this->redisId($productId)]);
    }

    /** {@inheritdoc} */
    public function has(string $productId): bool
    {
        return (bool) $this->client->exists($this->redisId($productId));
    }

    private function redisId(string $productId): string
    {
        return 'product:' . $productId;
    }
}
