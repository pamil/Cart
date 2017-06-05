<?php

declare(strict_types=1);

namespace Pamil\Cart\Infrastructure\Repository;

use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\Repository\AggregateNotFoundException;
use Pamil\Cart\Application\Exception\CartNotFoundException;
use Pamil\Cart\Application\Repository\CartRepository;
use Pamil\Cart\Domain\Model\Cart;
use Pamil\Cart\Domain\Model\CartId;

final class BroadwayCartRepository implements CartRepository
{
    /** @var EventSourcingRepository */
    private $eventSourcingRepository;

    public function __construct(EventSourcingRepository $eventSourcingRepository)
    {
        $this->eventSourcingRepository = $eventSourcingRepository;
    }

    /** {@inheritdoc} */
    public function get(CartId $cartId): Cart
    {
        try {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->eventSourcingRepository->load($cartId->toString());
        } catch (AggregateNotFoundException $exception) {
            throw CartNotFoundException::create($cartId);
        }
    }

    /** {@inheritdoc} */
    public function has(CartId $cartId): bool
    {
        try {
            $this->get($cartId);

            return true;
        } catch (CartNotFoundException $exception) {
            return false;
        }
    }

    /** {@inheritdoc} */
    public function save(Cart $cart): void
    {
        $this->eventSourcingRepository->save($cart);
    }
}
