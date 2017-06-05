<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\ReflectionAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Pamil\Cart\Application\Command\PickUpCart;
use Pamil\Cart\Application\CommandHandler\PickUpCartHandler;
use Pamil\Cart\Domain\Event\CartWasPickedUp;
use Pamil\Cart\Domain\Model\Cart;
use Pamil\Cart\Domain\Model\CartId;
use Pamil\Cart\Infrastructure\Repository\BroadwayCartRepository;

final class PickUpCartHandlerTest extends CommandHandlerScenarioTestCase
{
    /** {@inheritdoc} */
    protected function createCommandHandler(EventStore $eventStore, EventBus $eventBus): CommandHandler
    {
        $cartRepository = new BroadwayCartRepository(new EventSourcingRepository(
            $eventStore,
            $eventBus,
            Cart::class,
            new ReflectionAggregateFactory()
        ));

        return new PickUpCartHandler($cartRepository);
    }

    /** @test */
    public function it_picks_up_the_cart(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->withAggregateId($cartId->toString())
            ->when(new PickUpCart($cartId))
            ->then([
                new CartWasPickedUp($cartId),
            ])
        ;
    }

    /**
     * @test
     *
     * @expectedException \Pamil\Cart\Application\CommandHandler\CartAlreadyPickedUpException
     */
    public function it_fails_if_trying_to_pick_up_the_same_cart_twice(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->withAggregateId($cartId->toString())
            ->given([new CartWasPickedUp($cartId)])
            ->when(new PickUpCart($cartId))
        ;
    }
}
