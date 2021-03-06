<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Infrastructure\Repository;

use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\Repository\AggregateNotFoundException;
use Pamil\CommandCart\Application\Exception\CartNotFoundException;
use Pamil\CommandCart\Application\Repository\CartRepository;
use Pamil\CommandCart\Domain\Model\Cart;

final class BroadwayCartRepository implements CartRepository
{
    /** @var EventSourcingRepository */
    private $eventSourcingRepository;

    public function __construct(EventSourcingRepository $eventSourcingRepository)
    {
        $this->eventSourcingRepository = $eventSourcingRepository;
    }

    /** {@inheritdoc} */
    public function get(string $cartId): Cart
    {
        try {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->eventSourcingRepository->load($cartId);
        } catch (AggregateNotFoundException $exception) {
            throw CartNotFoundException::create($cartId);
        }
    }

    /** {@inheritdoc} */
    public function has(string $cartId): bool
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
