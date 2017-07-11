<?php

declare(strict_types=1);

namespace Pamil\QueryCart\Application\Projector;

use Broadway\Domain\DomainMessage;
use Broadway\ReadModel\Projector;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\Cart\Domain\Event\CartPickedUp;
use Pamil\QueryCart\Application\Repository\CartRepository;
use Pamil\QueryCart\Domain\Model\Cart;

final class CartProjector extends Projector
{
    /** @var CartRepository */
    private $repository;

    public function __construct(CartRepository $repository)
    {
        $this->repository = $repository;
    }

    public function applyCartPickedUp(CartPickedUp $event): void
    {
        $this->repository->save(new Cart($event->cartId()));
    }

    public function applyCartItemAdded(CartItemAdded $event, DomainMessage $message): void
    {
        $cartDetails = $this->repository->get($message->getId());

        $cartDetails->addItem($event->productId(), $event->quantity());

        $this->repository->save($cartDetails);
    }

    public function applyCartItemQuantityAdjusted(CartItemQuantityAdjusted $event, DomainMessage $message): void
    {
        $cartDetails = $this->repository->get($message->getId());

        $cartDetails->adjustItemQuantity($event->productId(), $event->quantity());

        $this->repository->save($cartDetails);
    }

    public function applyCartItemRemoved(CartItemRemoved $event, DomainMessage $message): void
    {
        $cartDetails = $this->repository->get($message->getId());

        $cartDetails->removeItem($event->productId());

        $this->repository->save($cartDetails);
    }
}
