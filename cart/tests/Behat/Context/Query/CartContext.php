<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Query;

use Behat\Behat\Context\Context;
use Broadway\EventHandling\SimpleEventBus;
use Pamil\Cart\Common\Domain\Event\CartItemAdded;
use Pamil\Cart\Common\Domain\Event\CartPickedUp;
use Pamil\Cart\Read\Application\Projector\CartProjector;
use Pamil\Cart\Read\Domain\Model\Cart;
use Pamil\Cart\Read\Infrastructure\Repository\InMemoryCartRepository;
use Pamil\Cart\Write\Domain\Model\CartId;
use PHPUnit\Framework\Assert;
use Tests\Pamil\Cart\Behat\Scenario\Read\InfrastructureReadScenario;
use Tests\Pamil\Cart\Behat\SharedStorage;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    /** @var InMemoryCartRepository  */
    private $repository;

    /** @var Cart */
    private $cart;

    public function __construct(SharedStorage $sharedStorage)
    {
        $eventBus = new SimpleEventBus();

        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('cart', new InfrastructureReadScenario($eventBus));
        $this->repository = new InMemoryCartRepository();

        $eventBus->subscribe(new CartProjector($this->repository));
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
     * @When I ask for the cart details
     */
    public function iAskForTheCartDetails(): void
    {
        $this->scenario()->when(function (string $cartId): void {
            $this->cart = $this->repository->get($cartId);
        });
    }

    /**
     * @Then there should be :number cart items in the cart
     */
    public function thereShouldBeNumberOfCartItemsInTheCart(int $number): void
    {
        $this->scenario()->then(function () use ($number) : void {
            Assert::assertCount($number, $this->cart->items());
        });
    }

    private function scenario(): InfrastructureReadScenario
    {
        return $this->sharedStorage->get('cart');
    }
}
