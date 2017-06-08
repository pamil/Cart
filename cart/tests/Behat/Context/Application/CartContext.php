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
use Pamil\Cart\Write\Application\Command\AddCartItem;
use Pamil\Cart\Write\Application\Command\AdjustCartItemQuantity;
use Pamil\Cart\Write\Application\Command\PickUpCart;
use Pamil\Cart\Write\Application\Command\RemoveCartItem;
use Pamil\Cart\Write\Application\CommandHandler\AddCartItemHandler;
use Pamil\Cart\Write\Application\CommandHandler\AdjustCartItemQuantityHandler;
use Pamil\Cart\Write\Application\CommandHandler\PickUpCartHandler;
use Pamil\Cart\Write\Application\CommandHandler\RemoveCartItemHandler;
use Pamil\Cart\Write\Application\Exception\CartAlreadyPickedUpException;
use Pamil\Cart\Write\Domain\Model\Cart;
use Pamil\Cart\Write\Domain\Model\CartId;
use Pamil\Cart\Write\Infrastructure\Repository\BroadwayCartRepository;
use Tests\Pamil\Cart\Behat\InfrastructureScenario;
use Tests\Pamil\Cart\Behat\SharedStorage;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(SharedStorage $sharedStorage)
    {
        $eventStore = new InMemoryEventStore();

        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('cart', new InfrastructureScenario($eventStore));

        $cartRepository = new BroadwayCartRepository(new EventSourcingRepository(
            $eventStore,
            new SimpleEventBus(),
            Cart::class,
            new ReflectionAggregateFactory()
        ));

        $this->commandBus = new SimpleCommandBus();
        $this->commandBus->subscribe(new PickUpCartHandler($cartRepository));
        $this->commandBus->subscribe(new AddCartItemHandler($cartRepository));
        $this->commandBus->subscribe(new RemoveCartItemHandler($cartRepository));
        $this->commandBus->subscribe(new AdjustCartItemQuantityHandler($cartRepository));
    }

    /**
     * @When I pick up a cart
     */
    public function pickUpCart(): void
    {
        $cartId = CartId::generate();

        $this->scenario()
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
            $this->scenario()->when(function (string $cartId) {
                $this->commandBus->dispatch(new PickUpCart($cartId));
            });
        } catch (CartAlreadyPickedUpException $exception) {
            return;
        }
    }

    /**
     * @When I add :number :cartItemId cart items to that cart
     */
    public function addCartItem(int $number, string $cartItemId): void
    {
        $this->scenario()->when(function (string $cartId) use ($number, $cartItemId) {
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
        $this->scenario()->when(function (string $cartId) use ($number, $cartItemId) {
            $this->commandBus->dispatch(new AdjustCartItemQuantity($cartId, $cartItemId, $number));
        });
    }

    /**
     * @When I remove :cartItemId cart item from the cart
     */
    public function removeCartItem(string $cartItemId): void
    {
        $this->scenario()->when(function (string $cartId) use ($cartItemId) {
            $this->commandBus->dispatch(new RemoveCartItem($cartId, $cartItemId));
        });
    }

    private function scenario(): InfrastructureScenario
    {
        return $this->sharedStorage->get('cart');
    }
}
