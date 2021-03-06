<?php

declare(strict_types=1);

namespace Tests\Pamil\CommandCart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\ReflectionAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Pamil\Cart\Domain\Event\CartPickedUp;
use Pamil\CommandCart\Application\Command\PickUpCart;
use Pamil\CommandCart\Application\CommandHandler\PickUpCartHandler;
use Pamil\CommandCart\Domain\Model\Cart;
use Pamil\CommandCart\Domain\Model\CartId;
use Pamil\CommandCart\Infrastructure\Repository\BroadwayCartRepository;

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
            ->when(new PickUpCart($cartId->toString()))
            ->then([
                new CartPickedUp($cartId->toString()),
            ])
        ;
    }

    /**
     * @test
     *
     * @expectedException \Pamil\CommandCart\Application\Exception\CartAlreadyPickedUpException
     */
    public function it_fails_if_trying_to_pick_up_the_same_cart_twice(): void
    {
        $cartId = CartId::generate();

        $this->scenario
            ->withAggregateId($cartId->toString())
            ->given([new CartPickedUp($cartId->toString())])
            ->when(new PickUpCart($cartId->toString()))
        ;
    }

    /** @test */
    public function it_does_not_fail_if_trying_to_handle_unsupported_command(): void
    {
        $this->scenario
            ->when(new \stdClass())
        ;
    }
}
