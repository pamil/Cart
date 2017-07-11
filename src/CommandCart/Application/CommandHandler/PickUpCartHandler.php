<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\CommandCart\Application\Command\PickUpCart;
use Pamil\CommandCart\Application\Exception\CartAlreadyPickedUpException;
use Pamil\CommandCart\Application\Repository\CartRepository;
use Pamil\CommandCart\Domain\Model\Cart;
use Pamil\CommandCart\Domain\Model\CartId;

final class PickUpCartHandler implements CommandHandler
{
    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /** @throws CartAlreadyPickedUpException */
    public function __invoke(PickUpCart $command): void
    {
        if ($this->cartRepository->has($command->cartId())) {
            throw CartAlreadyPickedUpException::create($command->cartId());
        }

        $cart = Cart::pickUp(CartId::fromString($command->cartId()));

        $this->cartRepository->save($cart);
    }

    public function handle($command): void
    {
        if (!$command instanceof PickUpCart) {
            return;
        }

        $this($command);
    }
}
