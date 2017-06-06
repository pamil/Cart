<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\Cart\Domain\Event\CartPickedUp;
use Pamil\Cart\Domain\Model\Cart;
use Pamil\Cart\Domain\Model\CartId;
use Pamil\Cart\Domain\Model\Quantity;
use Tests\Pamil\Cart\Behat\DomainScenario;

final class CartContext implements Context
{
    /** @var DomainScenario  */
    private $broadway;

    public function __construct()
    {
        $this->broadway = new DomainScenario(Cart::class);
    }

    /**
     * @When I pick up a cart
     */
    public function iPickUpCart(): void
    {
        $cartId = CartId::generate();

        $this->broadway
            ->withAggregateId($cartId->toString())
            ->when(function () use ($cartId) {
                return Cart::pickUp($cartId);
            })
        ;
    }

    /**
     * @Then the cart should be picked up
     */
    public function theCartShouldBePickedUp(): void
    {
        $this->broadway->then(function (Cart $cart) {
            return new CartPickedUp($cart->getAggregateRootId());
        });
    }

    /**
     * @Given the cart was picked up
     */
    public function theCartWasPickedUp(): void
    {
        $cartId = CartId::generate();

        $this->broadway
            ->withAggregateId($cartId->toString())
            ->given(new CartPickedUp($cartId->toString()))
        ;
    }

    /**
     * @When I add :number :cartItemId cart items to that cart
     */
    public function iAddCartItemsToThatCart(int $number, string $cartItemId): void
    {
        $this->broadway->when(function (Cart $cart) use ($number, $cartItemId) {
            $cart->addItem($cartItemId, new Quantity($number));
        });
    }

    /**
     * @Then :number :cartItemId cart items should be added to the cart
     */
    public function cartItemsShouldBeAddedToTheCart(int $number, string $cartItemId): void
    {
        $this->broadway->then(new CartItemAdded($cartItemId, $number));
    }

    /**
     * @Given :number :cartItemId cart items were added to the cart
     */
    public function cartItemsWereAddedToTheCart(int $number, string $cartItemId): void
    {
        $this->broadway->given(new CartItemAdded($cartItemId, $number));
    }

    /**
     * @When I remove :cartItemId cart item from the cart
     */
    public function iRemoveCartItemFromTheCart(string $cartItemId): void
    {
        $this->broadway->when(function (Cart $cart) use ($cartItemId) {
            $cart->removeItem($cartItemId);
        });
    }

    /**
     * @Then the :cartItemId cart item should be removed from the cart
     */
    public function theCartItemShouldBeRemovedFromTheCart(string $cartItemId): void
    {
        $this->broadway->then(new CartItemRemoved($cartItemId));
    }

    /**
     * @When I adjust :cartItemId cart item quantity to :number
     */
    public function iAdjustCartItemQuantity(string $cartItemId, int $number): void
    {
        $this->broadway->when(function (Cart $cart) use ($cartItemId, $number) {
            $cart->adjustItemQuantity($cartItemId, new Quantity($number));
        });
    }

    /**
     * @Then the :cartItemId cart item quantity should be adjusted to :number
     */
    public function theCartItemQuantityShouldBeAdjusted(string $cartItemId, int $number): void
    {
        $this->broadway->then(new CartItemQuantityAdjusted($cartItemId, $number));
    }

    /**
     * @When I try to add :number :cartItemId cart items to that cart
     */
    public function iTryToAddCartItemsToThatCart(int $number, string $cartItemId): void
    {
        try {
            $this->broadway->when(function (Cart $cart) use ($number, $cartItemId) {
                $cart->addItem($cartItemId, new Quantity($number));
            });
        } catch (\Throwable $throwable) {
            return;
        }
    }

    /**
     * @Then :number :cartItemId cart items should not be added to the cart
     */
    public function cartItemsShouldNotBeAddedToTheCart(int $number, string $cartItemId): void
    {
        $this->broadway->thenNot(new CartItemAdded($cartItemId, $number));
    }
}
