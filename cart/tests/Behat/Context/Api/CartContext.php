<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Broadway\EventStore\EventStore;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\Cart\Domain\Event\CartPickedUp;
use Pamil\Cart\Domain\Model\CartId;
use Symfony\Bundle\FrameworkBundle\Client;
use Tests\Pamil\Cart\Behat\DomainScenario;
use Tests\Pamil\Cart\Behat\InfrastructureScenario;

final class CartContext implements Context
{
    /** @var DomainScenario  */
    private $broadway;

    /** @var Client */
    private $client;

    public function __construct(EventStore $eventStore, Client $client)
    {
        $this->broadway = new InfrastructureScenario($eventStore);
        $this->client = $client;
    }

    /**
     * @Given the cart was picked up
     */
    public function cartPickedUp(): void
    {
        $cartId = CartId::generate();

        $this->broadway
            ->withAggregateId($cartId->toString())
            ->given(new CartPickedUp($cartId->toString()))
        ;
    }

    /**
     * @Given :number :cartItemId cart items were added to the cart
     */
    public function cartItemAdded(int $number, string $cartItemId): void
    {
        $this->broadway->given(new CartItemAdded($cartItemId, $number));
    }

    /**
     * @When I pick up a cart
     */
    public function pickUpCart(): void
    {
        $cartId = CartId::generate();

        $this->broadway
            ->withAggregateId($cartId->toString())
            ->when(function (string $cartId) {
                $this->client->request('POST', '/' . $cartId);
            })
        ;
    }

    /**
     * @When I try to pick that cart up again
     */
    public function tryToPickUpCart(): void
    {
        $this->broadway
            ->when(function (string $cartId) {
                $this->client->request('POST', '/' . $cartId);
            })
        ;
    }

    /**
     * @When I add :number :cartItemId cart items to that cart
     * @When I try to add :number :cartItemId cart items to that cart
     */
    public function addCartItem(int $number, string $cartItemId): void
    {
        $this->broadway->when(function (string $cartId) use ($number, $cartItemId) {
            $this->client->request('POST', '/' . $cartId . '/items', [], [], [], json_encode([
                'cartItemId' => $cartItemId,
                'quantity' => $number,
            ]));
        });
    }

    /**
     * @When I adjust :cartItemId cart item quantity to :number
     */
    public function adjustCartItemQuantity(string $cartItemId, int $number): void
    {
        $this->broadway->when(function (string $cartId) use ($cartItemId, $number) {
            $this->client->request('PUT', '/' . $cartId . '/items', [], [], [], json_encode([
                'cartItemId' => $cartItemId,
                'quantity' => $number,
            ]));
        });
    }

    /**
     * @When I remove :cartItemId cart item from the cart
     */
    public function removeCartItem(string $cartItemId): void
    {
        $this->broadway->when(function (string $cartId) use ($cartItemId) {
            $this->client->request('DELETE', '/' . $cartId . '/items', [], [], [], json_encode([
                'cartItemId' => $cartItemId,
            ]));
        });
    }

    /**
     * @Then the cart should be picked up
     */
    public function cartShouldBePickedUp(): void
    {
        $this->broadway->then(function (string $cartId) {
            return new CartPickedUp($cartId);
        });
    }

    /**
     * @Then the cart should not be picked up
     */
    public function theCartShouldNotBePickedUp(): void
    {
        $this->broadway->thenNot(function (string $cartId) {
            return new CartPickedUp($cartId);
        });
    }

    /**
     * @Then :number :cartItemId cart items should be added to the cart
     */
    public function cartItemShouldBeAdded(int $number, string $cartItemId): void
    {
        $this->broadway->then(new CartItemAdded($cartItemId, $number));
    }

    /**
     * @Then :number :cartItemId cart items should not be added to the cart
     */
    public function cartItemShouldNotBeAdded(int $number, string $cartItemId): void
    {
        $this->broadway->thenNot(new CartItemAdded($cartItemId, $number));
    }

    /**
     * @Then the :cartItemId cart item quantity should be adjusted to :number
     */
    public function cartItemQuantityShouldBeAdjusted(string $cartItemId, int $number): void
    {
        $this->broadway->then(new CartItemQuantityAdjusted($cartItemId, $number));
    }

    /**
     * @Then the :cartItemId cart item should be removed from the cart
     */
    public function cartItemShouldBeRemoved(string $cartItemId): void
    {
        $this->broadway->then(new CartItemRemoved($cartItemId));
    }
}
