<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Write\Domain\Model;

use Broadway\EventSourcing\AggregateFactory\ReflectionAggregateFactory;
use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use Pamil\Cart\Common\Domain\Event\CartItemAdded;
use Pamil\Cart\Common\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Common\Domain\Event\CartItemRemoved;
use Pamil\Cart\Common\Domain\Event\CartPickedUp;
use Pamil\Cart\Write\Domain\Model\Cart;
use Pamil\Cart\Write\Domain\Model\CartId;
use Pamil\Cart\Write\Domain\Model\Quantity;
use Pamil\Cart\Write\Domain\Repository\ProductCatalogue;

final class CartTest extends AggregateRootScenarioTestCase
{
    /** {@inheritdoc} */
    protected function getAggregateRootClass(): string
    {
        return Cart::class;
    }

    /** {@inheritdoc} */
    protected function getAggregateRootFactory()
    {
        return new ReflectionAggregateFactory();
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
                new CartPickedUp($cartId->toString()),
            ])
        ;
    }

    /** @test */
    public function cart_item_added(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
            ])
            ->when(function (Cart $cart) {
                $cart->addItem(
                    new class implements ProductCatalogue {
                        public function has(string $productId): bool { return true; }
                    },
                    'Fallout',
                    new Quantity(2)
                );
            })
            ->then([
                new CartItemAdded('Fallout', 2),
            ])
        ;
    }

    /** @test */
    public function cart_item_quantity_adjusted(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
                new CartItemAdded('Fallout', 2),
            ])
            ->when(function (Cart $cart) {
                $cart->adjustItemQuantity('Fallout', new Quantity(1));
            })
            ->then([
                new CartItemQuantityAdjusted('Fallout', 1),
            ])
        ;
    }

    /** @test */
    public function cart_item_removed(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
                new CartItemAdded('Fallout', 2),
            ])
            ->when(function (Cart $cart) {
                $cart->removeItem('Fallout');
            })
            ->then([
                new CartItemRemoved('Fallout'),
            ])
        ;
    }

    /** @test */
    public function cart_item_quantity_adjusted_to_zero_removes_item(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
                new CartItemAdded('Fallout', 2),
            ])
            ->when(function (Cart $cart) {
                $cart->adjustItemQuantity('Fallout', new Quantity(0));
            })
            ->then([
                new CartItemRemoved('Fallout'),
            ])
        ;
    }

    /** @test */
    public function cart_item_added_twice_makes_quantity_adjusted(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
                new CartItemAdded('Fallout', 2),
            ])
            ->when(function (Cart $cart) {
                $cart->addItem(
                    new class implements ProductCatalogue {
                        public function has(string $productId): bool { return true; }
                    },
                    'Fallout',
                    new Quantity(3)
                );
            })
            ->then([
                new CartItemQuantityAdjusted('Fallout', 5),
            ])
        ;
    }

    /**
     * @test
     *
     * @expectedException \Pamil\Cart\Write\Domain\Exception\CartItemNotFoundException
     */
    public function cart_item_fails_while_adjusting_quantity_if_it_was_not_added_before()
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
            ])
            ->when(function (Cart $cart) {
                $cart->adjustItemQuantity('Fallout', new Quantity(1));
            })
        ;
    }

    /**
     * @test
     *
     * @expectedException \Pamil\Cart\Write\Domain\Exception\CartItemNotFoundException
     */
    public function cart_fails_while_removing_if_it_was_not_added_before()
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
            ])
            ->when(function (Cart $cart) {
                $cart->removeItem('Fallout');
            })
        ;
    }

    /**
     * @test
     *
     * @expectedException \Pamil\Cart\Write\Domain\Exception\ProductNotFoundException
     */
    public function cart_fails_while_trying_to_add_unexisting_product(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
            ])
            ->when(function (Cart $cart) {
                $cart->addItem(
                    new class implements ProductCatalogue {
                        public function has(string $productId): bool { return false; }
                    },
                    'Fallout',
                    new Quantity(2)
                );
            })
        ;
    }

    /**
     * @test
     *
     * @expectedException \Pamil\Cart\Write\Domain\Exception\CartItemsLimitReachedException
     */
    public function cart_fails_if_trying_to_add_more_than_three_different_products()
    {
        $cartId = CartId::generate();

        $this->scenario
            ->given([
                new CartPickedUp($cartId->toString()),
                new CartItemAdded('Fallout', 3),
                new CartItemAdded('Don\’t Starve', 5),
                new CartItemAdded('Icewind Dale', 9),
                new CartItemAdded('Fallout', 1),
            ])
            ->when(function (Cart $cart) {
                $cart->addItem(
                    new class implements ProductCatalogue {
                        public function has(string $productId): bool { return true; }
                    },
                    'Bloodborne',
                    new Quantity(2)
                );
            })
        ;
    }
}
