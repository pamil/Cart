<?php

declare(strict_types=1);

namespace Pamil\Cart\Infrastructure\ReadModel;

use Predis\Client;

final class RedisCartRepository implements CartRepository
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** {@inheritdoc} */
    public function get(string $id): Cart
    {
        $payload = json_decode($this->client->get($id) ?: '', true);

        if (null === $payload) {
            throw CartNotFoundException::create($id);
        }

        return Cart::deserialize($payload);
    }

    /** {@inheritdoc} */
    public function save(Cart $cart): void
    {
        $this->client->set($cart->id(), json_encode($cart->serialize()));
    }
}
