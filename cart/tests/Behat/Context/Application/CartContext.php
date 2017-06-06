<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Broadway\CommandHandling\CommandBus;
use Broadway\CommandHandling\SimpleCommandBus;
use Broadway\EventHandling\SimpleEventBus;
use Broadway\EventSourcing\AggregateFactory\ReflectionAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\InMemoryEventStore;
use Pamil\Cart\Application\Command\AddCartItem;
use Pamil\Cart\Application\Command\AdjustCartItemQuantity;
use Pamil\Cart\Application\Command\PickUpCart;
use Pamil\Cart\Application\Command\RemoveCartItem;
use Pamil\Cart\Application\CommandHandler\AddCartItemHandler;
use Pamil\Cart\Application\CommandHandler\AdjustCartItemQuantityHandler;
use Pamil\Cart\Application\CommandHandler\PickUpCartHandler;
use Pamil\Cart\Application\CommandHandler\RemoveCartItemHandler;
use Pamil\Cart\Application\Exception\CartAlreadyPickedUpException;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\Cart\Domain\Event\CartPickedUp;
use Pamil\Cart\Domain\Model\Cart;
use Pamil\Cart\Domain\Model\CartId;
use Pamil\Cart\Infrastructure\Repository\BroadwayCartRepository;
use Tests\Pamil\Cart\Behat\InfrastructureScenario;

final class CartContext implements Context
{
    /** @var InfrastructureScenario  */
    private $broadway;

    /** @var CommandBus */
    private $commandBus;

    public function __construct()
    {
        $eventStore = new InMemoryEventStore();
        $cartRepository = new BroadwayCartRepository(new EventSourcingRepository(
            $eventStore,
            new SimpleEventBus(),
            Cart::class,
            new ReflectionAggregateFactory()
        ));

        $this->broadway = new InfrastructureScenario($eventStore);

        $this->commandBus = new SimpleCommandBus();
        $this->commandBus->subscribe(new PickUpCartHandler($cartRepository));
        $this->commandBus->subscribe(new AddCartItemHandler($cartRepository));
        $this->commandBus->subscribe(new RemoveCartItemHandler($cartRepository));
        $this->commandBus->subscribe(new AdjustCartItemQuantityHandler($cartRepository));
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
                $this->commandBus->dispatch(new PickUpCart($cartId));
            })
        ;
    }

    /**
     * @When I try to pick that cart up again
     */
    public function tryToPickUpCart(): void
    {
        try {
            $this->broadway
                ->when(function (string $cartId) {
                    $this->commandBus->dispatch(new PickUpCart($cartId));
                })
            ;
        } catch (CartAlreadyPickedUpException $exception) {
            return;
        }
    }

    /**
     * @When I add :number :cartItemId cart items to that cart
     */
    public function addCartItem(int $number, string $cartItemId): void
    {
        $this->broadway->when(function (string $cartId) use ($number, $cartItemId) {
            $this->commandBus->dispatch(new AddCartItem($cartId, $cartItemId, $number));
        });
    }

    /**
     * @When I try to add :number :cartItemId cart items to that cart
     */
    public function tryToAddCartItem(int $number, string $cartItemId): void
    {
        try {
            $this->addCartItem($number, $cartItemId);
        } catch (\Throwable $throwable) {
            return;
        }
    }

    /**
     * @When I adjust :cartItemId cart item quantity to :number
     */
    public function adjustCartItemQuantity(string $cartItemId, int $number): void
    {
        $this->broadway->when(function (string $cartId) use ($number, $cartItemId) {
            $this->commandBus->dispatch(new AdjustCartItemQuantity($cartId, $cartItemId, $number));
        });
    }

    /**
     * @When I remove :cartItemId cart item from the cart
     */
    public function removeCartItem(string $cartItemId): void
    {
        $this->broadway->when(function (string $cartId) use ($cartItemId) {
            $this->commandBus->dispatch(new RemoveCartItem($cartId, $cartItemId));
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
