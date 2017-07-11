<?php

declare(strict_types=1);

namespace Pamil\QueryCart\Infrastructure\Repository;

use Pamil\QueryCart\Application\Repository\CartNotFoundException;
use Pamil\QueryCart\Application\Repository\CartRepository;
use Pamil\QueryCart\Domain\Model\Cart;

final class InMemoryCartRepository implements CartRepository
{
    /** @var Cart[] */
    private $carts = [];

    /** {@inheritdoc} */
    public function get(string $id): Cart
    {
        if (!array_key_exists($id, $this->carts)) {
            throw CartNotFoundException::create($id);
        }

        return $this->carts[$id];
    }

    /** {@inheritdoc} */
    public function save(Cart $cart): void
    {
        $this->carts[$cart->id()] = $cart;
    }
}
