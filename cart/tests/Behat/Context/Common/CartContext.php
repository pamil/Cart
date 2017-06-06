<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Common;

use Behat\Behat\Context\Context;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\Cart\Domain\Event\CartPickedUp;
use Pamil\Cart\Domain\Model\CartId;
use Tests\Pamil\Cart\Behat\ScenarioStorage;
use Tests\Pamil\Cart\Behat\Scenario;

final class CartContext implements Context
{
    /** @var ScenarioStorage */
    private $scenarioStorage;

    public function __construct(ScenarioStorage $scenarioStorage)
    {
        $this->scenarioStorage = $scenarioStorage;
    }

    /**
     * @Given the cart was picked up
     */
    public function cartPickedUp(): void
    {
        $cartId = CartId::generate();

        $this->scenario()
            ->withAggregateId($cartId->toString())
            ->given(new CartPickedUp($cartId->toString()))
        ;
    }

    /**
     * @Given :number :cartItemId cart items were added to the cart
     */
    public function cartItemAdded(int $number, string $cartItemId): void
    {
        $this->scenario()->given(new CartItemAdded($cartItemId, $number));
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

    private function scenario(): Scenario
    {
        return $this->scenarioStorage->get('cart');
    }
}
