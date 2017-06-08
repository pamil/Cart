<?php

declare(strict_types=1);

namespace Pamil\Cart\Read\Application\Projector;

use Broadway\Domain\DomainMessage;
use Broadway\ReadModel\Projector;
use Pamil\Cart\Common\Domain\Event\CartItemAdded;
use Pamil\Cart\Common\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Common\Domain\Event\CartItemRemoved;
use Pamil\Cart\Common\Domain\Event\CartPickedUp;
use Pamil\Cart\Read\Application\Repository\CartRepository;
use Pamil\Cart\Read\Domain\Model\Cart;

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

        $cartDetails->addItem($event->cartItemId(), $event->quantity());

        $this->repository->save($cartDetails);
    }

    public function applyCartItemQuantityAdjusted(CartItemQuantityAdjusted $event, DomainMessage $message): void
    {
        $cartDetails = $this->repository->get($message->getId());

        $cartDetails->adjustItemQuantity($event->cartItemId(), $event->quantity());

        $this->repository->save($cartDetails);
    }

    public function applyCartItemRemoved(CartItemRemoved $event, DomainMessage $message): void
    {
        $cartDetails = $this->repository->get($message->getId());

        $cartDetails->removeItem($event->cartItemId());

        $this->repository->save($cartDetails);
    }
}
