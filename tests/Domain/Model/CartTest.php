<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Domain\Model;

use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use Pamil\Cart\Domain\Event\CartWasPickedUp;
use Pamil\Cart\Domain\Model\Cart;
use Pamil\Cart\Domain\Model\CartId;

final class CartTest extends AggregateRootScenarioTestCase
{
    /** {@inheritdoc} */
    protected function getAggregateRootClass(): string
    {
        return Cart::class;
    }

    /** @test */
    public function cart_is_picked_up(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->when(function () use ($cartId) {
                return Cart::pickUp($cartId);
            })
            ->then([
                new CartWasPickedUp($cartId),
            ])
        ;
    }
}
