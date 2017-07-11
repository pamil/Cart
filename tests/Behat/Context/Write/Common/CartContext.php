<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Write\Common;

use Behat\Behat\Context\Context;
use Pamil\BroadwayScenarioHelper\Scenario\Write\WriteScenario;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\Cart\Domain\Event\CartPickedUp;
use Tests\Pamil\Cart\Behat\Storage\SharedStorage;

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
     * @Then :number :productId cart items should be added to the cart
     */
    public function cartItemShouldBeAdded(int $number, string $productId): void
    {
        $this->scenario()->then(new CartItemAdded($productId, $number));
    }

    /**
     * @Then :number :productId cart items should not be added to the cart
     */
    public function cartItemShouldNotBeAdded(int $number, string $productId): void
    {
        $this->scenario()->thenNot(new CartItemAdded($productId, $number));
    }

    /**
     * @Then the :productId cart item quantity should be adjusted to :number
     */
    public function cartItemQuantityShouldBeAdjusted(string $productId, int $number): void
    {
        $this->scenario()->then(new CartItemQuantityAdjusted($productId, $number));
    }

    /**
     * @Then the :productId cart item should be removed from the cart
     */
    public function cartItemShouldBeRemoved(string $productId): void
    {
        $this->scenario()->then(new CartItemRemoved($productId));
    }

    private function scenario(): WriteScenario
    {
        return $this->sharedStorage->get('scenario');
    }
}
