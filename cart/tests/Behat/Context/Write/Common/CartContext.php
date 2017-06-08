<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Write\Common;

use Behat\Behat\Context\Context;
use Pamil\Cart\Common\Domain\Event\CartItemAdded;
use Pamil\Cart\Common\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Common\Domain\Event\CartItemRemoved;
use Pamil\Cart\Common\Domain\Event\CartPickedUp;
use Tests\Pamil\Cart\Behat\Scenario\Write\WriteScenario;
use Tests\Pamil\Cart\Behat\SharedStorage;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    public function __construct(SharedStorage $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then the cart should be picked up
     */
    public function cartShouldBePickedUp(): void
    {
        $this->scenario()->then(function (string $cartId) {
            return new CartPickedUp($cartId);
        });
    }

    /**
     * @Then the cart should not be picked up
     */
    public function theCartShouldNotBePickedUp(): void
    {
        $this->scenario()->thenNot(function (string $cartId) {
            return new CartPickedUp($cartId);
        });
    }

    /**
     * @Then :number :cartItemId cart items should be added to the cart
     */
    public function cartItemShouldBeAdded(int $number, string $cartItemId): void
    {
        $this->scenario()->then(new CartItemAdded($cartItemId, $number));
    }

    /**
     * @Then :number :cartItemId cart items should not be added to the cart
     */
    public function cartItemShouldNotBeAdded(int $number, string $cartItemId): void
    {
        $this->scenario()->thenNot(new CartItemAdded($cartItemId, $number));
    }

    /**
     * @Then the :cartItemId cart item quantity should be adjusted to :number
     */
    public function cartItemQuantityShouldBeAdjusted(string $cartItemId, int $number): void
    {
        $this->scenario()->then(new CartItemQuantityAdjusted($cartItemId, $number));
    }

    /**
     * @Then the :cartItemId cart item should be removed from the cart
     */
    public function cartItemShouldBeRemoved(string $cartItemId): void
    {
        $this->scenario()->then(new CartItemRemoved($cartItemId));
    }

    private function scenario(): WriteScenario
    {
        return $this->sharedStorage->get('cart');
    }
}
