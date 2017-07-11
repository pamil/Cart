<?php

declare(strict_types=1);

namespace Tests\Pamil\Behat\Context\Read\Application;

use Behat\Behat\Context\Context;
use Broadway\EventHandling\SimpleEventBus;
use Pamil\BroadwayScenarioHelper\Scenario\Read\InfrastructureReadScenario;
use Pamil\QueryCart\Application\Projector\CartProjector;
use Pamil\QueryCart\Domain\Model\Cart;
use Pamil\QueryCart\Infrastructure\Repository\InMemoryCartRepository;
use PHPUnit\Framework\Assert;
use Tests\Pamil\Behat\Storage\SharedStorage;

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
        $this->sharedStorage->define('scenario', new InfrastructureReadScenario($eventBus));

        $this->repository = new InMemoryCartRepository();

        $eventBus->subscribe(new CartProjector($this->repository));
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
        return $this->sharedStorage->get('scenario');
    }
}
