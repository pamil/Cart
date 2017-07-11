<?php

declare(strict_types=1);

namespace Pamil\Cart\Read\Infrastructure\Repository;

use Pamil\Cart\Read\Application\Repository\CartNotFoundException;
use Pamil\Cart\Read\Application\Repository\CartRepository;
use Pamil\Cart\Read\Domain\Model\Cart;
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
        $payload = json_decode($this->client->get($this->redisId($id)) ?: '', true);

        if (null === $payload) {
            throw CartNotFoundException::create($id);
        }

        return Cart::deserialize($payload);
    }

    /** {@inheritdoc} */
    public function save(Cart $cart): void
    {
        $this->client->set($this->redisId($cart->id()), json_encode($cart->serialize()));
    }

    private function redisId(string $cartId): string
    {
        return 'cart:' . $cartId;
    }
}
