<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Write\Application;

use Behat\Behat\Context\Context;
use Broadway\CommandHandling\CommandBus;
use Broadway\CommandHandling\SimpleCommandBus;
use Broadway\EventHandling\SimpleEventBus;
use Broadway\EventSourcing\AggregateFactory\ReflectionAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\InMemoryEventStore;
use Pamil\BroadwayScenarioHelper\Scenario\Write\InfrastructureWriteScenario;
use Tests\Pamil\Cart\Behat\Storage\SharedStorage;
use Pamil\CommandCart\Application\Command\AddCartItem;
use Pamil\CommandCart\Application\Command\AdjustCartItemQuantity;
use Pamil\CommandCart\Application\Command\PickUpCart;
use Pamil\CommandCart\Application\Command\RemoveCartItem;
use Pamil\CommandCart\Application\CommandHandler\AddCartItemHandler;
use Pamil\CommandCart\Application\CommandHandler\AdjustCartItemQuantityHandler;
use Pamil\CommandCart\Application\CommandHandler\PickUpCartHandler;
use Pamil\CommandCart\Application\CommandHandler\RemoveCartItemHandler;
use Pamil\CommandCart\Application\Exception\CartAlreadyPickedUpException;
use Pamil\CommandCart\Domain\Model\Cart;
use Pamil\CommandCart\Domain\Model\CartId;
use Pamil\CommandCart\Infrastructure\Repository\BroadwayCartRepository;
use Pamil\CommandCart\Infrastructure\Repository\InMemoryProductCatalogue;
use Pamil\CommandCart\Infrastructure\Repository\ProductCatalogue;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    /** @var ProductCatalogue */
    private $productCatalogue;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(SharedStorage $sharedStorage)
    {
        $eventStore = new InMemoryEventStore();

        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('scenario', new InfrastructureWriteScenario($eventStore));

        $this->productCatalogue = new InMemoryProductCatalogue();

        $cartRepository = new BroadwayCartRepository(new EventSourcingRepository(
            $eventStore,
            new SimpleEventBus(),
            Cart::class,
            new ReflectionAggregateFactory()
        ));

        $this->commandBus = new SimpleCommandBus();
        $this->commandBus->subscribe(new PickUpCartHandler($cartRepository));
        $this->commandBus->subscribe(new AddCartItemHandler($cartRepository, $this->productCatalogue));
        $this->commandBus->subscribe(new RemoveCartItemHandler($cartRepository));
        $this->commandBus->subscribe(new AdjustCartItemQuantityHandler($cartRepository));
    }

    /**
     * @Given product :product was added to the catalogue
     * @Given products :product1, :product2, :product3 and :product4 were added to the catalogue
     */
    public function productAddedToCatalogue(string ...$productsIds): void
    {
        foreach ($productsIds as $productId) {
            $this->productCatalogue->add($productId);
        }
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
     * @When I add :number :productId cart items to that cart
     */
    public function addCartItem(int $number, string $productId): void
    {
        $this->scenario()->when(function (string $cartId) use ($number, $productId) {
            $this->commandBus->dispatch(new AddCartItem($cartId, $productId, $number));
        });
    }

    /**
     * @When I try to add :number :productId cart items to that cart
     */
    public function tryToAddCartItem(int $number, string $productId): void
    {
        try {
            $this->addCartItem($number, $productId);
        } catch (\Throwable $throwable) {
            return;
        }
    }

    /**
     * @When I adjust :productId cart item quantity to :number
     */
    public function adjustCartItemQuantity(string $productId, int $number): void
    {
        $this->scenario()->when(function (string $cartId) use ($number, $productId) {
            $this->commandBus->dispatch(new AdjustCartItemQuantity($cartId, $productId, $number));
        });
    }

    /**
     * @When I remove :productId cart item from the cart
     */
    public function removeCartItem(string $productId): void
    {
        $this->scenario()->when(function (string $cartId) use ($productId) {
            $this->commandBus->dispatch(new RemoveCartItem($cartId, $productId));
        });
    }

    private function scenario(): InfrastructureWriteScenario
    {
        return $this->sharedStorage->get('scenario');
    }
}
